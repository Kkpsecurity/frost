<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Update the first non-completed lesson to paused
$result = DB::table('inst_lesson')
    ->whereNull('completed_at')
    ->orderBy('id', 'asc')
    ->limit(1)
    ->update(['is_paused' => true]);

echo "Updated $result lesson(s) to paused\n";

// Show the updated lesson
$updated = DB::table('inst_lesson')
    ->whereNull('completed_at')
    ->where('is_paused', true)
    ->first();

if ($updated) {
    echo "✓ Lesson ID {$updated->id} is now PAUSED (Lesson {$updated->lesson_id})\n";
} else {
    echo "No paused lessons found\n";
}
