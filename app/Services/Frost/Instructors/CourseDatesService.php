<?php

declare(strict_types=1);

namespace App\Services\Frost\Instructors;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service for managing course dates and bulletin board data
 * Handles course statistics, upcoming courses, and bulletin board content
 */
class CourseDatesService
{
    /**
     * Get bulletin board data with course statistics
     *
     * @return array
     */
    public function getBulletinBoardData(): array
    {
        $courseDates = DB::table('course_dates');

        // Get basic statistics
        $stats = $this->getCourseDateStatistics($courseDates);

        // Get upcoming and recent courses
        $upcomingCourses = $this->getUpcomingCourses($courseDates);
        $recentHistory = $this->getRecentCourseHistory($courseDates);

        // Get chart data
        $chartData = $this->getChartData($stats);

        return [
            'bulletin_board' => [
                'stats' => $stats,
                'upcoming_classes' => $upcomingCourses,
                'recent_history' => $recentHistory,
                'charts' => $chartData
            ],
            'metadata' => [
                'generated_at' => now()->toISOString(),
                'view_type' => 'bulletin_board',
                'has_course_dates' => $stats['total_course_dates'] > 0
            ]
        ];
    }

    /**
     * Get course date statistics
     *
     * @param \Illuminate\Database\Query\Builder $courseDates
     * @return array
     */
    private function getCourseDateStatistics($courseDates): array
    {
        $totalCourseDates = $courseDates->count();
        $activeCourseDates = $courseDates->where('is_active', true)->count();
        $upcomingCourseDates = $courseDates->where('starts_at', '>', now())->where('is_active', true)->count();

        $thisWeekCourseDates = $courseDates->whereBetween('starts_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->where('is_active', true)->count();

        $thisMonthCourseDates = $courseDates->whereBetween('starts_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->where('is_active', true)->count();

        return [
            'total_course_dates' => $totalCourseDates,
            'active_course_dates' => $activeCourseDates,
            'upcoming_course_dates' => $upcomingCourseDates,
            'this_week_course_dates' => $thisWeekCourseDates,
            'this_month_course_dates' => $thisMonthCourseDates
        ];
    }

    /**
     * Get upcoming courses for bulletin board
     *
     * @param \Illuminate\Database\Query\Builder $courseDates
     * @return array
     */
    private function getUpcomingCourses($courseDates): array
    {
        $upcomingCourses = $courseDates->where('starts_at', '>', now())
            ->where('is_active', true)
            ->orderBy('starts_at', 'asc')
            ->limit(5)
            ->get();

        return $upcomingCourses->map(function ($courseDate) {
            return [
                'id' => $courseDate->id,
                'course_unit_id' => $courseDate->course_unit_id,
                'starts_at' => $courseDate->starts_at,
                'ends_at' => $courseDate->ends_at,
                'formatted_date' => Carbon::parse($courseDate->starts_at)->format('M j, Y g:i A')
            ];
        })->toArray();
    }

    /**
     * Get recent course history
     *
     * @param \Illuminate\Database\Query\Builder $courseDates
     * @return array
     */
    private function getRecentCourseHistory($courseDates): array
    {
        $recentCourseDates = $courseDates->where('starts_at', '<', now())
            ->orderBy('starts_at', 'desc')
            ->limit(5)
            ->get();

        return $recentCourseDates->map(function ($courseDate) {
            return [
                'id' => $courseDate->id,
                'course_unit_id' => $courseDate->course_unit_id,
                'starts_at' => $courseDate->starts_at,
                'ends_at' => $courseDate->ends_at,
                'formatted_date' => Carbon::parse($courseDate->starts_at)->format('M j, Y g:i A')
            ];
        })->toArray();
    }

    /**
     * Get chart data for visualization
     *
     * @param array $stats
     * @return array
     */
    private function getChartData(array $stats): array
    {
        return [
            'course_activity' => [
                'labels' => ['Total', 'Active', 'Upcoming', 'This Week', 'This Month'],
                'data' => [
                    $stats['total_course_dates'],
                    $stats['active_course_dates'],
                    $stats['upcoming_course_dates'],
                    $stats['this_week_course_dates'],
                    $stats['this_month_course_dates']
                ]
            ]
        ];
    }

    /**
     * Get today's lessons for instructor dashboard
     *
     * @return array
     */
    public function getTodaysLessons(): array
    {
        $today = now()->format('Y-m-d');

        $todaysCourseDates = DB::table('course_dates')
            ->whereDate('starts_at', $today)
            ->where('is_active', true)
            ->orderBy('starts_at', 'asc')
            ->get();

        if ($todaysCourseDates->isEmpty()) {
            return [
                'lessons' => [],
                'message' => "No courses scheduled for today ({$today})",
                'has_lessons' => false,
                'metadata' => [
                    'date' => $today,
                    'count' => 0,
                    'generated_at' => now()->toISOString()
                ]
            ];
        }

        $formattedLessons = $todaysCourseDates->map(function ($courseDate) {
            // Get course and course unit details
            $courseUnit = DB::table('course_units')->find($courseDate->course_unit_id);
            $course = $courseUnit ? DB::table('courses')->find($courseUnit->course_id) : null;

            // Get student count for this course
            $studentCount = DB::table('course_auths')
                ->where('course_id', $course->id ?? 0)
                ->where('is_active', true)
                ->count();

            // Determine status based on current time
            $now = now();
            $startTime = Carbon::parse($courseDate->starts_at);
            $endTime = Carbon::parse($courseDate->ends_at);

            $status = 'scheduled';
            if ($now->between($startTime, $endTime)) {
                $status = 'in-progress';
            } elseif ($now->gt($endTime)) {
                $status = 'completed';
            }

            return [
                'id' => $courseDate->id,
                'time' => $startTime->format('h:i A'),
                'duration' => $startTime->diffForHumans($endTime, true),
                'course_name' => $course->title ?? 'Unknown Course',
                'course_code' => $course->title ?? 'N/A',
                'lesson_name' => $courseUnit->title ?? 'Unknown Lesson',
                'module' => 'Module ' . ($courseUnit->sequence ?? 'N/A'),
                'student_count' => $studentCount,
                'status' => $status,
                'starts_at' => $courseDate->starts_at,
                'ends_at' => $courseDate->ends_at
            ];
        })->toArray();

        return [
            'lessons' => $formattedLessons,
            'message' => count($formattedLessons) . ' lessons scheduled for today',
            'has_lessons' => true,
            'metadata' => [
                'date' => $today,
                'count' => count($formattedLessons),
                'generated_at' => now()->toISOString()
            ]
        ];
    }

    /**
     * Get upcoming lessons for instructor dashboard
     *
     * @return array
     */
    public function getUpcomingLessons(): array
    {
        $now = now();
        $nextWeek = now()->addWeek();

        $upcomingCourseDates = DB::table('course_dates')
            ->where('starts_at', '>', $now)
            ->where('starts_at', '<=', $nextWeek)
            ->where('is_active', true)
            ->orderBy('starts_at', 'asc')
            ->limit(10)
            ->get();

        if ($upcomingCourseDates->isEmpty()) {
            return [
                'lessons' => [],
                'message' => 'No upcoming courses scheduled for the next week',
                'has_lessons' => false,
                'metadata' => [
                    'date_range' => $now->format('Y-m-d') . ' to ' . $nextWeek->format('Y-m-d'),
                    'count' => 0,
                    'generated_at' => now()->toISOString()
                ]
            ];
        }

        $formattedLessons = $upcomingCourseDates->map(function ($courseDate) {
            // Get course and course unit details
            $courseUnit = DB::table('course_units')->find($courseDate->course_unit_id);
            $course = $courseUnit ? DB::table('courses')->find($courseUnit->course_id) : null;

            // Get student count for this course
            $studentCount = DB::table('course_auths')
                ->where('course_id', $course->id ?? 0)
                ->where('is_active', true)
                ->count();

            $startTime = Carbon::parse($courseDate->starts_at);
            $endTime = Carbon::parse($courseDate->ends_at);

            return [
                'id' => $courseDate->id,
                'date' => $startTime->format('M j, Y'),
                'time' => $startTime->format('h:i A'),
                'duration' => $startTime->diffForHumans($endTime, true),
                'course_name' => $course->title ?? 'Unknown Course',
                'course_code' => $course->title ?? 'N/A',
                'lesson_name' => $courseUnit->title ?? 'Unknown Lesson',
                'module' => 'Module ' . ($courseUnit->sequence ?? 'N/A'),
                'student_count' => $studentCount,
                'status' => 'scheduled',
                'starts_at' => $courseDate->starts_at,
                'ends_at' => $courseDate->ends_at,
                'days_until' => now()->diffInDays($startTime)
            ];
        })->toArray();

        return [
            'lessons' => $formattedLessons,
            'message' => count($formattedLessons) . ' upcoming lessons in the next week',
            'has_lessons' => true,
            'metadata' => [
                'date_range' => $now->format('Y-m-d') . ' to ' . $nextWeek->format('Y-m-d'),
                'count' => count($formattedLessons),
                'generated_at' => now()->toISOString()
            ]
        ];
    }

    /**
     * Get previous lessons for instructor dashboard
     *
     * @return array
     */
    public function getPreviousLessons(): array
    {
        $now = now();
        $lastWeek = now()->subWeek();

        $previousCourseDates = DB::table('course_dates')
            ->where('ends_at', '<', $now)
            ->where('ends_at', '>=', $lastWeek)
            ->where('is_active', true)
            ->orderBy('ends_at', 'desc')
            ->limit(10)
            ->get();

        if ($previousCourseDates->isEmpty()) {
            return [
                'lessons' => [],
                'message' => 'No completed courses in the past week',
                'has_lessons' => false,
                'metadata' => [
                    'date_range' => $lastWeek->format('Y-m-d') . ' to ' . $now->format('Y-m-d'),
                    'count' => 0,
                    'generated_at' => now()->toISOString()
                ]
            ];
        }

        $formattedLessons = $previousCourseDates->map(function ($courseDate) {
            // Get course and course unit details
            $courseUnit = DB::table('course_units')->find($courseDate->course_unit_id);
            $course = $courseUnit ? DB::table('courses')->find($courseUnit->course_id) : null;

            // Get student count for this course
            $studentCount = DB::table('course_auths')
                ->where('course_id', $course->id ?? 0)
                ->where('is_active', true)
                ->count();

            $startTime = Carbon::parse($courseDate->starts_at);
            $endTime = Carbon::parse($courseDate->ends_at);

            return [
                'id' => $courseDate->id,
                'date' => $endTime->format('M j, Y'),
                'time' => $startTime->format('h:i A') . ' - ' . $endTime->format('h:i A'),
                'duration' => $startTime->diffForHumans($endTime, true),
                'course_name' => $course->title ?? 'Unknown Course',
                'course_code' => $course->title ?? 'N/A',
                'lesson_name' => $courseUnit->title ?? 'Unknown Lesson',
                'module' => 'Module ' . ($courseUnit->sequence ?? 'N/A'),
                'student_count' => $studentCount,
                'status' => 'completed',
                'starts_at' => $courseDate->starts_at,
                'ends_at' => $courseDate->ends_at,
                'days_ago' => $endTime->diffInDays(now())
            ];
        })->toArray();

        return [
            'lessons' => $formattedLessons,
            'message' => count($formattedLessons) . ' lessons completed in the past week',
            'has_lessons' => true,
            'metadata' => [
                'date_range' => $lastWeek->format('Y-m-d') . ' to ' . $now->format('Y-m-d'),
                'count' => count($formattedLessons),
                'generated_at' => now()->toISOString()
            ]
        ];
    }

    /**
     * Get course dates for a specific date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getCourseDatesInRange(Carbon $startDate, Carbon $endDate): array
    {
        $courseDates = DB::table('course_dates')
            ->whereBetween('starts_at', [$startDate, $endDate])
            ->where('is_active', true)
            ->orderBy('starts_at', 'asc')
            ->get();

        return $courseDates->map(function ($courseDate) {
            return [
                'id' => $courseDate->id,
                'course_unit_id' => $courseDate->course_unit_id,
                'starts_at' => $courseDate->starts_at,
                'ends_at' => $courseDate->ends_at,
                'formatted_date' => Carbon::parse($courseDate->starts_at)->format('M j, Y g:i A'),
                'duration_hours' => Carbon::parse($courseDate->starts_at)->diffInHours(Carbon::parse($courseDate->ends_at))
            ];
        })->toArray();
    }
}
