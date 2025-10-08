<?php
/**
 * INVESTIGATE COURSEAUTH COUNT - Active vs Total
 * 
 * This script investigates the difference between total CourseAuths
 * and active CourseAuths that should be eligible for today's class.
 */

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== INVESTIGATING COURSEAUTH COUNT - ACTIVE vs TOTAL ===\n\n";

try {
    // Get today's course date
    $courseDate = \App\Models\CourseDate::with([
        'CourseUnit.Course.CourseAuths'
    ])->whereDate('starts_at', today())->first();
    
    if (!$courseDate) {
        echo "❌ No CourseDate found for today\n";
        exit(1);
    }
    
    $course = $courseDate->CourseUnit->Course;
    
    echo "📊 COURSE ANALYSIS:\n";
    echo "==================\n";
    echo "Course: {$course->title}\n";
    echo "CourseDate ID: {$courseDate->id}\n";
    echo "Date: " . \Carbon\Carbon::parse($courseDate->starts_at)->format('M j, Y g:i A') . "\n\n";
    
    // Get all CourseAuths for this course
    $allCourseAuths = $course->CourseAuths;
    echo "🔍 COURSEAUTH BREAKDOWN:\n";
    echo "========================\n";
    echo "Total CourseAuths: {$allCourseAuths->count()}\n\n";
    
    // Check CourseAuth status variations
    $statusGroups = $allCourseAuths->groupBy('status');
    echo "📋 BY STATUS:\n";
    foreach ($statusGroups as $status => $auths) {
        echo "- {$status}: {$auths->count()}\n";
    }
    echo "\n";
    
    // Check active CourseAuths only
    $activeCourseAuths = $allCourseAuths->where('status', 'active');
    echo "✅ ACTIVE COURSEAUTHS: {$activeCourseAuths->count()}\n\n";
    
    // Check for other filtering criteria
    $completedCourseAuths = $allCourseAuths->where('status', 'completed');
    $cancelledCourseAuths = $allCourseAuths->where('status', 'cancelled');
    $refundedCourseAuths = $allCourseAuths->where('status', 'refunded');
    
    echo "📊 DETAILED BREAKDOWN:\n";
    echo "=====================\n";
    echo "Active (eligible for class): {$activeCourseAuths->count()}\n";
    echo "Completed: {$completedCourseAuths->count()}\n";
    echo "Cancelled: {$cancelledCourseAuths->count()}\n";
    echo "Refunded: {$refundedCourseAuths->count()}\n\n";
    
    // Check if there are CourseAuths with progress that shouldn't be in this class
    echo "🎯 COURSE PROGRESS ANALYSIS:\n";
    echo "============================\n";
    
    $courseUnitDay = $courseDate->CourseUnit->day ?? 1;
    echo "This CourseDate is for Day: {$courseUnitDay}\n\n";
    
    // Check if we can determine which students should be in this specific class
    echo "💡 RECOMMENDATION FOR 'REGISTERED' COUNT:\n";
    echo "=========================================\n";
    echo "Current: {$allCourseAuths->count()} total (includes completed, cancelled, etc.)\n";
    echo "Better: {$activeCourseAuths->count()} active (only students who can attend)\n\n";
    
    if ($activeCourseAuths->count() != $allCourseAuths->count()) {
        echo "🎯 ISSUE IDENTIFIED:\n";
        echo "The 'registered' count should show ACTIVE CourseAuths only!\n";
        echo "- Total: {$allCourseAuths->count()} (includes all statuses)\n";
        echo "- Active: {$activeCourseAuths->count()} (eligible students only)\n";
        echo "- Difference: " . ($allCourseAuths->count() - $activeCourseAuths->count()) . " (completed/cancelled/refunded)\n\n";
        
        echo "🔧 SUGGESTED FIX:\n";
        echo "Change from: \$course->CourseAuths->count()\n";
        echo "Change to: \$course->CourseAuths->where('status', 'active')->count()\n\n";
    }
    
    // Additional check: Are there CourseAuths that have already completed this course unit?
    echo "📚 COURSE UNIT SPECIFIC ANALYSIS:\n";
    echo "=================================\n";
    
    // Check if students have StudentUnits for previous instances of this CourseUnit
    $previousStudentUnits = \App\Models\StudentUnit::whereHas('CourseDate', function($q) use ($courseDate) {
        $q->where('course_unit_id', $courseDate->course_unit_id)
          ->where('id', '!=', $courseDate->id); // Different CourseDate but same CourseUnit
    })->with('CourseAuth')->get();
    
    if ($previousStudentUnits->count() > 0) {
        echo "Students who attended this CourseUnit before: {$previousStudentUnits->count()}\n";
        echo "These students might not need to attend this specific class again.\n\n";
    }
    
    echo "🎯 FINAL RECOMMENDATION:\n";
    echo "========================\n";
    echo "For Course Dates Management 'registered' count, use:\n";
    echo "✅ Active CourseAuths: {$activeCourseAuths->count()}\n";
    echo "❌ NOT Total CourseAuths: {$allCourseAuths->count()}\n\n";
    
    echo "This gives instructors the accurate count of students who are\n";
    echo "eligible and able to attend this specific class session.\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}

echo "=== INVESTIGATION COMPLETE ===\n";