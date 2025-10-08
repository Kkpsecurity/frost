<?php
/**
 * INVESTIGATE STUDENTUNIT RECORDS - Why 131 students showing as "in class"
 * 
 * This script investigates why StudentUnit records exist for today's course
 * when no class has actually started yet.
 */

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== INVESTIGATING STUDENTUNIT RECORDS - WHY 131 STUDENTS? ===\n\n";

try {
    // Get today's course date
    $courseDate = \App\Models\CourseDate::whereDate('starts_at', today())->first();
    
    if (!$courseDate) {
        echo "❌ No CourseDate found for today\n";
        exit(1);
    }
    
    echo "📊 COURSE DATE ANALYSIS:\n";
    echo "ID: {$courseDate->id}\n";
    echo "Starts At: {$courseDate->starts_at}\n";
    echo "Created At: {$courseDate->created_at}\n";
    echo "Is Active: " . ($courseDate->is_active ? 'YES' : 'NO') . "\n\n";
    
    // Check if there's an InstUnit (class started)
    $instUnit = $courseDate->InstUnit;
    echo "🏫 CLASS STATUS:\n";
    if ($instUnit) {
        echo "InstUnit ID: {$instUnit->id}\n";
        echo "Created At: {$instUnit->created_at}\n";
        echo "Created By: {$instUnit->created_by}\n";
        echo "Completed At: " . ($instUnit->completed_at ?? 'NOT COMPLETED') . "\n";
        echo "STATUS: Class has been started by instructor\n\n";
    } else {
        echo "InstUnit: NONE\n";
        echo "STATUS: Class has NOT been started yet\n\n";
    }
    
    // Get StudentUnit records
    $studentUnits = \App\Models\StudentUnit::where('course_date_id', $courseDate->id)->get();
    echo "👥 STUDENTUNIT ANALYSIS:\n";
    echo "Total StudentUnit records: {$studentUnits->count()}\n\n";
    
    if ($studentUnits->count() > 0) {
        echo "🔍 FIRST 5 STUDENTUNIT RECORDS:\n";
        foreach ($studentUnits->take(5) as $studentUnit) {
            echo "- ID: {$studentUnit->id}\n";
            echo "  Created At: {$studentUnit->created_at}\n";
            echo "  Course Date ID: {$studentUnit->course_date_id}\n";
            echo "  Course Auth ID: {$studentUnit->course_auth_id}\n";
            echo "  User ID: " . ($studentUnit->CourseAuth->user_id ?? 'N/A') . "\n";
            echo "  User Name: " . ($studentUnit->CourseAuth->User->fname ?? 'N/A') . " " . ($studentUnit->CourseAuth->User->lname ?? '') . "\n\n";
        }
        
        // Check creation patterns
        $creationDates = $studentUnits->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
        });
        
        echo "📅 STUDENTUNIT CREATION DATES:\n";
        foreach ($creationDates as $date => $units) {
            echo "- {$date}: {$units->count()} StudentUnits created\n";
        }
        echo "\n";
        
        // Check if these are from previous class sessions
        $today = today()->format('Y-m-d');
        $todayCreated = $studentUnits->filter(function($unit) use ($today) {
            return \Carbon\Carbon::parse($unit->created_at)->format('Y-m-d') === $today;
        });
        
        $previousCreated = $studentUnits->filter(function($unit) use ($today) {
            return \Carbon\Carbon::parse($unit->created_at)->format('Y-m-d') !== $today;
        });
        
        echo "🎯 CREATION DATE BREAKDOWN:\n";
        echo "Created Today ({$today}): {$todayCreated->count()}\n";
        echo "Created Previously: {$previousCreated->count()}\n\n";
        
        if ($previousCreated->count() > 0) {
            echo "⚠️  ISSUE IDENTIFIED:\n";
            echo "These StudentUnit records were created on previous days but are still\n";
            echo "associated with today's CourseDate. This suggests:\n";
            echo "1. CourseDate might be reused from previous sessions\n";
            echo "2. StudentUnits aren't being cleaned up after class completion\n";
            echo "3. Data integrity issue with course date scheduling\n\n";
        }
        
    } else {
        echo "✅ No StudentUnit records found (correct if class hasn't started)\n\n";
    }
    
    // Compare with enrollment (CourseAuth)
    $course = $courseDate->CourseUnit->Course;
    $courseAuths = $course->CourseAuths;
    
    echo "📊 ENROLLMENT vs ATTENDANCE COMPARISON:\n";
    echo "Course: {$course->title}\n";
    echo "Total Enrolled (CourseAuth): {$courseAuths->count()}\n";
    echo "Total In Class (StudentUnit): {$studentUnits->count()}\n\n";
    
    echo "🎯 RECOMMENDATION FOR UI:\n";
    echo "Show both counts for comparison:\n";
    echo "- {$courseAuths->count()} registered (CourseAuth records)\n";
    echo "- {$studentUnits->count()} attending (StudentUnit records)\n\n";
    
    if ($studentUnits->count() > 0 && !$instUnit) {
        echo "❌ DATA INCONSISTENCY FOUND:\n";
        echo "StudentUnit records exist but no InstUnit (class not started)\n";
        echo "This indicates stale data or data integrity issues.\n\n";
        
        echo "🔧 SUGGESTED FIXES:\n";
        echo "1. Clean up StudentUnits from previous sessions\n";
        echo "2. Only show StudentUnits when InstUnit exists (class actually started)\n";
        echo "3. Add date validation to ensure StudentUnits match CourseDate timing\n\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}

echo "=== INVESTIGATION COMPLETE ===\n";