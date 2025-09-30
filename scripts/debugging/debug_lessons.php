<?php
// Quick debug script to check what's happening with lessons
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUG LESSONS TODAY ===\n";
echo "Today: " . now()->format('Y-m-d H:i:s') . "\n\n";

// The service now returns empty results intentionally
$today = now()->format('Y-m-d');
echo "Service configured to return empty results for clean dashboard\n\n";

$courseDates = collect(); // Empty collection

foreach ($courseDates as $courseDate) {
    echo "=== COURSE DATE {$courseDate->id} ===\n";
    echo "Start Time: {$courseDate->starts_at}\n";
    echo "End Time: {$courseDate->ends_at}\n";

    $instUnit = $courseDate->InstUnit;
    echo "InstUnit exists: " . ($instUnit ? 'YES' : 'NO') . "\n";

    if ($instUnit) {
        echo "InstUnit ID: {$instUnit->id}\n";
        echo "Created By: {$instUnit->created_by}\n";
        echo "Completed At: " . ($instUnit->completed_at ?? 'NULL') . "\n";

        $instructor = $instUnit->GetCreatedBy();
        if ($instructor) {
            echo "Instructor: {$instructor->fname} {$instructor->lname}\n";
        } else {
            echo "Instructor: NOT FOUND\n";
        }
    }
    echo "\n";
}

// Test the service directly
echo "=== SERVICE OUTPUT ===\n";
$service = new \App\Services\Frost\Instructors\CourseDatesService();
$result = $service->getTodaysLessons();

echo "Lessons count: " . count($result['lessons']) . "\n";
echo "Message: {$result['message']}\n";

foreach ($result['lessons'] as $lesson) {
    echo "\n--- LESSON {$lesson['id']} ---\n";
    echo "Class Status: {$lesson['class_status']}\n";
    echo "Instructor: " . ($lesson['instructor_name'] ?? 'NULL') . "\n";
    echo "Buttons: " . json_encode($lesson['buttons']) . "\n";
    echo "InstUnit: " . ($lesson['inst_unit'] ? 'EXISTS' : 'NULL') . "\n";
}
