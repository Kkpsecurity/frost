<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 CourseDate Management System Investigation\n";
echo "==============================================\n\n";

use App\Models\CourseDate;
use Carbon\Carbon;

// Check current CourseDate records
echo "1️⃣ **Current CourseDate Records Analysis**\n";
echo "-------------------------------------------\n";

$totalCourseDates = CourseDate::count();
$activeCourseDates = CourseDate::where('is_active', true)->count();
$inactiveCourseDates = CourseDate::where('is_active', false)->count();

echo "📊 **Overall Statistics:**\n";
echo "   Total CourseDate records: {$totalCourseDates}\n";
echo "   Active (is_active = true): {$activeCourseDates}\n";
echo "   Inactive (is_active = false): {$inactiveCourseDates}\n\n";

// Check today's CourseDate records
echo "📅 **Today's CourseDate Records:** " . now()->format('Y-m-d') . "\n";
$todayActive = CourseDate::whereDate('starts_at', today())->where('is_active', true)->count();
$todayInactive = CourseDate::whereDate('starts_at', today())->where('is_active', false)->count();
echo "   Active today: {$todayActive}\n";
echo "   Inactive today: {$todayInactive}\n\n";

// Check this week's CourseDate records
echo "📅 **This Week's CourseDate Records:** " . now()->startOfWeek()->format('Y-m-d') . " to " . now()->endOfWeek()->format('Y-m-d') . "\n";
$thisWeekRecords = CourseDate::whereBetween('starts_at', [now()->startOfWeek(), now()->endOfWeek()])->get();

foreach ($thisWeekRecords as $record) {
    $date = Carbon::parse($record->starts_at)->format('Y-m-d l');
    $status = $record->is_active ? '✅ ACTIVE' : '⚠️  INACTIVE';
    $courseName = $record->courseUnit->course->title;
    $unitTitle = $record->courseUnit->admin_title;
    echo "   {$date}: {$courseName} - {$unitTitle} [{$status}]\n";
}

echo "\n2️⃣ **Calendar Display Analysis**\n";
echo "---------------------------------\n";

// Test what the calendar would show
echo "🌐 **Calendar Query (MiscQueries::CalenderDates)**\n";
echo "Current logic: WHERE is_active = true AND starts_at >= first of month\n\n";

$courses = \App\Models\Course::where('is_active', true)->get();
$totalCalendarEvents = 0;

foreach ($courses as $course) {
    $calendarEvents = \App\Classes\MiscQueries::CalenderDates($course);
    echo "📚 **{$course->title}**: {$calendarEvents->count()} events in calendar\n";
    $totalCalendarEvents += $calendarEvents->count();
}

echo "\n📊 **Calendar Summary:**\n";
echo "   Total events showing in calendar: {$totalCalendarEvents}\n";
echo "   Events filtered out (inactive): " . ($totalCourseDates - $totalCalendarEvents) . "\n\n";

echo "3️⃣ **Proposed Changes Analysis**\n";
echo "---------------------------------\n";

echo "🎯 **Goal**: Show ALL CourseDate records in calendar (active + inactive)\n";
echo "📝 **Current Issue**: Calendar filters by is_active = true\n";
echo "🔧 **Required Changes**:\n";
echo "   1. Update MiscQueries::CalenderDates() to remove is_active filter\n";
echo "   2. Modify CourseDateGeneratorService to create with is_active = false\n";
echo "   3. Add visual indicators for active vs inactive in calendar\n";
echo "   4. Ensure activation service works properly\n\n";

echo "4️⃣ **Impact Assessment**\n";
echo "-------------------------\n";

if ($inactiveCourseDates > 0) {
    echo "⚠️  **Found {$inactiveCourseDates} inactive CourseDate records**\n";
    echo "   These would become visible in calendar after changes\n";
} else {
    echo "ℹ️  **All CourseDate records are currently active**\n";
    echo "   Changes would mainly affect future record generation\n";
}

echo "\n📋 **Next Steps**:\n";
echo "   [ ] Update calendar query to show all records\n";
echo "   [ ] Test calendar display with inactive records\n";
echo "   [ ] Modify generation service default is_active = false\n";
echo "   [ ] Find/create CourseDate activation service\n";
echo "   [ ] Add visual distinction in calendar UI\n";

echo "\n🎉 Investigation Complete!\n";
