<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use Carbon\Carbon;

class TodayCourseDateSeeder extends Seeder
{
    /**
     * Seed today's course dates for live classes
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Creating course dates for today...');

        // Get today's date in Eastern timezone
        $today = Carbon::today('America/New_York');

        // Define course schedules for today
        $courseSchedules = [
            [
                'course_unit_id' => 1, // Day 1 of FL-D40 Day Course
                'start_time' => '08:00',
                'end_time' => '17:00',
                'description' => 'FL-D40-D1 (Day Course - Day 1)'
            ],
            // Uncomment to add more courses for the same day
            // [
            //     'course_unit_id' => 6, // Night 1 of FL-D40 Night Course
            //     'start_time' => '18:00',
            //     'end_time' => '22:00',
            //     'description' => 'FL-D40-N1 (Night Course - Night 1)'
            // ],
        ];

        foreach ($courseSchedules as $schedule) {
            // Check if course unit exists
            $courseUnit = CourseUnit::find($schedule['course_unit_id']);
            if (!$courseUnit) {
                $this->command->warn("Course unit {$schedule['course_unit_id']} not found. Skipping...");
                continue;
            }

            // Create start and end timestamps
            $startsAt = $today->copy()->setTimeFromTimeString($schedule['start_time']);
            $endsAt = $today->copy()->setTimeFromTimeString($schedule['end_time']);

            // Check if course date already exists
            $existingCourseDate = CourseDate::where('course_unit_id', $schedule['course_unit_id'])
                ->whereDate('starts_at', $today->toDateString())
                ->first();

            if ($existingCourseDate) {
                $this->command->warn("Course date already exists for {$schedule['description']} on {$today->toDateString()}");
                continue;
            }

            // Create new course date
            $courseDate = CourseDate::create([
                'is_active' => true,
                'course_unit_id' => $schedule['course_unit_id'],
                'starts_at' => $startsAt,
                'ends_at' => $endsAt
            ]);

            $this->command->info("✅ Created: {$schedule['description']} | {$startsAt->format('g:i A')} - {$endsAt->format('g:i A')} | ID: {$courseDate->id}");
        }

        $this->command->info('🎯 Today\'s course dates created successfully!');

        // Display today's schedule
        $this->displayTodaysSchedule();
    }

    /**
     * Display today's course schedule
     */
    private function displayTodaysSchedule()
    {
        $today = Carbon::today('America/New_York');

        $todaysCourses = CourseDate::with(['courseUnit.course'])
            ->whereDate('starts_at', $today->toDateString())
            ->where('is_active', true)
            ->orderBy('starts_at')
            ->get();

        if ($todaysCourses->isEmpty()) {
            $this->command->warn('No active courses found for today.');
            return;
        }

        $this->command->info("\n📅 Today's Course Schedule ({$today->toDateString()}):");
        $this->command->table(
            ['ID', 'Course', 'Unit', 'Start Time', 'End Time', 'Duration'],
            $todaysCourses->map(function ($courseDate) {
                return [
                    $courseDate->id,
                    $courseDate->courseUnit->course->title,
                    $courseDate->courseUnit->admin_title,
                    $courseDate->starts_at->format('g:i A T'),
                    $courseDate->ends_at->format('g:i A T'),
                    $courseDate->starts_at->diffForHumans($courseDate->ends_at, true)
                ];
            })
        );
    }
}
