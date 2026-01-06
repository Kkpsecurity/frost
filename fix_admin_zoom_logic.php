<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Admin Role and Fixing Zoom Logic ===" . PHP_EOL;

// Get the admin user (instructor ID 2)
$admin = App\Models\Admin::find(2);
if ($admin) {
    echo "Admin found: {$admin->name} (ID: {$admin->id})" . PHP_EOL;
    echo "Admin role_id: {$admin->role_id}" . PHP_EOL;

    // Check if this is an admin role (1 or 2)
    if (in_array($admin->role_id, [1, 2])) {
        echo "✅ Admin has admin/sys-admin role - should use instructor_admin@stgroupusa.com" . PHP_EOL;
        $correctZoomEmail = 'instructor_admin@stgroupusa.com';
    } else {
        echo "⚠️ Admin has regular instructor role - should use course-specific Zoom" . PHP_EOL;
        $correctZoomEmail = 'course-specific';
    }

    echo PHP_EOL . "=== Current Zoom Status ===" . PHP_EOL;
    $allZoomCreds = App\Models\ZoomCreds::all();
    foreach($allZoomCreds as $zoomCred) {
        echo "- {$zoomCred->zoom_email}: {$zoomCred->zoom_status}" . PHP_EOL;
    }

    if ($correctZoomEmail !== 'course-specific') {
        echo PHP_EOL . "=== Fixing Zoom Credentials ===" . PHP_EOL;

        // The admin should use instructor_admin@stgroupusa.com
        // Since all classes ended, ALL Zoom credentials should be disabled
        foreach($allZoomCreds as $zoomCred) {
            if ($zoomCred->zoom_status === 'enabled') {
                echo "🔄 Disabling {$zoomCred->zoom_email}..." . PHP_EOL;
                $zoomCred->zoom_status = 'disabled';
                $zoomCred->save();
                echo "✅ {$zoomCred->zoom_email} disabled!" . PHP_EOL;
            } else {
                echo "✅ {$zoomCred->zoom_email} already disabled" . PHP_EOL;
            }
        }
    }

    echo PHP_EOL . "=== Final Zoom Status ===" . PHP_EOL;
    $allZoomCreds = App\Models\ZoomCreds::all();
    foreach($allZoomCreds as $zoomCred) {
        echo "- {$zoomCred->zoom_email}: {$zoomCred->zoom_status}" . PHP_EOL;
    }

} else {
    echo "❌ Admin not found!" . PHP_EOL;
}
