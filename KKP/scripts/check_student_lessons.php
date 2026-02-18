<?php

/**
 * Check Student Lessons (Today)
 *
 * Usage:
 *   php check_student_lessons.php --user_id=2
 *   php check_student_lessons.php --course_date_id=10773 --user_id=2
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

function parseArgs(array $argv): array
{
    $args = [
        'course_date_id' => null,
        'user_id' => null,
    ];

    foreach ($argv as $arg) {
        if (str_starts_with($arg, '--course_date_id=')) {
            $args['course_date_id'] = (int) substr($arg, strlen('--course_date_id='));
            continue;
        }
        if (str_starts_with($arg, '--user_id=')) {
            $args['user_id'] = (int) substr($arg, strlen('--user_id='));
            continue;
        }
    }

    return $args;
}

$args = parseArgs(array_slice($argv, 1));
$courseDateId = $args['course_date_id'];
$userId = $args['user_id'];

if (!$courseDateId) {
    $courseDateId = (int) (DB::table('course_dates')
        ->whereDate('starts_at', today())
        ->orderBy('starts_at', 'asc')
        ->value('id') ?? 0);
}

if (!$courseDateId) {
    echo "❌ Could not determine course_date_id for today. Pass --course_date_id=...\n";
    exit(1);
}

if (!$userId) {
    echo "❌ Pass --user_id=...\n";
    exit(1);
}

echo "\n=== Student Lessons for Today ===\n";
echo "Date: " . date('Y-m-d') . "\n";
echo "course_date_id: {$courseDateId}\n";
echo "user_id: {$userId}\n";
echo str_repeat('=', 80) . "\n\n";

$studentUnits = DB::table('student_unit')
    ->join('course_auths', 'student_unit.course_auth_id', '=', 'course_auths.id')
    ->where('student_unit.course_date_id', $courseDateId)
    ->where('course_auths.user_id', $userId)
    ->select([
        'student_unit.id',
        'student_unit.course_auth_id',
        'student_unit.inst_unit_id',
        'student_unit.course_date_id',
        'student_unit.created_at',
        'student_unit.completed_at',
        'student_unit.unit_completed',
    ])
    ->orderByDesc('student_unit.id')
    ->get();

if ($studentUnits->isEmpty()) {
    echo "No student_unit rows found for this user+course_date.\n\n";
    exit(0);
}

foreach ($studentUnits as $su) {
    echo "StudentUnit ID: {$su->id}\n";
    echo "  course_auth_id: {$su->course_auth_id}\n";
    echo "  inst_unit_id:   {$su->inst_unit_id}\n";
    echo "  created_at:     {$su->created_at}\n";
    echo "  completed_at:   {$su->completed_at}\n";
    echo "  unit_completed: " . ((bool) ($su->unit_completed ?? false) ? 'true' : 'false') . "\n";

    $lessons = DB::table('student_lesson')
        ->where('student_unit_id', $su->id)
        ->orderBy('lesson_id', 'asc')
        ->get([
            'id',
            'lesson_id',
            'inst_lesson_id',
            'created_at',
            'completed_at',
            'completed_by',
            'dnc_at',
        ]);

    echo "  student_lesson rows: " . $lessons->count() . "\n";

    foreach ($lessons as $sl) {
        $isCompleted = $sl->completed_at ? 'YES' : 'NO';
        echo "    - SL#{$sl->id} lesson_id={$sl->lesson_id} inst_lesson_id={$sl->inst_lesson_id} completed={$isCompleted} created_at={$sl->created_at} completed_at={$sl->completed_at}\n";
    }

    echo str_repeat('-', 80) . "\n\n";
}

echo "Done.\n\n";
