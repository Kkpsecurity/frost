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
}
