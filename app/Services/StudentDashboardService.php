<?php

namespace App\Services;

use App\Casts\JSONCast;
use App\Models\User;
use App\Services\ClassroomDashboardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Exception;

/**
 * Student Dashboard Service
 *
 * Handles data preparation and organization for the main student dashboard.
 * Focuses specifically on student course authorizations, progress tracking, and student-specific analytics.
 * Uses ClassroomDashboardService for classroom-related data.
 */
class StudentDashboardService
{
    protected ?User $user;
    protected ClassroomDashboardService $classroomService;

    public function __construct(?User $user = null)
    {
        $this->user = $user;
        $this->classroomService = new ClassroomDashboardService($user);


    }

    /**
     * Get complete dashboard data for the user
     */
    public function getClassData(): array
    {
        if (!$this->user) {
            return $this->getEmptyDashboardData();
        }

        try {
            $classroomData = $this->classroomService->getClassroomData();
            $instructorData = $classroomData['instructors'];
            $courseDates = $classroomData['courseDates'];

            return [
                'instructors' => $instructorData,
                'courseDates' => $courseDates,
            ];
        } catch (\Exception $e) {
            Log::error('StudentDashboardService: Error getting dashboard data: ' . $e->getMessage());
            return $this->getEmptyDashboardData();
        }
    }

    /**
     * Get empty dashboard data structure (for missing user / errors)
     */
    protected function getEmptyDashboardData(): array
    {
        return [
            'instructors' => collect(),
            'courseDates' => collect(),
        ];
    }

    /**
     * Get student data matching the actual User model structure
     * Based on user-provided data structure: id, fname, lname, email, class
     */
    public function getStudentData(): array
    {
        if (!$this->user) {
            return [];
        }

        return [
            'id' => $this->user->id,
            'fname' => $this->user->fname,
            'lname' => $this->user->lname,
            'email' => $this->user->email,
            'email_verified_at' => 'datetime',

            'password' => 'string',
            // 100
            'remember_token' => 'string',
            // 100

            'avatar' => 'string',
            'use_gravatar' => 'boolean',

            'student_info' => [
                'fname' => $this->user->fname,
                'middle_initial' => $this->user->student_info['middle_initial'] ?? null,
                'lname' => $this->user->lname,
                'email' => $this->user->email,
                'suffix' => $this->user->student_info['suffix'] ?? null,
                'dob' => $this->user->student_info['dob'] ?? null,
                'phone' => $this->user->student_info['phone'] ?? null,
            ],

            'email_opt_in' => 'boolean',

            'created_at' => 'datetime',
            'update_at' => 'datetime',
        ];
    }

