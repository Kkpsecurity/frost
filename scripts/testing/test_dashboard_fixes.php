<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Dashboard Service Fixes ===\n\n";

// Test the CourseDatesService fixes
use App\Services\Frost\Instructors\CourseDatesService;

$courseDateService = new CourseDatesService();

// Test getTodaysLessons method which contains our fixes
echo "=== Testing Today's Lessons (contains our fixes) ===\n";
$todaysLessons = $courseDateService->getTodaysLessons();

if (isset($todaysLessons['lessons']) && count($todaysLessons['lessons']) > 0) {
    $firstLesson = $todaysLessons['lessons'][0];

    echo "First Lesson Data:\n";
    echo "- Course Name: " . ($firstLesson['course_name'] ?? 'missing') . "\n";
    echo "- Module: " . ($firstLesson['module'] ?? 'missing') . "\n";
    echo "- Class Status: " . ($firstLesson['class_status'] ?? 'missing') . "\n";
    echo "- Instructor Name: " . ($firstLesson['instructor_name'] ?? 'null') . "\n";
    echo "- InstUnit Present: " . ($firstLesson['inst_unit'] ? 'Yes' : 'No') . "\n\n";

    // Test Module Fix
    if (isset($firstLesson['module']) && $firstLesson['module'] !== 'Module N/A' && !str_contains($firstLesson['module'], 'N/A')) {
        echo "✅ Module Display Fix: Working - showing '{$firstLesson['module']}'\n";
    } else {
        echo "❌ Module Display Fix: Still showing N/A or missing\n";
    }

    // Test Instructor Logic Fix
    $hasInstructor = !empty($firstLesson['instructor_name']);
    $hasInstUnit = !empty($firstLesson['inst_unit']);
    $isUnassigned = $firstLesson['class_status'] === 'unassigned';

    echo "\n=== Instructor Logic Consistency ===\n";
    echo "Has Instructor Name: " . ($hasInstructor ? 'Yes' : 'No') . "\n";
    echo "Has InstUnit: " . ($hasInstUnit ? 'Yes' : 'No') . "\n";
    echo "Status is Unassigned: " . ($isUnassigned ? 'Yes' : 'No') . "\n";

    if ($hasInstructor && $isUnassigned) {
        echo "❌ Instructor Logic Bug: Has instructor but status is unassigned (stale data)\n";
    } elseif ($hasInstUnit && $isUnassigned && $hasInstructor) {
        echo "❌ InstUnit Logic Bug: InstUnit exists with instructor but status is unassigned\n";
    } else {
        echo "✅ Instructor Assignment Logic: Consistent\n";
    }

    // Test additional courses if available
    if (count($todaysLessons['lessons']) > 1) {
        echo "\n=== Testing Additional Courses ===\n";
        $inconsistentCount = 0;
        $moduleFixCount = 0;

        foreach ($todaysLessons['lessons'] as $lesson) {
            // Count module fixes
            if (isset($lesson['module']) && $lesson['module'] !== 'Module N/A' && !str_contains($lesson['module'], 'N/A')) {
                $moduleFixCount++;
            }

            // Count logic inconsistencies
            $hasInstructorName = !empty($lesson['instructor_name']);
            $isUnassigned = $lesson['class_status'] === 'unassigned';
            if ($hasInstructorName && $isUnassigned) {
                $inconsistentCount++;
            }
        }

        $totalLessons = count($todaysLessons['lessons']);
        echo "Module fixes working: {$moduleFixCount}/{$totalLessons} courses\n";
        echo "Logic inconsistencies found: {$inconsistentCount}/{$totalLessons} courses\n";

        if ($moduleFixCount === $totalLessons) {
            echo "✅ All modules display properly\n";
        } else {
            echo "⚠️ Some modules still showing N/A\n";
        }

        if ($inconsistentCount === 0) {
            echo "✅ All instructor logic is consistent\n";
        } else {
            echo "❌ Some instructor logic inconsistencies remain\n";
        }
    }

} else {
    echo "⚠️ No lessons found for today\n";
    echo "Trying a different approach...\n\n";

    // Get bulletin board data instead
    $bulletinData = $courseDateService->getBulletinBoardData();
    if (isset($bulletinData['upcoming_courses']) && count($bulletinData['upcoming_courses']) > 0) {
        echo "=== Testing Bulletin Board Data ===\n";
        $firstCourse = $bulletinData['upcoming_courses'][0];
        echo "First Course Module: " . ($firstCourse['module'] ?? 'missing') . "\n";

        if (isset($firstCourse['module']) && $firstCourse['module'] !== 'Module N/A') {
            echo "✅ Module fix working in bulletin board\n";
        } else {
            echo "❌ Module fix not working in bulletin board\n";
        }
    }
}

echo "\n=== Test Complete ===\n";
