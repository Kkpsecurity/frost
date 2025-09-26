<?php

declare(strict_types=1);

namespace App\Services\Frost\Scheduling;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Models\Course;
use App\Models\CourseDate;
use App\Models\CourseUnit;

/**
 * Service for automatically generating CourseDate records
 * Based on existing course schedule patterns
 * UPDATED: Generate 2 weeks ahead by default
 */
class CourseDateGeneratorService
{
    /**
     * Default configuration - Updated for 2 weeks ahead
     */
    private array $defaultConfig = [
        'weekdays_only' => true,
        'start_time' => '09:00',
        'duration_hours' => 3,
        'advance_days' => 14, // Generate 2 weeks ahead (14 days)
        'skip_weekends' => true,
        'skip_holidays' => true
    ];

    /**
     * Get the active courses with course units
     *
     * @return Collection
     */
    private function getActiveCourses(): Collection
    {
        return Course::where('is_active', true)
            ->whereHas('CourseUnits') // Only courses with units
            ->get();
    }

    /**
     * Get the next day number for a course type (global counter)
     *
     * @param Course $course
     * @return int
     */
    private function getNextDayNumberForCourse(Course $course): int
    {
        // Get the highest day number for this course
        $lastCourseDate = CourseDate::whereHas('courseUnit', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })->orderBy('day_number', 'desc')->first();

