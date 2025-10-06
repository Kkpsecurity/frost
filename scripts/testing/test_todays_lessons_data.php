<?php

/**
 * Test Today's Lessons API Data
 * 
 * This script tests the actual data returned by the CourseDatesService
 * to verify what the React component should receive.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Today's Lessons Data Structure\n";
echo "=========================================\n\n";

try {
    // Create the service directly
    $courseDatesService = app(\App\Services\Frost\Instructors\CourseDatesService::class);
    
    echo "📅 Testing getTodaysLessons() method:\n";
    $result = $courseDatesService->getTodaysLessons();
    
    echo "✅ Service method executed successfully\n\n";
    
    // Show the structure
    echo "📊 Data Structure:\n";
    echo "- lessons count: " . count($result['lessons'] ?? []) . "\n";
    echo "- has_lessons: " . ($result['has_lessons'] ? 'true' : 'false') . "\n";
    echo "- message: " . ($result['message'] ?? 'none') . "\n";
    echo "- assignment_history count: " . count($result['assignment_history'] ?? []) . "\n\n";
    
    if (!empty($result['lessons'])) {
        echo "🎯 Sample Lesson Data:\n";
        $firstLesson = $result['lessons'][0];
        echo "ID: " . ($firstLesson['id'] ?? 'N/A') . "\n";
        echo "Course Name: " . ($firstLesson['course_name'] ?? 'N/A') . "\n";
        echo "Lesson Name: " . ($firstLesson['lesson_name'] ?? 'N/A') . "\n";
        echo "Time: " . ($firstLesson['time'] ?? 'N/A') . "\n";
        echo "Class Status: " . ($firstLesson['class_status'] ?? 'NULL/UNDEFINED') . "\n";
        echo "Student Count: " . ($firstLesson['student_count'] ?? 0) . "\n";
        echo "Lesson Count: " . ($firstLesson['lesson_count'] ?? 0) . "\n";
        echo "Instructor: " . ($firstLesson['instructor_name'] ?? 'NULL') . "\n";
        echo "Assistant: " . ($firstLesson['assistant_name'] ?? 'NULL') . "\n";
        echo "Buttons: " . json_encode($firstLesson['buttons'] ?? []) . "\n";
        echo "InstUnit: " . ($firstLesson['inst_unit'] ? 'EXISTS' : 'NULL') . "\n\n";
        
        echo "🔍 All Lessons:\n";
        foreach ($result['lessons'] as $i => $lesson) {
            printf("  %d. %s - %s (%s) - Status: %s\n", 
                $i + 1,
                $lesson['course_name'] ?? 'N/A',
                $lesson['lesson_name'] ?? 'N/A', 
                $lesson['time'] ?? 'N/A',
                $lesson['class_status'] ?? 'UNDEFINED'
            );
        }
    } else {
        echo "❌ No lessons found for today\n";
    }
    
    echo "\n📋 Full JSON Response:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}