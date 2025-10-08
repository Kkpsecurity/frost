<?php
/**
 * TEST ACTIVE COURSEAUTH COUNT FIX
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING ACTIVE COURSEAUTH COUNT FIX ===\n\n";

try {
    $courseDate = \App\Models\CourseDate::with(['CourseUnit.Course.CourseAuths', 'InstUnit', 'StudentUnits'])
        ->whereDate('starts_at', today())->first();
    
    $course = $courseDate->CourseUnit->Course;
    
    // OLD METHOD (what was showing 9,908)
    $totalCourseAuths = $course->CourseAuths->count();
    
    // NEW METHOD (active/non-completed only)
    $activeCourseAuths = $course->CourseAuths->whereNull('completed_at')->count();
    $completedCourseAuths = $course->CourseAuths->whereNotNull('completed_at')->count();
    
    // Attendance count
    $hasStarted = $courseDate->InstUnit !== null;
    $actualAttending = $hasStarted ? $courseDate->StudentUnits->count() : 0;
    
    echo "📊 COURSEAUTH COUNT COMPARISON:\n";
    echo "===============================\n";
    echo "Course: {$course->title}\n";
    echo "Date: " . \Carbon\Carbon::parse($courseDate->starts_at)->format('M j, Y g:i A') . "\n\n";
    
    echo "OLD COUNT (was showing):\n";
    echo "- Total CourseAuths: {$totalCourseAuths}\n\n";
    
    echo "NEW COUNT (will show):\n";
    echo "- Active CourseAuths (not completed): {$activeCourseAuths}\n";
    echo "- Completed CourseAuths: {$completedCourseAuths}\n";
    echo "- Total: " . ($activeCourseAuths + $completedCourseAuths) . "\n\n";
    
    echo "ATTENDANCE COUNT:\n";
    echo "- Class Started: " . ($hasStarted ? 'YES' : 'NO') . "\n";
    echo "- Students Attending: {$actualAttending}\n\n";
    
    echo "🎯 UI WILL NOW SHOW:\n";
    echo "====================\n";
    echo "Badge 1: [{$activeCourseAuths} registered] (blue - students eligible for class)\n";
    echo "Badge 2: [{$actualAttending} attending] (gray - class not started yet)\n\n";
    
    echo "✅ IMPROVEMENT:\n";
    echo "===============\n";
    echo "BEFORE: {$totalCourseAuths} registered (included completed students)\n";
    echo "AFTER:  {$activeCourseAuths} registered (only active/eligible students)\n";
    echo "DIFFERENCE: " . ($totalCourseAuths - $activeCourseAuths) . " completed students removed from count\n\n";
    
    if ($activeCourseAuths > 0) {
        echo "📈 ACTIVE STUDENT ANALYSIS:\n";
        echo "==========================\n";
        $activeAuths = $course->CourseAuths->whereNull('completed_at')->take(5);
        echo "Sample active CourseAuths:\n";
        foreach ($activeAuths as $i => $auth) {
            $user = \App\Models\User::find($auth->user_id);
            $userName = $user ? "{$user->fname} {$user->lname}" : "User {$auth->user_id}";
            echo "- {$userName} (enrolled: " . \Carbon\Carbon::createFromTimestamp($auth->created_at)->format('M j, Y') . ")\n";
        }
        echo "\n";
    }
    
    echo "🎯 SUMMARY:\n";
    echo "===========\n";
    echo "This count now represents students who:\n";
    echo "✅ Have enrolled in the course (CourseAuth exists)\n";
    echo "✅ Have NOT completed the course (completed_at is null)\n";
    echo "✅ Are eligible to attend today's class\n";
    echo "❌ Does NOT include students who already completed the course\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "=== TEST COMPLETE ===\n";