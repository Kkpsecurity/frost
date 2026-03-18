<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$deleteIds = [24364, 24365, 24366];

echo "=== Checking for child records linked to course_auth IDs: " . implode(', ', $deleteIds) . " ===\n\n";

$tables = [
    'student_unit'     => 'course_auth_id',
    'student_activity' => 'course_auth_id',
    'exam_auths'       => 'course_auth_id',
    'self_study_lessons' => 'course_auth_id',
    'orders'           => 'course_auth_id',
];

$hasChildren = false;
foreach ($tables as $table => $col) {
    $count = DB::table($table)->whereIn($col, $deleteIds)->count();
    echo "{$table}.{$col}: {$count} row(s)\n";
    if ($count > 0) $hasChildren = true;
}

if ($hasChildren) {
    echo "\n⚠️  Child records exist — review before deleting!\n";
    exit(1);
}

echo "\n✅ No child records. Safe to delete.\n\n";

// DELETE
foreach ($deleteIds as $id) {
    $deleted = DB::table('course_auths')->where('id', $id)->delete();
    echo "Deleted course_auth ID {$id}: " . ($deleted ? "✅ OK" : "⚠️  not found") . "\n";
}

echo "\n=== Done. Remaining course_auths for user 2 ===\n\n";
$remaining = DB::table('course_auths')->where('user_id', 2)->orderBy('created_at', 'desc')->get(['id', 'course_id', 'start_date', 'completed_at', 'created_at']);
foreach ($remaining as $r) {
    echo "ID={$r->id} | course={$r->course_id} | start={$r->start_date} | completed={$r->completed_at} | created={$r->created_at}\n";
}
