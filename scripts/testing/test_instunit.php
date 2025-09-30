<?php

// Check InstUnit details for our course date
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Checking InstUnit for CourseDate 10552\n";
echo "==========================================\n\n";

// Check all InstUnits for this course date
$instUnits = DB::table('inst_unit')
    ->where('course_date_id', 10552)
    ->get();

echo "Found " . $instUnits->count() . " InstUnit records for CourseDate 10552:\n\n";

foreach ($instUnits as $instUnit) {
    $instructor = DB::table('users')->find($instUnit->created_by);
    $assistant = $instUnit->assistant_id ? DB::table('users')->find($instUnit->assistant_id) : null;

    echo "InstUnit ID: {$instUnit->id}\n";
    echo "  Created by: " . ($instructor ? $instructor->fname . ' ' . $instructor->lname : 'Unknown') . " (ID: {$instUnit->created_by})\n";
    echo "  Assistant: " . ($assistant ? $assistant->fname . ' ' . $assistant->lname : 'None') . "\n";
    echo "  Created at: {$instUnit->created_at}\n";
    echo "  Completed at: " . ($instUnit->completed_at ?: 'Not completed') . "\n";
    echo "  Status: " . ($instUnit->completed_at ? 'Completed' : 'Active') . "\n";
    echo "\n";
}

// For today's live class, the status should be:
$now = now();
$startTime = \Carbon\Carbon::parse('2025-09-19 08:00:00-04');
$endTime = \Carbon\Carbon::parse('2025-09-19 17:00:00-04');

echo "=== Today's Class Analysis ===\n";
echo "Current time: " . $now->format('Y-m-d H:i:s T') . "\n";
echo "Class time: " . $startTime->format('Y-m-d H:i:s T') . " - " . $endTime->format('Y-m-d H:i:s T') . "\n";
echo "Time status: ";

if ($now->lt($startTime)) {
    echo "Before class time\n";
    echo "Expected buttons: Info about start time\n";
} elseif ($now->between($startTime, $endTime)) {
    echo "During class time\n";
    if ($instUnits->isEmpty()) {
        echo "Expected buttons: 'Start Class'\n";
    } else {
        $activeInstUnit = $instUnits->whereNull('completed_at')->first();
        if ($activeInstUnit) {
            echo "Expected buttons: 'Take Control', 'Assist'\n";
        } else {
            echo "All InstUnits completed - Expected buttons: 'Start Class' (for new session)\n";
        }
    }
} else {
    echo "After class time\n";
    echo "Expected buttons: Info about class ended\n";
}

echo "\n🎯 For live teaching, you may need to start a NEW class session!\n";
