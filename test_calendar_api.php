<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING CALENDAR API ENDPOINT ===\n\n";

// Simulate the API call that the calendar makes
echo "Simulating API call to getScheduleData()...\n\n";

// Get active courses
$activeCourses = App\Models\Course::where('is_active', true)->get();
echo "Active courses found: " . $activeCourses->count() . "\n";

$allEvents = [];

foreach ($activeCourses as $course) {
    echo "\nProcessing course: {$course->title} (ID: {$course->id})\n";

    // Get course dates for this course (similar to MiscQueries::CalenderDates)
    $courseUnitIds = App\Models\CourseUnit::where('course_id', $course->id)->pluck('id');
    echo "Course unit IDs: " . $courseUnitIds->implode(', ') . "\n";

    $courseDates = App\Models\CourseDate::where('is_active', true)
        ->where('starts_at', '>=', '2025-09-01')
        ->whereIn('course_unit_id', $courseUnitIds)
        ->orderBy('starts_at')
        ->get();

    echo "Course dates found: " . $courseDates->count() . "\n";

    foreach ($courseDates as $date) {
        $event = [
            'title' => $date->CalendarTitle(),
            'start' => $date->StartsAt('YYYY-MM-DD HH:mm'),
            'end' => $date->EndsAt('YYYY-MM-DD HH:mm'),
            'url' => route('courses.show', $course->id),
            'course_type' => strpos(strtolower($course->title), 'armed') !== false ||
                           strpos(strtolower($course->title), 'd40') !== false ? 'D40' : 'G28',
            'course_id' => $course->id,
            'course_title' => $course->title
        ];

        $allEvents[] = $event;
        echo "  - Event: {$event['title']} | {$event['start']} - {$event['end']} | Type: {$event['course_type']}\n";
    }
}

echo "\n=== FINAL EVENTS ARRAY ===\n";
echo "Total events: " . count($allEvents) . "\n\n";

echo json_encode([
    'success' => true,
    'events' => $allEvents
], JSON_PRETTY_PRINT);

echo "\n\n=== API TEST COMPLETE ===\n";
