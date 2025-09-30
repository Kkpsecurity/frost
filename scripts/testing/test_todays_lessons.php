<?php

/**
 * Test Script: Check Today's Lessons API Endpoint
 *
 * This script tests the instructor dashboard API endpoint to verify
 * that the InstUnit data is properly returned in the lessons.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Today's Lessons API Test ===\n\n";

try {
    // Set up test user (instructor)
    $instructorId = 1; // Chris Jones
    $instructor = User::find($instructorId);

    if (!$instructor) {
        echo "ERROR: Instructor user not found!\n";
        exit(1);
    }

    echo "✓ Found instructor: {$instructor->fullname()}\n";

    // Set authenticated user
    Auth::login($instructor);
    echo "✓ Authenticated as instructor: {$instructor->fullname()}\n";

    // Create the service and get today's lessons
    $service = new \App\Services\Frost\Instructors\CourseDatesService();
    $lessonsData = $service->getTodaysLessons();

    echo "\n--- Today's Lessons Data ---\n";
    echo "Message: " . $lessonsData['message'] . "\n";
    echo "Has Lessons: " . ($lessonsData['has_lessons'] ? 'YES' : 'NO') . "\n";
    echo "Lesson Count: " . count($lessonsData['lessons']) . "\n";

    if (!empty($lessonsData['lessons'])) {
        echo "\n--- Lesson Details ---\n";
        foreach ($lessonsData['lessons'] as $index => $lesson) {
            echo "#{$index + 1}: {$lesson['course_name']} - {$lesson['lesson_name']}\n";
            echo "  - ID: {$lesson['id']}\n";
            echo "  - Time: {$lesson['time']}\n";
            echo "  - Status: {$lesson['class_status']}\n";
            echo "  - Instructor: " . ($lesson['instructor_name'] ?? 'Not Assigned') . "\n";
            echo "  - Assistant: " . ($lesson['assistant_name'] ?? 'None') . "\n";
            echo "  - Student Count: {$lesson['student_count']}\n";

            if (isset($lesson['buttons']) && !empty($lesson['buttons'])) {
                echo "  - Available Actions: " . implode(', ', array_keys($lesson['buttons'])) . "\n";
            }

            if (isset($lesson['inst_unit']) && $lesson['inst_unit']) {
                echo "  - InstUnit ID: {$lesson['inst_unit']['id']}\n";
                echo "  - InstUnit Created: {$lesson['inst_unit']['created_at']}\n";
                echo "  - InstUnit Completed: " . ($lesson['inst_unit']['completed_at'] ?? 'Not Completed') . "\n";
            } else {
                echo "  - InstUnit: None\n";
            }
            echo "\n";
        }
    }

    // Show assignment history summary
    if (!empty($lessonsData['assignment_history'])) {
        echo "--- Assignment History Summary ---\n";
        echo "Recent assignments: " . count($lessonsData['assignment_history']) . "\n";

        foreach (array_slice($lessonsData['assignment_history'], 0, 3) as $history) {
            echo "  - {$history['date']} {$history['time']}: {$history['course_name']} ({$history['assignment_status']})\n";
        }
    }

} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
