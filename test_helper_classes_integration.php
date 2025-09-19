<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use App\Models\CourseAuth;
use App\Services\StudentDashboardService;
use App\Classes\CourseAuthObj;

echo "=== TESTING HELPER CLASSES INTEGRATION ===\n\n";

try {
    // Find a user with course auths
    $user = User::whereHas('CourseAuths')->first();

    if (!$user) {
        echo "❌ No user found with course auths\n";
        exit;
    }

    echo "✓ Testing with User: {$user->fname} {$user->lname} (ID: {$user->id})\n\n";

    // Get a course auth for testing
    $courseAuth = $user->CourseAuths()->first();

    if (!$courseAuth) {
        echo "❌ No course auth found for user\n";
        exit;
    }

    echo "✓ Testing CourseAuth ID: {$courseAuth->id}\n";
    echo "✓ Course: {$courseAuth->Course->title}\n\n";

    // Test CourseAuthObj helper class directly
    echo "--- TESTING CourseAuthObj DIRECTLY ---\n";
    $courseAuthObj = new CourseAuthObj($courseAuth);
    $courseUnitObjs = $courseAuthObj->CourseUnitObjs();

    echo "✓ CourseAuthObj created successfully\n";
    echo "✓ CourseUnitObjs count: {$courseUnitObjs->count()}\n";

    if ($courseUnitObjs->count() > 0) {
        $firstUnitObj = $courseUnitObjs->first();
        $unit = $firstUnitObj->CourseUnit();
        $unitLessons = $firstUnitObj->CourseUnitLessons();

        echo "✓ First Unit: {$unit->title} (ID: {$unit->id})\n";
        echo "✓ Unit lessons count: {$unitLessons->count()}\n";

        if ($unitLessons->count() > 0) {
            $firstLesson = $unitLessons->first()->Lesson;
            if ($firstLesson) {
                echo "✓ First lesson: {$firstLesson->title}\n";
            }
        }
    }

    echo "\n--- TESTING StudentDashboardService WITH HELPER CLASSES ---\n";

    // Test refactored StudentDashboardService
    $dashboardService = new StudentDashboardService($user);
    $lessonsData = $dashboardService->getLessonsForCourse($courseAuth);

    echo "✓ Service created and lessons retrieved\n";
    echo "✓ Modality: {$lessonsData['modality']}\n";
    echo "✓ Total lessons: " . $lessonsData['lessons']->count() . "\n";
    echo "✓ Current day only: " . ($lessonsData['current_day_only'] ? 'Yes' : 'No') . "\n";

    if ($lessonsData['lessons']->count() > 0) {
        echo "✓ Completed lessons: " . $lessonsData['lessons']->where('is_completed', true)->count() . "\n";

        echo "\n--- SAMPLE LESSONS (first 3) ---\n";
        foreach ($lessonsData['lessons']->take(3) as $index => $lesson) {
            $status = $lesson['is_completed'] ? '✅ Completed' : '⏳ Not Completed';
            echo ($index + 1) . ". {$lesson['title']}\n";
            echo "   Unit: {$lesson['unit_title']}\n";
            echo "   Status: {$status}\n";
            echo "   Credit Minutes: {$lesson['credit_minutes']}\n\n";
        }
    }

    echo "=== INTEGRATION TEST COMPLETED SUCCESSFULLY ===\n";

} catch (Exception $e) {
    echo "❌ Error during integration test:\n";
    echo "   {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}\n";
    echo "   Line: {$e->getLine()}\n";

    if (str_contains($e->getMessage(), 'Class') && str_contains($e->getMessage(), 'not found')) {
        echo "\n💡 Make sure the CourseAuthObj and CourseUnitObj classes are properly loaded\n";
    }
}
