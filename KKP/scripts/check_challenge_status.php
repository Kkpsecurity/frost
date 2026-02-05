<?php

/**
 * Quick Challenge Debug Script
 * Check if challenges are being created and what their status is
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Challenge;
use App\Models\StudentLesson;
use Illuminate\Support\Facades\DB;

echo "=== CHALLENGE SYSTEM DEBUG ===\n\n";

// Check CHALLENGER_DISABLED setting
$disabled = config('challenger.disabled', false);
echo "1. CHALLENGER_DISABLED: " . ($disabled ? 'TRUE (DISABLED!)' : 'false (enabled)') . "\n\n";

// Check for active student lessons
echo "2. Active Student Lessons (not completed):\n";
$activeStudentLessons = StudentLesson::whereNull('completed_at')
    ->whereNull('dnc_at')
    ->where('created_at', '>=', now()->subDay())
    ->orderBy('created_at', 'desc')
    ->get();

if ($activeStudentLessons->isEmpty()) {
    echo "   ❌ No active student lessons found\n\n";
} else {
    foreach ($activeStudentLessons as $sl) {
        $duration = now()->diffInMinutes($sl->created_at);
        echo "   StudentLesson ID: {$sl->id}\n";
        echo "   - Student ID: {$sl->student_id}\n";
        echo "   - Lesson ID: {$sl->lesson_id}\n";
        echo "   - Created: {$sl->created_at} ({$duration} minutes ago)\n";
        echo "   - Completed: " . ($sl->completed_at ?? 'NULL') . "\n";
        echo "\n";
    }
}

// Check for challenges created today
echo "3. Challenges Created Today:\n";
$challenges = Challenge::where('created_at', '>=', now()->startOfDay())
    ->orderBy('created_at', 'desc')
    ->get();

if ($challenges->isEmpty()) {
    echo "   ❌ No challenges found today\n";
    echo "   This could mean:\n";
    echo "   - Not enough time has passed (need 5+ minutes)\n";
    echo "   - CHALLENGER_DISABLED is true\n";
    echo "   - No active lessons meet the criteria\n\n";
} else {
    foreach ($challenges as $challenge) {
        $status = 'ACTIVE';
        if ($challenge->completed_at) {
            $status = '✅ COMPLETED';
        } elseif ($challenge->failed_at) {
            $status = '❌ FAILED';
        } elseif (now()->gt($challenge->expires_at)) {
            $status = '⏰ EXPIRED';
        }

        echo "   Challenge ID: {$challenge->id} - {$status}\n";
        echo "   - StudentLesson ID: {$challenge->student_lesson_id}\n";
        echo "   - Created: {$challenge->created_at}\n";
        echo "   - Expires: {$challenge->expires_at}\n";
        echo "   - Is Final: " . ($challenge->is_final ? 'YES' : 'no') . "\n";
        echo "   - Is EOL: " . ($challenge->is_eol ? 'YES' : 'no') . "\n";
        echo "   - Completed: " . ($challenge->completed_at ?? 'NULL') . "\n";
        echo "   - Failed: " . ($challenge->failed_at ?? 'NULL') . "\n";
        echo "\n";
    }
}

// Check classroom poll data for one of the active lessons
if (!$activeStudentLessons->isEmpty()) {
    echo "4. Checking Challenger::Ready() for first active lesson:\n";
    $firstLesson = $activeStudentLessons->first();

    try {
        // Get completed lesson IDs for this student
        $completedLessonIds = StudentLesson::where('student_id', $firstLesson->student_id)
            ->whereNotNull('completed_at')
            ->pluck('lesson_id')
            ->toArray();

        echo "   StudentLesson ID: {$firstLesson->id}\n";
        echo "   Student ID: {$firstLesson->student_id}\n";
        echo "   Completed lessons: " . implode(', ', $completedLessonIds) . "\n";

        $challengerResponse = \App\Classes\Challenger::Ready($firstLesson, $completedLessonIds);

        if ($challengerResponse && $challengerResponse->challenge_id) {
            echo "   ✅ CHALLENGE IS READY!\n";
            echo "   - Challenge ID: {$challengerResponse->challenge_id}\n";
            echo "   - Is Final: " . ($challengerResponse->is_final ? 'YES' : 'no') . "\n";
            echo "   - Is EOL: " . ($challengerResponse->is_eol ? 'YES' : 'no') . "\n";
        } else {
            echo "   ⏳ No challenge ready yet (waiting for time window)\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "=== END DEBUG ===\n";
