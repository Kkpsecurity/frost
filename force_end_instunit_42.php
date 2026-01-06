<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$instUnit = App\Models\InstUnit::find(42);
if ($instUnit) {
    echo 'Found InstUnit 42:' . PHP_EOL;
    echo '- ID: ' . $instUnit->id . PHP_EOL;
    echo '- Course Date ID: ' . $instUnit->course_date_id . PHP_EOL;
    echo '- Created by: ' . $instUnit->created_by . PHP_EOL;
    echo '- Created at: ' . $instUnit->created_at . PHP_EOL;
    echo '- Completed at: ' . ($instUnit->completed_at ?: 'NULL (still active)') . PHP_EOL;

    // Get the course date info
    $courseDate = $instUnit->courseDate;
    if ($courseDate) {
        echo '- CourseDate starts: ' . $courseDate->starts_at . PHP_EOL;
        echo '- CourseDate ends: ' . $courseDate->ends_at . PHP_EOL;
    }

    // Force end this InstUnit
    echo PHP_EOL . 'Force-ending InstUnit 42...' . PHP_EOL;
    $instUnit->completed_at = now();
    $instUnit->completed_by = $instUnit->created_by; // Mark as completed by same instructor
    $instUnit->save();

    echo '✅ InstUnit 42 has been force-ended!' . PHP_EOL;
} else {
    echo '❌ InstUnit 42 not found' . PHP_EOL;
}
