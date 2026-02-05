<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHALLENGER SETUP CHECK ===\n\n";

// Check config
echo "1. Configuration:\n";
echo "   CHALLENGER_DISBLED: " . (config('challenger.disabled') ? 'TRUE (DISABLED!)' : 'false (enabled)') . "\n";
echo "   CHALLENGER_DEV_MODE: " . (config('challenger.dev_mode') ? 'TRUE (fast timing)' : 'false (normal timing)') . "\n\n";

// Check timings
echo "2. Challenge Timing Windows:\n";
if (config('challenger.dev_mode')) {
    echo "   🚀 DEV MODE ACTIVE - Fast Timings:\n";
    echo "   - First challenge: " . config('challenger.dev_lesson_start_min') . "s - " . config('challenger.dev_lesson_start_max') . "s\n";
    echo "   - Random: " . config('challenger.dev_lesson_random_min') . "s - " . config('challenger.dev_lesson_random_max') . "s\n";
    echo "   - Final: " . config('challenger.dev_final_challenge_min') . "s - " . config('challenger.dev_final_challenge_max') . "s\n";
} else {
    echo "   ⏰ Production Timings:\n";
    echo "   - First challenge: " . config('challenger.lesson_start_min') . "s - " . config('challenger.lesson_start_max') . "s\n";
    echo "   - Random: " . config('challenger.lesson_random_min') . "s - " . config('challenger.lesson_random_max') . "s\n";
    echo "   - Final: " . config('challenger.final_challenge_min') . "s - " . config('challenger.final_challenge_max') . "s\n";
}
echo "\n";

// Check active lessons
echo "3. Active Student Lessons (created in last 10 minutes):\n";
$recentLessons = \App\Models\StudentLesson::whereNull('completed_at')
    ->whereNull('dnc_at')
    ->where('created_at', '>=', now()->subMinutes(10))
    ->with('studentUnit')
    ->get();

if ($recentLessons->isEmpty()) {
    echo "   ❌ No active student lessons found\n";
} else {
    foreach ($recentLessons as $sl) {
        $age = now()->diffInSeconds($sl->created_at);
        $ageMin = floor($age / 60);
        $ageSec = $age % 60;

        echo "   StudentLesson #{$sl->id}\n";
        echo "   - Student ID: {$sl->student_id}\n";
        echo "   - Lesson ID: {$sl->lesson_id}\n";
        echo "   - Age: {$ageMin}m {$ageSec}s\n";

        if (config('challenger.dev_mode')) {
            $minWait = config('challenger.dev_lesson_start_min');
            $maxWait = config('challenger.dev_lesson_start_max');
            if ($age < $minWait) {
                echo "   - ⏳ Too early (wait " . ($minWait - $age) . "s more)\n";
            } elseif ($age > $maxWait) {
                echo "   - ⚠️ Window passed (challenge should exist)\n";
            } else {
                echo "   - ✅ In challenge window!\n";
            }
        }
        echo "\n";
    }
}

// Check for challenges created today
echo "4. Challenges Created Today:\n";
$challenges = \App\Models\Challenge::where('created_at', '>=', now()->startOfDay())
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($challenges->isEmpty()) {
    echo "   ❌ No challenges created today\n";
} else {
    foreach ($challenges as $c) {
        $status = 'ACTIVE';
        if ($c->completed_at) $status = '✅ COMPLETED';
        elseif ($c->failed_at) $status = '❌ FAILED';
        elseif (now()->gt($c->expires_at)) $status = '⏰ EXPIRED';

        echo "   Challenge #{$c->id} - {$status}\n";
        echo "   - StudentLesson ID: {$c->student_lesson_id}\n";
        echo "   - Created: {$c->created_at->format('H:i:s')}\n";
        echo "   - Expires: {$c->expires_at->format('H:i:s')}\n";
        echo "\n";
    }
}

echo "=== END CHECK ===\n";
