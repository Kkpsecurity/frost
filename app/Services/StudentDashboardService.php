<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Casts\JSONCast;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\ClassroomDashboardService;

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
            $allCourseAuths = $this->user->courseAuths()->get();

            Log::info('StudentDashboardService: Basic courseAuths query result', [
                'count' => $allCourseAuths->count(),
                'raw_sql' => $this->user->courseAuths()->toSql(),
                'bindings' => $this->user->courseAuths()->getBindings()
            ]);

            // If basic query works, try adding course relationship
            if ($allCourseAuths->count() > 0) {
                Log::info('StudentDashboardService: Basic query worked, trying with course relationship');
                $allCourseAuths = $this->user->courseAuths()
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
     * Get classroom lesson data for sidebar display
     * Returns lessons based on course type (online vs offline)
     */
    public function getClassroomLessons($courseAuth): array
    {
        if (!$courseAuth || !$courseAuth->course) {
            return [
                'lessons' => collect(),
                'modality' => 'unknown',
                'current_day_only' => false,
            ];
        }

        try {
            $course = $courseAuth->course;

            // Get all course units and their lessons
            $courseUnits = $course->GetCourseUnits();
            $allLessons = collect();

            foreach ($courseUnits as $unit) {
                $unitLessons = $unit->GetLessons();
                foreach ($unitLessons as $lesson) {
                    $allLessons->push([
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'unit_id' => $unit->id,
                        'unit_title' => $unit->title,
                        'unit_ordering' => $unit->ordering,
                        'credit_minutes' => $lesson->credit_minutes,
                        'video_seconds' => $lesson->video_seconds,
                    ]);
                }
            }

            // Sort lessons by unit ordering
            $sortedLessons = $allLessons->sortBy('unit_ordering');

            // Determine modality (default to offline if we can't determine)
            $modality = $this->determineCourseModality($courseAuth);
            $currentDayOnly = ($modality === 'online');

            // For online courses, filter to current day lessons
            if ($currentDayOnly) {
                $sortedLessons = $this->filterCurrentDayLessons($sortedLessons, $courseAuth);
            }

            Log::info('StudentDashboardService: Generated classroom lessons', [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'total_units' => $courseUnits->count(),
                'total_lessons' => $allLessons->count(),
                'filtered_lessons' => $sortedLessons->count(),
                'modality' => $modality,
                'current_day_only' => $currentDayOnly,
            ]);

            return [
                'lessons' => $sortedLessons,
                'modality' => $modality,
                'current_day_only' => $currentDayOnly,
            ];

        } catch (Exception $e) {
            Log::error('StudentDashboardService: Error getting classroom lessons', [
                'course_auth_id' => $courseAuth->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'lessons' => collect(),
                'modality' => 'unknown',
                'current_day_only' => false,
            ];
        }
    }

    /**
     * Determine course modality (online vs offline)
     */
    private function determineCourseModality($courseAuth): string
    {
        try {
            // Check if there are any classrooms scheduled for this course
            // that are marked as online
            $hasOnlineClassrooms = \App\Models\Classroom::whereHas('courseDate', function ($query) use ($courseAuth) {
                $query->whereHas('courseUnit', function ($subQuery) use ($courseAuth) {
                    $subQuery->where('course_id', $courseAuth->course_id);
                });
            })
                ->where('modality', 'online')
                ->exists();

            if ($hasOnlineClassrooms) {
                return 'online';
            }

            // Check for in-person classrooms
            $hasInPersonClassrooms = \App\Models\Classroom::whereHas('courseDate', function ($query) use ($courseAuth) {
                $query->whereHas('courseUnit', function ($subQuery) use ($courseAuth) {
                    $subQuery->where('course_id', $courseAuth->course_id);
                });
            })
                ->where('modality', 'in_person')
                ->exists();

            if ($hasInPersonClassrooms) {
                return 'in_person';
            }

            // Default to offline if no classrooms are defined
            return 'offline';

        } catch (Exception $e) {
            Log::error('StudentDashboardService: Error determining course modality', [
                'course_auth_id' => $courseAuth->id,
                'error' => $e->getMessage(),
            ]);
            return 'offline';
        }
    }

    /**
     * Filter lessons to current day only (for online courses)
     */
    private function filterCurrentDayLessons($lessons, $courseAuth): Collection
    {
        try {
            // For now, return all lessons since we don't have the logic
            // to determine which lessons are for "today"
            // This would require course date scheduling logic

            // TODO: Add logic to filter based on course schedule
            // - Get today's course dates
            // - Find which lessons are scheduled for today
            // - Return only those lessons

            return $lessons;

        } catch (Exception $e) {
            Log::error('StudentDashboardService: Error filtering current day lessons', [
                'course_auth_id' => $courseAuth->id,
                'error' => $e->getMessage(),
            ]);
            return $lessons;
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
