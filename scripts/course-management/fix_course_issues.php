<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🛠️  Fixing CourseDate Issues\n";
echo "============================\n\n";

use App\Models\CourseDate;
use Carbon\Carbon;

// Step 1: Remove duplicates
echo "1️⃣ Removing Duplicate D Courses:\n";
$duplicateIdsToRemove = [10541, 10542, 10543, 10533];

foreach ($duplicateIdsToRemove as $id) {
    $courseDate = CourseDate::find($id);
    if ($courseDate) {
        $date = Carbon::parse($courseDate->starts_at)->format('Y-m-d');
        echo "   ❌ Removing ID {$id} on {$date}\n";
        $courseDate->delete();
    }
}

echo "✅ Duplicates removed\n\n";

// Step 2: Fix sequential day numbering
echo "2️⃣ Fixing Day Numbering:\n";

// Get remaining CourseDate records in order
$courseDates = CourseDate::with(['courseUnit.course'])
    ->whereBetween('starts_at', ['2025-09-22', '2025-10-05'])
    ->orderBy('starts_at')
    ->get();

$dCourseDay = 1; // Start D courses at Day 1
$gCourseDay = 1; // Start G courses at Day 1

foreach ($courseDates as $courseDate) {
    $courseName = $courseDate->courseUnit->course->title;
    $currentUnit = $courseDate->courseUnit->admin_title;
    $date = Carbon::parse($courseDate->starts_at)->format('Y-m-d');

    if (strpos($courseName, 'D40') !== false) {
        // Fix D40 course numbering
        $newUnit = "FL-D40-D{$dCourseDay}";
        if ($currentUnit !== $newUnit) {
            echo "   📝 {$date}: {$currentUnit} → {$newUnit}\n";

            // Find the correct CourseUnit for this day
            $targetCourseUnit = \App\Models\CourseUnit::where('course_id', $courseDate->courseUnit->course_id)
                ->where('admin_title', $newUnit)
                ->first();

            if ($targetCourseUnit) {
                $courseDate->course_unit_id = $targetCourseUnit->id;
                $courseDate->save();
            } else {
                echo "   ⚠️  Warning: CourseUnit {$newUnit} not found\n";
            }
        }
        $dCourseDay++;

    } elseif (strpos($courseName, 'G28') !== false) {
        // Fix G28 course numbering
        $newUnit = "FL-G28-D{$gCourseDay}";
        if ($currentUnit !== $newUnit) {
            echo "   📝 {$date}: {$currentUnit} → {$newUnit}\n";

            // Find the correct CourseUnit for this day
            $targetCourseUnit = \App\Models\CourseUnit::where('course_id', $courseDate->courseUnit->course_id)
                ->where('admin_title', $newUnit)
                ->first();

            if ($targetCourseUnit) {
                $courseDate->course_unit_id = $targetCourseUnit->id;
                $courseDate->save();
            } else {
                echo "   ⚠️  Warning: CourseUnit {$newUnit} not found\n";
            }
        }
        $gCourseDay++;
    }
}

echo "\n✅ Day numbering fixed\n\n";

// Step 3: Verify results
echo "3️⃣ Verification:\n";

$verifyDates = CourseDate::with(['courseUnit.course'])
    ->whereBetween('starts_at', ['2025-09-22', '2025-10-05'])
    ->orderBy('starts_at')
    ->get();

$dayCount = [];
foreach ($verifyDates as $courseDate) {
    $date = Carbon::parse($courseDate->starts_at)->format('Y-m-d l');
    $courseName = $courseDate->courseUnit->course->title;
    $unitTitle = $courseDate->courseUnit->admin_title;

    echo "   📅 {$date}: {$courseName} - {$unitTitle}\n";

    // Count courses per day
    $dateKey = Carbon::parse($courseDate->starts_at)->format('Y-m-d');
    if (!isset($dayCount[$dateKey])) {
        $dayCount[$dateKey] = [];
    }
    $courseType = strpos($courseName, 'D40') !== false ? 'D' : 'G';
    $dayCount[$dateKey][] = $courseType;
}

echo "\n🔍 Final Check:\n";
$issues = 0;
foreach ($dayCount as $date => $types) {
    $duplicates = array_diff_assoc($types, array_unique($types));
    if (!empty($duplicates)) {
        echo "   ❌ {$date}: Still has duplicates - " . implode(', ', $types) . "\n";
        $issues++;
    }
}

if ($issues === 0) {
    echo "   ✅ No duplicate course types found!\n";
}

echo "\n🎉 CourseDate fixes complete!\n";
echo "Total records: " . $verifyDates->count() . "\n";
echo "Issues found: {$issues}\n";
