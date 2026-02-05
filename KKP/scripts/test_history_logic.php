<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Challenge History Endpoint Logic ===\n\n";

// Simulate what the endpoint does for user_id 2
$userId = 2;

echo "Looking up user_id: {$userId}\n";

$courseAuth = \App\Models\CourseAuth::where('user_id', $userId)
    ->orderBy('id', 'desc')
    ->first();

if (!$courseAuth) {
    echo "No course auth found\n";
    exit;
}

echo "CourseAuth ID: {$courseAuth->id}\n";

$studentUnit = \App\Models\StudentUnit::where('course_auth_id', $courseAuth->id)
    ->orderBy('id', 'desc')
    ->first();

if (!$studentUnit) {
    echo "No student unit found\n";
    exit;
}

echo "StudentUnit ID: {$studentUnit->id}\n";

$studentLessons = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
    ->pluck('id');

echo "StudentLesson IDs: " . $studentLessons->implode(', ') . "\n";
echo "Total StudentLessons: " . $studentLessons->count() . "\n\n";

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

echo "Challenges found: " . $challenges->count() . "\n\n";

if ($challenges->count() > 0) {
    echo "Challenge IDs: ";
    foreach ($challenges as $ch) {
        echo $ch->id . " ";
    }
    echo "\n\n";

    // Show the JSON structure that would be returned
    $result = [
        'success' => true,
        'challenges' => $challenges->map(function ($challenge) {
            return [
                'id' => $challenge->id,
                'created_at' => $challenge->created_at,
                'expires_at' => $challenge->expires_at,
                'completed_at' => $challenge->completed_at,
                'failed_at' => $challenge->failed_at,
                'is_final' => $challenge->is_final,
                'is_eol' => $challenge->is_eol,
                'student_lesson' => [
                    'id' => $challenge->StudentLesson->id,
                    'lesson' => [
                        'id' => $challenge->StudentLesson->Lesson->id,
                        'name' => $challenge->StudentLesson->Lesson->name,
                    ],
                ],
            ];
        }),
        'stats' => [
            'completed' => $challenges->where('completed_at', '!=', null)->count(),
            'failed' => $challenges->where('failed_at', '!=', null)->count(),
            'expired' => $challenges->where('expires_at', '<', now())
                ->whereNull('completed_at')
                ->whereNull('failed_at')
                ->count(),
        ],
    ];

    echo "JSON Response Preview:\n";
    echo json_encode($result, JSON_PRETTY_PRINT);
}
