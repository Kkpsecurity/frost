<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 Fixing Course Unit Cycling\n";
echo "==============================\n\n";

use App\Models\CourseDate;
use App\Models\CourseUnit;
use Carbon\Carbon;

// Get all CourseDate records in order
$courseDates = CourseDate::with(['courseUnit.course'])
    ->whereBetween('starts_at', ['2025-09-22', '2025-10-05'])
    ->orderBy('starts_at')
    ->get();

echo "📅 Current Schedule (before fix):\n";
foreach ($courseDates as $courseDate) {
    $date = Carbon::parse($courseDate->starts_at)->format('Y-m-d l');
    $courseName = $courseDate->courseUnit->course->title;
    $unitTitle = $courseDate->courseUnit->admin_title;
    echo "   {$date}: {$courseName} - {$unitTitle}\n";
}

echo "\n🔧 Applying Proper Unit Cycling:\n";

// Track day counters for each course type
$dDayCounter = 1; // D40 course day counter (starts at 1)
$gDayCounter = 1; // G28 course day counter (starts at 1)

// Get available units for each course
$d40Course = \App\Models\Course::where('title', 'LIKE', '%D40%')->first();
$g28Course = \App\Models\Course::where('title', 'LIKE', '%G28%')->first();

$d40Units = CourseUnit::where('course_id', $d40Course->id)->orderBy('ordering')->get();
$g28Units = CourseUnit::where('course_id', $g28Course->id)->orderBy('ordering')->get();

echo "   📚 D40 Course has {$d40Units->count()} units (will cycle)\n";
echo "   📚 G28 Course has {$g28Units->count()} units (will cycle)\n\n";

foreach ($courseDates as $courseDate) {
    $courseName = $courseDate->courseUnit->course->title;
    $currentUnit = $courseDate->courseUnit->admin_title;
    $date = Carbon::parse($courseDate->starts_at)->format('Y-m-d');

    if (strpos($courseName, 'D40') !== false) {
        // Calculate which unit to use (cycle through available units)
        $unitIndex = ($dDayCounter - 1) % $d40Units->count();
        $targetUnit = $d40Units[$unitIndex];
        $newUnitTitle = $targetUnit->admin_title;

        if ($currentUnit !== $newUnitTitle) {
            echo "   📝 {$date}: D40 Day {$dDayCounter} → {$currentUnit} → {$newUnitTitle}\n";
            $courseDate->course_unit_id = $targetUnit->id;
            $courseDate->save();
        }
        $dDayCounter++;

    } elseif (strpos($courseName, 'G28') !== false) {
        // Calculate which unit to use (cycle through available units)
        $unitIndex = ($gDayCounter - 1) % $g28Units->count();
        $targetUnit = $g28Units[$unitIndex];
        $newUnitTitle = $targetUnit->admin_title;

        if ($currentUnit !== $newUnitTitle) {
            echo "   📝 {$date}: G28 Day {$gDayCounter} → {$currentUnit} → {$newUnitTitle}\n";
            $courseDate->course_unit_id = $targetUnit->id;
            $courseDate->save();
        }
        $gDayCounter++;
    }
}

echo "\n✅ Unit cycling fixed!\n\n";

// Verify final schedule
echo "📅 Final Schedule (after fix):\n";
$finalDates = CourseDate::with(['courseUnit.course'])
    ->whereBetween('starts_at', ['2025-09-22', '2025-10-05'])
    ->orderBy('starts_at')
    ->get();

$dDayDisplay = 1;
$gDayDisplay = 1;

foreach ($finalDates as $courseDate) {
    $date = Carbon::parse($courseDate->starts_at)->format('Y-m-d l');
    $courseName = $courseDate->courseUnit->course->title;
    $unitTitle = $courseDate->courseUnit->admin_title;

    if (strpos($courseName, 'D40') !== false) {
        echo "   {$date}: {$courseName} Day {$dDayDisplay} - {$unitTitle}\n";
        $dDayDisplay++;
    } elseif (strpos($courseName, 'G28') !== false) {
        echo "   {$date}: {$courseName} Day {$gDayDisplay} - {$unitTitle}\n";
        $gDayDisplay++;
    }
}

echo "\n🎉 Course unit cycling is now correct!\n";
echo "   D40 Course: Cycles through 5 units (D1→D2→D3→D4→D5→D1...)\n";
echo "   G28 Course: Cycles through 3 units (D1→D2→D3→D1...)\n";