        return $lastCourseDate ? ($lastCourseDate->day_number + 1) : 1;
    }

    /**
     * Generate CourseDate records for the next 2 weeks (default)
     *
     * @param int|null $advanceDays How many days ahead to generate (default: 14)
     * @return array Summary of generation results
     */
    public function generateWeeklyCourseDates(?int $advanceDays = null): array
    {
        $advanceDays = $advanceDays ?? $this->defaultConfig['advance_days'];
        $startDate = now()->addDay(); // Start from tomorrow
        $endDate = now()->addDays($advanceDays);

        return $this->generateCourseDatesForRange($startDate, $endDate);
    }

    /**
     * Generate CourseDate records for the next 2 weeks (preferred method)
     *
     * @param int|null $advanceDays How many days ahead to generate (default: 14)
     * @return array Summary of generation results
     */
    public function generateTwoWeeksAhead(?int $advanceDays = null): array
    {
        $advanceDays = $advanceDays ?? 14; // Default to 2 weeks
        $startDate = now()->addDay(); // Start from tomorrow
        $endDate = now()->addDays($advanceDays);

        Log::info('CourseDateGeneratorService: Generating 2 weeks ahead', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'advance_days' => $advanceDays
        ]);

        return $this->generateCourseDatesForRange($startDate, $endDate);
    }

    /**
     * Generate CourseDate records for a specific date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function generateCourseDatesForRange(Carbon $startDate, Carbon $endDate): array
    {
        $results = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_days' => $startDate->diffInDays($endDate),
            'courses_processed' => 0,
            'dates_created' => 0,
            'dates_skipped' => 0,
            'errors' => [],
            'course_details' => []
        ];

        $activeCourses = $this->getActiveCourses();
        $results['courses_processed'] = $activeCourses->count();

        foreach ($activeCourses as $course) {
            try {
                $courseResults = $this->generateCourseDatesForCourse($course, $startDate, $endDate);
                $results['dates_created'] += $courseResults['dates_created'];
                $results['dates_skipped'] += $courseResults['dates_skipped'];
                $results['course_details'][$course->title] = $courseResults;

                Log::info('CourseDateGeneratorService: Course processed', [
                    'course' => $course->title,
                    'dates_created' => $courseResults['dates_created'],
                    'dates_skipped' => $courseResults['dates_skipped']
                ]);

            } catch (\Exception $e) {
                $error = "Error processing course {$course->title}: " . $e->getMessage();
                $results['errors'][] = $error;
                Log::error('CourseDateGeneratorService: Course processing error', [
                    'course' => $course->title,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('CourseDateGeneratorService: Generation completed', [
            'total_created' => $results['dates_created'],
            'total_skipped' => $results['dates_skipped'],
            'total_errors' => count($results['errors'])
        ]);

        return $results;
    }

    /**
     * Generate CourseDate records for a single course within date range
     *
     * @param Course $course
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function generateCourseDatesForCourse(Course $course, Carbon $startDate, Carbon $endDate): array
    {
        $results = [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'dates_created' => 0,
            'dates_skipped' => 0,
            'date_details' => []
        ];

        $currentDate = $startDate->copy();
        $dayNumber = $this->getNextDayNumberForCourse($course);

        while ($currentDate->lte($endDate)) {
            // Skip if this date shouldn't have courses for this course type
            if (!$this->shouldCourseRunOnDate($course, $currentDate)) {
                $currentDate->addDay();
                continue;
            }

            // Check if CourseDate already exists for this course on this date
            $existingCourseDate = CourseDate::whereDate('starts_at', $currentDate->format('Y-m-d'))
                ->whereHas('courseUnit', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })
                ->first();

            if ($existingCourseDate) {
                $results['dates_skipped']++;
                $results['date_details'][] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => 'skipped',
                    'reason' => 'CourseDate already exists'
                ];
                $currentDate->addDay();
                continue;
            }

            // Check for course type conflicts (only one D or G course per day)
            if ($this->courseTypeConflictExists($course, $currentDate)) {
                $results['dates_skipped']++;
                $results['date_details'][] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => 'skipped',
                    'reason' => 'Course type conflict (only one D or G course per day)'
                ];
                $currentDate->addDay();
                continue;
            }

            // Get the course unit to assign (cycle through units)
            $courseUnit = $this->getCourseUnitForDay($course, $dayNumber);

            if (!$courseUnit) {
                $results['dates_skipped']++;
                $results['date_details'][] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => 'skipped',
                    'reason' => 'No course unit found for day ' . $dayNumber
                ];
                $currentDate->addDay();
                continue;
            }

            // Create CourseDate record
            $startTime = $currentDate->copy()->setTimeFromTimeString($this->defaultConfig['start_time']);
            $endTime = $startTime->copy()->addHours($this->defaultConfig['duration_hours']);

            CourseDate::create([
                'course_unit_id' => $courseUnit->id,
                'starts_at' => $startTime,
                'ends_at' => $endTime,
                'day_number' => $dayNumber,
                'is_active' => false, // Inactive by default as requested
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $results['dates_created']++;
            $results['date_details'][] = [
                'date' => $currentDate->format('Y-m-d'),
                'status' => 'created',
                'course_unit' => $courseUnit->admin_title,
                'day_number' => $dayNumber,
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'end_time' => $endTime->format('Y-m-d H:i:s')
            ];

            $dayNumber++;
            $currentDate->addDay();
        }

        return $results;
    }

    /**
     * Determine if a course should run on a specific date
     * Based on course-specific scheduling rules
     *
     * @param Course $course
     * @param Carbon $date
     * @return bool
     */
    private function shouldCourseRunOnDate(Course $course, Carbon $date): bool
    {
        // Skip weekends for all courses
        if ($this->defaultConfig['skip_weekends'] && $date->isWeekend()) {
            return false;
        }

        // Skip holidays for all courses
        if ($this->defaultConfig['skip_holidays'] && $this->isHoliday($date)) {
            return false;
        }

        // Course-specific rules
        $courseTitle = strtolower($course->title);

        // D classes: Every weekday (Monday - Friday)
        if (str_contains($courseTitle, 'd40') || str_contains($courseTitle, 'd ')) {
            return $date->isWeekday(); // Monday through Friday
        }

        // G classes: Every other week, Monday-Wednesday only
        if (str_contains($courseTitle, 'g28') || str_contains($courseTitle, 'g ')) {
            // Only Monday, Tuesday, Wednesday
            if (!in_array($date->dayOfWeek, [1, 2, 3])) { // 1=Monday, 2=Tuesday, 3=Wednesday
                return false;
            }

            // Every other week logic - check if it's a G week
            $weekNumber = $date->weekOfYear;
            return ($weekNumber % 2) === 1; // Odd weeks only
        }

        // Default: Allow all weekdays
        return $date->isWeekday();
    }

    /**
     * Check if there's a course type conflict on the given date
     * Rule: Only one D course OR one G course per day, but not the same course twice
     *
     * @param Course $course
     * @param Carbon $date
     * @return bool
     */
    private function courseTypeConflictExists(Course $course, Carbon $date): bool
    {
        $courseTitle = strtolower($course->title);

        // Check for existing CourseDate records on this date
        $existingCourseDates = CourseDate::whereDate('starts_at', $date->format('Y-m-d'))
            ->with('courseUnit.course')
            ->get();

        foreach ($existingCourseDates as $existingCourseDate) {
            $existingCourseTitle = strtolower($existingCourseDate->courseUnit->course->title ?? '');

            // Same course type conflict check
            if ((str_contains($courseTitle, 'd40') || str_contains($courseTitle, 'd ')) &&
                (str_contains($existingCourseTitle, 'd40') || str_contains($existingCourseTitle, 'd '))) {
                return true; // Two D courses on same day
            }

            if ((str_contains($courseTitle, 'g28') || str_contains($courseTitle, 'g ')) &&
                (str_contains($existingCourseTitle, 'g28') || str_contains($existingCourseTitle, 'g '))) {
                return true; // Two G courses on same day
            }

            // Exact same course conflict
            if ($course->id === $existingCourseDate->courseUnit->course->id) {
                return true; // Same course twice on same day
            }
        }

        return false; // No conflicts
    }

    /**
     * Get the appropriate course unit for a given day number
     * Cycles through course units in sequence
     *
     * @param Course $course
     * @param int $dayNumber
     * @return CourseUnit|null
     */
    private function getCourseUnitForDay(Course $course, int $dayNumber): ?CourseUnit
    {
        $courseUnits = $course->CourseUnits()->orderBy('ordering')->get();

        if ($courseUnits->isEmpty()) {
            return null;
        }

        // Cycle through units: day 1 = unit 1, day 2 = unit 2, etc.
        // When we reach the end, start over
        $unitIndex = ($dayNumber - 1) % $courseUnits->count();
        return $courseUnits->get($unitIndex);
    }

    /**
     * Check if a date is a holiday
     * Simple implementation - can be expanded with holiday API
     *
     * @param Carbon $date
     * @return bool
     */
    private function isHoliday(Carbon $date): bool
    {
        // Simple holiday detection - can be expanded
        $holidays = [
            '01-01', // New Year's Day
            '07-04', // Independence Day
            '12-25', // Christmas Day
        ];

        return in_array($date->format('m-d'), $holidays);
    }

    /**
     * Clean up invalid CourseDate records
     * Removes records that violate scheduling rules
     *
     * @return array
     */
    public function cleanupInvalidCourseDates(): array
    {
        $results = [
            'invalid_weekends' => 0,
            'invalid_duplicates' => 0,
            'total_removed' => 0
        ];

        // Remove weekend CourseDate records
        $weekendCourseDates = CourseDate::whereIn('starts_at', function ($query) {
            $query->selectRaw('starts_at')
                ->from('course_dates')
                ->whereRaw('EXTRACT(DOW FROM starts_at) IN (0, 6)'); // Sunday=0, Saturday=6
        })->get();

        foreach ($weekendCourseDates as $courseDate) {
            Log::info('CourseDateGeneratorService: Removing weekend CourseDate', [
                'id' => $courseDate->id,
                'date' => $courseDate->starts_at
            ]);
            $courseDate->delete();
            $results['invalid_weekends']++;
        }

        $results['total_removed'] = $results['invalid_weekends'] + $results['invalid_duplicates'];

        return $results;
    }

    /**
     * Preview what dates would be generated without actually creating them
     *
     * @param int|null $advanceDays
     * @return array
     */
    public function previewGeneration(?int $advanceDays = null): array
    {
        $advanceDays = $advanceDays ?? $this->defaultConfig['advance_days'];
        $startDate = now()->addDay();
        $endDate = now()->addDays($advanceDays);

        $preview = [
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'total_days' => $startDate->diffInDays($endDate)
            ],
            'courses' => [],
            'estimated_total' => 0
        ];

        $activeCourses = $this->getActiveCourses();

        foreach ($activeCourses as $course) {
            $currentDate = $startDate->copy();
            $estimatedDates = 0;

            while ($currentDate->lte($endDate)) {
                if ($this->shouldCourseRunOnDate($course, $currentDate) &&
                    !$this->courseTypeConflictExists($course, $currentDate)) {
                    $estimatedDates++;
                }
                $currentDate->addDay();
            }

            $preview['courses'][$course->title] = [
                'course_id' => $course->id,
                'estimated_dates' => $estimatedDates
            ];

            $preview['estimated_total'] += $estimatedDates;
        }

        return $preview;
    }
}
