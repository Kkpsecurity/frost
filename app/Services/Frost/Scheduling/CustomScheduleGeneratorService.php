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
 * Custom Schedule Generator Service
 * 
 * Generates course dates following specific patterns:
 * - Every 3 days (Monday, Wednesday)
 * - Every other week pattern
 * - Customizable for different course types
 */
class CustomScheduleGeneratorService
{
    /**
     * Default configuration for custom scheduling
     */
    private array $config = [
        'start_time' => '08:00',
        'duration_hours' => 3,
        'advance_weeks' => 8, // Generate 8 weeks ahead
        'skip_holidays' => true
    ];

    /**
     * Generate course dates following "Every 3 Days - Monday/Wednesday - Every Other Week" pattern
     * 
     * @param int|null $courseId Specific course ID (optional)
     * @param int|null $advanceWeeks How many weeks ahead to generate
     * @return array Generation results
     */
    public function generateMondayWednesdayEveryOtherWeek(?int $courseId = null, ?int $advanceWeeks = null): array
    {
        $advanceWeeks = $advanceWeeks ?? $this->config['advance_weeks'];
        $startDate = now()->startOfWeek(); // Start from current Monday
        $endDate = now()->addWeeks($advanceWeeks);

        Log::info('CustomScheduleGeneratorService: Generating Monday/Wednesday every other week', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'advance_weeks' => $advanceWeeks,
            'course_id' => $courseId
        ]);

