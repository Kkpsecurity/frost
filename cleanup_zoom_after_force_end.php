<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking and Cleaning Up Zoom Credentials ===" . PHP_EOL;

// Get recently completed InstUnits for instructor ID 2 (that we just force-ended)
$recentlyCompleted = App\Models\InstUnit::where('created_by', 2)
    ->whereNotNull('completed_at')
    ->where('completed_at', '>=', now()->subHour())
    ->with('CourseDate.CourseUnit.Course')
    ->get();

echo "Found {$recentlyCompleted->count()} recently completed InstUnits to check for Zoom cleanup:" . PHP_EOL;

foreach($recentlyCompleted as $instUnit) {
    $course = $instUnit->CourseDate?->CourseUnit?->Course;
    $courseTitle = strtoupper($course->title ?? '');

    echo "- InstUnit {$instUnit->id}: Course '{$courseTitle}'" . PHP_EOL;

    // Determine which Zoom email was used based on course type (same logic as controller)
    $zoomEmail = null;
    if (strpos($courseTitle, ' D') !== false || strpos($courseTitle, 'D40') !== false || strpos($courseTitle, 'D20') !== false) {
        $zoomEmail = 'instructor_d@stgroupusa.com';
    } elseif (strpos($courseTitle, ' G') !== false || strpos($courseTitle, 'G40') !== false || strpos($courseTitle, 'G20') !== false) {
        $zoomEmail = 'instructor_g@stgroupusa.com';
    } else {
        $zoomEmail = 'instructor_admin@stgroupusa.com';  // Default
    }

    echo "  Zoom email to check: {$zoomEmail}" . PHP_EOL;

    $zoomCreds = App\Models\ZoomCreds::where('zoom_email', $zoomEmail)->first();
    if ($zoomCreds) {
        echo "  Current Zoom status: {$zoomCreds->zoom_status}" . PHP_EOL;

        if ($zoomCreds->zoom_status === 'enabled') {
            echo "  🔄 Disabling Zoom credentials..." . PHP_EOL;
            $zoomCreds->zoom_status = 'disabled';
            $zoomCreds->save();
            echo "  ✅ Zoom credentials disabled!" . PHP_EOL;
        } else {
            echo "  ✅ Zoom already disabled" . PHP_EOL;
        }
    } else {
        echo "  ❌ Zoom credentials not found for email {$zoomEmail}" . PHP_EOL;
    }
    echo PHP_EOL;
}

echo "=== Final Zoom Status Check ===" . PHP_EOL;
$allZoomCreds = App\Models\ZoomCreds::all();
foreach($allZoomCreds as $zoomCred) {
    echo "- {$zoomCred->zoom_email}: {$zoomCred->zoom_status}" . PHP_EOL;
}
