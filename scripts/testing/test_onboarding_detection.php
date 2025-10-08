<?php
/**
 * Quick test script to check onboarding detection
 * Run: php test_onboarding_detection.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ONBOARDING DETECTION TEST ===\n\n";

// Check CourseDate records
$today = \Carbon\Carbon::today();
echo "Today: " . $today->toDateString() . "\n";

$totalCourseDates = \App\Models\CourseDate::count();
echo "Total CourseDate records: " . $totalCourseDates . "\n";

$activeCourseDate = \App\Models\CourseDate::whereDate('starts_at', '<=', $today)
    ->whereDate('ends_at', '>=', $today)
    ->first();

echo "Active CourseDate for today: " . ($activeCourseDate ? "ID {$activeCourseDate->id}" : "None") . "\n";

if ($activeCourseDate) {
    echo "  - Starts At: " . $activeCourseDate->starts_at . "\n";
    echo "  - Ends At: " . $activeCourseDate->ends_at . "\n";
    echo "  - Course ID: " . $activeCourseDate->course_id . "\n";
}

// Check InstUnit records
$activeInstUnit = null;
if ($activeCourseDate) {
    $activeInstUnit = \App\Models\InstUnit::where('course_date_id', $activeCourseDate->id)
        ->whereNull('ended_at')
        ->first();
    
    echo "Active InstUnit: " . ($activeInstUnit ? "ID {$activeInstUnit->id}" : "None") . "\n";
}

// Check users
$userCount = \App\Models\User::count();
echo "Total users: " . $userCount . "\n";

if ($userCount > 0) {
    $testUser = \App\Models\User::first();
    echo "Test user: " . $testUser->email . " (ID: {$testUser->id})\n";
    
    // Check CourseAuths for this user
    $courseAuths = $testUser->CourseAuths()->count();
    echo "User's CourseAuths: " . $courseAuths . "\n";
    
    if ($activeCourseDate && $courseAuths > 0) {
        // Check if user has CourseAuth for this course
        $userCourseAuth = $activeCourseDate->Course?->CourseAuths()->where('user_id', $testUser->id)->first();
        echo "User has CourseAuth for active course: " . ($userCourseAuth ? "Yes (ID {$userCourseAuth->id})" : "No") . "\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";