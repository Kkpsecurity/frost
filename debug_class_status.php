<?php
/**
 * Debug script to investigate the class status issue
 * FL-D40-D2 class showing as "EXPIRED" instead of "ACTIVE"
 */

require_once __DIR__ . '/vendor/autoload.php';

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG CLASS STATUS ISSUE ===\n";
echo "Date: " . now()->format('Y-m-d H:i:s') . "\n";
echo "Investigating FL-D40-D2 class status issue\n\n";

// 1. Check today's course dates
echo "1. CHECKING TODAY'S COURSE DATES:\n";
$today = now()->format('Y-m-d');
echo "   Today: {$today}\n";

$todaysCourseDates = \App\Models\CourseDate::whereDate('starts_at', $today)
    ->where('is_active', true)
    ->with(['courseUnit.course', 'instUnit'])
    ->orderBy('starts_at')
    ->get();

echo "   Found " . $todaysCourseDates->count() . " course dates for today\n";

foreach ($todaysCourseDates as $courseDate) {
    $course = $courseDate->courseUnit->course ?? null;
    $courseTitle = $course ? $course->title : 'Unknown Course';

    echo "\n   Course Date ID: {$courseDate->id}\n";
    echo "   Course: {$courseTitle}\n";
    echo "   Unit: " . ($courseDate->courseUnit->title ?? 'Unknown Unit') . "\n";
    echo "   Start Time: {$courseDate->starts_at}\n";
    echo "   End Time: {$courseDate->ends_at}\n";
    echo "   Is Active: " . ($courseDate->is_active ? 'YES' : 'NO') . "\n";

    // Check if this is the FL-D40-D2 class
    if (strpos($courseTitle, 'FL-D40-D2') !== false || strpos($courseTitle, 'D40') !== false) {
        echo "   *** THIS IS THE FL-D40-D2 CLASS ***\n";

        // Check InstUnit status
        $instUnit = $courseDate->instUnit;
        if ($instUnit) {
            echo "   InstUnit Found: ID {$instUnit->id}\n";
            echo "   Created At: {$instUnit->created_at}\n";
            echo "   Completed At: " . ($instUnit->completed_at ?? 'NULL') . "\n";
            echo "   Created By: {$instUnit->created_by}\n";
        } else {
            echo "   InstUnit: NOT FOUND\n";
        }

        // Check time logic
        $now = now();
        $startTime = \Carbon\Carbon::parse($courseDate->starts_at);
        $endTime = \Carbon\Carbon::parse($courseDate->ends_at);

        echo "   Current Time: " . $now->format('Y-m-d H:i:s') . "\n";
        echo "   Class Start: " . $startTime->format('Y-m-d H:i:s') . "\n";
        echo "   Class End: " . $endTime->format('Y-m-d H:i:s') . "\n";

        echo "   Time Checks:\n";
        echo "     - Is now >= start? " . ($now->gte($startTime) ? 'YES' : 'NO') . "\n";
        echo "     - Is now <= end? " . ($now->lte($endTime) ? 'YES' : 'NO') . "\n";
        echo "     - Is now between start and end? " . ($now->between($startTime, $endTime) ? 'YES' : 'NO') . "\n";

        // Apply the same logic as CourseDatesService
        if ($instUnit) {
            if ($instUnit->completed_at) {
                echo "   STATUS: COMPLETED (InstUnit has completed_at)\n";
            } else {
                if ($now->gte($startTime)) {
                    echo "   STATUS: IN_PROGRESS (InstUnit exists, class time started)\n";
                } else {
                    echo "   STATUS: ASSIGNED (InstUnit exists, before class time)\n";
                }
            }
        } else {
            if ($now->lt($startTime)) {
                echo "   STATUS: SCHEDULED (No InstUnit, before class time)\n";
            } elseif ($now->between($startTime, $endTime)) {
                echo "   STATUS: UNASSIGNED (No InstUnit, during class time)\n";
            } else {
                echo "   STATUS: EXPIRED (No InstUnit, after class time)\n";
            }
        }
    }
}

// 2. Test the getTodaysLessons method
echo "\n\n2. TESTING getTodaysLessons() METHOD:\n";
try {
    $service = new \App\Services\Frost\Instructors\CourseDatesService();
    $lessons = $service->getTodaysLessons();

    echo "   Lessons found: " . count($lessons['lessons']) . "\n";
    echo "   Message: " . $lessons['message'] . "\n";

    foreach ($lessons['lessons'] as $lesson) {
        echo "\n   Lesson: {$lesson['course_name']}\n";
        echo "   Status: {$lesson['class_status']}\n";
        echo "   Time: {$lesson['time']}\n";
        echo "   Starts At: {$lesson['starts_at']}\n";
        echo "   Ends At: {$lesson['ends_at']}\n";

        if (strpos($lesson['course_name'], 'FL-D40-D2') !== false || strpos($lesson['course_name'], 'D40') !== false) {
            echo "   *** THIS IS THE PROBLEMATIC FL-D40-D2 CLASS ***\n";
            print_r($lesson);
        }
    }

} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "\n\n=== END DEBUG ===\n";
