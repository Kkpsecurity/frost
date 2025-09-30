<?php

require 'bootstrap/app.php';
$app = app();

echo "=== Course Unit Lessons Count Analysis ===\n\n";

// Get the course from course_date
$courseDate = App\Models\CourseDate::find(1);
if (!$courseDate) {
    echo "CourseDate not found\n";
    exit;
}

$course = $courseDate->GetCourse();
echo "Course: {$course->title_long}\n";

$courseUnits = $course->GetCourseUnits();
echo "Total CourseUnits: " . $courseUnits->count() . "\n\n";

$totalLessons = 0;
foreach ($courseUnits as $index => $courseUnit) {
    $courseUnitLessons = $courseUnit->GetCourseUnitLessons();
    $lessonsCount = $courseUnitLessons->count();
    $totalLessons += $lessonsCount;

    echo "CourseUnit " . ($index + 1) . ": {$courseUnit->title}\n";
    echo "  CourseUnitLessons: $lessonsCount\n";

    if ($lessonsCount > 0) {
        foreach ($courseUnitLessons as $cul) {
            $lesson = $cul->GetLesson();
            echo "    - {$lesson->title} ({$cul->progress_minutes} min)\n";
        }
    }
    echo "\n";
}

echo "Total Lessons across all CourseUnits: $totalLessons\n";
echo "\n=== Testing CourseDatesService Output ===\n";

$service = new App\Services\Frost\Instructors\CourseDatesService();
$data = $service->getTodaysLessons();

foreach ($data as $lesson) {
    echo "lesson_count returned by service: " . $lesson['lesson_count'] . "\n";
}
