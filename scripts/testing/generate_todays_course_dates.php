<?php

/**
 * Generate Today's CourseDate Records for Instructor Dashboard Testing
 * 
 * This script creates CourseDate records for today to test the bulletin board display.
 * Creates multiple course sessions with different times and course units.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🎯 Generating Today's CourseDate Records for Instructor Dashboard Testing\n";
echo "Date: " . Carbon::now()->toDateString() . "\n\n";

try {
    // Get today's date
    $today = Carbon::now()->startOfDay();
    
    // Check if we already have course dates for today
    $existingCount = DB::table('course_dates')
        ->whereDate('starts_at', $today->toDateString())
        ->where('is_active', true)
        ->count();
    
    if ($existingCount > 0) {
        echo "⚠️  Found {$existingCount} existing CourseDate records for today.\n";
        echo "Do you want to create additional records? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $choice = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($choice) !== 'y') {
            echo "❌ Cancelled. No new records created.\n";
            exit;
        }
    }
    
    // Sample course data to create
    $courseDates = [
        [
            'course_unit_id' => 1,  // Assuming course unit 1 exists
            'title' => 'Florida Class D 40 Hour - Day 1',
            'starts_at' => $today->copy()->setTime(8, 0),   // 8:00 AM
            'ends_at' => $today->copy()->setTime(17, 0),    // 5:00 PM
        ],
        [
            'course_unit_id' => 2,  // Assuming course unit 2 exists
            'title' => 'Florida Class G 40 Hour - Day 1', 
            'starts_at' => $today->copy()->setTime(9, 0),   // 9:00 AM
            'ends_at' => $today->copy()->setTime(18, 0),    // 6:00 PM
        ],
        [
            'course_unit_id' => 3,  // Assuming course unit 3 exists
            'title' => 'Security Fundamentals - Module 1',
            'starts_at' => $today->copy()->setTime(10, 0),  // 10:00 AM
            'ends_at' => $today->copy()->setTime(16, 0),    // 4:00 PM
        ],
        [
            'course_unit_id' => 4,  // Assuming course unit 4 exists
            'title' => 'Risk Assessment Training',
            'starts_at' => $today->copy()->setTime(13, 0),  // 1:00 PM
            'ends_at' => $today->copy()->setTime(17, 30),   // 5:30 PM
        ]
    ];
    
    $createdCount = 0;
    
    foreach ($courseDates as $courseData) {
        // Check if course_unit_id exists
        $courseUnitExists = DB::table('course_units')
            ->where('id', $courseData['course_unit_id'])
            ->exists();
        
        if (!$courseUnitExists) {
            echo "⚠️  CourseUnit ID {$courseData['course_unit_id']} doesn't exist. Skipping '{$courseData['title']}'.\n";
            continue;
        }
        
        // Create the CourseDate record
        $courseDate = DB::table('course_dates')->insertGetId([
            'is_active' => true,
            'course_unit_id' => $courseData['course_unit_id'],
            'starts_at' => $courseData['starts_at'],
            'ends_at' => $courseData['ends_at'],
        ]);
        
        echo "✅ Created CourseDate ID: {$courseDate} - {$courseData['title']}\n";
        echo "   📅 {$courseData['starts_at']->format('g:i A')} - {$courseData['ends_at']->format('g:i A')}\n";
        
        $createdCount++;
    }
    
    echo "\n🎉 Successfully created {$createdCount} CourseDate records for today!\n\n";
    
    // Display summary of today's course dates
    $todaysCourses = DB::table('course_dates')
        ->join('course_units', 'course_dates.course_unit_id', '=', 'course_units.id')
        ->join('courses', 'course_units.course_id', '=', 'courses.id')
        ->whereDate('course_dates.starts_at', $today->toDateString())
        ->where('course_dates.is_active', true)
        ->select(
            'course_dates.id',
            'courses.title as course_title',
            'course_units.title as unit_title',
            'course_dates.starts_at',
            'course_dates.ends_at'
        )
        ->orderBy('course_dates.starts_at')
        ->get();
    
    echo "📋 Today's Course Schedule:\n";
    echo str_repeat('-', 80) . "\n";
    printf("%-6s %-25s %-20s %-10s %-10s\n", 'ID', 'Course', 'Unit', 'Start', 'End');
    echo str_repeat('-', 80) . "\n";
    
    foreach ($todaysCourses as $course) {
        $startTime = Carbon::parse($course->starts_at)->format('g:i A');
        $endTime = Carbon::parse($course->ends_at)->format('g:i A');
        
        printf("%-6s %-25s %-20s %-10s %-10s\n", 
            $course->id,
            substr($course->course_title, 0, 24),
            substr($course->unit_title, 0, 19),
            $startTime,
            $endTime
        );
    }
    
    echo str_repeat('-', 80) . "\n";
    echo "Total: " . count($todaysCourses) . " courses scheduled for today\n\n";
    
    echo "🚀 Ready to test Instructor Dashboard!\n";
    echo "Navigate to: /admin/instructors/\n";
    echo "You should see course cards in the bulletin board.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}