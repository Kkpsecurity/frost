<?php
/**
 * Test script to verify ClassroomService returns buttons property
 * Run: php test-classroom-buttons.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Authenticate as admin - just get first user
$admin = \App\Models\User::first();

if (!$admin) {
    echo "❌ No users found\n";
    exit(1);
}

auth('admin')->login($admin);

echo "✅ Authenticated as admin: {$admin->email}\n\n";

// Get ClassroomService
$classroomService = app(\App\Services\Frost\Instructors\ClassroomService::class);

echo "📡 Calling ClassroomService::getClassroomData()...\n\n";

$data = $classroomService->getClassroomData();

echo "✅ Data received!\n";
echo "📊 Course Dates Count: " . count($data['courseDates'] ?? []) . "\n\n";

if (!empty($data['courseDates'])) {
    $firstCourse = $data['courseDates'][0];
    echo "🎯 First Course Date:\n";
    echo "   ID: {$firstCourse['id']}\n";
    echo "   Course: {$firstCourse['course_name']}\n";
    echo "   Status: {$firstCourse['class_status']}\n";
    echo "   Time: {$firstCourse['time']}\n";

    if (isset($firstCourse['buttons'])) {
        echo "   ✅ BUTTONS PROPERTY EXISTS!\n";
        echo "   Buttons: " . json_encode($firstCourse['buttons'], JSON_PRETTY_PRINT) . "\n";

        if (isset($firstCourse['buttons']['start_class'])) {
            echo "   ✅✅✅ START CLASS BUTTON FOUND!\n";
        } else {
            echo "   ⚠️ No start_class button (buttons: " . implode(', ', array_keys($firstCourse['buttons'])) . ")\n";
        }
    } else {
        echo "   ❌ NO BUTTONS PROPERTY! This is the bug!\n";
    }

    echo "\n📋 Full first course data:\n";
    echo json_encode($firstCourse, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "⚠️ No course dates found for today\n";
    echo "💡 Try running: php artisan course:generate-dates\n";
}

echo "\n✅ Test complete!\n";
