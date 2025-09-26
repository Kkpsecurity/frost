<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\Frost\Instructors\CourseDatesService;

echo "Testing Assignment History\n\n";

$service = new CourseDatesService();
$history = $service->getCourseDateAssignmentHistory();

echo "Found " . count($history) . " records in assignment history:\n\n";

foreach ($history as $record) {
    echo "📅 {$record['date']} - {$record['time']}\n";
    echo "   Course: {$record['course_name']}\n";
    echo "   Unit: {$record['day_number']} ({$record['unit_code']})\n";
    echo "   Status: " . strtoupper($record['assignment_status']) . "\n";
    if ($record['instructor']) {
        echo "   Instructor: {$record['instructor']}\n";
    }
    if ($record['assigned_at']) {
        echo "   Assigned: {$record['assigned_at']}\n";
    }
    if ($record['completed_at']) {
        echo "   Completed: {$record['completed_at']}\n";
    }
    echo "\n";
}

echo "🎯 This data should populate the Assignment History Table!\n";