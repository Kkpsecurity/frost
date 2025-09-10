<?php

namespace App\Services;

use App\Models\User;
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
     * Currently returns empty collection as no classes are scheduled for the day
     */
    public function getCourseDates(): Collection
    {
        if (!$this->user) {
            return collect();
        }

        try {
            // At this stage, no classes are scheduled for the day so courseDates should be empty
            // This would use ClassroomQueries when classes become available

            Log::info('ClassroomDashboardService: Getting course dates', [
                'user_id' => $this->user?->id,
                'stage' => 'no_classes_for_day'
            ]);

            return collect();

        } catch (Exception $e) {
            Log::error('ClassroomDashboardService: Error getting course dates', [
                'user_id' => $this->user?->id,
                'error'   => $e->getMessage(),
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
