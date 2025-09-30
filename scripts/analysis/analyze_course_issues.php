<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Analyzing Current CourseDate Issues\n";
echo "=====================================\n\n";

use App\Models\CourseDate;
use Carbon\Carbon;

// Get all CourseDate records for the next 2 weeks
$startDate = Carbon::parse('2025-09-22'); // Start of this week
$endDate = Carbon::parse('2025-10-05');   // End of next week

$courseDates = CourseDate::with(['courseUnit.course'])
    ->whereBetween('starts_at', [$startDate, $endDate])
    ->orderBy('starts_at')
    ->get();

echo "📅 Current CourseDate Records ({$courseDates->count()} total):\n";
echo "=" . str_repeat("=", 60) . "\n";

$dayGroups = [];
$duplicates = [];

foreach ($courseDates as $courseDate) {
    $startsAt = Carbon::parse($courseDate->starts_at);
    $date = $startsAt->format('Y-m-d');
    $dayName = $startsAt->format('l'); // Monday, Tuesday, etc.
    $courseName = $courseDate->courseUnit->course->title;
    $unitTitle = $courseDate->courseUnit->admin_title;

    // Group by date
    if (!isset($dayGroups[$date])) {
        $dayGroups[$date] = [];
    }
    $dayGroups[$date][] = [
        'id' => $courseDate->id,
        'course' => $courseName,
        'unit' => $unitTitle,
        'day_name' => $dayName
    ];

    // Check for duplicates (same course type on same day)
    $courseType = '';
    if (strpos($courseName, 'D40') !== false) $courseType = 'D';
    if (strpos($courseName, 'G28') !== false) $courseType = 'G';

    $key = $date . '_' . $courseType;
    if (!isset($duplicates[$key])) {
        $duplicates[$key] = [];
    }
    $duplicates[$key][] = $courseDate->id;
}

// Display by day
foreach ($dayGroups as $date => $courses) {
    $dayName = Carbon::parse($date)->format('l, M j');
    echo "\n📍 {$dayName} ({$date}):\n";

    foreach ($courses as $course) {
        echo "   • ID {$course['id']}: {$course['course']} - {$course['unit']}\n";
    }

    // Highlight duplicates
    if (count($courses) > 1) {
        $courseTypes = [];
        foreach ($courses as $course) {
            $type = strpos($course['course'], 'D40') !== false ? 'D' : 'G';
            $courseTypes[] = $type;
        }
        $duplicateTypes = array_diff_assoc($courseTypes, array_unique($courseTypes));
        if (!empty($duplicateTypes)) {
            echo "   ⚠️  DUPLICATE DETECTED: Multiple " . implode(', ', array_unique($duplicateTypes)) . " courses on same day!\n";
        }
    }
}

echo "\n\n🔍 Issues Found:\n";
echo "================\n";

// Find duplicates
$duplicateIssues = [];
foreach ($duplicates as $key => $ids) {
    if (count($ids) > 1) {
        list($date, $type) = explode('_', $key);
        $duplicateIssues[] = [
            'date' => $date,
            'type' => $type,
            'ids' => $ids
        ];
    }
}

if (!empty($duplicateIssues)) {
    echo "\n❌ DUPLICATE COURSE TYPES ON SAME DAY:\n";
    foreach ($duplicateIssues as $issue) {
        echo "   • {$issue['date']}: Multiple {$issue['type']} courses (IDs: " . implode(', ', $issue['ids']) . ")\n";
    }
} else {
    echo "✅ No duplicate course types found\n";
}

// Check day numbering progression
echo "\n🔢 Day Numbering Analysis:\n";
$dCoursesByDate = [];
$gCoursesByDate = [];

foreach ($courseDates as $courseDate) {
    $startsAt = Carbon::parse($courseDate->starts_at);
    $date = $startsAt->format('Y-m-d');
    $courseName = $courseDate->courseUnit->course->title;
    $unitTitle = $courseDate->courseUnit->admin_title;

    if (strpos($courseName, 'D40') !== false) {
        $dCoursesByDate[$date] = $unitTitle;
    }
    if (strpos($courseName, 'G28') !== false) {
        $gCoursesByDate[$date] = $unitTitle;
    }
}

echo "\n📚 D40 Course Progression:\n";
foreach ($dCoursesByDate as $date => $unit) {
    echo "   {$date}: {$unit}\n";
}

echo "\n📚 G28 Course Progression:\n";
foreach ($gCoursesByDate as $date => $unit) {
    echo "   {$date}: {$unit}\n";
}

// Action plan
echo "\n\n🛠️  ACTION PLAN:\n";
echo "===============\n";

if (!empty($duplicateIssues)) {
    echo "1. Remove duplicate CourseDate records\n";
    foreach ($duplicateIssues as $issue) {
        // Keep the first one, remove the rest
        $toRemove = array_slice($issue['ids'], 1);
        echo "   • Keep ID {$issue['ids'][0]}, remove IDs: " . implode(', ', $toRemove) . "\n";
    }
}

echo "2. Fix day numbering to be sequential across weeks\n";
echo "3. Regenerate CourseDate records with proper progression\n";

echo "\n✨ Ready to proceed with fixes? [y/N]: ";