        return $this->generateCustomPattern($startDate, $endDate, 'monday_wednesday_biweekly', $courseId);
    }

    /**
     * Generate course dates following "Every 3 Days" pattern (Mon, Thu, Sun cycle)
     * 
     * @param int|null $courseId Specific course ID (optional)
     * @param int|null $advanceWeeks How many weeks ahead to generate
     * @return array Generation results
     */
    public function generateEveryThreeDaysPattern(?int $courseId = null, ?int $advanceWeeks = null): array
    {
        $advanceWeeks = $advanceWeeks ?? $this->config['advance_weeks'];
        $startDate = now()->addDay(); // Start from tomorrow
        $endDate = now()->addWeeks($advanceWeeks);

        Log::info('CustomScheduleGeneratorService: Generating every 3 days pattern', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'advance_weeks' => $advanceWeeks,
            'course_id' => $courseId
        ]);

        return $this->generateCustomPattern($startDate, $endDate, 'every_three_days', $courseId);
    }

    /**
     * Generate course dates following custom patterns
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string $pattern Pattern type
     * @param int|null $courseId Specific course ID (optional)
     * @return array
     */
    private function generateCustomPattern(Carbon $startDate, Carbon $endDate, string $pattern, ?int $courseId = null): array
    {
        $results = [
            'pattern' => $pattern,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_days' => $startDate->diffInDays($endDate),
            'courses_processed' => 0,
            'dates_created' => 0,
            'dates_skipped' => 0,
            'errors' => [],
            'course_details' => []
        ];

        // Get courses to process
        $courses = $this->getCoursesToProcess($courseId);
        $results['courses_processed'] = $courses->count();

        foreach ($courses as $course) {
            try {
                $courseResults = $this->generateCourseSchedule($course, $startDate, $endDate, $pattern);
                $results['dates_created'] += $courseResults['dates_created'];
                $results['dates_skipped'] += $courseResults['dates_skipped'];
                $results['course_details'][$course->title] = $courseResults;

                Log::info('CustomScheduleGeneratorService: Course processed', [
                    'course' => $course->title,
                    'pattern' => $pattern,
                    'dates_created' => $courseResults['dates_created'],
                    'dates_skipped' => $courseResults['dates_skipped']
                ]);

            } catch (\Exception $e) {
                $error = "Error processing course {$course->title}: " . $e->getMessage();
                $results['errors'][] = $error;
                Log::error('CustomScheduleGeneratorService: Course processing error', [
                    'course' => $course->title,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('CustomScheduleGeneratorService: Pattern generation completed', [
            'pattern' => $pattern,
            'total_created' => $results['dates_created'],
            'total_skipped' => $results['dates_skipped'],
            'total_errors' => count($results['errors'])
        ]);

        return $results;
    }

    /**
     * Generate schedule for a single course following the specified pattern
     * 
     * @param Course $course
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string $pattern
     * @return array
     */
    private function generateCourseSchedule(Course $course, Carbon $startDate, Carbon $endDate, string $pattern): array
    {
        $results = [
            'course_id' => $course->id,
            'course_title' => $course->title,
            'pattern' => $pattern,
            'dates_created' => 0,
            'dates_skipped' => 0,
            'date_details' => []
        ];

        $currentDate = $startDate->copy();
        $dayNumber = $this->getNextDayNumberForCourse($course);
        $weekCounter = 0; // For biweekly patterns

        while ($currentDate->lte($endDate)) {
            // Check if this date matches the pattern
            if (!$this->dateMatchesPattern($currentDate, $pattern, $weekCounter)) {
                $currentDate->addDay();
                continue;
            }

            // Skip holidays
            if ($this->config['skip_holidays'] && $this->isHoliday($currentDate)) {
                $results['dates_skipped']++;
                $results['date_details'][] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => 'skipped',
                    'reason' => 'Holiday'
                ];
                $currentDate->addDay();
                continue;
            }

            // Check if CourseDate already exists
            if ($this->courseDateExists($course, $currentDate)) {
                $results['dates_skipped']++;
                $results['date_details'][] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => 'skipped',
                    'reason' => 'CourseDate already exists'
                ];
                $currentDate->addDay();
                continue;
            }

            // Get course unit for this day
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
            $startTime = $currentDate->copy()->setTimeFromTimeString($this->config['start_time']);
            $endTime = $startTime->copy()->addHours($this->config['duration_hours']);

            CourseDate::create([
                'course_unit_id' => $courseUnit->id,
                'starts_at' => $startTime,
                'ends_at' => $endTime,
                'day_number' => $dayNumber,
                'is_active' => false, // Inactive by default
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $results['dates_created']++;
            $results['date_details'][] = [
                'date' => $currentDate->format('Y-m-d'),
                'day_of_week' => $currentDate->format('l'),
                'status' => 'created',
                'course_unit' => $courseUnit->admin_title,
                'day_number' => $dayNumber,
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'end_time' => $endTime->format('Y-m-d H:i:s')
            ];

            $dayNumber++;
            $currentDate->addDay();

            // Increment week counter for patterns that need it
            if ($currentDate->dayOfWeek === Carbon::MONDAY) {
                $weekCounter++;
            }
        }

        return $results;
    }

    /**
     * Check if a date matches the specified pattern
     * 
     * @param Carbon $date
     * @param string $pattern
     * @param int $weekCounter
     * @return bool
     */
    private function dateMatchesPattern(Carbon $date, string $pattern, int $weekCounter): bool
    {
        switch ($pattern) {
            case 'monday_wednesday_biweekly':
                // Monday and Wednesday, every other week
                $isCorrectDay = in_array($date->dayOfWeek, [Carbon::MONDAY, Carbon::WEDNESDAY]);
                $isCorrectWeek = ($weekCounter % 2) === 0; // Even weeks (0, 2, 4...)
                return $isCorrectDay && $isCorrectWeek;

            case 'every_three_days':
                // Every 3 days from start date
                $daysSinceStart = $date->diffInDays(now());
                return ($daysSinceStart % 3) === 0;

            case 'monday_wednesday_friday':
                // Monday, Wednesday, Friday every week
                return in_array($date->dayOfWeek, [Carbon::MONDAY, Carbon::WEDNESDAY, Carbon::FRIDAY]);

            case 'tuesday_thursday':
                // Tuesday and Thursday every week
                return in_array($date->dayOfWeek, [Carbon::TUESDAY, Carbon::THURSDAY]);

            default:
                return false;
        }
    }

    /**
     * Get courses to process (all active or specific course)
     * 
     * @param int|null $courseId
     * @return Collection
     */
    private function getCoursesToProcess(?int $courseId = null): Collection
    {
        $query = Course::where('is_active', true)
            ->whereHas('CourseUnits');

        if ($courseId) {
            $query->where('id', $courseId);
        }

        return $query->get();
    }

    /**
     * Get the next day number for a course
     * 
     * @param Course $course
     * @return int
     */
    private function getNextDayNumberForCourse(Course $course): int
    {
        $lastCourseDate = CourseDate::whereHas('courseUnit', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })->orderBy('day_number', 'desc')->first();

        return $lastCourseDate ? ($lastCourseDate->day_number + 1) : 1;
    }

    /**
     * Check if a CourseDate already exists for this course on this date
     * 
     * @param Course $course
     * @param Carbon $date
     * @return bool
     */
    private function courseDateExists(Course $course, Carbon $date): bool
    {
        return CourseDate::whereDate('starts_at', $date->format('Y-m-d'))
            ->whereHas('courseUnit', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->exists();
    }

    /**
     * Get the appropriate course unit for a given day number
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
        $unitIndex = ($dayNumber - 1) % $courseUnits->count();
        return $courseUnits->get($unitIndex);
    }

    /**
     * Check if a date is a holiday
     * 
     * @param Carbon $date
     * @return bool
     */
    private function isHoliday(Carbon $date): bool
    {
        $holidays = [
            '01-01', // New Year's Day
            '07-04', // Independence Day
            '11-11', // Veterans Day
            '11-22', // Thanksgiving (approximate)
            '12-25', // Christmas Day
        ];

        return in_array($date->format('m-d'), $holidays);
    }

    /**
     * Preview what dates would be generated for a pattern
     * 
     * @param string $pattern
     * @param int|null $courseId
     * @param int|null $advanceWeeks
     * @return array
     */
    public function previewPattern(string $pattern, ?int $courseId = null, ?int $advanceWeeks = null): array
    {
        $advanceWeeks = $advanceWeeks ?? $this->config['advance_weeks'];
        $startDate = now()->startOfWeek();
        $endDate = now()->addWeeks($advanceWeeks);

        $preview = [
            'pattern' => $pattern,
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'total_weeks' => $advanceWeeks
            ],
            'courses' => [],
            'estimated_total' => 0,
            'sample_dates' => []
        ];

        $courses = $this->getCoursesToProcess($courseId);

        foreach ($courses as $course) {
            $currentDate = $startDate->copy();
            $estimatedDates = 0;
            $sampleDates = [];
            $weekCounter = 0;

            while ($currentDate->lte($endDate) && count($sampleDates) < 10) {
                if ($this->dateMatchesPattern($currentDate, $pattern, $weekCounter)) {
                    $estimatedDates++;
                    $sampleDates[] = [
                        'date' => $currentDate->format('Y-m-d'),
                        'day_name' => $currentDate->format('l')
                    ];
                }
                $currentDate->addDay();

                if ($currentDate->dayOfWeek === Carbon::MONDAY) {
                    $weekCounter++;
                }
            }

            $preview['courses'][$course->title] = [
                'course_id' => $course->id,
                'estimated_dates' => $estimatedDates,
                'sample_dates' => $sampleDates
            ];

            $preview['estimated_total'] += $estimatedDates;
        }

        return $preview;
    }

    /**
     * Generate multiple patterns at once
     * 
     * @param array $patterns Array of pattern names
     * @param int|null $courseId
     * @param int|null $advanceWeeks
     * @return array
     */
    public function generateMultiplePatterns(array $patterns, ?int $courseId = null, ?int $advanceWeeks = null): array
    {
        $results = [
            'patterns_processed' => count($patterns),
            'total_dates_created' => 0,
            'total_dates_skipped' => 0,
            'pattern_results' => []
        ];

        foreach ($patterns as $pattern) {
            try {
                switch ($pattern) {
                    case 'monday_wednesday_biweekly':
                        $patternResult = $this->generateMondayWednesdayEveryOtherWeek($courseId, $advanceWeeks);
                        break;
                    case 'every_three_days':
                        $patternResult = $this->generateEveryThreeDaysPattern($courseId, $advanceWeeks);
                        break;
                    default:
                        $patternResult = ['error' => "Unknown pattern: $pattern"];
                }

                $results['pattern_results'][$pattern] = $patternResult;
                $results['total_dates_created'] += $patternResult['dates_created'] ?? 0;
                $results['total_dates_skipped'] += $patternResult['dates_skipped'] ?? 0;

            } catch (\Exception $e) {
                $results['pattern_results'][$pattern] = [
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }
}