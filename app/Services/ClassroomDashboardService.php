<?php

namespace App\Services;

use App\Models\User;
use App\Models\CourseDate;
use App\Classes\ClassroomQueries;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Exception;

/**
 * Classroom Dashboard Service
 *
 * Handles data preparation for classroom-related dashboard features.
 * Manages instructor data, course dates, and classroom scheduling.
 */
class ClassroomDashboardService
{
    protected ?User $user;

    public function __construct(?User $user = null)
    {
        $this->user = $user;

        Log::info('ClassroomDashboardService: Constructor called', [
            'user_passed' => !is_null($user),
            'user_id' => $user?->id,
            'user_stored' => !is_null($this->user),
            'stored_user_id' => $this->user?->id,
        ]);
    }

    /**
     * Get classroom dashboard data
     */
    public function getClassroomData(): array
    {
        if (!$this->user) {
            return $this->getEmptyClassroomData();
        }

        try {
            $instructorData = $this->getInstructorData();
            $courseDates = $this->getCourseDates();

            return [
                'instructors' => $instructorData,
                'courseDates' => $courseDates,
                'stats' => [
                    'total_instructors' => $instructorData->count(),
                    'total_course_dates' => $courseDates->count(),
                ],
            ];
        } catch (Exception $e) {
            Log::error('ClassroomDashboardService: Error getting classroom data', [
                'user_id' => $this->user?->id,
                'error' => $e->getMessage(),
            ]);
            return $this->getEmptyClassroomData();
        }
    }

    /**
     * Get instructor data for user's active courses
     * Currently returns empty collection as no classes are scheduled
     */
    public function getInstructorData(): Collection
    {
        if (!$this->user) {
            return collect();
        }

        try {
            // At this stage, no classes are scheduled so instructors should be empty
            // This would use ClassroomQueries when classes are available

            Log::info('ClassroomDashboardService: Getting instructor data', [
                'user_id' => $this->user?->id,
                'stage' => 'no_classes_scheduled'
            ]);

            return collect();

        } catch (Exception $e) {
            Log::error('ClassroomDashboardService: Error getting instructor data', [
                'user_id' => $this->user?->id,
                'error'   => $e->getMessage(),
            ]);
            return collect();
        }
    }

