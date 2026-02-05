<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Challenge History for User 2 ===\n\n";

// Simulate what the endpoint does when course_auth_id=2 is passed
$courseAuthId = 2;
$userId = 2;

echo "Course Auth ID: {$courseAuthId}\n";
echo "User ID: {$userId}\n\n";

// Verify ownership
$courseAuth = \App\Models\CourseAuth::where('id', $courseAuthId)
    ->where('user_id', $userId)
    ->first();

if (!$courseAuth) {
    echo "❌ CourseAuth not found or unauthorized\n";
    exit;
}

echo "✅ CourseAuth found: {$courseAuth->id}\n\n";

// Get student unit
$studentUnit = \App\Models\StudentUnit::where('course_auth_id', $courseAuth->id)
    ->orderBy('id', 'desc')
    ->first();

if (!$studentUnit) {
    echo "❌ No student unit found\n";
    exit;
}

echo "✅ StudentUnit ID: {$studentUnit->id}\n\n";

// Get student lessons
$studentLessons = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
    ->pluck('id');

echo "StudentLesson IDs: " . $studentLessons->implode(', ') . "\n";
echo "Total StudentLessons: " . $studentLessons->count() . "\n\n";

// Get challenges
$challenges = \App\Models\Challenge::with(['StudentLesson.Lesson'])
    ->whereIn('student_lesson_id', $studentLessons)
    ->where(function ($query) {
        $query->whereNotNull('completed_at')
            ->orWhereNotNull('failed_at')
            ->orWhere('expires_at', '<', now());
    })
    ->orderBy('created_at', 'desc')
    ->limit(20)
    ->get();

echo "✅ Challenges found: " . $challenges->count() . "\n\n";

if ($challenges->count() > 0) {
    echo "Sample challenges:\n";
    foreach ($challenges->take(5) as $ch) {
        $lessonName = $ch->StudentLesson->Lesson->name ?? 'Unknown';
        echo "  ID {$ch->id}: {$lessonName} - Completed: " . ($ch->completed_at ? 'YES' : 'NO') . "\n";
    }

    $stats = [
        'completed' => $challenges->where('completed_at', '!=', null)->count(),
        'failed' => $challenges->where('failed_at', '!=', null)->count(),
        'expired' => $challenges->where('expires_at', '<', now())
            ->whereNull('completed_at')
            ->whereNull('failed_at')
            ->count(),
    ];

    echo "\n📈 Stats:\n";
    echo "  Completed: {$stats['completed']}\n";
    echo "  Failed: {$stats['failed']}\n";
    echo "  Expired: {$stats['expired']}\n";
}
