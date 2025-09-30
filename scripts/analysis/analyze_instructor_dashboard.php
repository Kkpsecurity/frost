<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Instructor Dashboard Analysis\n";
echo "================================\n\n";

use App\Models\CourseDate;
use Carbon\Carbon;

$today = now()->format('Y-m-d');
echo "📅 **Today's Date**: {$today}\n\n";

// Check today's CourseDate records
echo "1️⃣ **Today's CourseDate Records**\n";
echo "---------------------------------\n";

$todayCourseDates = CourseDate::whereDate('starts_at', $today)
    ->with(['courseUnit.course'])
    ->orderBy('starts_at')
    ->get();

echo "📊 **Total CourseDate records for today**: {$todayCourseDates->count()}\n";

if ($todayCourseDates->count() > 0) {
    foreach ($todayCourseDates as $courseDate) {
        $status = $courseDate->is_active ? '✅ ACTIVE' : '⚠️  INACTIVE';
        $courseName = $courseDate->courseUnit->course->title;
        $unitTitle = $courseDate->courseUnit->admin_title;
        $startTime = Carbon::parse($courseDate->starts_at)->format('H:i');

        echo "   • ID {$courseDate->id}: {$courseName} - {$unitTitle} at {$startTime} [{$status}]\n";
    }
} else {
    echo "   ❌ No CourseDate records found for today\n";
}

// Check upcoming CourseDate records
echo "\n2️⃣ **Upcoming CourseDate Records (Next 14 Days)**\n";
echo "--------------------------------------------------\n";

$upcoming = CourseDate::whereBetween('starts_at', [now()->addDay(), now()->addDays(14)])
    ->with(['courseUnit.course'])
    ->orderBy('starts_at')
    ->get();

echo "📊 **Total upcoming CourseDate records**: {$upcoming->count()}\n";

$byStatus = $upcoming->groupBy('is_active');
$activeUpcoming = $byStatus->get(1, collect())->count();
$inactiveUpcoming = $byStatus->get(0, collect())->count();

echo "   ✅ Active upcoming: {$activeUpcoming}\n";
echo "   ⚠️  Inactive upcoming: {$inactiveUpcoming}\n";

// Show sample upcoming courses
if ($upcoming->count() > 0) {
    echo "\n📚 **Sample Upcoming Courses**:\n";
    foreach ($upcoming->take(5) as $courseDate) {
        $date = Carbon::parse($courseDate->starts_at)->format('Y-m-d l');
        $status = $courseDate->is_active ? '✅' : '⚠️';
        $courseName = $courseDate->courseUnit->course->title;
        $unitTitle = $courseDate->courseUnit->admin_title;

        echo "   {$status} {$date}: {$courseName} - {$unitTitle}\n";
    }

    if ($upcoming->count() > 5) {
        echo "   ... and " . ($upcoming->count() - 5) . " more upcoming courses\n";
    }
}

// Check instructor dashboard service
echo "\n3️⃣ **Instructor Dashboard Service Analysis**\n";
echo "---------------------------------------------\n";

// Let's check what the instructor dashboard is looking for
echo "🔍 **Investigating dashboard logic...**\n";

// Check if there are any active CourseDate records for today that should show
$activeTodayCount = CourseDate::whereDate('starts_at', $today)
    ->where('is_active', true)
    ->count();

echo "📊 **Active CourseDate records for today**: {$activeTodayCount}\n";

if ($activeTodayCount > 0) {
    echo "✅ **There ARE active courses today - dashboard should show them**\n";
} else {
    echo "❌ **No active courses today - 'No Courses Scheduled' is correct**\n";
}

echo "\n4️⃣ **Recommendations**\n";
echo "----------------------\n";

if ($todayCourseDates->count() === 0) {
    echo "🚨 **Issue**: No CourseDate records exist for today\n";
    echo "💡 **Solution**: Run course generation to create CourseDate records\n";
    echo "📝 **Command**: php artisan course:generate-dates --days=14\n";
} elseif ($activeTodayCount === 0) {
    echo "🚨 **Issue**: CourseDate records exist but are inactive\n";
    echo "💡 **Solution**: Activate today's CourseDate records\n";
    echo "📝 **Command**: php artisan course:activate-dates\n";
} else {
    echo "🤔 **Issue**: Active courses exist but dashboard shows 'No Courses Scheduled'\n";
    echo "💡 **Solution**: Check instructor dashboard service logic\n";
}

echo "\n📊 **Summary for Instructor Dashboard Updates**:\n";
echo "===============================================\n";
echo "1. **Upcoming Courses Panel**: Show {$upcoming->count()} upcoming courses (next 14 days)\n";
echo "2. **Auto-generate 2 weeks ahead**: " . ($upcoming->count() >= 10 ? "✅ Sufficient" : "⚠️ Need more") . "\n";
echo "3. **Today's Active Courses**: " . ($activeTodayCount > 0 ? "✅ Available" : "❌ Missing") . "\n";

echo "\n🔗 **Next Steps**:\n";
echo "1. Generate CourseDate records for next 2 weeks\n";
echo "2. Activate today's CourseDate records\n";
echo "3. Update instructor dashboard to show today's active courses\n";
echo "4. Add upcoming courses panel\n";
