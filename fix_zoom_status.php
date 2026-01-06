<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Current Zoom Status ===" . PHP_EOL;

// Check current zoom credentials
$zoomCreds = App\Models\ZoomCreds::all();
foreach($zoomCreds as $cred) {
    echo "- {$cred->zoom_email}: {$cred->zoom_status}" . PHP_EOL;
}

echo PHP_EOL . "=== Current Active InstUnit Details ===" . PHP_EOL;
$currentInstUnit = App\Models\InstUnit::with('CourseDate.CourseUnit.Course')->find(43);
if ($currentInstUnit) {
    $course = $currentInstUnit->CourseDate?->CourseUnit?->Course;
    $courseTitle = strtoupper($course->title ?? '');

    echo "InstUnit 43 Details:" . PHP_EOL;
    echo "- Course: {$courseTitle}" . PHP_EOL;
    echo "- CourseDate: {$currentInstUnit->course_date_id}" . PHP_EOL;
    echo "- Created: {$currentInstUnit->created_at}" . PHP_EOL;
    echo "- Created by: {$currentInstUnit->created_by}" . PHP_EOL;

    // Determine expected zoom email for this course
    $expectedZoomEmail = null;
    if (strpos($courseTitle, ' D') !== false || strpos($courseTitle, 'D40') !== false || strpos($courseTitle, 'D20') !== false) {
        $expectedZoomEmail = 'instructor_d@stgroupusa.com';
    } elseif (strpos($courseTitle, ' G') !== false || strpos($courseTitle, 'G40') !== false || strpos($courseTitle, 'G20') !== false) {
        $expectedZoomEmail = 'instructor_g@stgroupusa.com';
    } else {
        $expectedZoomEmail = 'instructor_admin@stgroupusa.com';
    }

    echo "- Expected Zoom email: {$expectedZoomEmail}" . PHP_EOL;

    $zoomCred = App\Models\ZoomCreds::where('zoom_email', $expectedZoomEmail)->first();
    echo "- Current Zoom status: " . ($zoomCred ? $zoomCred->zoom_status : 'NOT FOUND') . PHP_EOL;

    // Check if this is the problem - Zoom was re-enabled when class started
    if ($zoomCred && $zoomCred->zoom_status === 'enabled') {
        echo PHP_EOL . "🚨 PROBLEM FOUND: Zoom was re-enabled for this class session!" . PHP_EOL;
        echo "Disabling Zoom now..." . PHP_EOL;

        $zoomCred->zoom_status = 'disabled';
        $zoomCred->save();

        echo "✅ Zoom disabled successfully!" . PHP_EOL;
    }
}

echo PHP_EOL . "=== Final Zoom Status ===" . PHP_EOL;
$finalZoomCreds = App\Models\ZoomCreds::all();
foreach($finalZoomCreds as $cred) {
    echo "- {$cred->zoom_email}: {$cred->zoom_status}" . PHP_EOL;
}
