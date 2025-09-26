<?php

declare(strict_types=1);

namespace App\Services\Frost\Scheduling;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\CourseDate;

/**
 * Service for activating CourseDate records on their scheduled date
 *
 * This service should run daily (e.g., at 6:00 AM) to activate CourseDate records
 * that are scheduled for today. It changes is_active from false to true.
 */
class CourseDateActivationService
{
    /**
     * Activate CourseDate records for today
     *
     * @param string|null $timezone Timezone for date calculations (default: America/New_York)
     * @return array Summary of activation results
     */
    public function activateCourseDatesForToday(?string $timezone = null): array
    {
        $timezone = $timezone ?? 'America/New_York';
        $today = Carbon::now($timezone)->format('Y-m-d');

        Log::info('CourseDateActivation: Starting daily activation', [
            'date' => $today,
            'timezone' => $timezone
        ]);

        // Find inactive CourseDate records scheduled for today
        $inactiveCourseDates = CourseDate::whereDate('starts_at', $today)
            ->where('is_active', false)
            ->with(['courseUnit.course'])
            ->get();

        $results = [
            'date' => $today,
            'found_inactive' => $inactiveCourseDates->count(),
            'activated' => 0,
            'errors' => [],
            'details' => []
        ];

        if ($inactiveCourseDates->isEmpty()) {
            Log::info('CourseDateActivation: No inactive CourseDate records found for today');
            return $results;
        }

        // Activate each CourseDate record
        foreach ($inactiveCourseDates as $courseDate) {
            try {
                $courseDate->update(['is_active' => true]);
                $results['activated']++;

                $courseName = $courseDate->courseUnit->course->title;
                $unitTitle = $courseDate->courseUnit->admin_title;
                $startTime = Carbon::parse($courseDate->starts_at)->format('H:i');

                $results['details'][] = [
                    'id' => $courseDate->id,
                    'course' => $courseName,
                    'unit' => $unitTitle,
                    'start_time' => $startTime
                ];

                Log::info('CourseDateActivation: Activated CourseDate', [
                    'id' => $courseDate->id,
                    'course' => $courseName,
                    'unit' => $unitTitle,
                    'starts_at' => $courseDate->starts_at
                ]);

            } catch (\Exception $e) {
                $error = "Failed to activate CourseDate ID {$courseDate->id}: " . $e->getMessage();
                $results['errors'][] = $error;

                Log::error('CourseDateActivation: Failed to activate CourseDate', [
                    'id' => $courseDate->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        Log::info('CourseDateActivation: Completed daily activation', [
            'date' => $today,
            'found_inactive' => $results['found_inactive'],
            'activated' => $results['activated'],
            'errors' => count($results['errors'])
        ]);

        return $results;
    }

    /**
     * Activate CourseDate records for a specific date
     *
     * @param Carbon $date Date to activate CourseDate records for
     * @return array Summary of activation results
     */
    public function activateCourseDatesForDate(Carbon $date): array
    {
        $dateString = $date->format('Y-m-d');

        Log::info('CourseDateActivation: Starting activation for specific date', [
            'date' => $dateString
        ]);

        // Find inactive CourseDate records for the specified date
        $inactiveCourseDates = CourseDate::whereDate('starts_at', $dateString)
            ->where('is_active', false)
            ->with(['courseUnit.course'])
            ->get();

        $results = [
            'date' => $dateString,
            'found_inactive' => $inactiveCourseDates->count(),
            'activated' => 0,
            'errors' => [],
            'details' => []
        ];

        // Activate each CourseDate record
        foreach ($inactiveCourseDates as $courseDate) {
            try {
                $courseDate->update(['is_active' => true]);
                $results['activated']++;

                $courseName = $courseDate->courseUnit->course->title;
                $unitTitle = $courseDate->courseUnit->admin_title;
                $startTime = Carbon::parse($courseDate->starts_at)->format('H:i');

                $results['details'][] = [
                    'id' => $courseDate->id,
                    'course' => $courseName,
                    'unit' => $unitTitle,
                    'start_time' => $startTime
                ];

                Log::info('CourseDateActivation: Activated CourseDate for specific date', [
                    'id' => $courseDate->id,
                    'course' => $courseName,
                    'unit' => $unitTitle,
                    'date' => $dateString
                ]);

            } catch (\Exception $e) {
                $error = "Failed to activate CourseDate ID {$courseDate->id}: " . $e->getMessage();
                $results['errors'][] = $error;

                Log::error('CourseDateActivation: Failed to activate CourseDate for specific date', [
                    'id' => $courseDate->id,
                    'date' => $dateString,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    /**
     * Get preview of CourseDate records that would be activated for today
     *
     * @param string|null $timezone Timezone for date calculations
     * @return array Preview information
     */
    public function previewActivationForToday(?string $timezone = null): array
    {
        $timezone = $timezone ?? 'America/New_York';
        $today = Carbon::now($timezone)->format('Y-m-d');

        $inactiveCourseDates = CourseDate::whereDate('starts_at', $today)
            ->where('is_active', false)
            ->with(['courseUnit.course'])
            ->get();

        $preview = [
            'date' => $today,
            'timezone' => $timezone,
            'inactive_count' => $inactiveCourseDates->count(),
            'courses' => []
        ];

        foreach ($inactiveCourseDates as $courseDate) {
            $courseName = $courseDate->courseUnit->course->title;
            $unitTitle = $courseDate->courseUnit->admin_title;
            $startTime = Carbon::parse($courseDate->starts_at)->format('H:i');

            $preview['courses'][] = [
                'id' => $courseDate->id,
                'course' => $courseName,
                'unit' => $unitTitle,
                'start_time' => $startTime,
                'starts_at' => $courseDate->starts_at
            ];
        }

        return $preview;
    }

    /**
     * Deactivate CourseDate records (for testing or emergency purposes)
     *
     * @param array $courseDateIds Array of CourseDate IDs to deactivate
     * @return array Summary of deactivation results
     */
    public function deactivateCourseDates(array $courseDateIds): array
    {
        $results = [
            'requested_ids' => $courseDateIds,
            'deactivated' => 0,
            'errors' => [],
            'details' => []
        ];

        foreach ($courseDateIds as $id) {
            try {
                $courseDate = CourseDate::find($id);
                if (!$courseDate) {
                    $results['errors'][] = "CourseDate ID {$id} not found";
                    continue;
                }

                $courseDate->update(['is_active' => false]);
                $results['deactivated']++;

                $courseName = $courseDate->courseUnit->course->title;
                $unitTitle = $courseDate->courseUnit->admin_title;

                $results['details'][] = [
                    'id' => $id,
                    'course' => $courseName,
                    'unit' => $unitTitle
                ];

                Log::info('CourseDateActivation: Deactivated CourseDate', [
                    'id' => $id,
                    'course' => $courseName,
                    'unit' => $unitTitle
                ]);

            } catch (\Exception $e) {
                $error = "Failed to deactivate CourseDate ID {$id}: " . $e->getMessage();
                $results['errors'][] = $error;

                Log::error('CourseDateActivation: Failed to deactivate CourseDate', [
                    'id' => $id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }
}
