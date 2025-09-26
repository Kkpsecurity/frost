<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\Course;
use Carbon\Carbon;

class CreateCourseDateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:create-date
                            {course_unit_id : Course Unit ID, "list" to show available units, or "auto" to auto-calculate day}
                            {date=today : Date (today, tomorrow, or YYYY-MM-DD)}
                            {--start-time=08:00 : Start time (HH:MM)}
                            {--end-time=17:00 : End time (HH:MM)}
                            {--timezone=America/New_York : Timezone}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new course date for live classes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== D License Course Date Creator ===');

        // Get parameters
        $courseUnitId = $this->argument('course_unit_id');
        $dateInput = $this->argument('date') ?? 'today';
        $startTime = $this->option('start-time');
        $endTime = $this->option('end-time');
        $timezone = $this->option('timezone');

        // Show available course units if requested
        if ($courseUnitId === 'list') {
            $this->info('Available Course Units:');
            $courseUnits = CourseUnit::with('course')->get();
            foreach ($courseUnits as $unit) {
                $this->line("  {$unit->id}: {$unit->course->title} - {$unit->title} ({$unit->admin_title})");
            }

            return 1;
        }

        // Parse date
        $courseDate = match($dateInput) {
            'today' => Carbon::today($timezone),
            'tomorrow' => Carbon::tomorrow($timezone),
            default => Carbon::createFromFormat('Y-m-d', $dateInput, $timezone)
        };

        if (!$courseDate) {
            $this->error("Invalid date format. Use 'today', 'tomorrow', or 'YYYY-MM-DD'");
            return 1;
        }

        // Auto-calculate day number if course_unit_id is 'auto'
        if ($courseUnitId === 'auto') {
            $dayOfWeek = (int)$courseDate->format('N'); // 1=Monday, 2=Tuesday, ..., 5=Friday
            $this->info("Auto-calculating day number based on date: Day {$dayOfWeek} ({$courseDate->format('l')})");

            // Find D License day course unit
            $courseUnit = CourseUnit::whereHas('course', function($query) {
                $query->where('title', 'like', '%D40%')
                      ->where('title', 'like', '%(Dy)%'); // Day version
            })
            ->where('ordering', $dayOfWeek)
            ->first();

            if (!$courseUnit) {
                $this->error("Could not find Day {$dayOfWeek} course unit for D License");
                return 1;
            }

            $courseUnitId = $courseUnit->id;
            $this->info("Found: {$courseUnit->title} (ID: {$courseUnitId})");
        }

        // Create start and end timestamps
        $startsAt = $courseDate->copy()->setTimeFromTimeString($startTime);
        $endsAt = $courseDate->copy()->setTimeFromTimeString($endTime);

        // Check if course date already exists for this unit and date
        $existingCourseDate = CourseDate::where('course_unit_id', $courseUnitId)
            ->whereDate('starts_at', $courseDate->toDateString())
            ->first();

        if ($existingCourseDate) {
            $this->warn("Course date already exists for {$courseUnit->admin_title} on {$courseDate->toDateString()}");

            if ($this->confirm('Do you want to update the existing course date?')) {
                $existingCourseDate->update([
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'is_active' => true
                ]);

                $this->info("Updated existing course date (ID: {$existingCourseDate->id})");
                $this->displayCourseDate($existingCourseDate);
                return 0;
            } else {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Create new course date
        $newCourseDate = CourseDate::create([
            'is_active' => true,
            'course_unit_id' => $courseUnitId,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt
        ]);

        $this->info("✅ Successfully created course date!");

        // Refresh the model to ensure proper casting
        $newCourseDate->fresh();
        $this->displayCourseDate($newCourseDate);

        return 0;
    }

    /**
     * Display course date information
     */
    private function displayCourseDate(CourseDate $courseDate)
    {
        $courseUnit = $courseDate->courseUnit;
        $course = $courseUnit->course;

        // Ensure dates are Carbon instances
        $startsAt = Carbon::parse($courseDate->starts_at);
        $endsAt = Carbon::parse($courseDate->ends_at);

        $this->table(
            ['Field', 'Value'],
            [
                ['Course Date ID', $courseDate->id],
                ['Course', $course->title],
                ['Course Unit', "{$courseUnit->title} ({$courseUnit->admin_title})"],
                ['Date', $startsAt->toDateString()],
                ['Start Time', $startsAt->format('g:i A T')],
                ['End Time', $endsAt->format('g:i A T')],
                ['Status', $courseDate->is_active ? '✅ Active' : '❌ Inactive'],
                ['Duration', $startsAt->diffForHumans($endsAt, true)]
            ]
        );

        $this->line('');
        $this->info('🎯 Live class is ready! Students will now see ONLINE status in their dashboard.');
    }
}
