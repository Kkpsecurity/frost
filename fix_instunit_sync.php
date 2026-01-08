<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDate;
use App\Models\InstUnit;
use Carbon\Carbon;

echo "=== InstUnit Cleanup and Synchronization Fix ===\n\n";

// Get all InstUnits that are still active but their CourseDate has passed
$now = Carbon::now();
$yesterday = $now->copy()->subDay()->startOfDay();

echo "Current time: {$now}\n";
echo "Looking for active InstUnits with CourseDates before today...\n\n";

$staleInstUnits = InstUnit::whereNull('completed_at')
    ->whereHas('CourseDate', function($q) use ($yesterday) {
        $q->where('ends_at', '<', $yesterday);
    })
    ->with('CourseDate')
    ->get();

if ($staleInstUnits->isEmpty()) {
    echo "✓ No stale InstUnits found.\n";
} else {
    echo "Found {$staleInstUnits->count()} stale InstUnit(s) that need to be completed:\n\n";

    foreach ($staleInstUnits as $iu) {
        $cd = $iu->CourseDate;
        echo "InstUnit ID: {$iu->id}\n";
        echo "  CourseDate: {$cd->id} (ended at: {$cd->ends_at})\n";
        echo "  Created: " . Carbon::createFromTimestamp($iu->created_at)->format('Y-m-d H:i:s') . "\n";
        echo "  Instructor: {$iu->created_by}\n";
        echo "  Should be marked as completed.\n\n";
    }

    echo "Would you like to auto-complete these stale InstUnits? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    if (trim($line) === 'y' || trim($line) === 'Y') {
        foreach ($staleInstUnits as $iu) {
            $cd = $iu->CourseDate;
            // Use DB::update to directly set the timestamp in the database
            DB::table('inst_unit')
                ->where('id', $iu->id)
                ->update([
                    'completed_at' => $cd->ends_at
                ]);
            echo "✓ Marked InstUnit #{$iu->id} as completed at {$cd->ends_at}\n";
        }
        echo "\n✓ All stale InstUnits have been completed.\n";
    } else {
        echo "\nSkipped auto-completion.\n";
    }
}

echo "\n=== Current Status ===\n";
$today = Carbon::today();
$todayCourseDates = CourseDate::whereDate('starts_at', $today)
    ->with('InstUnit')
    ->get();

echo "Today's CourseDates ({$today->format('Y-m-d')}):\n";
if ($todayCourseDates->isEmpty()) {
    echo "  No classes scheduled for today.\n";
} else {
    foreach ($todayCourseDates as $cd) {
        echo "  CourseDate #{$cd->id} at {$cd->starts_at->format('H:i')}\n";
        if ($cd->InstUnit) {
            echo "    ✓ InstUnit #{$cd->InstUnit->id} exists (class started)\n";
        } else {
            echo "    ✗ No InstUnit (instructor hasn't started class yet)\n";
        }
    }
}

echo "\n=== Recommendations ===\n";
echo "1. Always end classes properly by completing InstUnit\n";
echo "2. Check date synchronization - instructor and student should see same date\n";
echo "3. Instructor should start class for TODAY's CourseDate, not yesterday's\n";
echo "4. Add auto-completion logic for InstUnits past their CourseDate's ends_at time\n";
