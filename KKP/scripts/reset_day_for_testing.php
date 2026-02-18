<?php

/**
 * Reset Day For Testing
 *
 * Resets "today" classroom state so pause/lesson/activity tracking can be re-tested cleanly.
 *
 * Safety:
 * - CLI only
 * - Dry-run by default (requires --yes to execute)
 *
 * Usage examples:
 *   php reset_day_for_testing.php --course_date_id=10757 --yes
 *   php reset_day_for_testing.php --course_date_id=10757 --user_id=2 --delete_student_units=1 --yes
 *   php reset_day_for_testing.php --yes   (auto-detects today's course_date_id)
 */

if (php_sapi_name() !== 'cli') {
    echo "This script is CLI-only.\n";
    exit(1);
}

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

function parseArgs(array $argv): array
{
    $args = [
        'course_date_id' => null,
        'user_id' => null,
        'yes' => false,
        'delete_student_units' => false,
    ];

    foreach ($argv as $arg) {
        if ($arg === '--yes') {
            $args['yes'] = true;
            continue;
        }

        if ($arg === '--delete_student_units=1' || $arg === '--delete_student_units=true') {
            $args['delete_student_units'] = true;
            continue;
        }

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
$doExecute = (bool) $args['yes'];
$deleteStudentUnits = (bool) $args['delete_student_units'];

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

$courseDate = \App\Models\CourseDate::find($courseDateId);
if (!$courseDate) {
    echo "❌ course_date_id {$courseDateId} not found.\n";
    exit(1);
}

$instUnit = $courseDate->InstUnit;
$instUnitId = $instUnit?->id;

echo "=== RESET DAY FOR TESTING ===\n";
echo "course_date_id: {$courseDateId}\n";
echo "user_id filter: " . ($userId ? (string) $userId : 'ALL') . "\n";
echo "delete_student_units: " . ($deleteStudentUnits ? 'YES' : 'NO') . "\n";
echo "mode: " . ($doExecute ? 'EXECUTE' : 'DRY-RUN (add --yes to run)') . "\n";
echo str_repeat('=', 80) . "\n\n";

// Find StudentUnit IDs for this course date (optionally filtered by user)
$studentUnitIdsQuery = DB::table('student_unit')
    ->where('student_unit.course_date_id', $courseDateId);

if ($userId) {
    $studentUnitIdsQuery = $studentUnitIdsQuery
        ->join('course_auths', 'student_unit.course_auth_id', '=', 'course_auths.id')
        ->where('course_auths.user_id', $userId)
        ->select('student_unit.id');
} else {
    $studentUnitIdsQuery = $studentUnitIdsQuery->select('student_unit.id');
}

$studentUnitIds = $studentUnitIdsQuery->pluck('id')->map(fn($v) => (int) $v)->values();

$studentLessonCount = $studentUnitIds->isEmpty()
    ? 0
    : (int) DB::table('student_lesson')->whereIn('student_unit_id', $studentUnitIds)->count();

$studentActivityCount = (int) DB::table('student_activity')
    ->when($userId, fn($q) => $q->where('user_id', $userId))
    ->where('course_date_id', $courseDateId)
    ->count();

$instLessonIds = collect();
$instLessonCount = 0;
$instBreakCount = 0;

if ($instUnitId) {
    $instLessonIds = DB::table('inst_lesson')->where('inst_unit_id', $instUnitId)->pluck('id');
    $instLessonCount = (int) $instLessonIds->count();

    if ($instLessonIds->isNotEmpty()) {
        $instBreakCount = (int) DB::table('inst_lesson_breaks')->whereIn('inst_lesson_id', $instLessonIds)->count();
    }
}

echo "Will delete:\n";
echo "- student_activity rows: {$studentActivityCount}\n";
echo "- student_lesson rows:   {$studentLessonCount}\n";
echo "- inst_lesson_breaks:    {$instBreakCount}\n";
echo "- inst_lesson rows:      {$instLessonCount}\n";
if ($deleteStudentUnits) {
    echo "- student_unit rows:    " . (int) $studentUnitIds->count() . "\n";
}

echo "\n";

if (!$doExecute) {
    echo "✅ Dry-run complete. Re-run with --yes to execute.\n\n";
    exit(0);
}

DB::beginTransaction();
try {
    // Student activity (scoped by course_date_id + optional user_id)
    $deletedStudentActivity = DB::table('student_activity')
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->where('course_date_id', $courseDateId)
        ->delete();

    // Student lessons (scoped by student_unit ids)
    $deletedStudentLessons = 0;
    if ($studentUnitIds->isNotEmpty()) {
        $deletedStudentLessons = DB::table('student_lesson')
            ->whereIn('student_unit_id', $studentUnitIds)
            ->delete();
    }

    // Instructor breaks then lessons
    $deletedBreaks = 0;
    $deletedInstLessons = 0;
    if ($instUnitId) {
        if ($instLessonIds->isNotEmpty()) {
            $deletedBreaks = DB::table('inst_lesson_breaks')
                ->whereIn('inst_lesson_id', $instLessonIds)
                ->delete();
        }

        $deletedInstLessons = DB::table('inst_lesson')
            ->where('inst_unit_id', $instUnitId)
            ->delete();
    }

    // Optional: delete student units for a truly clean join/start flow
    $deletedStudentUnits = 0;
    if ($deleteStudentUnits && $studentUnitIds->isNotEmpty()) {
        $deletedStudentUnits = DB::table('student_unit')
            ->whereIn('id', $studentUnitIds)
            ->delete();
    }

    DB::commit();

    echo "✅ Reset complete. Deleted:\n";
    echo "- student_activity: {$deletedStudentActivity}\n";
    echo "- student_lesson:   {$deletedStudentLessons}\n";
    echo "- inst_lesson_breaks: {$deletedBreaks}\n";
    echo "- inst_lesson:      {$deletedInstLessons}\n";
    if ($deleteStudentUnits) {
        echo "- student_unit:     {$deletedStudentUnits}\n";
    }

    echo "\nNext test flow:\n";
    echo "1) Student opens classroom (creates/uses StudentUnit)\n";
    echo "2) Instructor starts lesson (creates InstLesson + StudentLesson)\n";
    echo "3) Instructor pauses/resumes lesson (creates breaks)\n";
    echo "4) Instructor completes lesson\n";
    echo "5) Run: php check_student_activity.php\n\n";

    exit(0);
} catch (Throwable $e) {
    DB::rollBack();
    echo "❌ Reset failed: {$e->getMessage()}\n";
    exit(1);
}