    /**
     * Get merged course authorizations (active + completed) as a simple collection
     * SIMPLIFIED VERSION: Just get ALL course auths for now to get data flowing
     * INCLUDES course relationship data for table display
     */
    public function getCourseAuths(): Collection
    {
        if (!$this->user) {
            Log::warning('StudentDashboardService: No user provided to getCourseAuths');
            return collect();
        }

        try {
            Log::info('StudentDashboardService: Starting getCourseAuths (SIMPLIFIED)', [
                'user_id' => $this->user->id,
                'user_email' => $this->user->email
            ]);

            // SIMPLIFIED APPROACH: Just get ALL course auths for this user
            // Start with NO relationships to see if that's the issue
            $allCourseAuths = $this->user->CourseAuths()->get();

            Log::info('StudentDashboardService: Basic courseAuths query result', [
                'count' => $allCourseAuths->count(),
                'raw_sql' => $this->user->CourseAuths()->toSql(),
                'bindings' => $this->user->CourseAuths()->getBindings()
            ]);

            // If basic query works, try adding course relationship
            if ($allCourseAuths->count() > 0) {
                Log::info('StudentDashboardService: Basic query worked, trying with course relationship');
                $allCourseAuths = $this->user->CourseAuths()
                    ->with('course')
                    ->get();

                Log::info('StudentDashboardService: With course relationship', [
                    'count' => $allCourseAuths->count(),
                    'first_has_course' => $allCourseAuths->first()?->course !== null
                ]);
            }

            Log::info('StudentDashboardService: ALL course auths for user', [
                'user_id' => $this->user->id,
                'total_course_auths' => $allCourseAuths->count(),
                'course_auth_ids' => $allCourseAuths->pluck('id')->toArray(),
                'course_ids' => $allCourseAuths->pluck('course_id')->toArray(),
                'first_auth_sample' => $allCourseAuths->first() ? [
                    'id' => $allCourseAuths->first()->id,
                    'course_id' => $allCourseAuths->first()->course_id,
                    'user_id' => $allCourseAuths->first()->user_id,
                    'expire_date' => $allCourseAuths->first()->expire_date,
                    'completed_at' => $allCourseAuths->first()->completed_at,
                    'disabled_at' => $allCourseAuths->first()->disabled_at,
                    'has_course' => $allCourseAuths->first()->course !== null,
                    'course_title' => $allCourseAuths->first()->course?->title ?? 'No Course',
                ] : 'NO AUTHS FOUND'
            ]);

            // If we have course auths, log details about each one
            if ($allCourseAuths->count() > 0) {
                foreach ($allCourseAuths as $index => $auth) {
                    Log::info("StudentDashboardService: CourseAuth #{$index}", [
                        'auth_id' => $auth->id,
                        'course_id' => $auth->course_id,
                        'course_title' => $auth->course?->title ?? 'NO COURSE LOADED',
                        'has_course_relation' => $auth->course !== null,
                        'expire_date' => $auth->expire_date,
                        'completed_at' => $auth->completed_at,
                        'disabled_at' => $auth->disabled_at,
                    ]);
                }
            }

            return $allCourseAuths;

        } catch (Exception $e) {
            Log::error('StudentDashboardService: Error getting course auths', [
                'user_id' => $this->user?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect();
        }
    }

    /**
     * Get lessons for a specific course when courseDates is empty (self-paced mode)
     * Uses CourseAuth -> Course -> CourseUnits -> Lessons pattern
     *
     * @param \App\Models\CourseAuth $courseAuth
     * @return array
     */
    public function getLessonsForCourse($courseAuth): array
    {
        if (!$courseAuth || !$courseAuth->Course) {
            return [
                'lessons' => collect(),
                'modality' => 'unknown',
                'current_day_only' => false,
            ];
        }

        try {
            $course = $courseAuth->Course;

            // Get all course units using proper model method
            $courseUnits = $course->GetCourseUnits();
            $allLessons = collect();

            Log::info('StudentDashboardService: Getting lessons for course', [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'course_units_count' => $courseUnits->count(),
                'course_auth_id' => $courseAuth->id,
            ]);

            foreach ($courseUnits as $unit) {
                // Get lessons for this unit
                $unitLessons = $unit->GetLessons();

                Log::info('StudentDashboardService: Processing course unit', [
                    'unit_id' => $unit->id,
                    'unit_title' => $unit->title,
                    'unit_ordering' => $unit->ordering,
                    'lessons_count' => $unitLessons->count(),
                ]);

                foreach ($unitLessons as $lesson) {
                    // Check if student has progress on this lesson
                    $isCompleted = $this->isLessonCompleted($courseAuth, $unit, $lesson);

                    $allLessons->push([
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'unit_id' => $unit->id,
                        'unit_title' => $unit->title,
                        'unit_ordering' => $unit->ordering,
                        'credit_minutes' => $lesson->credit_minutes ?? 0,
                        'video_seconds' => $lesson->video_seconds ?? 0,
                        'is_completed' => $isCompleted,
                    ]);
                }
            }

            // Sort lessons by unit ordering, then by lesson ID
            $sortedLessons = $allLessons->sortBy([
                ['unit_ordering', 'asc'],
                ['id', 'asc']
            ]);

            Log::info('StudentDashboardService: Lessons retrieved successfully', [
                'course_id' => $course->id,
                'total_units' => $courseUnits->count(),
                'total_lessons' => $allLessons->count(),
                'completed_lessons' => $allLessons->where('is_completed', true)->count(),
            ]);

            return [
                'lessons' => $sortedLessons->values(), // Reset keys
                'modality' => 'self_paced', // Indicate self-paced mode
                'current_day_only' => false, // Show all lessons
            ];

        } catch (Exception $e) {
            Log::error('StudentDashboardService: Error getting lessons for course', [
                'course_auth_id' => $courseAuth->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'lessons' => collect(),
                'modality' => 'unknown',
                'current_day_only' => false,
            ];
        }
    }

    /**
     * Check if a lesson is completed by the student
     * Uses StudentUnit and StudentLesson models to determine completion
     *
     * @param \App\Models\CourseAuth $courseAuth
     * @param \App\Models\CourseUnit $unit
     * @param \App\Models\Lesson $lesson
     * @return bool
     */
    private function isLessonCompleted($courseAuth, $unit, $lesson): bool
    {
        try {
            // Get StudentUnit for this course auth and unit
            $studentUnit = \App\Models\StudentUnit::where('course_auth_id', $courseAuth->id)
                ->where('course_unit_id', $unit->id)
                ->first();

            if (!$studentUnit) {
                return false; // No student unit = not started
            }

            // Check if there's a completed StudentLesson
            $studentLesson = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
                ->where('lesson_id', $lesson->id)
                ->where('completed_at', '!=', null)
                ->exists();

            return $studentLesson;

        } catch (Exception $e) {
            Log::error('StudentDashboardService: Error checking lesson completion', [
                'course_auth_id' => $courseAuth->id,
                'unit_id' => $unit->id,
                'lesson_id' => $lesson->id,
                'error' => $e->getMessage(),
            ]);

            return false; // Default to not completed on error
        }
    }

    /**
     * Clear cache for student-specific data
     */
    public function clearCache(): void
    {
        if (!$this->user) {
            return;
        }

        try {
            $keys = [
                "student_dashboard_stats_{$this->user->id}",
                "student_course_auths_{$this->user->id}",
            ];

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            // Also clear classroom cache since dashboard depends on it
            $this->classroomService->clearCache();

            Log::info("StudentDashboardService: Cleared cache for user: {$this->user->id}");
        } catch (Exception $e) {
            Log::error('StudentDashboardService: Error clearing cache', [
                'user_id' => $this->user?->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
