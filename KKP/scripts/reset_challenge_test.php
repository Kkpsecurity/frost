<?php

/**
 * reset_challenge_test.php
 *
 * Resets challenge test data for both test students:
 *   - User ID 2  (Richard Clark)
 *   - User ID 17 (Student Two)
 *
 * Actions:
 *   1. Delete the 3 most recent challenges for each student's active StudentLesson
 *   2. Clear dnc_at on any StudentLesson that was marked DNC today
 *
 * Usage: php KKP/scripts/reset_challenge_test.php
 */

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Challenge;
use App\Models\StudentLesson;
use App\Models\StudentUnit;

$userIds = [2, 17];

foreach ($userIds as $userId) {

    echo "\n========================================\n";
    echo "User ID: {$userId}\n";
    echo "========================================\n";

    // Find all student units for this user
    $studentUnits = StudentUnit::whereHas('CourseAuth', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })->orderByDesc('id')->get();

    if ($studentUnits->isEmpty()) {
        echo "  ❌ No StudentUnits found\n";
        continue;
    }

    foreach ($studentUnits as $su) {

        echo "  StudentUnit ID: {$su->id}\n";

        // Get all active (non-completed) student lessons for today
        $studentLessons = StudentLesson::where('student_unit_id', $su->id)
            ->whereDate('created_at', now()->toDateString())
            ->get();

        if ($studentLessons->isEmpty()) {
            echo "  ❌ No StudentLessons today\n";
            continue;
        }

        foreach ($studentLessons as $sl) {

            echo "  StudentLesson ID: {$sl->id} (lesson_id:{$sl->lesson_id})\n";

            // ------------------------------------------------------------------
            // 1. Delete the 3 most recent challenges for this StudentLesson
            // ------------------------------------------------------------------
            $recentChallenges = Challenge::where('student_lesson_id', $sl->id)
                ->orderByDesc('id')
                ->limit(3)
                ->get();

            if ($recentChallenges->isEmpty()) {
                echo "    No challenges to delete\n";
            } else {
                $ids = $recentChallenges->pluck('id')->toArray();
                Challenge::whereIn('id', $ids)->delete();
                echo "    ✅ Deleted " . count($ids) . " challenge(s): " . implode(', ', $ids) . "\n";
            }

            // ------------------------------------------------------------------
            // 2. Clear dnc_at if set
            // ------------------------------------------------------------------
            if ($sl->dnc_at) {
                DB::table('student_lesson')
                    ->where('id', $sl->id)
                    ->update([
                        'dnc_at'     => null,
                        'updated_at' => now(),
                    ]);
                echo "    ✅ DNC cleared on StudentLesson {$sl->id}\n";
            } else {
                echo "    No DNC to clear\n";
            }
        }
    }
}

echo "\n✅ Done.\n\n";
