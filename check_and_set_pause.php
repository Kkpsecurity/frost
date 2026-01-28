<?php

/**
 * Check inst_lesson records and optionally set one to paused
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use App\Models\InstLesson;

echo "=== Checking inst_lesson records ===\n\n";

// Get all inst_lesson records
$lessons = DB::table('inst_lesson')
    ->select('id', 'inst_unit_id', 'lesson_id', 'is_paused', 'created_at', 'completed_at')
    ->get();

echo "Total inst_lesson records: " . count($lessons) . "\n\n";

foreach ($lessons as $lesson) {
    echo "ID: {$lesson->id} | Lesson: {$lesson->lesson_id} | Paused: " . ($lesson->is_paused ? 'YES' : 'NO') . " | Created: {$lesson->created_at}\n";
}

echo "\n=== Setting first non-completed lesson to paused ===\n\n";

// Find first non-completed lesson
$nonCompletedLesson = DB::table('inst_lesson')
    ->whereNull('completed_at')
    ->first();

if ($nonCompletedLesson) {
    echo "Found non-completed lesson: ID {$nonCompletedLesson->id}, Lesson {$nonCompletedLesson->lesson_id}\n";

    // Update to paused
    DB::table('inst_lesson')
        ->where('id', $nonCompletedLesson->id)
        ->update(['is_paused' => true]);

    echo "✓ Lesson {$nonCompletedLesson->id} is now PAUSED\n";
} else {
    echo "No non-completed lessons found\n";
}

echo "\n=== Final state ===\n\n";

$updatedLessons = DB::table('inst_lesson')
    ->select('id', 'lesson_id', 'is_paused', 'created_at', 'completed_at')
    ->get();

foreach ($updatedLessons as $lesson) {
    echo "ID: {$lesson->id} | Lesson: {$lesson->lesson_id} | Paused: " . ($lesson->is_paused ? 'YES ✓' : 'NO') . "\n";
}
