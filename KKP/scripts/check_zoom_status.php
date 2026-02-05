<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ZOOM CREDENTIALS STATUS ===\n\n";

$zoomCreds = DB::table('zoom_creds')
    ->select('zoom_email', 'zoom_status')
    ->get();

foreach ($zoomCreds as $cred) {
    $status = $cred->zoom_status === 'enabled' ? '🟢 ENABLED' : '🔴 DISABLED';
    echo "{$status} - {$cred->zoom_email}\n";
}

echo "\n";
