<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧹 Cleaning Up Duplicate CourseDate Records\n";
echo "==========================================\n\n";

use App\Models\CourseDate;
use Carbon\Carbon;

try {
    // Get all CourseDate records for the current week and next week
    $startDate = Carbon::parse('2025-09-22'); // This week start
    $endDate = Carbon::parse('2025-10-05');   // Next week end

    echo "📅 Checking CourseDate records from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}\n\n";

    $courseDates = CourseDate::whereBetween('starts_at', [$startDate, $endDate])
        ->orderBy('starts_at')
        ->orderBy('course_unit_id')
        ->get();

    echo "📊 Found " . $courseDates->count() . " CourseDate records\n\n";

    // Group by date to find duplicates
    $duplicates = [];
    $dateGroups = $courseDates->groupBy(function($cd) {
        return Carbon::parse($cd->starts_at)->format('Y-m-d');
    });

    foreach ($dateGroups as $date => $records) {
        if ($records->count() > 2) { // More than D and G class on same day
            echo "⚠️  Date {$date}: " . $records->count() . " records (potential duplicates)\n";
            foreach ($records as $record) {
                $courseName = $record->courseUnit->course->title ?? 'Unknown';
                $unitTitle = $record->courseUnit->admin_title ?? 'Unknown';
                echo "   - ID: {$record->id}, Course: {$courseName}, Unit: {$unitTitle}\n";
            }
            $duplicates[$date] = $records;
        } else {
            echo "✅ Date {$date}: " . $records->count() . " records (normal)\n";
        }
    }

    if (empty($duplicates)) {
        echo "\n✅ No duplicates found!\n";
        exit(0);
    }

    echo "\n🗑️  Found duplicates on " . count($duplicates) . " dates\n";
    echo "Do you want to clean up duplicates? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $choice = trim(fgets($handle));
    fclose($handle);

    if (strtolower($choice) !== 'y') {
        echo "❌ Cleanup cancelled\n";
        exit(0);
    }

    $deletedCount = 0;

    foreach ($duplicates as $date => $records) {
        echo "\n🧹 Cleaning up date {$date}...\n";

        // Keep only the first record for each course type (D and G)
        $keepRecords = [];
        $courseTypes = [];

        foreach ($records as $record) {
            $courseName = $record->courseUnit->course->title ?? '';
            $courseType = 'Unknown';

            if (strpos($courseName, 'D40') !== false || strpos($courseName, 'D ') !== false) {
                $courseType = 'D';
            } elseif (strpos($courseName, 'G28') !== false || strpos($courseName, 'G ') !== false) {
                $courseType = 'G';
            }

            if (!isset($courseTypes[$courseType])) {
                $courseTypes[$courseType] = $record;
                $keepRecords[] = $record->id;
                echo "   ✅ Keeping: ID {$record->id} ({$courseType}-type)\n";
            } else {
                // Delete duplicate
                $record->delete();
                $deletedCount++;
                echo "   🗑️  Deleted: ID {$record->id} ({$courseType}-type duplicate)\n";
            }
        }
    }

    echo "\n✅ Cleanup complete! Deleted {$deletedCount} duplicate records\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
