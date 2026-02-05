<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Challenge History Endpoint ===\n\n";

// Find student unit for user_id 2
$courseAuth = \App\Models\CourseAuth::where('user_id', 2)->orderBy('id', 'desc')->first();

if (!$courseAuth) {
    echo "No course auth found for user_id 2\n";
    exit;
}

$studentUnit = \App\Models\StudentUnit::where('course_auth_id', $courseAuth->id)
    ->orderBy('id', 'desc')
    ->first();

if (!$studentUnit) {
    echo "No student unit found for user_id 2\n";
    exit;
}

echo "Student Unit ID: {$studentUnit->id}\n\n";

// Get all student lessons for this unit
$studentLessons = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
    ->pluck('id');

echo "Student Lessons IDs: " . $studentLessons->implode(', ') . "\n";
echo "Total Student Lessons: " . $studentLessons->count() . "\n\n";

// Get all challenges for these lessons
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

echo "Total Challenges Found: " . $challenges->count() . "\n\n";

if ($challenges->count() > 0) {
    echo "Recent Challenges:\n";
    echo str_repeat('-', 120) . "\n";
    printf(
        "%-10s %-20s %-20s %-20s %-20s %-8s %-8s %s\n",
        "ID",
        "Created",
        "Expires",
        "Completed",
        "Failed",
        "IsFinal",
        "IsEOL",
        "Lesson"
    );
    echo str_repeat('-', 120) . "\n";

    foreach ($challenges as $challenge) {
        printf(
            "%-10s %-20s %-20s %-20s %-20s %-8s %-8s %s\n",
            $challenge->id,
            $challenge->created_at->format('Y-m-d H:i:s'),
            $challenge->expires_at->format('Y-m-d H:i:s'),
            $challenge->completed_at ? $challenge->completed_at->format('Y-m-d H:i:s') : 'NULL',
            $challenge->failed_at ? $challenge->failed_at->format('Y-m-d H:i:s') : 'NULL',
            $challenge->is_final ? 'Yes' : 'No',
            $challenge->is_eol ? 'Yes' : 'No',
            $challenge->StudentLesson->Lesson->name ?? 'Unknown'
        );
    }

    echo str_repeat('-', 120) . "\n";
}

// Calculate statistics
$stats = [
    'completed' => $challenges->where('completed_at', '!=', null)->count(),
    'failed' => $challenges->where('failed_at', '!=', null)->count(),
    'expired' => $challenges->where('expires_at', '<', now())
        ->whereNull('completed_at')
        ->whereNull('failed_at')
        ->count(),
];

echo "\n📈 Statistics:\n";
echo "  Completed: {$stats['completed']}\n";
echo "  Failed: {$stats['failed']}\n";
echo "  Expired: {$stats['expired']}\n";
