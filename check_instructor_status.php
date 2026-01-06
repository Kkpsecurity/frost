<?php
require_once 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get first admin user (instructor)
$admin = App\Models\Admin::first();
echo "Admin found: " . $admin->name . " (ID: " . $admin->id . ")\n";

// Check for active InstUnits
$activeInstUnits = App\Models\InstUnit::where('created_by', $admin->id)
    ->whereNull('completed_at')
    ->with('courseDate')
    ->get();

echo "Active InstUnits: " . $activeInstUnits->count() . "\n";
foreach($activeInstUnits as $unit) {
    $courseDate = $unit->courseDate;
    echo "- InstUnit ID: {$unit->id}\n";
    echo "  CourseDate ID: {$unit->course_date_id}\n";
    echo "  CourseDate starts_at: {$courseDate->starts_at}\n";
    echo "  InstUnit created: {$unit->created_at}\n";
    echo "  InstUnit updated: {$unit->updated_at}\n\n";
}

// Check today's course dates available
$today = now()->format('Y-m-d');
$todaysCourseDates = App\Models\CourseDate::whereDate('starts_at', $today)
    ->get();

echo "Today's CourseDate records: " . $todaysCourseDates->count() . "\n";
foreach($todaysCourseDates as $courseDate) {
    echo "- CourseDate ID: {$courseDate->id}\n";
    echo "  starts_at: {$courseDate->starts_at}\n";
    echo "  ends_at: {$courseDate->ends_at}\n\n";
}
