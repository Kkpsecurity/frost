<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Current InstUnit Status ===" . PHP_EOL;
$activeInstUnits = App\Models\InstUnit::whereNull('completed_at')->get();
echo "All active InstUnits: " . $activeInstUnits->count() . PHP_EOL;
foreach($activeInstUnits as $unit) {
    echo "- InstUnit ID: {$unit->id}, CourseDate: {$unit->course_date_id}, Created by: {$unit->created_by}" . PHP_EOL;
}

echo PHP_EOL . "=== Today CourseDate Status ===" . PHP_EOL;
$today = now()->format('Y-m-d');
$todaysCourseDates = App\Models\CourseDate::whereDate('starts_at', $today)->get();
echo "Today CourseDate records: " . $todaysCourseDates->count() . PHP_EOL;
foreach($todaysCourseDates as $courseDate) {
    echo "- CourseDate ID: {$courseDate->id}, starts: {$courseDate->starts_at}" . PHP_EOL;
}
