<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDate;
use App\Models\InstUnit;
use Carbon\Carbon;

echo "=== Checking InstUnit/CourseDate Synchronization Issue ===\n\n";

// Get today's date
$today = Carbon::today()->format('Y-m-d');
echo "Today's date: $today\n\n";

// Get today's CourseDates
echo "=== Today's CourseDates ===\n";
$courseDates = CourseDate::whereDate('starts_at', $today)->with('InstUnit')->get();

if ($courseDates->isEmpty()) {
    echo "No CourseDates found for today.\n\n";
} else {
    foreach ($courseDates as $cd) {
        echo "CourseDate ID: {$cd->id}\n";
        echo "  Starts at: {$cd->starts_at}\n";
        echo "  Course Unit ID: {$cd->course_unit_id}\n";

        // Check for InstUnit
        $instUnit = $cd->InstUnit;
        if ($instUnit) {
            echo "  ✓ InstUnit EXISTS (ID: {$instUnit->id})\n";
            echo "    - Created by: {$instUnit->created_by}\n";
            echo "    - Created at: {$instUnit->created_at}\n";
            echo "    - Completed at: " . ($instUnit->completed_at ?? 'NULL') . "\n";
        } else {
            echo "  ✗ NO InstUnit (Instructor has NOT started class)\n";
        }
        echo "\n";
    }
}

// Get all recent InstUnits
echo "\n=== Recent InstUnits (last 10) ===\n";
$instUnits = InstUnit::with('CourseDate')->latest()->take(10)->get();

if ($instUnits->isEmpty()) {
    echo "No InstUnits found.\n";
} else {
    foreach ($instUnits as $iu) {
        echo "InstUnit ID: {$iu->id}\n";
        echo "  Course Date ID: {$iu->course_date_id}\n";
        echo "  Created by: {$iu->created_by}\n";
        echo "  Created at: {$iu->created_at}\n";
        echo "  Completed at: " . ($iu->completed_at ?? 'NULL') . "\n";

        if ($iu->CourseDate) {
            echo "  CourseDate starts at: {$iu->CourseDate->starts_at}\n";
        }
        echo "\n";
    }
}

// Check if there are any active (not completed) InstUnits today
echo "\n=== Active InstUnits Today ===\n";
$activeInstUnits = InstUnit::whereNull('completed_at')
    ->whereHas('CourseDate', function($q) use ($today) {
        $q->whereDate('starts_at', $today);
    })
    ->with('CourseDate')
    ->get();

if ($activeInstUnits->isEmpty()) {
    echo "No active InstUnits today.\n";
} else {
    echo "Found {$activeInstUnits->count()} active InstUnit(s):\n";
    foreach ($activeInstUnits as $aiu) {
        echo "  InstUnit ID: {$aiu->id}, CourseDate: {$aiu->course_date_id}, Instructor: {$aiu->created_by}\n";
    }
}

echo "\n=== Summary ===\n";
echo "Total CourseDates today: {$courseDates->count()}\n";
echo "CourseDates with InstUnit: " . $courseDates->filter(fn($cd) => $cd->InstUnit !== null)->count() . "\n";
echo "CourseDates WITHOUT InstUnit: " . $courseDates->filter(fn($cd) => $cd->InstUnit === null)->count() . "\n";
echo "\nIf instructor started class but student sees waiting room, this indicates:\n";
echo "- CourseDate exists (student sees bulletin/waiting)\n";
echo "- InstUnit exists (instructor started class)\n";
echo "- BUT student's poll is NOT loading the InstUnit properly\n";
