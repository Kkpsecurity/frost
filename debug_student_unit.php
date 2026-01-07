<?php

/**
 * One-off debug script (local only): inspect student_unit rows for a course_date_id.
 * Usage: php debug_student_unit.php 74
 */

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$courseDateId = isset($argv[1]) ? (int) $argv[1] : 0;
if ($courseDateId <= 0) {
    fwrite(STDERR, "Usage: php debug_student_unit.php <course_date_id>\n");
    exit(2);
}

$adminId = isset($argv[2]) ? (int) $argv[2] : 0;

function fmtTs($value): string
{
    if ($value === null) {
        return 'null';
    }

    if ($value instanceof DateTimeInterface) {
        return $value->format(DateTimeInterface::ATOM);
    }

    if (is_int($value) || (is_string($value) && ctype_digit($value))) {
        return date(DateTimeInterface::ATOM, (int) $value);
    }

    return (string) $value;
}

$cd = App\Models\CourseDate::find($courseDateId);
echo "courseDateExists=" . ($cd ? '1' : '0') . PHP_EOL;
if ($cd) {
    $instUnit = $cd->InstUnit;
    echo "courseDateId={$cd->id}" . PHP_EOL;
    echo "instUnitId=" . ($instUnit ? $instUnit->id : 'null') . PHP_EOL;
    echo "instUnitCompletedAt=" . ($instUnit?->completed_at ? (string) $instUnit->completed_at : 'null') . PHP_EOL;
}

$rows = App\Models\StudentUnit::where('course_date_id', $courseDateId)
    ->orderByDesc('id')
    ->take(50)
    ->get();

echo "studentUnitCount=" . $rows->count() . PHP_EOL;
foreach ($rows as $r) {
    $attrs = $r->getAttributes();

    echo implode(',', [
        'id=' . ($attrs['id'] ?? 'null'),
        'course_auth_id=' . ($attrs['course_auth_id'] ?? 'null'),
        'user_id=' . ($attrs['user_id'] ?? 'n/a'),
        'course_date_id=' . ($attrs['course_date_id'] ?? 'null'),
        'inst_unit_id=' . ($attrs['inst_unit_id'] ?? 'null'),
        'created_at=' . fmtTs($attrs['created_at'] ?? null),
        'completed_at=' . fmtTs($attrs['completed_at'] ?? null),
        'ejected_at=' . fmtTs($attrs['ejected_at'] ?? null),
        'last_heartbeat_at=' . fmtTs($attrs['last_heartbeat_at'] ?? null),
        'left_at=' . fmtTs($attrs['left_at'] ?? null),
    ]) . PHP_EOL;
}

if ($rows->count() > 0) {
    echo "---- join_check ----" . PHP_EOL;
    try {
        $su = $rows->first();
        $ca = App\Models\CourseAuth::find($su->course_auth_id);
        echo "courseAuthExists=" . ($ca ? '1' : '0') . PHP_EOL;
        if ($ca) {
            echo "courseAuthId={$ca->id},courseAuthUserId={$ca->user_id},courseAuthCourseId={$ca->course_id}" . PHP_EOL;
            $user = App\Models\User::find($ca->user_id);
            echo "studentUserExists=" . ($user ? '1' : '0') . PHP_EOL;
            if ($user) {
                $name = trim((string)($user->fname ?? '') . ' ' . (string)($user->lname ?? ''));
                echo "studentUserId={$user->id},email={$user->email},name={$name}" . PHP_EOL;
            }
        }
    } catch (Throwable $e) {
        echo "joinCheckError=" . $e->getMessage() . PHP_EOL;
    }
}

if ($adminId > 0) {
    echo "---- service_check ----" . PHP_EOL;
    try {
        Auth::guard('admin')->loginUsingId($adminId);
        echo "adminAuthed=" . (Auth::guard('admin')->check() ? '1' : '0') . PHP_EOL;

        $svcInst = DB::table('inst_unit as iu')
            ->whereNull('iu.completed_at')
            ->where(function ($q) use ($adminId) {
                $q->where('iu.created_by', $adminId)
                    ->orWhere('iu.assistant_id', $adminId);
            })
            ->where('iu.course_date_id', $courseDateId)
            ->orderByDesc('iu.created_at')
            ->first();

        echo "servicePickedInstUnitId=" . ($svcInst?->id ?? 'null') . PHP_EOL;
        echo "servicePickedInstUnitCreatedAt=" . ($svcInst?->created_at ?? 'null') . PHP_EOL;

        echo "---- db_builder_check ----" . PHP_EOL;
        $baseCount = DB::table('student_unit')->where('course_date_id', $courseDateId)->count();
        echo "dbBaseCount={$baseCount}" . PHP_EOL;

        $joinCaCount = DB::table('student_unit as su')
            ->join('course_auths as ca', 'su.course_auth_id', '=', 'ca.id')
            ->where('su.course_date_id', $courseDateId)
            ->count();
        echo "dbJoinCourseAuthsCount={$joinCaCount}" . PHP_EOL;

        $joinUsersCount = DB::table('student_unit as su')
            ->join('course_auths as ca', 'su.course_auth_id', '=', 'ca.id')
            ->join('users as u', 'ca.user_id', '=', 'u.id')
            ->where('su.course_date_id', $courseDateId)
            ->count();
        echo "dbJoinUsersCount={$joinUsersCount}" . PHP_EOL;

        $instUnitId = $cd && $cd->InstUnit ? (int) $cd->InstUnit->id : 0;
        $instFilterCount = DB::table('student_unit as su')
            ->join('course_auths as ca', 'su.course_auth_id', '=', 'ca.id')
            ->join('users as u', 'ca.user_id', '=', 'u.id')
            ->where('su.course_date_id', $courseDateId)
            ->where(function ($q) use ($instUnitId) {
                $q->where('su.inst_unit_id', $instUnitId)
                    ->orWhere('su.inst_unit_id', 0)
                    ->orWhereNull('su.inst_unit_id');
            })
            ->count();
        echo "dbInstUnitFilterCount={$instFilterCount}" . PHP_EOL;

        $svc = app(App\Services\Frost\Students\BackendStudentService::class);
        $result = $svc->getOnlineStudentsForInstructor($courseDateId);

        if (array_key_exists('error', $result)) {
            echo "serviceErrorKey=" . (string)($result['error'] ?? '') . PHP_EOL;
        }

        if (array_key_exists('summary', $result)) {
            echo "serviceSummary=" . json_encode($result['summary']) . PHP_EOL;
        }

        if (array_key_exists('metadata', $result)) {
            echo "serviceMetadata=" . json_encode($result['metadata']) . PHP_EOL;
        }

        $count = is_array($result['students'] ?? null) ? count($result['students']) : -1;
        echo "serviceStudentsCount={$count}" . PHP_EOL;
        if ($count > 0) {
            echo "firstStudent=" . json_encode($result['students'][0]) . PHP_EOL;
        } else {
            echo "serviceRaw=" . json_encode($result) . PHP_EOL;
        }
    } catch (Throwable $e) {
        echo "serviceError=" . $e->getMessage() . PHP_EOL;
    }
}
