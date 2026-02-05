<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Challenge Creation Frequency Debug ===\n\n";

// Get the active lesson
$studentLesson = \App\Models\StudentLesson::find(209060);

if (!$studentLesson) {
    echo "Student lesson not found\n";
    exit;
}

echo "Student Lesson ID: {$studentLesson->id}\n";
echo "Created: {$studentLesson->created_at}\n";
echo "Minutes since start: " . $studentLesson->created_at->diffInMinutes(now()) . "\n\n";

// Get all challenges
$challenges = \App\Models\Challenge::where('student_lesson_id', $studentLesson->id)
    ->orderBy('id', 'asc')
    ->get();

echo "Total Challenges: " . $challenges->count() . "\n\n";

if ($challenges->count() > 1) {
    echo "Time between challenges:\n";
    echo str_repeat('-', 80) . "\n";

    for ($i = 1; $i < $challenges->count(); $i++) {
        $prev = $challenges[$i - 1];
        $curr = $challenges[$i];

        $timeDiff = $prev->created_at->diffInSeconds($curr->created_at);
        $minutes = floor($timeDiff / 60);
        $seconds = $timeDiff % 60;

        echo "Challenge {$prev->id} -> {$curr->id}: {$minutes}m {$seconds}s\n";
        echo "  Previous completed: " . ($prev->completed_at ? $prev->completed_at->format('H:i:s') : 'NULL') . "\n";
        echo "  Current created: {$curr->created_at->format('H:i:s')}\n";

        if ($timeDiff < 300) { // Less than 5 minutes
            echo "  ⚠️  WARNING: Less than 5 minutes apart!\n";
        }
        echo "\n";
    }
    echo str_repeat('-', 80) . "\n";
}

// Check config
echo "\n=== Current Config ===\n";
$config = config('challenger');
echo "Dev Mode: " . ($config['dev_mode'] ? 'TRUE' : 'FALSE') . "\n";
echo "lesson_random_min: {$config['lesson_random_min']} seconds (" . ($config['lesson_random_min'] / 60) . " minutes)\n";
echo "lesson_random_max: {$config['lesson_random_max']} seconds (" . ($config['lesson_random_max'] / 60) . " minutes)\n";

// Check latest challenge
echo "\n=== Latest Challenge Check ===\n";
$latest = $studentLesson->LatestChallenge;
if ($latest) {
    echo "LatestChallenge ID: {$latest->id}\n";
    echo "Created: {$latest->created_at}\n";
    echo "Completed: " . ($latest->completed_at ?: 'NULL') . "\n";
    echo "Failed: " . ($latest->failed_at ?: 'NULL') . "\n";
    echo "Should NOT create new challenge until this is completed/failed\n";
} else {
    echo "LatestChallenge: NULL (OK to create new challenge)\n";
}
