<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking StudentUnits for user_id=2, course_auth_id=2:\n\n";

$units = DB::table('student_unit')
    ->where('course_auth_id', 2)
    ->orderBy('created_at', 'desc')
    ->limit(20)
    ->get(['id', 'course_date_id', 'created_at']);

echo "Total StudentUnits found: " . $units->count() . "\n\n";

foreach($units as $u) {
    echo "StudentUnit ID: {$u->id} | CourseDate ID: {$u->course_date_id} | Created: {$u->created_at}\n";
}

echo "\n\nNow checking CourseDate dates:\n\n";

$courseDateIds = $units->pluck('course_date_id')->unique();

$dates = DB::table('course_dates')
    ->whereIn('id', $courseDateIds)
    ->get(['id', 'starts_at']);

foreach($dates as $d) {
    $dateStr = $d->starts_at ? date('Y-m-d', strtotime($d->starts_at)) : 'NULL';
    echo "CourseDate ID: {$d->id} | starts_at: {$d->starts_at} | Date: {$dateStr}\n";
}

echo "\n\nCurrent week (Mon-Fri):\n";
$monday = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
for($i = 0; $i < 5; $i++) {
    $date = $monday->copy()->addDays($i);
    echo "Day " . ($i+1) . ": " . $date->format('l, M d, Y') . " (" . $date->format('Y-m-d') . ")\n";
}
