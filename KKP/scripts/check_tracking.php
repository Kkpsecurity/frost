<?php
$root = dirname(__DIR__, 2);
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// ─── CONFIG ───────────────────────────────────────────────────────────────────
$courseDateId = 10797;
$instUnitId   = 10715;
$suIds        = DB::table('student_unit')
    ->where('course_date_id', $courseDateId)
    ->pluck('id')->toArray();
// ──────────────────────────────────────────────────────────────────────────────

echo "=== LESSON TRACKING CHECK (" . date('Y-m-d H:i:s') . ") ===" . PHP_EOL . PHP_EOL;

// 1. Student units — show online status
echo "=== 1. STUDENT UNITS (course_date #{$courseDateId}) ===" . PHP_EOL;
$now = new \DateTime();
foreach (DB::table('student_unit')->whereIn('id', $suIds)->get(['id', 'course_auth_id', 'updated_at']) as $r) {
    $updated = $r->updated_at ? new \DateTime($r->updated_at) : null;
    $mins    = $updated ? round(($now->getTimestamp() - $updated->getTimestamp()) / 60, 1) : '???';
    $online  = is_numeric($mins) && $mins <= 5 ? '🟢 ONLINE' : '🔴 OFFLINE';
    echo "  su#{$r->id} ca#{$r->course_auth_id} | last_activity: {$r->updated_at} ({$mins} min ago) | {$online}" . PHP_EOL;
}

// 2. Active inst_lesson
echo PHP_EOL . "=== 2. INST_LESSON (inst_unit #{$instUnitId}) ===" . PHP_EOL;
$instLessons = DB::table('inst_lesson')->where('inst_unit_id', $instUnitId)->orderBy('id')->get();
if ($instLessons->isEmpty()) {
    echo "  (none — instructor has not started a lesson yet)" . PHP_EOL;
} else {
    foreach ($instLessons as $il) {
        $state = $il->completed_at ? 'completed' : ($il->is_paused ? 'PAUSED' : 'ACTIVE');
        echo "  il#{$il->id} lesson_id:{$il->lesson_id} | {$state} | started:{$il->created_at} | completed:{$il->completed_at}" . PHP_EOL;
    }
}

// 3. Student lessons
echo PHP_EOL . "=== 3. STUDENT_LESSON ===" . PHP_EOL;
$studentLessons = DB::table('student_lesson')->whereIn('student_unit_id', $suIds)->orderBy('id')->get();
if ($studentLessons->isEmpty()) {
    echo "  (none — no StudentLesson rows created yet)" . PHP_EOL;
} else {
    foreach ($studentLessons as $sl) {
        $state = $sl->completed_at ? 'completed' : ($sl->dnc_at ? 'DNC' : 'open');
        echo "  sl#{$sl->id} | su:{$sl->student_unit_id} | lesson:{$sl->lesson_id} | il:{$sl->inst_lesson_id} | {$state}" . PHP_EOL;
    }
}

// 4. Lesson presence tracking
echo PHP_EOL . "=== 4. LESSON PRESENCE TRACKING ===" . PHP_EOL;
$rows = DB::table('student_activity')
    ->whereIn('student_unit_id', $suIds)
    ->whereIn('activity_type', ['lesson_assigned', 'lesson_absent'])
    ->orderBy('id')
    ->get(['id', 'user_id', 'student_unit_id', 'course_auth_id', 'activity_type', 'data', 'created_at']);
if ($rows->isEmpty()) {
    echo "  (none yet — start a lesson from the instructor dashboard)" . PHP_EOL;
} else {
    foreach ($rows as $r) {
        $data = json_decode($r->data ?? '{}', true);
        $presence = $data['presence'] ?? '?';
        $lessonId = $data['lesson_id'] ?? '?';
        echo "  SA#{$r->id} | {$r->activity_type} | presence:{$presence} | lesson:{$lessonId} | user:{$r->user_id} su:{$r->student_unit_id}" . PHP_EOL;
    }
}

// 5. Tab visibility tracking (attention tracking)
echo PHP_EOL . "=== 5. TAB VISIBILITY (ATTENTION TRACKING — last 20) ===" . PHP_EOL;
$tabRows = DB::table('student_activity')
    ->whereIn('student_unit_id', $suIds)
    ->whereIn('activity_type', ['tab_hidden', 'tab_visible'])
    ->orderByDesc('id')
    ->limit(20)
    ->get(['id', 'user_id', 'student_unit_id', 'activity_type', 'duration_seconds', 'data', 'created_at']);
if ($tabRows->isEmpty()) {
    echo "  (none yet — student must leave/return to the browser tab during class)" . PHP_EOL;
} else {
    foreach ($tabRows->reverse() as $r) {
        $data      = json_decode($r->data ?? '{}', true);
        $lessonId  = $data['lesson_id']      ?? '-';
        $ilId      = $data['inst_lesson_id'] ?? '-';
        $away      = $data['away_formatted'] ?? ($r->duration_seconds ? gmdate('H:i:s', $r->duration_seconds) : '-');
        $icon      = $r->activity_type === 'tab_hidden' ? '👁️‍🗨️ LEFT' : '👁️  BACK';
        $suffix    = $r->activity_type === 'tab_visible' ? " | away:{$away}" : '';
        echo "  SA#{$r->id} | {$icon} | lesson:{$lessonId} il:{$ilId} | user:{$r->user_id} su:{$r->student_unit_id}{$suffix} | {$r->created_at}" . PHP_EOL;
    }
}

echo PHP_EOL . "=== DONE ===" . PHP_EOL;
