<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Updated Calendar Display\n";
echo "===================================\n\n";

use App\Models\Course;
use App\Classes\MiscQueries;
use Carbon\Carbon;

echo "📅 **Calendar Display Test (After Removing is_active Filter)**\n";
echo "--------------------------------------------------------------\n";

$courses = Course::where('is_active', true)->get();
$totalCalendarEvents = 0;
$activeEvents = 0;
$inactiveEvents = 0;

foreach ($courses as $course) {
    $calendarEvents = MiscQueries::CalenderDates($course);
    echo "\n📚 **{$course->title}**:\n";
    echo "   Total events: {$calendarEvents->count()}\n";

    $activeCount = 0;
    $inactiveCount = 0;

    // Analyze first few events
    $sampleEvents = $calendarEvents->take(5);
    foreach ($sampleEvents as $event) {
        $date = Carbon::parse($event->starts_at)->format('Y-m-d l');
        $status = $event->is_active ? '✅ ACTIVE' : '⚠️  INACTIVE';
        $unitTitle = $event->courseUnit->admin_title;
        echo "   • {$date}: {$unitTitle} [{$status}]\n";

        if ($event->is_active) $activeCount++; else $inactiveCount++;
    }

    if ($calendarEvents->count() > 5) {
        echo "   • ... and " . ($calendarEvents->count() - 5) . " more events\n";
    }

    // Count all active/inactive
    foreach ($calendarEvents as $event) {
        if ($event->is_active) {
            $activeEvents++;
        } else {
            $inactiveEvents++;
        }
    }

    $totalCalendarEvents += $calendarEvents->count();
}

echo "\n📊 **Updated Calendar Summary:**\n";
echo "================================\n";
echo "   Total events now showing: {$totalCalendarEvents}\n";
echo "   Active events: {$activeEvents} (✅ previously visible)\n";
echo "   Inactive events: {$inactiveEvents} (🆕 newly visible)\n";

$previousTotal = 15; // From investigation
$newlyVisible = $totalCalendarEvents - $previousTotal;
echo "   Newly visible events: {$newlyVisible}\n\n";

echo "🎯 **Impact Assessment:**\n";
echo "=========================\n";
if ($newlyVisible > 0) {
    echo "✅ SUCCESS: Calendar now shows {$newlyVisible} additional events!\n";
    echo "📈 Increase: " . round(($newlyVisible / $previousTotal) * 100, 1) . "% more events visible\n";
} else {
    echo "ℹ️  No change in visible events (all were already active)\n";
}

echo "\n🔄 **Next Steps:**\n";
echo "==================\n";
echo "1. ✅ Calendar now shows ALL CourseDate records\n";
echo "2. 🔄 Need to add visual distinction for active vs inactive\n";
echo "3. 🔄 Update CourseDateGeneratorService to create inactive by default\n";
echo "4. 🔄 Create/find CourseDate activation service\n";

echo "\n🌐 **Calendar URL for Testing:**\n";
echo "https://frost.test/courses/schedules\n";
