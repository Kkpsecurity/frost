<?php

/**
 * Fix CourseDate Structure - Create Single Correct CourseDate for Today
 * 
 * Remove the incorrect multiple CourseDate records and create one proper
 * CourseDate representing today's scheduled class day.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing CourseDate Structure - Template-Instance Pattern\n";
echo "========================================================\n\n";

try {
    $today = Carbon::now()->startOfDay();
    
    echo "📅 Target Date: " . $today->toDateString() . "\n\n";
    
    // Remove the incorrect CourseDate records I created
    $incorrectIds = [10561, 10562, 10563, 10564];
    
    echo "🗑️  Removing incorrect CourseDate records...\n";
    foreach ($incorrectIds as $id) {
        $deleted = DB::table('course_dates')->where('id', $id)->delete();
        if ($deleted) {
            echo "   ✅ Deleted CourseDate ID: $id\n";
        }
    }
    
    echo "\n📋 Current CourseUnits available:\n";
    $courseUnits = DB::table('course_units')
        ->join('courses', 'course_units.course_id', '=', 'courses.id')
        ->select('course_units.*', 'courses.title as course_title')
        ->orderBy('course_units.course_id')
        ->orderBy('course_units.ordering')
        ->get();
    
    foreach ($courseUnits as $unit) {
        printf("   Unit ID: %d - %s - %s (Order: %d)\n", 
            $unit->id, 
            $unit->course_title, 
            $unit->title, 
            $unit->ordering
        );
    }
    
    echo "\n🎯 Creating ONE correct CourseDate for today:\n";
    
    // Let's create Day 1 of Florida D40 for today (typical 8-hour class day)
    $courseUnitId = 1; // Assuming this is Day 1 of Florida D40
    
    // Check if this course unit exists
    $courseUnit = DB::table('course_units')
        ->join('courses', 'course_units.course_id', '=', 'courses.id')
        ->where('course_units.id', $courseUnitId)
        ->select('course_units.*', 'courses.title as course_title')
        ->first();
    
    if (!$courseUnit) {
        echo "❌ CourseUnit ID $courseUnitId not found!\n";
        exit;
    }
    
    echo "📚 Selected CourseUnit:\n";
    echo "   ID: {$courseUnit->id}\n";
    echo "   Course: {$courseUnit->course_title}\n";
    echo "   Day: {$courseUnit->title}\n";
    echo "   Admin Title: {$courseUnit->admin_title}\n\n";
    
    // Create single CourseDate for today (typical 8-hour class day)
    $startTime = $today->copy()->setTime(9, 0);  // 9:00 AM
    $endTime = $today->copy()->setTime(17, 0);   // 5:00 PM (8 hours)
    
    $courseDate = DB::table('course_dates')->insertGetId([
        'is_active' => true,
        'course_unit_id' => $courseUnitId,
        'starts_at' => $startTime,
        'ends_at' => $endTime,
    ]);
    
    echo "✅ Created CourseDate ID: {$courseDate}\n";
    echo "   📅 Date: {$startTime->format('M j, Y')}\n";
    echo "   ⏰ Time: {$startTime->format('g:i A')} - {$endTime->format('g:i A')}\n";
    echo "   📚 Course: {$courseUnit->course_title}\n";
    echo "   📖 Day: {$courseUnit->title}\n";
    echo "   ⏱️  Duration: 8 hours\n\n";
    
    echo "🎉 Fixed! Now there's ONE correct CourseDate for today.\n\n";
    
    echo "📋 Today's Course Schedule (Corrected):\n";
    echo str_repeat('-', 60) . "\n";
    
    $todaysCourse = DB::table('course_dates')
        ->join('course_units', 'course_dates.course_unit_id', '=', 'course_units.id')
        ->join('courses', 'course_units.course_id', '=', 'courses.id')
        ->whereDate('course_dates.starts_at', $today->toDateString())
        ->where('course_dates.is_active', true)
        ->select(
            'course_dates.id',
            'courses.title as course_title',
            'course_units.title as unit_title',
            'course_units.admin_title',
            'course_dates.starts_at',
            'course_dates.ends_at'
        )
        ->first();
    
    if ($todaysCourse) {
        $startTime = Carbon::parse($todaysCourse->starts_at);
        $endTime = Carbon::parse($todaysCourse->ends_at);
        $duration = $startTime->diffInHours($endTime);
        
        printf("CourseDate ID: %d\n", $todaysCourse->id);
        printf("Course: %s\n", $todaysCourse->course_title);
        printf("Class Day: %s (%s)\n", $todaysCourse->unit_title, $todaysCourse->admin_title);
        printf("Schedule: %s - %s (%d hours)\n", 
            $startTime->format('g:i A'),
            $endTime->format('g:i A'),
            $duration
        );
    }
    
    echo str_repeat('-', 60) . "\n";
    echo "🚀 Ready to test! Navigate to: /admin/instructors/\n";
    echo "You should see ONE course card for today's class day.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}