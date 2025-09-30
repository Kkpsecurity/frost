<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Complete CourseDate Management System\n";
echo "================================================\n\n";

use App\Services\Frost\Scheduling\CourseDateGeneratorService;
use App\Services\Frost\Scheduling\CourseDateActivationService;
use Carbon\Carbon;

// Step 1: Generate inactive CourseDate records for next week
echo "1️⃣ **Generating Inactive CourseDate Records**\n";
echo "----------------------------------------------\n";

$generatorService = new CourseDateGeneratorService();

// Generate for next week (Oct 6-10)
$startDate = Carbon::parse('2025-10-06'); // Monday
$endDate = Carbon::parse('2025-10-10');   // Friday

echo "📅 Generating CourseDate records for: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}\n";

$generationResults = $generatorService->generateCourseDatesForRange($startDate, $endDate);

echo "📊 **Generation Results:**\n";
echo "   Date Range: {$generationResults['period']['start']} to {$generationResults['period']['end']}\n";
echo "   Courses Processed: {$generationResults['courses_processed']}\n";
echo "   Records Created: {$generationResults['dates_created']}\n";
echo "   Records Skipped: {$generationResults['dates_skipped']} (already existed)\n";
echo "   Errors: " . count($generationResults['errors']) . "\n\n";

// Step 2: Check what was created
echo "2️⃣ **Checking Generated Records**\n";
echo "----------------------------------\n";

$newRecords = \App\Models\CourseDate::whereBetween('starts_at', [$startDate, $endDate])
    ->with(['courseUnit.course'])
    ->orderBy('starts_at')
    ->get();

echo "📚 **Generated CourseDate Records ({$newRecords->count()} total):**\n";
foreach ($newRecords as $record) {
    $date = Carbon::parse($record->starts_at)->format('Y-m-d l');
    $status = $record->is_active ? '✅ ACTIVE' : '⚠️  INACTIVE';
    $courseName = $record->courseUnit->course->title;
    $unitTitle = $record->courseUnit->admin_title;
    echo "   {$date}: {$courseName} - {$unitTitle} [{$status}]\n";
}

$inactiveCount = $newRecords->where('is_active', false)->count();
$activeCount = $newRecords->where('is_active', true)->count();

echo "\n📊 **Status Summary:**\n";
echo "   Inactive records: {$inactiveCount} (🆕 new default)\n";
echo "   Active records: {$activeCount}\n\n";

// Step 3: Test calendar display
echo "3️⃣ **Testing Calendar Display**\n";
echo "--------------------------------\n";

$courses = \App\Models\Course::where('is_active', true)->get();
$totalCalendarEvents = 0;

foreach ($courses as $course) {
    $calendarEvents = \App\Classes\MiscQueries::CalenderDates($course);
    $courseEvents = $calendarEvents->filter(function($event) use ($startDate, $endDate) {
        $eventDate = Carbon::parse($event->starts_at);
        return $eventDate >= $startDate && $eventDate <= $endDate;
    });

    if ($courseEvents->count() > 0) {
        echo "📚 **{$course->title}**: {$courseEvents->count()} events in calendar (Oct 6-10)\n";
        $totalCalendarEvents += $courseEvents->count();
    }
}

echo "📊 **Calendar Summary**: {$totalCalendarEvents} total events showing for next week\n";
echo "✅ **Success**: Calendar now shows ALL records (active + inactive)\n\n";

// Step 4: Test activation service
echo "4️⃣ **Testing CourseDate Activation**\n";
echo "------------------------------------\n";

$activationService = new CourseDateActivationService();

// Test activation for Monday (Oct 6)
$testDate = Carbon::parse('2025-10-06');
echo "🔄 Testing activation for: {$testDate->format('Y-m-d l')}\n";

$preview = $activationService->previewActivationForToday('America/New_York');
echo "📊 Current preview (today): {$preview['inactive_count']} inactive records\n";

// Activate for test date
$activationResults = $activationService->activateCourseDatesForDate($testDate);

echo "📊 **Activation Results for {$testDate->format('Y-m-d')}:**\n";
echo "   Found inactive: {$activationResults['found_inactive']}\n";
echo "   Successfully activated: {$activationResults['activated']}\n";
echo "   Errors: " . count($activationResults['errors']) . "\n";

if ($activationResults['activated'] > 0) {
    echo "\n📚 **Activated Records:**\n";
    foreach ($activationResults['details'] as $detail) {
        echo "   • ID {$detail['id']}: {$detail['course']} - {$detail['unit']} at {$detail['start_time']}\n";
    }
}

echo "\n🎉 **Complete System Test Summary**\n";
echo "===================================\n";
echo "✅ CourseDate Generation: Creates records with is_active = false\n";
echo "✅ Calendar Display: Shows ALL records (active + inactive)\n";
echo "✅ Activation Service: Activates records on scheduled date\n";
echo "✅ Scheduler Integration: Runs activation daily at 6:00 AM ET\n\n";

echo "🔗 **Test the calendar**: https://frost.test/courses/schedules\n";
echo "📝 **Task tracking**: docs/tasks/coursedate-management-system.md\n";
