<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Fixing CourseDate day number based on date...\n\n";

// Get the CourseDate we created
$courseDate = DB::table('course_dates')
    ->join('course_units', 'course_dates.course_unit_id', '=', 'course_units.id')
    ->join('courses', 'course_units.course_id', '=', 'courses.id')
    ->where('course_dates.id', 10552)
    ->first(['course_dates.*', 'course_units.ordering', 'course_units.title as unit_title', 'courses.title as course_title', 'courses.id as course_id']);

if (!$courseDate) {
    echo "CourseDate not found!\n";
    exit;
}

echo "Current CourseDate:\n";
echo "ID: {$courseDate->id}\n";
echo "Course: {$courseDate->course_title}\n";
echo "Current Unit: {$courseDate->unit_title} (ordering: {$courseDate->ordering})\n";
echo "Date: {$courseDate->starts_at}\n\n";

// Calculate the day number based on the date
// Friday = day 5 of the week (Monday=1, Tuesday=2, Wednesday=3, Thursday=4, Friday=5)
$date = new DateTime($courseDate->starts_at);
$dayOfWeek = (int)$date->format('N'); // 1=Monday, 2=Tuesday, ..., 5=Friday

echo "Date analysis:\n";
echo "Day of week: {$dayOfWeek} (" . $date->format('l') . ")\n";
echo "Should be Day {$dayOfWeek}\n\n";

// Find the correct CourseUnit with the right ordering
$correctUnit = DB::table('course_units')
    ->where('course_id', $courseDate->course_id)
    ->where('ordering', $dayOfWeek)
    ->where('title', 'like', '%Day%') // Make sure we get the day version, not night
    ->first(['id', 'ordering', 'title', 'admin_title']);

if (!$correctUnit) {
    echo "Could not find CourseUnit with ordering {$dayOfWeek} for this course!\n";
    exit;
}

echo "Found correct CourseUnit:\n";
echo "ID: {$correctUnit->id}\n";
echo "Ordering: {$correctUnit->ordering}\n";
echo "Title: {$correctUnit->title}\n";
echo "Admin Title: {$correctUnit->admin_title}\n\n";

// Update the CourseDate
echo "Updating CourseDate to use correct unit...\n";
$updated = DB::table('course_dates')
    ->where('id', 10552)
    ->update(['course_unit_id' => $correctUnit->id]);

if ($updated) {
    echo "✅ Successfully updated CourseDate!\n\n";

    // Verify the update
    echo "Verification:\n";
    $updatedCourseDate = DB::table('course_dates')
        ->join('course_units', 'course_dates.course_unit_id', '=', 'course_units.id')
        ->where('course_dates.id', 10552)
        ->first(['course_dates.id', 'course_units.ordering', 'course_units.title', 'course_units.admin_title']);

    echo "CourseDate {$updatedCourseDate->id} now shows: {$updatedCourseDate->title} (ordering: {$updatedCourseDate->ordering})\n";
} else {
    echo "❌ Failed to update CourseDate\n";
}
