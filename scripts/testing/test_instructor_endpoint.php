<?php

// Test script to check instructor today lessons endpoint
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\Frost\Instructors\CourseDatesService;

echo "📊 Testing Instructor Today Lessons Endpoint\n";
echo "===========================================\n\n";

// Create service instance
$service = new CourseDatesService();

// Get today's lessons
$todaysLessons = $service->getTodaysLessons();

echo "Response from getTodaysLessons():\n";
echo json_encode($todaysLessons, JSON_PRETTY_PRINT);
echo "\n\n";

// Also check direct DB query for course dates
echo "Direct DB check for today's course dates:\n";
echo "=========================================\n";

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$today = Carbon::today();

$courseDates = DB::table('course_dates')
    ->whereDate('starts_at', $today->toDateString())
    ->where('is_active', true)
    ->get();

echo "Found " . $courseDates->count() . " course dates for today:\n";
foreach ($courseDates as $cd) {
    echo "- ID: {$cd->id}, Unit: {$cd->course_unit_id}, Start: {$cd->starts_at}\n";
}
