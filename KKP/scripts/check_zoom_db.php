<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check all Zoom credentials
$zoomCreds = DB::table('zoom_creds')->select('zoom_email', 'zoom_status')->get();

echo "=== Current Zoom Credentials Status ===\n\n";

foreach ($zoomCreds as $cred) {
    $status = $cred->zoom_status === 'enabled' ? '🟢 ENABLED' : '🔴 DISABLED';
    echo "{$cred->zoom_email}: {$status}\n";
}

echo "\n";
