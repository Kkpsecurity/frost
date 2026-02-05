<?php

/**
 * Debug script to check for any InstLessons across all InstUnits
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InstLesson;
use App\Models\InstUnit;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "=================================================================\n";
echo "🔍 CHECKING ALL INST_LESSONS IN DATABASE\n";
echo "=================================================================\n\n";

// Get all InstLessons with not completed
$allInstLessons = InstLesson::whereNull('completed_at')
    ->with(['InstUnit', 'Lesson'])
    ->orderBy('id', 'desc')
    ->get();

echo "Found " . $allInstLessons->count() . " InstLesson(s) that are NOT completed:\n\n";

foreach ($allInstLessons as $instLesson) {
    $instUnit = $instLesson->InstUnit;
    $lesson = $instLesson->Lesson;

    echo "InstLesson ID: {$instLesson->id}\n";
    echo "   Lesson: " . ($lesson ? $lesson->title : "Lesson {$instLesson->lesson_id}") . " (ID: {$instLesson->lesson_id})\n";
    echo "   InstUnit: {$instLesson->inst_unit_id}\n";
    echo "   Created: {$instLesson->created_at}\n";
    echo "   Completed: " . ($instLesson->completed_at ?? 'NULL') . "\n";
    echo "   Is Paused: " . ($instLesson->is_paused ? 'YES ⏸️' : 'NO ▶️') . "\n";

    if ($instUnit) {
        echo "   InstUnit Start: {$instUnit->start_time}\n";
        echo "   InstUnit Completed: " . ($instUnit->completed_at ?? 'NULL') . "\n";
    }

    echo "\n";
}

// Now check for the specific issue: any InstLesson that is paused
$pausedLessons = InstLesson::where('is_paused', true)
    ->whereNull('completed_at')
    ->with(['InstUnit', 'Lesson'])
    ->orderBy('id', 'desc')
    ->get();

if ($pausedLessons->count() > 0) {
    echo "⏸️  PAUSED LESSONS (These should NOT show as active):\n";
    echo "=================================================================\n\n";

    foreach ($pausedLessons as $instLesson) {
        $lesson = $instLesson->Lesson;
        echo "   Lesson: " . ($lesson ? $lesson->title : "Lesson {$instLesson->lesson_id}") . " (ID: {$instLesson->lesson_id})\n";
        echo "   InstLesson ID: {$instLesson->id}\n";
        echo "   Created: {$instLesson->created_at}\n";
        echo "   ⚠️  This lesson is PAUSED and should NOT be shown as active\n\n";
    }
} else {
    echo "✅ No paused lessons found\n\n";
}

echo "=================================================================\n";
echo "✅ DEBUG COMPLETE\n";
echo "=================================================================\n\n";

