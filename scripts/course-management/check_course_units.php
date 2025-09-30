<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "CourseUnits for D License:\n";

$units = DB::table('course_units')
    ->join('courses', 'course_units.course_id', '=', 'courses.id')
    ->where('courses.title', 'like', '%D%')
    ->orderBy('course_units.ordering')
    ->get(['course_units.id', 'course_units.ordering', 'course_units.title', 'course_units.admin_title', 'courses.title as course_title']);

foreach ($units as $unit) {
    echo "Course: {$unit->course_title} | ID: {$unit->id}, Ordering: {$unit->ordering}, Title: {$unit->title}, Admin: {$unit->admin_title}\n";
}

echo "\nCurrent CourseDate details:\n";

$courseDate = DB::table('course_dates')
    ->join('course_units', 'course_dates.course_unit_id', '=', 'course_units.id')
    ->join('courses', 'course_units.course_id', '=', 'courses.id')
    ->where('course_dates.id', 10552)
    ->first(['course_dates.*', 'course_units.ordering', 'course_units.title as unit_title', 'courses.title as course_title']);

if ($courseDate) {
    echo "CourseDate ID: {$courseDate->id}\n";
    echo "Course: {$courseDate->course_title}\n";
    echo "Unit Ordering: {$courseDate->ordering}\n";
    echo "Unit Title: {$courseDate->unit_title}\n";
    echo "Date: {$courseDate->starts_at}\n";
} else {
    echo "CourseDate not found\n";
}