    /**
     * Get course dates for user's courses
     *
     * BUSINESS LOGIC:
     * - Query CourseDates where is_active = true (set at 6 AM by admin)
     * - Return CourseDate regardless of InstUnit existence
     * - Frontend determines WAITING vs ONLINE based on InstUnit presence
     * - is_active flag is the source of truth, NOT time window filtering
     */
    public function getCourseDates(): Collection
    {
        if (!$this->user) {
            return collect();
        }

        try {
            // Get student's enrolled course IDs
            $studentCourseIds = $this->user->CourseAuths()
                ->pluck('course_id')
                ->toArray();

            if (empty($studentCourseIds)) {
                Log::info('ClassroomDashboardService: No course authorizations found', [
                    'user_id' => $this->user->id,
                ]);
                return collect();
            }

            // Get all course unit IDs for student's courses
            $courseUnitIds = \App\Models\CourseUnit::whereIn('course_id', $studentCourseIds)
                ->pluck('id')
                ->toArray();

            if (empty($courseUnitIds)) {
                Log::info('ClassroomDashboardService: No course units found for student courses', [
                    'user_id' => $this->user->id,
                    'course_ids' => $studentCourseIds,
                ]);
                return collect();
            }

            // Query CourseDates by is_active flag (set at 6 AM)
            // This is the CORRECT approach for students (not time window filtering)
            $activeCourses = CourseDate::where('starts_at', '>=', \App\Helpers\DateHelpers::DayStartSQL())
                ->where('ends_at', '<=', \App\Helpers\DateHelpers::DayEndSQL())
                ->where('is_active', true)
                ->whereIn('course_unit_id', $courseUnitIds)
                ->with(['CourseUnit.Course']) // Load course relationship for display
                ->get();

            Log::info('ClassroomDashboardService: Found active CourseDates by is_active flag', [
                'user_id' => $this->user->id,
                'student_course_ids' => $studentCourseIds,
                'course_unit_ids' => $courseUnitIds,
                'active_course_dates_count' => $activeCourses->count(),
                'course_date_ids' => $activeCourses->pluck('id')->toArray(),
                'course_dates_with_inst_unit' => $activeCourses->filter(function ($cd) {
                    return !is_null($cd->InstUnit);
                })->count(),
                'course_dates_without_inst_unit' => $activeCourses->filter(function ($cd) {
                    return is_null($cd->InstUnit);
                })->count(),
            ]);

            return $activeCourses;

        } catch (Exception $e) {
            Log::error('ClassroomDashboardService: Error getting course dates', [
                'user_id' => $this->user?->id,
                'error'   => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return collect();
        }
    }

    /**
     * Get empty classroom data structure
     */
    protected function getEmptyClassroomData(): array
    {
        return [
            'instructors' => collect(),
            'courseDates' => collect(),
            'stats' => [
                'total_instructors' => 0,
                'total_course_dates' => 0,
            ],
        ];
    }

    /**
     * Clear cache for classroom data
     */
    public function clearCache(): void
    {
        if (!$this->user) {
            return;
        }

        try {
            $keys = [
                "classroom_dashboard_data_{$this->user->id}",
                "classroom_instructors_{$this->user->id}",
                "classroom_course_dates_{$this->user->id}",
            ];

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            Log::info("ClassroomDashboardService: Cleared cache for user: {$this->user->id}");
        } catch (Exception $e) {
            Log::error('ClassroomDashboardService: Error clearing cache', [
                'user_id' => $this->user?->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    // =========================================================================
    // SESSION MANAGEMENT METHODS
    // =========================================================================

    /**
     * Find or create student session (StudentUnit)
     * Checks for existing session within 12-hour window
     * 
     * @param int $courseAuthId
     * @param int $courseDateId
     * @return \App\Models\StudentUnit
     * @throws Exception
     */
    public function findOrCreateSession(int $courseAuthId, int $courseDateId): \App\Models\StudentUnit
    {
        $userId = $this->user?->id ?? auth()->id();

        if (!$userId) {
            throw new Exception('No authenticated user found');
        }

        // Check for existing session within 12-hour window
        $existingSession = \App\Models\StudentUnit::where('user_id', $userId)
            ->where('course_date_id', $courseDateId)
            ->whereNull('completed_at')
            ->where('created_at', '>', now()->subHours(12))
            ->first();

        if ($existingSession) {
            // Resume existing session
            $existingSession->update([
                'last_heartbeat_at' => now(),
            ]);

            Log::info('ClassroomDashboardService: Resumed existing session', [
                'student_unit_id' => $existingSession->id,
                'user_id' => $userId,
                'session_age_minutes' => $existingSession->created_at->diffInMinutes(now()),
            ]);

            return $existingSession;
        }

        // Create new session
        $sessionExpiresAt = now()->addHours(12);

        $studentUnit = \App\Models\StudentUnit::create([
            'user_id' => $userId,
            'course_date_id' => $courseDateId,
            'last_heartbeat_at' => now(),
            'session_expires_at' => $sessionExpiresAt,
            'created_at' => now(),
        ]);

        Log::info('ClassroomDashboardService: Created new session', [
            'student_unit_id' => $studentUnit->id,
            'user_id' => $userId,
            'course_date_id' => $courseDateId,
            'expires_at' => $sessionExpiresAt,
        ]);

        return $studentUnit;
    }

    /**
     * Update heartbeat for active session
     * Called every 30 seconds from frontend
     * 
     * @param int $studentUnitId
     * @return void
     */
    public function updateHeartbeat(int $studentUnitId): void
    {
        try {
            $studentUnit = \App\Models\StudentUnit::find($studentUnitId);

            if (!$studentUnit) {
                Log::warning('ClassroomDashboardService: StudentUnit not found for heartbeat', [
                    'student_unit_id' => $studentUnitId,
                ]);
                return;
            }

            $studentUnit->update([
                'last_heartbeat_at' => now(),
            ]);

            Log::debug('ClassroomDashboardService: Heartbeat updated', [
                'student_unit_id' => $studentUnitId,
                'timestamp' => now(),
            ]);

        } catch (Exception $e) {
            Log::error('ClassroomDashboardService: Error updating heartbeat', [
                'student_unit_id' => $studentUnitId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if session has expired (12+ hours old)
     * 
     * @param int $studentUnitId
     * @return bool
     */
    public function checkSessionExpiration(int $studentUnitId): bool
    {
        try {
            $studentUnit = \App\Models\StudentUnit::find($studentUnitId);

            if (!$studentUnit) {
                return true; // Consider missing session as expired
            }

            // Check if session_expires_at has passed
            if ($studentUnit->session_expires_at && now()->gt($studentUnit->session_expires_at)) {
                return true;
            }

            // Fallback: check if created_at is older than 12 hours
            if ($studentUnit->created_at->lt(now()->subHours(12))) {
                return true;
            }

            return false;

        } catch (Exception $e) {
            Log::error('ClassroomDashboardService: Error checking session expiration', [
                'student_unit_id' => $studentUnitId,
                'error' => $e->getMessage(),
            ]);
            return true; // Consider error as expired for safety
        }
    }

    /**
     * Fail the current active lesson due to disconnect/timeout
     * 
     * @param int $studentUnitId
     * @param string $reason (connection_lost, timeout, left_intentionally)
     * @return void
     */
    public function failActiveLesson(int $studentUnitId, string $reason): void
    {
        try {
            // Find active lesson (started but not completed/failed)
            $activeLesson = \App\Models\StudentLesson::where('student_unit_id', $studentUnitId)
                ->whereNull('completed_at')
                ->whereNull('failed_at')
                ->first();

            if (!$activeLesson) {
                Log::info('ClassroomDashboardService: No active lesson to fail', [
                    'student_unit_id' => $studentUnitId,
                ]);
                return;
            }

            // Mark lesson as failed
            $activeLesson->update([
                'failed_at' => now(),
                'failure_reason' => $reason,
            ]);

            Log::info('ClassroomDashboardService: Failed active lesson', [
                'student_unit_id' => $studentUnitId,
                'student_lesson_id' => $activeLesson->id,
                'lesson_id' => $activeLesson->lesson_id,
                'reason' => $reason,
            ]);

        } catch (Exception $e) {
            Log::error('ClassroomDashboardService: Error failing active lesson', [
                'student_unit_id' => $studentUnitId,
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Record intentional student leave
     * 
     * @param int $studentUnitId
     * @param string|null $reason
     * @return void
     */
    public function recordStudentLeave(int $studentUnitId, ?string $reason = null): void
    {
        try {
            $studentUnit = \App\Models\StudentUnit::find($studentUnitId);

            if (!$studentUnit) {
                Log::warning('ClassroomDashboardService: StudentUnit not found for leave', [
                    'student_unit_id' => $studentUnitId,
                ]);
                return;
            }

            // Mark as left
            $studentUnit->update([
                'left_at' => now(),
            ]);

            // Fail any active lesson
            $this->failActiveLesson($studentUnitId, 'left_intentionally');

            Log::info('ClassroomDashboardService: Recorded student leave', [
                'student_unit_id' => $studentUnitId,
                'user_id' => $studentUnit->user_id,
                'reason' => $reason,
            ]);

        } catch (Exception $e) {
            Log::error('ClassroomDashboardService: Error recording student leave', [
                'student_unit_id' => $studentUnitId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
