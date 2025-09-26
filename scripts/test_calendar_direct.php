<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🗓️ Testing Calendar Data Directly\n";
echo "=================================\n\n";

use App\Http\Controllers\Web\Courses\CourseController;
use App\Classes\MiscQueries;

try {
    // Test 1: Direct MiscQueries call with courses
    echo "📊 Testing MiscQueries::CalenderDates()...\n";
    $activeCourses = \App\Services\RCache::Courses()->where('is_active', true);
    echo "   Active courses found: " . count($activeCourses) . "\n";

    $totalEvents = 0;
    foreach ($activeCourses as $course) {
        $calendarData = MiscQueries::CalenderDates($course);
        $courseEvents = count($calendarData);
        $totalEvents += $courseEvents;
        echo "   • Course '{$course->title}': {$courseEvents} events\n";

        if ($courseEvents > 0 && $totalEvents <= 3) {
            foreach (array_slice($calendarData->toArray(), 0, 2) as $event) {
                echo "     - Event ID {$event['id']}: {$event['starts_at']} to {$event['ends_at']}\n";
            }
        }
    }

    echo "   Total calendar events: {$totalEvents}\n";

    // Test 2: Controller method call
    echo "\n🎯 Testing CourseController::getScheduleData()...\n";
    $controller = new CourseController();

    // Create a mock request
    $request = new \Illuminate\Http\Request();

    $response = $controller->getScheduleData($request);
    $responseData = $response->getData(true);

    echo "   Response status: " . $response->getStatusCode() . "\n";
    echo "   Response type: " . gettype($responseData) . "\n";

    if (is_array($responseData)) {
        echo "   Response count: " . count($responseData) . "\n";

        if (!empty($responseData)) {
            echo "\n📋 First few response items:\n";
            foreach (array_slice($responseData, 0, 3) as $item) {
                if (is_array($item) || is_object($item)) {
                    $itemArray = is_object($item) ? (array)$item : $item;
                    echo "   • Item keys: " . implode(', ', array_keys($itemArray)) . "\n";

                    if (isset($itemArray['title'])) {
                        echo "     Title: " . $itemArray['title'] . "\n";
                    }
                    if (isset($itemArray['start'])) {
                        echo "     Start: " . $itemArray['start'] . "\n";
                    }
                }
            }
        }
    } else {
        echo "   Response data: " . substr(json_encode($responseData), 0, 200) . "...\n";
    }

    echo "\n✅ Calendar data test complete!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
    exit(1);
}
