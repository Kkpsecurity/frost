<?php

declare(strict_types=1);

namespace App\Services\Frost\Instructors;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service for managing classroom data and operations for instructors
 * Handles classroom information, capacity, enrollment, and schedule data
 */
class ClassroomService
{
    protected CourseDatesService $courseDatesService;

    public function __construct(CourseDatesService $courseDatesService)
    {
        $this->courseDatesService = $courseDatesService;
    }

    /**
     * Get classroom data for instructor dashboard
     * Now uses CourseDatesService to properly format course dates with buttons
     *
     * @return array
     */
    public function getClassroomData(): array
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return [
                'error' => 'Unauthenticated',
                'courseDates' => [],
                'courses' => [],
                'lessons' => [],
            ];
        }

        // Use CourseDatesService to get properly formatted today's lessons with buttons
        $todaysData = $this->courseDatesService->getTodaysLessons();
        $courseDates = $todaysData['lessons'] ?? [];

        $tz = 'America/New_York';
        $todayEt = Carbon::now($tz)->startOfDay();
        $tomorrowEt = $todayEt->copy()->addDay();
        $startUtc = $tomorrowEt->copy()->tz('UTC');
        $endUtc = $todayEt->copy()->addDays(7)->endOfDay()->tz('UTC');

        // Get upcoming course dates for the next 7 days (tomorrow onwards, ET)
        $upcomingDates = \App\Models\CourseDate::query()
            ->where('is_active', true)
            ->whereBetween('starts_at', [$startUtc, $endUtc])
            ->with(['courseUnit', 'courseUnit.course', 'courseUnit.lessons', 'studentUnits'])
            ->withCount(['studentUnits'])
            ->orderBy('starts_at', 'asc')
            ->get();

        Log::info('ClassroomService::getClassroomData', [
            'user_id' => $admin->id,
            'courseDates_count' => count($courseDates),
            'upcomingDates_count' => $upcomingDates->count(),
            'today_et' => $todayEt->toDateString(),
            'upcoming_window_utc' => [
                'start' => $startUtc->toIso8601String(),
                'end' => $endUtc->toIso8601String(),
            ],
            'first_course_buttons' => $courseDates[0]['buttons'] ?? 'no_buttons',
        ]);

        // Get all unique courses from the course dates
        $courses = collect($courseDates)->map(function ($cd) {
            return [
                'id' => $cd['id'] ?? null,
                'title' => $cd['course_name'] ?? 'Unknown',
                'code' => $cd['course_code'] ?? 'N/A',
            ];
        })->filter()->unique('id')->values();

        return [
            'courseDates' => $courseDates, // Already formatted with buttons by CourseDatesService
            'upcomingDates' => $upcomingDates->toArray(), // Upcoming week schedule
            'courses' => $courses->toArray(),
            'lessons' => [],
            'instUnit' => null,
            'instLessons' => [],
            'students' => [],
        ];
    }

    /**
     * Get active classrooms for instructor
     *
     * @param int|null $instructorId
     * @return array
     */
    public function getActiveClassrooms(?int $instructorId = null): array
    {
        // This would typically query actual classroom/course assignments
        // For now, return admin overview structure

        return [
            'active_classrooms' => [],
            'total_count' => 0,
            'metadata' => [
                'instructor_id' => $instructorId,
                'view_type' => 'active_classrooms',
                'last_updated' => now()->format('c')
            ]
        ];
    }

    /**
     * Get classroom schedule for a specific period
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getClassroomSchedule(string $startDate, string $endDate): array
    {
        // Query course_dates table for classroom schedule
        $schedule = DB::table('course_dates')
            ->whereBetween('starts_at', [$startDate, $endDate])
            ->where('is_active', true)
            ->orderBy('starts_at', 'asc')
            ->get();

        $formattedSchedule = $schedule->map(function ($item) {
            return [
                'id' => $item->id,
                'course_unit_id' => $item->course_unit_id,
                'start_time' => $item->starts_at,
                'end_time' => $item->ends_at,
                'date' => \Carbon\Carbon::parse($item->starts_at)->format('Y-m-d'),
                'day_of_week' => \Carbon\Carbon::parse($item->starts_at)->format('l'),
                'duration_minutes' => \Carbon\Carbon::parse($item->starts_at)->diffInMinutes(\Carbon\Carbon::parse($item->ends_at)),
                'status' => $item->is_active ? 'active' : 'inactive'
            ];
        })->toArray();

        return [
            'schedule' => $formattedSchedule,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_sessions' => count($formattedSchedule)
            ],
            'metadata' => [
                'generated_at' => now()->format('c'),
                'view_type' => 'classroom_schedule'
            ]
        ];
    }

    /**
     * Get classroom capacity and enrollment data
     *
     * @param int $classroomId
     * @return array
     */
    public function getClassroomCapacity(int $classroomId): array
    {
        // This would typically query classroom and enrollment tables
        // For now, return default structure

        return [
            'classroom_id' => $classroomId,
            'capacity' => 30, // Default capacity
            'current_enrollment' => 0,
            'available_spots' => 30,
            'enrollment_percentage' => 0,
            'waitlist_count' => 0,
            'metadata' => [
                'last_updated' => now()->format('c'),
                'data_source' => 'classroom_capacity_query'
            ]
        ];
    }

    /**
     * Get classroom equipment and resources
     *
     * @param int $classroomId
     * @return array
     */
    public function getClassroomResources(int $classroomId): array
    {
        return [
            'classroom_id' => $classroomId,
            'equipment' => [
                'projector' => true,
                'whiteboard' => true,
                'computers' => 0,
                'audio_system' => true
            ],
            'resources' => [
                'wifi_available' => true,
                'parking_spaces' => 20,
                'accessibility' => true
            ],
            'metadata' => [
                'last_updated' => now()->format('c'),
                'data_source' => 'classroom_resources_query'
            ]
        ];
    }
}
