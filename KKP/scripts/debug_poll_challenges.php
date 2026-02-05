<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Debug Student Poll Challenges Logic ===\n\n";

$userId = 2;

// Simulate the active_classroom data from poll
$activeClassroom = [
    'status' => 'active',
    'course_id' => 1,
    'course_auth_id' => 2,
    'course_date_id' => 10772,
    'inst_unit_id' => 10685,
    'student_unit' => [
        'id' => 100782,
        'course_auth_id' => 2,
        'course_date_id' => 10772,
    ],
];

echo "Active Classroom Data:\n";
echo "- course_auth_id: " . ($activeClassroom['student_unit']['course_auth_id'] ?? 'NULL') . "\n";
echo "- course_date_id: " . ($activeClassroom['course_date_id'] ?? 'NULL') . "\n";
echo "- student_unit.id: " . ($activeClassroom['student_unit']['id'] ?? 'NULL') . "\n\n";

$challenges = [];

try {
    if ($activeClassroom && isset($activeClassroom['student_unit']['course_auth_id'])) {
        echo "✅ activeClassroom exists and has student_unit.course_auth_id\n";

        $courseAuthId = $activeClassroom['student_unit']['course_auth_id'];
        echo "✅ courseAuthId: $courseAuthId\n";

        // Get StudentUnit for this CourseAuth
        $studentUnit = \App\Models\StudentUnit::where('course_auth_id', $courseAuthId)
            ->where('course_date_id', $activeClassroom['course_date_id'])
            ->first();

        if ($studentUnit) {
            echo "✅ StudentUnit found: ID {$studentUnit->id}\n";

            // Get all StudentLessons for this StudentUnit
            $studentLessons = \App\Models\StudentLesson::where('student_unit_id', $studentUnit->id)
                ->pluck('id');

            echo "✅ StudentLesson IDs: " . $studentLessons->implode(', ') . "\n";
            echo "   Total StudentLessons: " . $studentLessons->count() . "\n";

            if ($studentLessons->isNotEmpty()) {
                echo "✅ studentLessons is not empty\n";

                // Get challenges (completed, failed, or expired only - not pending)
                $query = \App\Models\Challenge::whereIn('student_lesson_id', $studentLessons)
                    ->where(function ($q) {
                        $q->whereNotNull('completed_at')
                            ->orWhereNotNull('failed_at')
                            ->orWhere('expires_at', '<', now());
                    });

                echo "\n📊 Challenge Query:\n";
                echo "SQL: " . $query->toSql() . "\n";
                echo "Bindings: " . json_encode($query->getBindings()) . "\n\n";

                $challenges = $query->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get()
                    ->map(function ($challenge) {
                        $isExpired = $challenge->expires_at && $challenge->expires_at < now()
                            && !$challenge->completed_at && !$challenge->failed_at;

                        return [
                            'id' => $challenge->id,
                            'student_lesson_id' => $challenge->student_lesson_id,
                            'lesson_name' => $challenge->StudentLesson?->Lesson?->title ?? 'Unknown',
                            'type' => $challenge->type,
                            'created_at' => $challenge->created_at?->toISOString(),
                            'completed_at' => $challenge->completed_at?->toISOString(),
                            'failed_at' => $challenge->failed_at?->toISOString(),
                            'expired_at' => $isExpired ? $challenge->expires_at?->toISOString() : null,
                            'is_final' => (bool) $challenge->is_final,
                            'is_eol' => (bool) $challenge->is_eol,
                        ];
                    })
                    ->toArray();

                echo "✅ Challenges found: " . count($challenges) . "\n\n";

                if (count($challenges) > 0) {
                    echo "Sample challenges:\n";
                    foreach (array_slice($challenges, 0, 5) as $challenge) {
                        echo "  - ID {$challenge['id']}: {$challenge['lesson_name']}\n";
                        echo "    Completed: " . ($challenge['completed_at'] ? 'YES' : 'NO') . "\n";
                        echo "    Failed: " . ($challenge['failed_at'] ? 'YES' : 'NO') . "\n";
                        echo "    Expired: " . ($challenge['expired_at'] ? 'YES' : 'NO') . "\n";
                    }
                } else {
                    echo "❌ No challenges returned from query\n";

                    // Check if challenges exist at all
                    $allChallenges = \App\Models\Challenge::whereIn('student_lesson_id', $studentLessons)->count();
                    echo "   Total challenges (any status): $allChallenges\n";

                    $completedChallenges = \App\Models\Challenge::whereIn('student_lesson_id', $studentLessons)
                        ->whereNotNull('completed_at')
                        ->count();
                    echo "   Completed challenges: $completedChallenges\n";

                    $failedChallenges = \App\Models\Challenge::whereIn('student_lesson_id', $studentLessons)
                        ->whereNotNull('failed_at')
                        ->count();
                    echo "   Failed challenges: $failedChallenges\n";

                    $expiredChallenges = \App\Models\Challenge::whereIn('student_lesson_id', $studentLessons)
                        ->whereNotNull('expired_at')
                        ->count();
                    echo "   Expired challenges: $expiredChallenges\n";
                }
            } else {
                echo "❌ studentLessons is empty\n";
            }
        } else {
            echo "❌ StudentUnit not found for course_auth_id=$courseAuthId, course_date_id={$activeClassroom['course_date_id']}\n";
        }
    } else {
        echo "❌ activeClassroom is null or missing student_unit.course_auth_id\n";
    }
} catch (\Throwable $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n📋 Final challenges array count: " . count($challenges) . "\n";
