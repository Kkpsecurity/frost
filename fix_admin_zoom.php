<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Admin Zoom Credentials ===" . PHP_EOL;

$adminZoom = App\Models\ZoomCreds::where('zoom_email', 'instructor_admin@stgroupusa.com')->first();

if ($adminZoom) {
    echo "Current admin Zoom status: {$adminZoom->zoom_status}" . PHP_EOL;

    if ($adminZoom->zoom_status === 'enabled') {
        echo "🔄 Disabling admin Zoom credentials..." . PHP_EOL;
        $adminZoom->zoom_status = 'disabled';
        $adminZoom->save();
        echo "✅ Admin Zoom credentials disabled!" . PHP_EOL;
    } else {
        echo "✅ Admin Zoom already disabled" . PHP_EOL;
    }
} else {
    echo "❌ Admin Zoom credentials not found!" . PHP_EOL;
}

echo PHP_EOL . "=== Final Zoom Status ===" . PHP_EOL;
$allZoomCreds = App\Models\ZoomCreds::all();
foreach($allZoomCreds as $zoomCred) {
    echo "- {$zoomCred->zoom_email}: {$zoomCred->zoom_status}" . PHP_EOL;
}
