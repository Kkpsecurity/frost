<?php

// Quick script to verify today's course dates
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDate;
use Carbon\Carbon;

$today = Carbon::today();
$todaysCourses = CourseDate::with(['courseUnit.course'])
    ->whereDate('starts_at', $today->toDateString())
    ->where('is_active', true)
    ->get();

echo "📅 Course Dates for Today ({$today->toDateString()}):\n";
echo "=" . str_repeat("=", 50) . "\n";

if ($todaysCourses->isEmpty()) {
    echo "❌ No course dates found for today.\n";
} else {
    foreach ($todaysCourses as $course) {
        $startTime = \Carbon\Carbon::parse($course->starts_at);
        $endTime = \Carbon\Carbon::parse($course->ends_at);

        echo "✅ ID: {$course->id}\n";
        echo "   Course: {$course->courseUnit->course->title}\n";
        echo "   Unit: {$course->courseUnit->admin_title} - {$course->courseUnit->title}\n";
        echo "   Time: {$startTime->format('g:i A')} - {$endTime->format('g:i A T')}\n";
        echo "   Status: " . ($course->is_active ? "Active" : "Inactive") . "\n";
        echo "\n";
    }

    echo "🎯 Live classes are ready! Students will see ONLINE status.\n";
}
