<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== INST_UNIT STATUS FOR COURSE_DATE_ID 10757 ===\n\n";

// Check InstUnit for this course_date_id
$instUnit = DB::table('inst_unit')
    ->where('course_date_id', 10757)
    ->first();

if ($instUnit) {
    echo "⚠️ InstUnit already exists:\n";
    echo "  - InstUnit ID: {$instUnit->id}\n";
    echo "  - Course Date ID: {$instUnit->course_date_id}\n";
    echo "  - Created By: {$instUnit->created_by}\n";
    echo "  - Created At: {$instUnit->created_at}\n";
    echo "  - Completed At: " . ($instUnit->completed_at ?? 'NULL (ACTIVE)') . "\n";
    echo "  - Completed By: " . ($instUnit->completed_by ?? 'NULL') . "\n";
    echo "\n";
} else {
    echo "✅ No InstUnit found for course_date_id 10757\n\n";
}

// Check what CourseDate this is
$courseDate = DB::table('course_dates')
    ->join('course_units', 'course_dates.course_unit_id', '=', 'course_units.id')
    ->join('courses', 'course_units.course_id', '=', 'courses.id')
    ->where('course_dates.id', 10757)
    ->select('course_dates.*', 'course_units.title as unit_title', 'courses.title as course_title')
    ->first();

if ($courseDate) {
    echo "=== COURSE_DATE 10757 DETAILS ===\n\n";
    echo "Course: {$courseDate->course_title}\n";
    echo "Unit: {$courseDate->unit_title}\n";
    echo "Starts At: {$courseDate->starts_at}\n";
    echo "Ends At: {$courseDate->ends_at}\n";
    echo "Is Active: " . ($courseDate->is_active ? 'YES' : 'NO') . "\n";
    echo "\n";
}

// Check today's date
echo "=== TODAY'S INFO ===\n\n";
echo "Today: " . date('Y-m-d') . "\n";
echo "CourseDate 10757 is for: " . date('Y-m-d', strtotime($courseDate->starts_at)) . "\n";

if (date('Y-m-d', strtotime($courseDate->starts_at)) === date('Y-m-d')) {
    echo "✅ This IS today's CourseDate\n";
} else {
    echo "⚠️ This is NOT today's CourseDate\n";
}

echo "\n";
