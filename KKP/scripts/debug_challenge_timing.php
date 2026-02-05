<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Challenge Timing Debug ===\n\n";

// Check dev mode setting
$devMode = config('challenger.dev_mode');
echo "CHALLENGER_DEV_MODE from config: " . ($devMode ? 'TRUE' : 'FALSE') . "\n";
echo "ENV value: " . env('CHALLENGER_DEV_MODE', 'not set') . "\n\n";

// Check timing settings
if ($devMode) {
    echo "Using DEV MODE timings:\n";
    echo "  First challenge: " . config('challenger.dev_lesson_start_min') . "-" . config('challenger.dev_lesson_start_max') . " seconds\n";
    echo "  Random challenge: " . config('challenger.dev_lesson_random_min') . "-" . config('challenger.dev_lesson_random_max') . " seconds\n";
    echo "  Final challenge: " . config('challenger.dev_final_challenge_min') . "-" . config('challenger.dev_final_challenge_max') . " seconds\n";
} else {
    echo "Using PRODUCTION timings:\n";
    echo "  First challenge: " . config('challenger.lesson_start_min') . "-" . config('challenger.lesson_start_max') . " minutes\n";
    echo "  Random challenge: " . config('challenger.lesson_random_min') . "-" . config('challenger.lesson_random_max') . " minutes\n";
    echo "  Final challenge: " . config('challenger.final_challenge_min') . "-" . config('challenger.final_challenge_max') . " minutes\n";
}

echo "\n=== Active Student Lesson ===\n";
// Check for active student lessons for user_id 2
$courseAuth = \App\Models\CourseAuth::where('user_id', 2)->orderBy('id', 'desc')->first();

if (!$courseAuth) {
    echo "No course auth found for user_id 2\n";
    exit;
}

$studentUnit = \App\Models\StudentUnit::where('course_auth_id', $courseAuth->id)
    ->orderBy('id', 'desc')
    ->first();

if ($studentUnit) {
    echo "Student Unit ID: {$studentUnit->id}\n";

    $activeLesson = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
        ->whereNull('completed_at')
        ->orderBy('id', 'desc')
        ->first();

    if ($activeLesson) {
        echo "Active Student Lesson ID: {$activeLesson->id}\n";
        echo "Created at: {$activeLesson->created_at}\n";
        echo "Minutes since start: " . $activeLesson->created_at->diffInMinutes(now()) . "\n";
        echo "Seconds since start: " . $activeLesson->created_at->diffInSeconds(now()) . "\n";

        // Check latest challenge
        $latestChallenge = $activeLesson->LatestChallenge;

        if ($latestChallenge) {
            echo "\n=== Latest Challenge (via relationship) ===\n";
            echo "Challenge ID: {$latestChallenge->id}\n";
            echo "Created: {$latestChallenge->created_at}\n";
            echo "Expires: {$latestChallenge->expires_at}\n";
            echo "Completed: " . ($latestChallenge->completed_at ?: 'NULL') . "\n";
            echo "Failed: " . ($latestChallenge->failed_at ?: 'NULL') . "\n";
        } else {
            echo "\nNo latest challenge found (relationship returns null)\n";
        }

        // Check all challenges for this lesson
        $allChallenges = \App\Models\Challenge::where('student_lesson_id', $activeLesson->id)
            ->orderBy('id', 'desc')
            ->get();

        echo "\n=== All Challenges for this lesson ===\n";
        echo "Total: " . $allChallenges->count() . "\n";
        foreach ($allChallenges as $ch) {
            echo "  ID {$ch->id}: Created={$ch->created_at}, Completed=" . ($ch->completed_at ?: 'NULL') . ", Failed=" . ($ch->failed_at ?: 'NULL') . "\n";
        }
    } else {
        echo "No active student lesson found\n";
    }
}
