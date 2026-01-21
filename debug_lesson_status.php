<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentUnit;
use App\Models\InstLesson;
use App\Models\InstUnit;
use App\Models\StudentLesson;

// Get today's CourseDate
$today = now()->format('Y-m-d');
$courseDate = DB::table('course_dates')
    ->whereDate('starts_at', $today)
    ->first();

if (!$courseDate) {
    echo "No CourseDate found for today\n";
    exit;
}

echo "CourseDate ID: {$courseDate->id}\n\n";

// Get InstUnit for today
$instUnit = InstUnit::where('course_date_id', $courseDate->id)
    ->whereNull('completed_at')
    ->latest('created_at')
    ->first();

if (!$instUnit) {
    echo "No active InstUnit found\n";
    exit;
}

echo "InstUnit ID: {$instUnit->id}\n";
echo "InstUnit Created: {$instUnit->created_at}\n\n";

// Get InstLessons
$instLessons = InstLesson::where('inst_unit_id', $instUnit->id)->get();

echo "InstLessons for today:\n";
foreach ($instLessons as $il) {
    echo "  - InstLesson ID: {$il->id}, Lesson ID: {$il->lesson_id}, Completed: " . ($il->completed_at ?? 'NULL') . "\n";
}

// Get StudentUnit
$studentUnit = StudentUnit::where('course_date_id', $courseDate->id)
    ->whereHas('CourseAuth', function($q) {
        $q->where('user_id', 1); // Richard Clark
    })
    ->first();

if (!$studentUnit) {
    echo "\nNo StudentUnit found for user 1\n";
    exit;
}

echo "\nStudentUnit ID: {$studentUnit->id}\n\n";

// Get StudentLessons
$studentLessons = StudentLesson::where('student_unit_id', $studentUnit->id)->get();

echo "ALL StudentLessons (any date):\n";
foreach ($studentLessons as $sl) {
    echo "  - StudentLesson ID: {$sl->id}, Lesson ID: {$sl->lesson_id}, InstLesson ID: {$sl->inst_lesson_id}, Completed: " . ($sl->completed_at ?? 'NULL') . "\n";
}

// Get only today's StudentLessons
$todayInstLessonIds = $instLessons->pluck('id')->toArray();
$todayStudentLessons = StudentLesson::where('student_unit_id', $studentUnit->id)
    ->whereIn('inst_lesson_id', $todayInstLessonIds)
    ->get();

echo "\nStudentLessons FILTERED by today's InstLessons:\n";
foreach ($todayStudentLessons as $sl) {
    echo "  - StudentLesson ID: {$sl->id}, Lesson ID: {$sl->lesson_id}, InstLesson ID: {$sl->inst_lesson_id}, Completed: " . ($sl->completed_at ?? 'NULL') . "\n";
}
