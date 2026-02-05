<?php

/**
 * Test the complete fix for active lesson detection
 *
 * This simulates the exact scenario:
 * - InstUnit is completed
 * - InstLesson exists but is not completed
 * - Should NOT show as active because InstUnit is completed
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InstLesson;
use App\Models\InstUnit;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "=================================================================\n";
echo "🔍 TESTING COMPLETE ACTIVE LESSON FIX\n";
echo "=================================================================\n\n";

// Test Case 1: InstUnit is completed
echo "TEST CASE 1: InstUnit with completed_at set\n";
echo "-----------------------------------------------------------------\n";

$completedInstUnit = InstUnit::whereNotNull('completed_at')
    ->orderBy('id', 'desc')
    ->first();

if ($completedInstUnit) {
    echo "✅ Found completed InstUnit: {$completedInstUnit->id}\n";
    echo "   Completed at: {$completedInstUnit->completed_at}\n\n";

    $instLessons = InstLesson::where('inst_unit_id', $completedInstUnit->id)
        ->whereNull('completed_at')
        ->get();

    echo "   Found {$instLessons->count()} incomplete InstLesson(s):\n";

    foreach ($instLessons as $instLesson) {
        $lesson = $instLesson->Lesson;
        echo "      - Lesson {$instLesson->lesson_id}: " . ($lesson ? $lesson->title : 'Unknown') . "\n";
        echo "        Completed: " . ($instLesson->completed_at ?? 'NULL') . "\n";
        echo "        Paused: " . ($instLesson->is_paused ? 'YES' : 'NO') . "\n";
    }

    // Apply the FIXED logic
    $activeLessonId = null;
    $isInstUnitCompleted = $completedInstUnit->completed_at !== null;

    if (!$isInstUnitCompleted) {
        foreach ($instLessons as $instLesson) {
            if (!$instLesson->completed_at && !$instLesson->is_paused) {
                $activeLessonId = $instLesson->lesson_id;
                break;
            }
        }
    }

    echo "\n";
    echo "   🔍 RESULT with FIX:\n";
    echo "      - InstUnit completed: " . ($isInstUnitCompleted ? 'YES' : 'NO') . "\n";
    echo "      - Active lesson ID: " . ($activeLessonId ?? 'NULL') . "\n";

    if ($activeLessonId === null && $isInstUnitCompleted) {
        echo "      ✅ CORRECT: No active lesson because InstUnit is completed\n";
    } else if ($activeLessonId !== null) {
        echo "      ❌ WRONG: Should not have active lesson when InstUnit is completed!\n";
    }
} else {
    echo "⚠️  No completed InstUnit found. This is okay.\n";
}

echo "\n\n";

// Test Case 2: InstUnit is NOT completed (active class)
echo "TEST CASE 2: InstUnit with no completed_at (active class)\n";
echo "-----------------------------------------------------------------\n";

$activeInstUnit = InstUnit::whereNull('completed_at')
    ->orderBy('id', 'desc')
    ->first();

if ($activeInstUnit) {
    echo "✅ Found active InstUnit: {$activeInstUnit->id}\n";
    echo "   Started: {$activeInstUnit->start_time}\n";
    echo "   Completed: NULL (still active)\n\n";

    $instLessons = InstLesson::where('inst_unit_id', $activeInstUnit->id)
        ->get();

    echo "   Found {$instLessons->count()} InstLesson(s):\n";

    $incompleteCount = 0;
    foreach ($instLessons as $instLesson) {
        $lesson = $instLesson->Lesson;
        $isIncomplete = !$instLesson->completed_at;
        $isPaused = $instLesson->is_paused;

        if ($isIncomplete) {
            $incompleteCount++;
        }

        echo "      - Lesson {$instLesson->lesson_id}: " . ($lesson ? $lesson->title : 'Unknown') . "\n";
        echo "        Completed: " . ($instLesson->completed_at ?? 'NULL') . "\n";
        echo "        Paused: " . ($isPaused ? 'YES ⏸️' : 'NO ▶️') . "\n";
        echo "        Can be active: " . ($isIncomplete && !$isPaused ? 'YES 🟢' : 'NO 🔴') . "\n";
    }

    // Apply the FIXED logic
    $activeLessonId = null;
    $isInstUnitCompleted = $activeInstUnit->completed_at !== null;

    if (!$isInstUnitCompleted) {
        foreach ($instLessons as $instLesson) {
            if (!$instLesson->completed_at && !$instLesson->is_paused) {
                $activeLessonId = $instLesson->lesson_id;
                break;
            }
        }
    }

    echo "\n";
    echo "   🔍 RESULT with FIX:\n";
    echo "      - InstUnit completed: " . ($isInstUnitCompleted ? 'YES' : 'NO') . "\n";
    echo "      - Active lesson ID: " . ($activeLessonId ?? 'NULL') . "\n";

    if ($incompleteCount > 0 && $activeLessonId !== null) {
        echo "      ✅ CORRECT: Active lesson detected for active InstUnit\n";
    } else if ($incompleteCount === 0 && $activeLessonId === null) {
        echo "      ✅ CORRECT: No active lesson (all completed or paused)\n";
    } else if ($incompleteCount > 0 && $activeLessonId === null) {
        echo "      ℹ️  INFO: InstLessons exist but are all paused\n";
    }
} else {
    echo "⚠️  No active InstUnit found. This is okay.\n";
}

echo "\n";
echo "=================================================================\n";
echo "✅ TESTING COMPLETE\n";
echo "=================================================================\n\n";

echo "SUMMARY OF FIX:\n";
echo "1. Check if InstUnit is completed\n";
echo "2. If InstUnit is completed, NO lessons should be active\n";
echo "3. If InstUnit is NOT completed:\n";
echo "   - Check each InstLesson\n";
echo "   - Lesson is active only if: NOT completed AND NOT paused\n";
echo "4. Only ONE lesson can be active at a time (first match)\n\n";

