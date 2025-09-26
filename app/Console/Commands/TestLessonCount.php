<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseDate;
use App\Services\Frost\Instructors\CourseDatesService;

class TestLessonCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-lesson-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test lesson counting logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== Course Unit Lessons Count Analysis ===");

        // Get first course date
        $courseDate = CourseDate::first();
        if (!$courseDate) {
            $this->error("No CourseDate found");
            return;
        }

        $this->info("CourseDate ID: {$courseDate->id}");

        $courseUnit = $courseDate->GetCourseUnit();
        $this->info("CourseUnit: {$courseUnit->title}");

        // Use CourseUnitObj to get lessons properly
        $courseUnitObj = new \App\Classes\CourseUnitObj($courseUnit);
        $lessons = $courseUnitObj->CourseUnitLessons();
        $lessonCount = $lessons->count();

        $this->info("Lessons in this CourseUnit: {$lessonCount}");

        // Show each lesson
        foreach ($lessons as $index => $lesson) {
            $lessonModel = $lesson->GetLesson();
            $this->info("  " . ($index + 1) . ". {$lessonModel->title}");
        }

        $this->info("\n=== Testing CourseDatesService Output ===");
        $service = new CourseDatesService();
        $data = $service->getTodaysLessons();

        if (isset($data['lessons']) && count($data['lessons']) > 0) {
            $firstLesson = $data['lessons'][0];
            $this->info("Service returned lesson_count: " . ($firstLesson['lesson_count'] ?? 'NOT_SET'));
        } else {
            $this->info("No lessons returned from service");
            $this->info("Data: " . json_encode($data, JSON_PRETTY_PRINT));
        }
    }
}
