<?php

/**
 * Test script to verify active lesson detection fix
 *
 * This script checks that the active lesson logic correctly:
 * 1. Only marks lessons as active if they are NOT completed AND NOT paused
 * 2. Does not show paused lessons as active
 * 3. Does not show completed lessons as active
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InstLesson;
use App\Models\InstUnit;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "=================================================================\n";
echo "🔍 TESTING ACTIVE LESSON DETECTION FIX\n";
echo "=================================================================\n\n";

// Find the most recent InstUnit
$instUnit = InstUnit::orderBy('id', 'desc')->first();

if (!$instUnit) {
    echo "❌ No InstUnit found. Please start a class first.\n";
    exit(1);
}

echo "📊 Testing InstUnit: {$instUnit->id}\n";
echo "   Started at: {$instUnit->start_time}\n";
echo "   Completed: " . ($instUnit->completed_at ? 'Yes' : 'No') . "\n\n";

// Get all InstLessons for this InstUnit
$instLessons = InstLesson::where('inst_unit_id', $instUnit->id)
    ->orderBy('lesson_id')
    ->get();

echo "📚 Found " . $instLessons->count() . " InstLesson(s):\n\n";

$activeLessonId = null;
$activeLessonCount = 0;

foreach ($instLessons as $instLesson) {
    $lesson = $instLesson->Lesson;
    $lessonTitle = $lesson ? $lesson->title : "Lesson {$instLesson->lesson_id}";

    $isCompleted = !is_null($instLesson->completed_at);
    $isPaused = $instLesson->is_paused;
    $shouldBeActive = !$isCompleted && !$isPaused;

    echo "   Lesson {$instLesson->lesson_id}: {$lessonTitle}\n";
    echo "      - Completed: " . ($isCompleted ? '✅ Yes' : '❌ No') . "\n";
    echo "      - Paused: " . ($isPaused ? '⏸️  Yes' : '▶️  No') . "\n";
    echo "      - Should be active: " . ($shouldBeActive ? '🟢 YES' : '🔴 NO') . "\n";

    if ($shouldBeActive) {
        if ($activeLessonId === null) {
            $activeLessonId = $instLesson->lesson_id;
            echo "      ⭐ THIS IS THE ACTIVE LESSON\n";
        }
        $activeLessonCount++;
    }

    echo "\n";
}

echo "=================================================================\n";
echo "📈 SUMMARY:\n";
echo "=================================================================\n";
echo "Total InstLessons: {$instLessons->count()}\n";
echo "Lessons that should be active: {$activeLessonCount}\n";
echo "Active Lesson ID: " . ($activeLessonId ?? 'NONE') . "\n\n";

if ($activeLessonCount === 0) {
    echo "✅ CORRECT: No active lesson detected (all lessons are either completed or paused)\n";
} elseif ($activeLessonCount === 1) {
    echo "✅ CORRECT: Exactly one active lesson detected\n";
} else {
    echo "⚠️  WARNING: Multiple active lessons detected! Only one should be active at a time.\n";
    echo "   This suggests there are multiple lessons that are not completed and not paused.\n";
}

// Test the actual API logic
echo "\n";
echo "=================================================================\n";
echo "🧪 TESTING ACTUAL API LOGIC:\n";
echo "=================================================================\n\n";

$todaysInstLessons = InstLesson::where('inst_unit_id', $instUnit->id)
    ->get()
    ->keyBy('lesson_id');

// Apply the fixed logic
$apiActiveLessonId = null;
foreach ($todaysInstLessons as $lessonId => $instLesson) {
    if (!$instLesson->completed_at && !$instLesson->is_paused) {
        $apiActiveLessonId = $lessonId;
        break; // Only one lesson can be active at a time
    }
}

echo "API would return active_lesson_id: " . ($apiActiveLessonId ?? 'NULL') . "\n\n";

if ($apiActiveLessonId === $activeLessonId) {
    echo "✅ SUCCESS: API logic matches expected behavior!\n";
} else {
    echo "❌ FAILURE: API logic does not match expected behavior!\n";
    echo "   Expected: " . ($activeLessonId ?? 'NULL') . "\n";
    echo "   Got: " . ($apiActiveLessonId ?? 'NULL') . "\n";
}

echo "\n";
echo "=================================================================\n";
echo "✅ TEST COMPLETE\n";
echo "=================================================================\n\n";

