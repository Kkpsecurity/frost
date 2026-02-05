<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING WHY NO CHALLENGE ===\n\n";

// Find the most recent StudentLesson
$studentLesson = \App\Models\StudentLesson::whereNull('completed_at')
    ->whereNull('dnc_at')
    ->orderBy('id', 'desc')
    ->first();

if (!$studentLesson) {
    echo "❌ NO ACTIVE STUDENT LESSON FOUND!\n";
    echo "This means the student hasn't joined the lesson yet.\n";
    exit;
}

echo "✅ Found StudentLesson:\n";
echo "   ID: {$studentLesson->id}\n";
echo "   Student ID: {$studentLesson->student_id}\n";
echo "   Lesson ID: {$studentLesson->lesson_id}\n";
echo "   Created: {$studentLesson->created_at}\n";
echo "   Age: " . now()->diffInSeconds($studentLesson->created_at) . " seconds\n\n";

// Check completed lessons
$completedLessonIds = \App\Models\StudentLesson::where('student_id', $studentLesson->student_id)
    ->whereNotNull('completed_at')
    ->where('created_at', '>=', now()->startOfDay())
    ->pluck('lesson_id')
    ->toArray();

echo "Completed lesson IDs today: " . (empty($completedLessonIds) ? "NONE" : implode(', ', $completedLessonIds)) . "\n\n";

// Try calling Challenger::Ready()
echo "Calling Challenger::Ready()...\n";
try {
    $response = \App\Classes\Challenger::Ready($studentLesson, $completedLessonIds);

    if ($response && $response->challenge_id) {
        echo "✅ CHALLENGE IS READY!\n";
        echo "   Challenge ID: {$response->challenge_id}\n";
        echo "   Is Final: " . ($response->is_final ? 'YES' : 'NO') . "\n";
        echo "   Is EOL: " . ($response->is_eol ? 'YES' : 'NO') . "\n\n";

        // Check if it exists
        $challenge = \App\Models\Challenge::find($response->challenge_id);
        if ($challenge) {
            echo "✅ Challenge exists in database\n";
            echo "   Expires: {$challenge->expires_at}\n";
            echo "   Completed: " . ($challenge->completed_at ?? 'NULL') . "\n";
            echo "   Failed: " . ($challenge->failed_at ?? 'NULL') . "\n";
        }
    } else {
        echo "❌ Challenger::Ready() returned NULL\n";
        echo "This means no challenge is ready yet.\n\n";

        // Check if it's just timing
        $age = now()->diffInSeconds($studentLesson->created_at);
        echo "Debug Info:\n";
        echo "   Lesson age: {$age} seconds\n";
        echo "   Dev mode window: 30-120 seconds\n";
        echo "   Should be ready: " . ($age >= 30 ? 'YES' : 'NO (too early)') . "\n";
    }
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
