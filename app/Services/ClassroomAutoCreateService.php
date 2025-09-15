<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\Classroom;
use App\Models\ClassroomParticipant;
use App\Models\ClassroomMaterial;
use App\Models\CourseAuth;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\MeetingService;
use App\Helpers\DateHelpers;

/**
 * Service for automatically creating classrooms from scheduled course dates
 * 
 * This service runs daily at 07:00 AM ET to scan today's course dates and create
 * fully prepared classroom sessions. It ensures idempotent operation - multiple
 * runs never create duplicates or corrupt existing data.
 */
class ClassroomAutoCreateService
{
    private array $config;
    private array $stats;
    private array $errors;
    private bool $dryRun;
    private ?string $onlyCourseDateId;

    public function __construct()
    {
        $this->config = config('auto_classroom', []);
        $this->resetStats();
        $this->dryRun = false;
        $this->onlyCourseDateId = null;
    }

    /**
     * Main entry point - create classrooms for today's course dates
     */
    public function createTodaysClassrooms(bool $dryRun = false, ?string $onlyCourseDateId = null): array
    {
        $this->dryRun = $dryRun;
        $this->onlyCourseDateId = $onlyCourseDateId;
        $this->resetStats();

        Log::info('ClassroomAutoCreate: Starting daily classroom creation', [
            'dry_run' => $this->dryRun,
            'only_course_date_id' => $this->onlyCourseDateId,
            'timezone' => $this->config['timezone'] ?? 'America/New_York',
        ]);

        // Step 0: Preconditions
        if (!$this->checkPreconditions()) {
            return $this->getResults();
        }

        // Step 1: Select today's work
        $candidateCourseDates = $this->getTodaysCourseDates();
        
        if ($candidateCourseDates->isEmpty()) {
            Log::info('ClassroomAutoCreate: No course dates found for today - clean exit');
            $this->stats['message'] = 'No course dates scheduled for today';
            return $this->getResults();
        }

        Log::info('ClassroomAutoCreate: Found candidate course dates', [
            'count' => $candidateCourseDates->count(),
            'course_date_ids' => $candidateCourseDates->pluck('id')->toArray(),
        ]);

        // Process each course date
        foreach ($candidateCourseDates as $courseDate) {
            $this->processCourseDate($courseDate);
        }

        // Final logging and metrics
        $this->finalizeResults();

        return $this->getResults();
    }

    /**
     * Step 0: Check preconditions
     */
    private function checkPreconditions(): bool
    {
        // Check feature flag
        if (!($this->config['enabled'] ?? true)) {
            $this->addError('Feature flag auto_classroom.enabled is false');
            return false;
        }

        // Check timezone
        $timezone = $this->config['timezone'] ?? 'America/New_York';
        try {
            Carbon::now($timezone);
        } catch (\Exception $e) {
            $this->addError("Invalid timezone configuration: {$timezone}");
            return false;
        }

        // Check required tables exist
        $requiredTables = ['course_dates', 'course_units', 'classrooms', 'courses'];
        foreach ($requiredTables as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                $this->addError("Required table {$table} does not exist");
                return false;
            }
        }

