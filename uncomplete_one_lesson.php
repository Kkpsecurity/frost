<?php

/**
 * Uncomplete One Lesson
 *
 * Removes one completed StudentLesson row for a given course_auth_id and purges
 * the PCLCache Redis key so the UI reflects the change.
 *
 * Usage:
 *   php uncomplete_one_lesson.php --course_auth_id=2 --dry_run=1
 *   php uncomplete_one_lesson.php --course_auth_id=2 --confirm=yes
 *
 * Optional:
 *   --lesson_id=123   Target a specific lesson_id (otherwise picks latest completed)
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

function argValue(array $argv, string $key): ?string
{
    foreach ($argv as $arg) {
        if (str_starts_with($arg, '--' . $key . '=')) {
            return substr($arg, strlen('--' . $key . '='));
        }
    }
    return null;
}

$courseAuthId = (int) (argValue(array_slice($argv, 1), 'course_auth_id') ?? 0);
$lessonIdArg = argValue(array_slice($argv, 1), 'lesson_id');
$lessonId = $lessonIdArg !== null ? (int) $lessonIdArg : null;
$dryRun = (int) (argValue(array_slice($argv, 1), 'dry_run') ?? 0) === 1;
$confirm = strtolower((string) (argValue(array_slice($argv, 1), 'confirm') ?? ''));

if ($courseAuthId <= 0) {
    echo "❌ Missing --course_auth_id=...\n";
    exit(1);
}

if (! $dryRun && $confirm !== 'yes') {
    echo "❌ Safety check: pass --confirm=yes to actually delete.\n";
    echo "   Tip: run with --dry_run=1 first.\n";
    exit(1);
}

$ca = CourseAuth::with('StudentUnits')->find($courseAuthId);
if (! $ca) {
    echo "❌ CourseAuth not found: {$courseAuthId}\n";
    exit(1);
}

$studentUnitIds = $ca->StudentUnits()->pluck('id')->values();
if ($studentUnitIds->isEmpty()) {
    echo "❌ No StudentUnits found for course_auth_id={$courseAuthId}.\n";
    exit(1);
}

$query = DB::table('student_lesson')
    ->whereIn('student_unit_id', $studentUnitIds)
    ->whereNotNull('completed_at');

if ($lessonId !== null && $lessonId > 0) {
    $query->where('lesson_id', $lessonId);
}

$target = $query
    ->orderByDesc('completed_at')
    ->orderByDesc('id')
    ->first([
        'id',
        'student_unit_id',
        'lesson_id',
        'inst_lesson_id',
        'created_at',
        'completed_at',
        'completed_by',
    ]);

if (! $target) {
    echo "❌ No completed student_lesson rows found for course_auth_id={$courseAuthId}";
    if ($lessonId !== null) {
        echo " and lesson_id={$lessonId}";
    }
    echo ".\n";
    echo "   Note: some flows store completion in self_study_lesson instead.\n";
    exit(1);
}

echo "\n=== Target student_lesson row ===\n";
echo "course_auth_id: {$courseAuthId}\n";
echo "student_lesson.id: {$target->id}\n";
echo "student_unit_id: {$target->student_unit_id}\n";
echo "lesson_id: {$target->lesson_id}\n";
echo "inst_lesson_id: {$target->inst_lesson_id}\n";
echo "completed_at: {$target->completed_at}\n";
echo "\n";

if ($dryRun) {
    echo "DRY RUN: no changes made.\n\n";
    exit(0);
}

DB::table('student_lesson')->where('id', $target->id)->delete();

// Purge the cached “previous completed lessons” key so UI updates immediately.
$redisKey = 'previous_completed_lessons:' . $courseAuthId;
Cache::store('redis')->connection()->del($redisKey);

// Force-recompute cache (best-effort; even if it fails, delete already happened)
try {
    $ca->PCLCache(true);
} catch (Throwable $e) {
    // ignore
}

echo "✅ Deleted student_lesson.id={$target->id} and purged redis key {$redisKey}.\n\n";
