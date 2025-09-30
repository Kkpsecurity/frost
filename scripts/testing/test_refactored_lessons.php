<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\User;
use App\Models\CourseAuth;
use App\Services\StudentDashboardService;
use App\Classes\CourseAuthObj;

echo "=== Testing Refactored StudentDashboardService with Helper Classes ===\n\n";

try {
    // Get a test user with course auths
    $user = User::whereHas('ActiveCourseAuths')->first();

    if (!$user) {
        echo "No user found with active course auths\n";
        exit;
    }

    echo "Testing User: {$user->fname} {$user->lname} (ID: {$user->id})\n\n";

    // Create service instance
    $service = new StudentDashboardService($user);

    // Get first course auth
    $courseAuth = $user->ActiveCourseAuths()->first();

    if (!$courseAuth) {
        echo "No active course auth found for user\n";
        exit;
    }

    echo "Testing CourseAuth ID: {$courseAuth->id}\n";
    echo "Course: {$courseAuth->Course->title}\n\n";

    // Test the refactored method using helper classes
    echo "=== REFACTORED METHOD (Using CourseAuthObj and CourseUnitObj) ===\n";
    $startTime = microtime(true);

    $lessonsResult = $service->getLessonsForCourse($courseAuth);

    $endTime = microtime(true);
    $executionTime = round(($endTime - $startTime) * 1000, 2);

    echo "Execution Time: {$executionTime}ms\n";
    echo "Lessons returned: " . $lessonsResult['lessons']->count() . "\n";
    echo "Modality: " . $lessonsResult['modality'] . "\n";
    echo "Current day only: " . ($lessonsResult['current_day_only'] ? 'Yes' : 'No') . "\n\n";

    if ($lessonsResult['lessons']->count() > 0) {
        echo "=== SAMPLE LESSONS (First 3) ===\n";
        foreach ($lessonsResult['lessons']->take(3) as $lesson) {
            echo "- {$lesson['title']} (Unit: {$lesson['unit_title']}, Completed: " .
                 ($lesson['is_completed'] ? 'Yes' : 'No') . ")\n";
        }
        echo "\n";

        // Show completion statistics
        $completedCount = $lessonsResult['lessons']->where('is_completed', true)->count();
        $totalCount = $lessonsResult['lessons']->count();
        $completionPercentage = $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 1) : 0;

        echo "=== COMPLETION STATISTICS ===\n";
        echo "Completed: {$completedCount}/{$totalCount} ({$completionPercentage}%)\n";
        echo "Credit Minutes Total: " . $lessonsResult['lessons']->sum('credit_minutes') . "\n\n";
    }

    // Test CourseAuthObj helper class directly
    echo "=== TESTING CourseAuthObj Helper Class Directly ===\n";
    $courseAuthObj = new CourseAuthObj($courseAuth);
    $courseUnitObjs = $courseAuthObj->CourseUnitObjs();

    echo "CourseUnitObjs count: " . $courseUnitObjs->count() . "\n";

    if ($courseUnitObjs->count() > 0) {
        echo "Sample CourseUnit: " . $courseUnitObjs->first()->CourseUnit()->title . "\n";
        echo "CourseUnitLessons in first unit: " . $courseUnitObjs->first()->CourseUnitLessons()->count() . "\n";
    }


} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
