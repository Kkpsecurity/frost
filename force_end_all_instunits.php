<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Force-ending all InstUnits for instructor ID 2 ===" . PHP_EOL;

$activeInstUnits = App\Models\InstUnit::where('created_by', 2)
    ->whereNull('completed_at')
    ->get();

echo "Found {$activeInstUnits->count()} active InstUnits to force-end:" . PHP_EOL;

foreach($activeInstUnits as $unit) {
    echo "- Force-ending InstUnit {$unit->id} (CourseDate {$unit->course_date_id})..." . PHP_EOL;

    $unit->completed_at = now();
    $unit->completed_by = 2; // Mark as completed by instructor
    $unit->save();

    echo "  ✅ InstUnit {$unit->id} force-ended!" . PHP_EOL;
}

echo PHP_EOL . "=== Final Status Check ===" . PHP_EOL;
$remainingActive = App\Models\InstUnit::where('created_by', 2)
    ->whereNull('completed_at')
    ->count();

echo "Remaining active InstUnits for instructor 2: {$remainingActive}" . PHP_EOL;

if ($remainingActive === 0) {
    echo "✅ SUCCESS: All InstUnits force-ended. Instructor should now see BulletinBoard!" . PHP_EOL;
} else {
    echo "❌ PROBLEM: Some InstUnits still active!" . PHP_EOL;
}