        Log::info('ClassroomAutoCreate: Preconditions passed');
        return true;
    }

    /**
     * Step 1: Get today's course dates that need classroom creation
     */
    private function getTodaysCourseDates(): Collection
    {
        $timezone = $this->config['timezone'] ?? 'America/New_York';
        $today = Carbon::now($timezone)->format('Y-m-d');

        $query = CourseDate::whereDate('starts_at', $today)
            ->whereIn('is_active', [true, 1])
            ->whereNull('classroom_created_at'); // Idempotency guard

        // If processing specific course date only
        if ($this->onlyCourseDateId) {
            $query->where('id', $this->onlyCourseDateId);
        }

        // Safety limit
        $maxPerRun = $this->config['safety']['max_classrooms_per_run'] ?? 100;
        $query->limit($maxPerRun);

        return $query->orderBy('starts_at')->get();
    }

    /**
     * Process a single course date
     */
    private function processCourseDate(CourseDate $courseDate): void
    {
        $courseDateId = $courseDate->id;
        
        Log::info("ClassroomAutoCreate: Processing CourseDate {$courseDateId}", [
            'course_date_id' => $courseDateId,
            'starts_at' => $courseDate->starts_at,
            'course_unit_id' => $courseDate->course_unit_id,
        ]);

        try {
            // Step 2: Idempotency Guard - check if classroom already exists
            if ($this->classroomAlreadyExists($courseDate)) {
                Log::info("ClassroomAutoCreate: Classroom already exists for CourseDate {$courseDateId}");
                $this->stats['skipped']++;
                return;
            }

            // Step 3: Eligibility checks
            if (!$this->isEligibleForClassroomCreation($courseDate)) {
                $this->stats['skipped']++;
                return;
            }

            // Step 4-15: Create classroom in transaction
            if (!$this->dryRun) {
                DB::transaction(function () use ($courseDate) {
                    $this->createClassroomForCourseDate($courseDate);
                });
            } else {
                Log::info("ClassroomAutoCreate: [DRY RUN] Would create classroom for CourseDate {$courseDate->id}");
            }

            $this->stats['created']++;

        } catch (\Exception $e) {
            $this->addError("Failed to process CourseDate {$courseDateId}: " . $e->getMessage());
            $this->stats['failed']++;
            
            Log::error("ClassroomAutoCreate: Error processing CourseDate {$courseDateId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Step 2: Check if classroom already exists (idempotency)
     */
    private function classroomAlreadyExists(CourseDate $courseDate): bool
    {
        return Classroom::where('course_date_id', $courseDate->id)->exists();
    }

    /**
     * Step 3: Check eligibility for classroom creation
     */
    private function isEligibleForClassroomCreation(CourseDate $courseDate): bool
    {
        $courseDateId = $courseDate->id;

        // Check CourseUnit is active
        $courseUnit = $courseDate->GetCourseUnit();
        if (!$courseUnit) {
            $this->addError("CourseDate {$courseDateId}: CourseUnit not found");
            return false;
        }

        // Check Course is active
        $course = $courseUnit->GetCourse();
        if (!$course || !$course->is_active) {
            $this->addError("CourseDate {$courseDateId}: Course is not active");
            return false;
        }

        // Check seats/capacity
        if ($this->config['safety']['validate_enrollment_capacity'] ?? true) {
            $enrollmentCount = $this->getEnrollmentCount($courseDate);
            if ($enrollmentCount <= 0) {
                Log::warning("CourseDate {$courseDateId}: No enrolled students found");
                // Don't fail - might be valid to create empty classroom
            }
        }

        // Check instructor assignment if required
        if ($this->config['safety']['require_instructor_assigned'] ?? true) {
            if (!$this->hasAssignedInstructor($courseDate)) {
                $this->addError("CourseDate {$courseDateId}: No instructor assigned");
                return false;
            }
        }

        return true;
    }

    /**
     * Main classroom creation workflow (Steps 4-15)
     */
    private function createClassroomForCourseDate(CourseDate $courseDate): void
    {
        // Step 5: Create classroom shell
        $classroom = $this->createClassroomShell($courseDate);

        // Step 6: Meeting resource
        $this->setupMeetingResource($classroom, $courseDate);

        // Step 7: Instructor binding
        $this->bindInstructor($classroom, $courseDate);

        // Step 8: Roster build
        $this->buildRoster($classroom, $courseDate);

        // Step 9: Materials seeding
        $this->seedMaterials($classroom, $courseDate);

        // Step 10: Capacity & policies
        $this->setCapacityAndPolicies($classroom, $courseDate);

        // Step 11: Cache & search index
        $this->updateCacheAndSearch($classroom);

        // Step 12: Status flip + stamp
        $this->finalizeClassroom($classroom, $courseDate);

        // Step 13: Notifications
        $this->sendNotifications($classroom, $courseDate);

        // Step 14: Audit & metrics
        $this->auditAndMetrics($classroom, $courseDate);

        Log::info("ClassroomAutoCreate: Successfully created classroom {$classroom->id} for CourseDate {$courseDate->id}");
    }

    /**
     * Step 5: Create classroom shell
     */
    private function createClassroomShell(CourseDate $courseDate): Classroom
    {
        $courseUnit = $courseDate->GetCourseUnit();
        $course = $courseUnit->GetCourse();

        $classroom = new Classroom([
            'course_date_id' => $courseDate->id,
            'course_unit_id' => $courseDate->course_unit_id,
            'title' => $course->title . ' - ' . $courseUnit->title,
            'starts_at' => $courseDate->starts_at,
            'ends_at' => $courseDate->ends_at,
            'modality' => 'online', // Default, can be overridden
            'location' => 'TBA',
            'status' => 'preparing',
            'capacity' => $this->config['classroom']['default_capacity'] ?? 30,
            'waitlist_policy' => $this->config['classroom']['default_waitlist_policy'] ?? 'none',
            'created_by' => null, // system created
            'creation_metadata' => [
                'created_by_system' => true,
                'auto_create_version' => '1.0',
                'created_at_et' => Carbon::now('America/New_York')->toISOString(),
            ],
        ]);

        $classroom->save();

        Log::debug("ClassroomAutoCreate: Created classroom shell", [
            'classroom_id' => $classroom->id,
            'course_date_id' => $courseDate->id,
            'title' => $classroom->title,
        ]);

        return $classroom;
    }

    /**
     * Step 6: Setup meeting resource
     */
    private function setupMeetingResource(Classroom $classroom, CourseDate $courseDate): void
    {
        if ($classroom->modality === 'online') {
            // Generate meeting link
            if ($this->config['meeting']['generate_meeting_links'] ?? true) {
                $meetingData = $this->generateMeetingLink($courseDate);
                
                $classroom->update([
                    'meeting_url' => $meetingData['url'] ?? null,
                    'meeting_id' => $meetingData['meeting_id'] ?? null,
                    'meeting_config' => $meetingData['config'] ?? [],
                    'join_instructions' => $meetingData['instructions'] ?? 'Please join the meeting using the provided link.',
                ]);
            }
        } else {
            // In-person location
            $classroom->update([
                'location' => $this->getPhysicalLocation($courseDate),
            ]);
        }
    }

    /**
     * Step 7: Bind instructor
     */
    private function bindInstructor(Classroom $classroom, CourseDate $courseDate): void
    {
        $instructor = $this->getAssignedInstructor($courseDate);
        
        if ($instructor) {
            ClassroomParticipant::create([
                'classroom_id' => $classroom->id,
                'user_id' => $instructor->id,
                'role' => 'instructor',
                'status' => 'enrolled',
                'metadata' => [
                    'call_time_minutes_before' => 30,
                    'assigned_by_system' => true,
                ],
            ]);

            Log::debug("ClassroomAutoCreate: Bound instructor to classroom", [
                'classroom_id' => $classroom->id,
                'instructor_id' => $instructor->id,
                'instructor_name' => $instructor->name ?? 'Unknown',
            ]);
        }
    }

    /**
     * Step 8: Build roster
     */
    private function buildRoster(Classroom $classroom, CourseDate $courseDate): void
    {
        $enrolledStudents = $this->getEnrolledStudents($courseDate);
        
        foreach ($enrolledStudents as $student) {
            ClassroomParticipant::create([
                'classroom_id' => $classroom->id,
                'user_id' => $student->id,
                'role' => 'student',
                'status' => 'enrolled',
                'metadata' => [
                    'enrollment_source' => 'course_auth',
                    'auto_enrolled' => true,
                ],
            ]);
        }

        Log::debug("ClassroomAutoCreate: Built classroom roster", [
            'classroom_id' => $classroom->id,
            'student_count' => $enrolledStudents->count(),
        ]);
    }

    /**
     * Step 9: Seed materials
     */
    private function seedMaterials(Classroom $classroom, CourseDate $courseDate): void
    {
        $defaultMaterials = $this->config['default_materials'] ?? [];
        
        foreach ($defaultMaterials as $key => $materialConfig) {
            ClassroomMaterial::create([
                'classroom_id' => $classroom->id,
                'type' => $materialConfig['type'],
                'title' => $materialConfig['title'],
                'description' => $materialConfig['description'] ?? null,
                'is_required' => $materialConfig['is_required'] ?? false,
                'sort_order' => $materialConfig['sort_order'] ?? 0,
                'is_active' => true,
                // file_path and url will be populated later as needed
            ]);
        }

        Log::debug("ClassroomAutoCreate: Seeded default materials", [
            'classroom_id' => $classroom->id,
            'material_count' => count($defaultMaterials),
        ]);
    }

    /**
     * Step 10: Set capacity and policies
     */
    private function setCapacityAndPolicies(Classroom $classroom, CourseDate $courseDate): void
    {
        $lateJoinCutoffMinutes = $this->config['classroom']['late_join_cutoff_minutes'] ?? 30;
        $lateJoinCutoff = Carbon::parse($classroom->starts_at)->addMinutes($lateJoinCutoffMinutes);

        $classroom->update([
            'late_join_cutoff' => $lateJoinCutoff,
        ]);
    }

    /**
     * Step 11: Cache and search index updates
     */
    private function updateCacheAndSearch(Classroom $classroom): void
    {
        if ($this->config['cache']['warm_caches'] ?? true) {
            // Warm relevant caches
            Cache::forget("classroom.{$classroom->id}");
            Cache::forget("classroom_participants.{$classroom->id}");
        }

        if ($this->config['cache']['update_search_index'] ?? true) {
            // Update search index (implementation depends on search system)
            // This could be Elasticsearch, Algolia, etc.
        }
    }

    /**
     * Step 12: Finalize classroom
     */
    private function finalizeClassroom(Classroom $classroom, CourseDate $courseDate): void
    {
        $now = now();
        
        $classroom->update([
            'status' => 'ready',
            'classroom_created_at' => $now,
        ]);

        // Update course date
        $courseDate->update([
            'classroom_created_at' => $now,
            'classroom_metadata' => [
                'classroom_id' => $classroom->id,
                'auto_created' => true,
                'created_at' => $now->toISOString(),
            ],
        ]);

        Log::debug("ClassroomAutoCreate: Finalized classroom", [
            'classroom_id' => $classroom->id,
            'status' => 'ready',
        ]);
    }

    /**
     * Step 13: Send notifications
     */
    private function sendNotifications(Classroom $classroom, CourseDate $courseDate): void
    {
        if (!($this->config['notifications']['enabled'] ?? true)) {
            return;
        }

        $instructor = $classroom->getInstructor();
        
        if ($instructor && ($this->config['notifications']['instructor_email'] ?? true)) {
            // Send instructor notification
            // This would integrate with your notification system
            Log::info("ClassroomAutoCreate: Would send instructor notification", [
                'instructor_id' => $instructor->id,
                'classroom_id' => $classroom->id,
            ]);
        }

        if ($this->config['notifications']['admin_alerts'] ?? true) {
            // Send admin notification for monitoring
            Log::info("ClassroomAutoCreate: Would send admin notification", [
                'classroom_id' => $classroom->id,
                'student_count' => $classroom->currentEnrollment(),
            ]);
        }
    }

    /**
     * Step 14: Audit and metrics
     */
    private function auditAndMetrics(Classroom $classroom, CourseDate $courseDate): void
    {
        $auditData = [
            'classroom_id' => $classroom->id,
            'course_date_id' => $courseDate->id,
            'created_at' => now()->toISOString(),
            'participants_count' => $classroom->currentEnrollment(),
            'materials_count' => $classroom->materials()->count(),
            'meeting_configured' => !empty($classroom->meeting_url),
            'processing_time_ms' => 0, // Would be calculated
        ];

        Log::info('ClassroomAutoCreate: Audit record', $auditData);

        // Emit metrics (this would integrate with your metrics system)
        if ($this->config['logging']['metrics_enabled'] ?? true) {
            // This could be StatsD, CloudWatch, etc.
        }
    }

    /**
     * Helper methods
     */

    private function hasAssignedInstructor(CourseDate $courseDate): bool
    {
        // This would check your instructor assignment system
        // Could be a field on course_dates, or a separate assignment table
        return true; // Placeholder
    }

    private function getAssignedInstructor(CourseDate $courseDate): ?User
    {
        // This would fetch the assigned instructor
        // Implementation depends on your instructor assignment system
        return User::where('role', 'instructor')->first(); // Placeholder
    }

    private function getEnrollmentCount(CourseDate $courseDate): int
    {
        // Get enrollment count for this course date
        $courseUnit = $courseDate->GetCourseUnit();
        $course = $courseUnit->GetCourse();
        
        return CourseAuth::where('course_id', $course->id)
            ->where('is_active', true)
            ->count();
    }

    private function getEnrolledStudents(CourseDate $courseDate): Collection
    {
        // Get enrolled students for this course date
        $courseUnit = $courseDate->GetCourseUnit();
        $course = $courseUnit->GetCourse();
        
        return User::whereHas('CourseAuths', function ($query) use ($course) {
            $query->where('course_id', $course->id)
                  ->where('is_active', true);
        })->get();
    }

    private function generateMeetingLink(CourseDate $courseDate): array
    {
        // This would integrate with Zoom API, Teams, etc.
        return [
            'url' => 'https://zoom.us/j/placeholder',
            'meeting_id' => 'placeholder-meeting-id',
            'config' => [
                'password' => 'placeholder',
                'waiting_room' => true,
            ],
            'instructions' => 'Join the meeting 5 minutes before the scheduled start time.',
        ];
    }

    private function getPhysicalLocation(CourseDate $courseDate): string
    {
        // This would determine physical classroom location
        return 'Main Building - Room 101';
    }

    /**
     * Statistics and results management
     */
    
    private function resetStats(): void
    {
        $this->stats = [
            'created' => 0,
            'skipped' => 0,
            'failed' => 0,
            'total_processed' => 0,
            'start_time' => now(),
            'end_time' => null,
            'duration_ms' => 0,
            'message' => null,
        ];
        $this->errors = [];
    }

    private function addError(string $error): void
    {
        $this->errors[] = $error;
        Log::error("ClassroomAutoCreate: {$error}");
    }

    private function finalizeResults(): void
    {
        $this->stats['end_time'] = now();
        $this->stats['duration_ms'] = $this->stats['start_time']->diffInMilliseconds($this->stats['end_time']);
        $this->stats['total_processed'] = $this->stats['created'] + $this->stats['skipped'] + $this->stats['failed'];

        Log::info('ClassroomAutoCreate: Completed classroom creation run', [
            'stats' => $this->stats,
            'error_count' => count($this->errors),
        ]);
    }

    private function getResults(): array
    {
        return [
            'success' => count($this->errors) === 0,
            'stats' => $this->stats,
            'errors' => $this->errors,
            'dry_run' => $this->dryRun,
        ];
    }
}
