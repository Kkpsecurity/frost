<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING CALENDAR DATA ===\n\n";

// Check courses
echo "COURSES:\n";
$courses = App\Models\Course::where('is_active', true)->get();
foreach ($courses as $course) {
    echo "- ID: {$course->id}, Title: {$course->title}\n";
}

echo "\nCOURSE UNITS:\n";
$courseUnits = App\Models\CourseUnit::all();
foreach ($courseUnits as $unit) {
    echo "- ID: {$unit->id}, Course ID: {$unit->course_id}, Title: {$unit->title}\n";
}

echo "\nCOURSE DATES:\n";
$courseDates = App\Models\CourseDate::where('is_active', true)
    ->where('starts_at', '>=', '2025-09-01')
    ->orderBy('starts_at')
    ->get();

echo "Found " . $courseDates->count() . " course dates for September 2025\n";
foreach ($courseDates as $date) {
    echo "- ID: {$date->id}, Unit ID: {$date->course_unit_id}, Starts: {$date->starts_at}, Ends: {$date->ends_at}\n";
}

echo "\n=== END CHECK ===\n";
