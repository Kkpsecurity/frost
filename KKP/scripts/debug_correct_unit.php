<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Find Correct Student Unit ===\n\n";

// The student unit with challenges is 100782
$studentUnit = \App\Models\StudentUnit::find(100782);

if (!$studentUnit) {
    echo "Student unit 100782 not found\n";
    exit;
}

echo "Student Unit ID: {$studentUnit->id}\n";
echo "Course Auth ID: {$studentUnit->course_auth_id}\n";

$courseAuth = $studentUnit->CourseAuth;
echo "User ID: {$courseAuth->user_id}\n\n";

// Get active lesson
$activeLesson = \App\Models\StudentLesson::where('student_unit_id', 100782)
    ->whereNull('completed_at')
    ->orderBy('id', 'desc')
    ->first();

if ($activeLesson) {
    echo "=== Active Lesson ===\n";
    echo "Student Lesson ID: {$activeLesson->id}\n";
    echo "Created: {$activeLesson->created_at}\n";
    echo "Minutes since start: " . $activeLesson->created_at->diffInMinutes(now()) . "\n\n";

    // Check latest challenge via relationship
    $latestChallenge = $activeLesson->LatestChallenge;

    if ($latestChallenge) {
        echo "=== Latest Challenge (via relationship - should be NULL if all completed) ===\n";
        echo "Challenge ID: {$latestChallenge->id}\n";
        echo "Completed: " . ($latestChallenge->completed_at ?: 'NULL') . "\n";
        echo "Failed: " . ($latestChallenge->failed_at ?: 'NULL') . "\n";
    } else {
        echo "✅ Latest Challenge is NULL (correct - all challenges completed)\n";
    }

    echo "\n=== All Challenges for this lesson ===\n";
    $allChallenges = \App\Models\Challenge::where('student_lesson_id', $activeLesson->id)
        ->orderBy('id', 'desc')
        ->get();

    foreach ($allChallenges as $ch) {
        echo "  ID {$ch->id}: Completed=" . ($ch->completed_at ? $ch->completed_at->format('H:i:s') : 'NULL') . ", Failed=" . ($ch->failed_at ?: 'NULL') . "\n";
    }
}

echo "\n=== Test History Query ===\n";
$studentLessons = \App\Models\StudentLesson::where('student_unit_id', 100782)->pluck('id');
echo "Student Lessons: " . $studentLessons->implode(', ') . "\n";

$challenges = \App\Models\Challenge::with(['StudentLesson.Lesson'])
    ->whereIn('student_lesson_id', $studentLessons)
    ->where(function ($query) {
        $query->whereNotNull('completed_at')
            ->orWhereNotNull('failed_at')
            ->orWhere('expires_at', '<', now());
    })
    ->orderBy('created_at', 'desc')
    ->get();

echo "Challenges found: " . $challenges->count() . "\n";
