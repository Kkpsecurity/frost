<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\CourseAuth;
use App\Services\StudentDashboardService;

echo "Testing StudentDashboardService::getLessonsForCourse() method\n";
echo "=============================================================\n\n";

// Get a user with course auths
$user = User::whereHas('CourseAuths')->first();

if (!$user) {
    echo "❌ No user found with course authorizations\n";
    exit(1);
}

echo "✅ Found user: {$user->fname} {$user->lname} (ID: {$user->id})\n";

// Get user's course auths
$courseAuths = $user->CourseAuths()->with('Course')->get();
echo "✅ User has " . $courseAuths->count() . " course authorizations\n\n";

if ($courseAuths->count() === 0) {
    echo "❌ No course authorizations found\n";
    exit(1);
}

// Test the service
$service = new StudentDashboardService($user);

foreach ($courseAuths->take(3) as $courseAuth) { // Test first 3 only
    echo "Testing CourseAuth ID: {$courseAuth->id}\n";
    echo "Course: " . ($courseAuth->Course->title ?? 'Unknown') . "\n";
    echo "Course ID: {$courseAuth->course_id}\n";

    $lessonsData = $service->getLessonsForCourse($courseAuth);

    echo "Lessons returned: " . count($lessonsData['lessons'] ?? []) . "\n";
    echo "Modality: " . ($lessonsData['modality'] ?? 'unknown') . "\n";
    echo "Current day only: " . ($lessonsData['current_day_only'] ? 'Yes' : 'No') . "\n";

    if (!empty($lessonsData['lessons'])) {
        echo "Sample lessons:\n";
        $lessons = is_array($lessonsData['lessons']) ? $lessonsData['lessons'] : $lessonsData['lessons']->toArray();
        foreach (array_slice($lessons, 0, 3) as $lesson) {
            echo "  - {$lesson['title']} (Unit: {$lesson['unit_title']}, Completed: " .
                 ($lesson['is_completed'] ? 'Yes' : 'No') . ")\n";
        }
    } else {
        echo "❌ No lessons found for this course\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "Test completed!\n";
