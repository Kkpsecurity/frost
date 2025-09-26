<?php

// Simple test to verify CourseUnitObj lesson counting
use App\Models\CourseDate;
use App\Classes\CourseUnitObj;

echo "=== Testing CourseUnitObj Lesson Count ===\n";

// Get first course date
$courseDate = CourseDate::first();
if (!$courseDate) {
    echo "No CourseDate found\n";
    exit;
}

echo "CourseDate ID: {$courseDate->id}\n";

$courseUnit = $courseDate->GetCourseUnit();
echo "CourseUnit: {$courseUnit->title}\n";

// Use CourseUnitObj to get lessons
$courseUnitObj = new CourseUnitObj($courseUnit);
$lessons = $courseUnitObj->CourseUnitLessons();
$lessonCount = $lessons->count();

echo "Lessons in this CourseUnit: {$lessonCount}\n";

// Show each lesson
foreach ($lessons as $index => $lesson) {
    $lessonModel = $lesson->GetLesson();
    echo "  " . ($index + 1) . ". {$lessonModel->title}\n";
}

echo "\nTesting CourseDatesService...\n";
$service = new App\Services\Frost\Instructors\CourseDatesService();
$data = $service->getTodaysLessons();

if (isset($data['lessons']) && count($data['lessons']) > 0) {
    $firstLesson = $data['lessons'][0];
    echo "Service returned lesson_count: " . ($firstLesson['lesson_count'] ?? 'NOT_SET') . "\n";
} else {
    echo "No lessons returned from service\n";
}
