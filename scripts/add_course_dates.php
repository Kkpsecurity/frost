<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ADDING COURSE DATES FOR SEPTEMBER 2025 ===\n\n";

// Create D40 (Armed) course dates - 5-day course starting Sept 23rd
echo "Creating D40 (Armed) course - Sept 23-27, 2025:\n";
$d40StartDate = '2025-09-23';
for ($day = 1; $day <= 5; $day++) {
    $date = date('Y-m-d', strtotime($d40StartDate . ' +' . ($day - 1) . ' days'));
    $startTime = $date . ' 08:00:00';
    $endTime = $date . ' 17:00:00';

    $courseDate = App\Models\CourseDate::create([
        'is_active' => true,
        'course_unit_id' => $day, // Course units 1-5 for D40 days 1-5
        'starts_at' => $startTime,
        'ends_at' => $endTime,
    ]);

    echo "- Day {$day}: {$date} 8:00 AM - 5:00 PM (ID: {$courseDate->id})\n";
}

echo "\nCreating G28 (Unarmed) course - Sept 9-11, 2025:\n";
$g28StartDate = '2025-09-09';
for ($day = 1; $day <= 3; $day++) {
    $date = date('Y-m-d', strtotime($g28StartDate . ' +' . ($day - 1) . ' days'));
    $startTime = $date . ' 09:00:00';
    $endTime = $date . ' 16:00:00';

    $courseDate = App\Models\CourseDate::create([
        'is_active' => true,
        'course_unit_id' => 15 + $day, // Course units 16-18 for G28 days 1-3
        'starts_at' => $startTime,
        'ends_at' => $endTime,
    ]);

    echo "- Day {$day}: {$date} 9:00 AM - 4:00 PM (ID: {$courseDate->id})\n";
}

echo "\nCreating another G28 (Unarmed) course - Sept 30 - Oct 2, 2025:\n";
$g28StartDate2 = '2025-09-30';
for ($day = 1; $day <= 3; $day++) {
    $date = date('Y-m-d', strtotime($g28StartDate2 . ' +' . ($day - 1) . ' days'));
    $startTime = $date . ' 09:00:00';
    $endTime = $date . ' 16:00:00';

    $courseDate = App\Models\CourseDate::create([
        'is_active' => true,
        'course_unit_id' => 15 + $day, // Course units 16-18 for G28 days 1-3
        'starts_at' => $startTime,
        'ends_at' => $endTime,
    ]);

    echo "- Day {$day}: {$date} 9:00 AM - 4:00 PM (ID: {$courseDate->id})\n";
}

echo "\n=== VERIFYING CREATED COURSE DATES ===\n";
$courseDates = App\Models\CourseDate::where('is_active', true)
    ->where('starts_at', '>=', '2025-09-01')
    ->where('starts_at', '<', '2025-11-01')
    ->orderBy('starts_at')
    ->get();

echo "Total course dates created: " . $courseDates->count() . "\n\n";

foreach ($courseDates as $date) {
    $courseUnit = App\Models\CourseUnit::find($date->course_unit_id);
    $course = App\Models\Course::find($courseUnit->course_id);
    echo "- {$date->starts_at} to {$date->ends_at} | {$course->title} - {$courseUnit->title}\n";
}

echo "\n=== COURSE DATES ADDED SUCCESSFULLY ===\n";
