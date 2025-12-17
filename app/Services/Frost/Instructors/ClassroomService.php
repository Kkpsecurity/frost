<?php

declare(strict_types=1);

namespace App\Services\Frost\Instructors;

use Illuminate\Support\Facades\DB;

/**
 * Service for managing classroom data and operations for instructors
 * Handles classroom information, capacity, enrollment, and schedule data
 */
class ClassroomService
{
    /**
     * Get classroom data for instructor dashboard
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

        // Get ALL active course dates for today (instructor can choose which to teach)
        $courseDates = \App\Models\CourseDate::where('is_active', true)
            ->whereDate('starts_at', today())
            ->with(['courseUnit', 'courseUnit.course', 'courseUnit.lessons', 'studentUnits'])
            ->withCount(['studentUnits'])
            ->orderBy('starts_at', 'asc')
            ->get();

        // Get upcoming course dates for the next 7 days (tomorrow onwards)
        $upcomingDates = \App\Models\CourseDate::where('is_active', true)
            ->whereDate('starts_at', '>', today())
            ->whereDate('starts_at', '<=', today()->addDays(7))
            ->with(['courseUnit', 'courseUnit.course', 'courseUnit.lessons', 'studentUnits'])
            ->withCount(['studentUnits'])
            ->orderBy('starts_at', 'asc')
            ->get();

        \Log::info('ClassroomService::getClassroomData', [
            'user_id' => $admin->id,
            'courseDates_count' => $courseDates->count(),
            'upcomingDates_count' => $upcomingDates->count(),
            'today' => today()->toDateString(),
        ]);

        // Get all unique courses from the course dates
        $courses = $courseDates->map(function ($cd) {
            return $cd->courseUnit?->course;
        })->filter()->unique('id')->values();

        return [
            'courseDates' => $courseDates->toArray(), // Convert to array
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
