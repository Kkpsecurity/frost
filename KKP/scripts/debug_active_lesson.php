<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== DEBUGGING LESSON STATUS API ===\n\n";

// Check today's InstLessons
$instLessons = DB::table('inst_lesson')
    ->join('inst_unit', 'inst_unit.id', '=', 'inst_lesson.inst_unit_id')
    ->join('course_dates', 'course_dates.id', '=', 'inst_unit.course_date_id')
    ->whereDate('course_dates.starts_at', now()->format('Y-m-d'))
    ->select('inst_lesson.*')
    ->get();

echo "Today's InstLessons:\n";
foreach ($instLessons as $il) {
    $completed = $il->completed_at ? 'COMPLETED' : 'ACTIVE';
    echo "  - InstLesson ID: {$il->id}, Lesson ID: {$il->lesson_id}, Status: {$completed}\n";
}

// Check which lesson is active (no completed_at)
$activeInstLesson = DB::table('inst_lesson')
    ->join('inst_unit', 'inst_unit.id', '=', 'inst_lesson.inst_unit_id')
    ->join('course_dates', 'course_dates.id', '=', 'inst_unit.course_date_id')
    ->whereDate('course_dates.starts_at', now()->format('Y-m-d'))
    ->whereNull('inst_lesson.completed_at')
    ->select('inst_lesson.*')
    ->first();

echo "\nActive InstLesson (completed_at = NULL):\n";
if ($activeInstLesson) {
    echo "  ❌ FOUND: Lesson ID {$activeInstLesson->lesson_id} is ACTIVE!\n";
    echo "  This means the instructor hasn't finished this lesson yet.\n";
} else {
    echo "  ✅ NONE - All lessons completed or no lessons started\n";
}

// Check lesson names
echo "\nLesson Names:\n";
$lessons = DB::table('lessons')->whereIn('id', [6, 7])->get();
foreach ($lessons as $lesson) {
    echo "  - Lesson {$lesson->id}: {$lesson->title}\n";
}

echo "\n=== CONCLUSION ===\n";
if ($activeInstLesson) {
    $lessonName = DB::table('lessons')->where('id', $activeInstLesson->lesson_id)->value('title');
    echo "❌ PROBLEM: Lesson {$activeInstLesson->lesson_id} ({$lessonName}) shows as ACTIVE\n";
    echo "   because InstLesson {$activeInstLesson->id} has completed_at = NULL\n";
    echo "\n";
    echo "SOLUTION: Instructor must COMPLETE the lesson on their dashboard\n";
    echo "   OR we need to manually set completed_at for InstLesson {$activeInstLesson->id}\n";
} else {
    echo "✅ All lessons are either completed or not started.\n";
}
