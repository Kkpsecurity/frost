<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ACTIVE INST_UNITS CHECK ===\n\n";

$activeInstUnits = DB::table('inst_unit')
    ->whereNull('completed_at')
    ->get();

if ($activeInstUnits->isEmpty()) {
    echo "✅ No active InstUnits found - All sessions closed\n";
} else {
    echo "⚠️ Found " . $activeInstUnits->count() . " active InstUnit(s):\n\n";
    foreach ($activeInstUnits as $unit) {
        echo "InstUnit #{$unit->id}\n";
        echo "  - Course Date ID: {$unit->course_date_id}\n";
        echo "  - Created By: {$unit->created_by}\n";
        echo "  - Created At: {$unit->created_at}\n";
        echo "\n";
    }
}

echo "\n=== ZOOM CREDENTIALS STATUS ===\n\n";

$zoomCreds = DB::table('zoom_creds')
    ->select('zoom_email', 'zoom_status')
    ->get();

foreach ($zoomCreds as $cred) {
    $status = $cred->zoom_status === 'enabled' ? '🟢 ENABLED' : '🔴 DISABLED';
    echo "{$status} - {$cred->zoom_email}\n";
}

echo "\n";
