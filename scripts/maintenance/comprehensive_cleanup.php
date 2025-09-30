<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧹 Comprehensive CourseDate Cleanup - Remove All Invalid Records\n";
echo "================================================================\n\n";

use App\Models\CourseDate;
use Carbon\Carbon;

// Get all CourseDate records
$allCourseDates = CourseDate::with(['courseUnit.course'])->get();

echo "📊 Total CourseDate records: " . $allCourseDates->count() . "\n\n";

$invalidRecords = [];
$validRecords = [];

foreach ($allCourseDates as $courseDate) {
    $date = Carbon::parse($courseDate->starts_at);
    $course = $courseDate->courseUnit->course ?? null;

    if (!$course) {
        $invalidRecords[] = [
            'id' => $courseDate->id,
            'reason' => 'No associated course',
            'date' => $date->format('Y-m-d l'),
            'course' => 'Unknown'
        ];
        continue;
    }

    $courseTitle = $course->title;
    $isWeekend = $date->isWeekend();

    // Rule 1: No weekend classes (Saturday/Sunday)
    if ($isWeekend) {
        $invalidRecords[] = [
            'id' => $courseDate->id,
            'reason' => 'Weekend class (invalid)',
            'date' => $date->format('Y-m-d l'),
            'course' => $courseTitle
        ];
        continue;
    }

    // Rule 2: G classes only every other week (Mon-Wed)
    if (strpos($courseTitle, 'G28') !== false) {
        // Calculate weeks since epoch (Sept 22, 2025 = week 0)
        $epochDate = Carbon::create(2025, 9, 22); // Monday Sept 22, 2025
        $currentWeekStart = $date->copy()->startOfWeek();
        $weeksDiff = $epochDate->diffInWeeks($currentWeekStart);

        // G classes only on odd weeks (1, 3, 5...) - this makes next week valid
        if ($weeksDiff % 2 === 0) {
            $invalidRecords[] = [
                'id' => $courseDate->id,
                'reason' => "G class on even week (week $weeksDiff - off week)",
                'date' => $date->format('Y-m-d l'),
                'course' => $courseTitle
            ];
            continue;
        }

        // G classes only Mon-Wed
        if (!in_array($date->dayOfWeek, [1, 2, 3])) {
            $invalidRecords[] = [
                'id' => $courseDate->id,
                'reason' => 'G class not Mon-Wed',
                'date' => $date->format('Y-m-d l'),
                'course' => $courseTitle
            ];
            continue;
        }
    }

    // Rule 3: D classes only Mon-Fri (already covered by weekend check)
    if (strpos($courseTitle, 'D40') !== false) {
        // D classes are Mon-Fri, weekends already filtered out above
    }

    // If we get here, the record is valid
    $validRecords[] = [
        'id' => $courseDate->id,
        'date' => $date->format('Y-m-d l'),
        'course' => $courseTitle
    ];
}

echo "❌ Invalid Records Found: " . count($invalidRecords) . "\n";
echo "✅ Valid Records: " . count($validRecords) . "\n\n";

if (!empty($invalidRecords)) {
    echo "📋 Invalid Records to Delete:\n";
    echo "=============================\n";

    $groupedInvalid = [];
    foreach ($invalidRecords as $record) {
        $groupedInvalid[$record['reason']][] = $record;
    }

    foreach ($groupedInvalid as $reason => $records) {
        echo "\n🚫 {$reason}: " . count($records) . " records\n";
        foreach (array_slice($records, 0, 5) as $record) { // Show first 5
            echo "   • ID {$record['id']}: {$record['course']} on {$record['date']}\n";
        }
        if (count($records) > 5) {
            echo "   • ... and " . (count($records) - 5) . " more\n";
        }
    }

    echo "\n⚠️  Do you want to DELETE these " . count($invalidRecords) . " invalid records? (y/N): ";
    $handle = fopen("php://stdin", "r");
    $confirm = trim(fgets($handle));
    fclose($handle);

    if (strtolower($confirm) === 'y' || strtolower($confirm) === 'yes') {
        echo "\n🗑️  Deleting invalid records...\n";

        $invalidIds = array_column($invalidRecords, 'id');
        $deleted = CourseDate::whereIn('id', $invalidIds)->delete();

        echo "✅ Deleted {$deleted} invalid CourseDate records\n";

        // Show remaining valid records
        echo "\n📅 Remaining Valid Schedule:\n";
        echo "============================\n";

        $remainingValid = CourseDate::with(['courseUnit.course'])
            ->orderBy('starts_at')
            ->get();

        $currentWeek = null;
        foreach ($remainingValid as $courseDate) {
            $date = Carbon::parse($courseDate->starts_at);
            $week = $date->format('W-Y');

            if ($week !== $currentWeek) {
                $currentWeek = $week;
                echo "\n📅 Week of " . $date->startOfWeek()->format('M j') . ":\n";
            }

            $course = $courseDate->courseUnit->course;
            echo "   • {$date->format('l M j')}: {$course->title} (Day {$courseDate->courseUnit->ordering})\n";
        }

    } else {
        echo "❌ Cleanup cancelled\n";
    }
} else {
    echo "🎉 No invalid records found! All CourseDate records are valid.\n";
}

echo "\n✨ Cleanup Complete!\n";
