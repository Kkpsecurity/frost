<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDate;
use App\Models\StudentUnit;
use App\Models\StudentLesson;

echo "=== CHECKING STUDENT LESSONS (Today's Classes) ===\n\n";

// Get recent course dates with students (last 7 days)
$courseDates = CourseDate::with('StudentUnits')
    ->whereDate('starts_at', '>=', now()->subDays(7))
    ->orderBy('starts_at', 'desc')
    ->get();

if ($courseDates->isEmpty()) {
    echo "❌ No recent course dates found\n";
    exit;
}

echo "Recent CourseDates with Students:\n";
echo str_repeat('-', 80) . "\n";
foreach ($courseDates as $cd) {
    $studentCount = $cd->StudentUnits->count();
    if ($studentCount > 0) {
        echo sprintf("ID: %-4d | Date: %s | Students: %d\n",
            $cd->id,
            $cd->starts_at->format('Y-m-d H:i'),
            $studentCount
        );
    }
}

// Get the most recent one
$mostRecent = $courseDates->first(function($cd) {
    return $cd->StudentUnits->count() > 0;
});

if (!$mostRecent) {
    echo "\n❌ No course dates with students found\n";
    exit;
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "CHECKING CourseDate ID: {$mostRecent->id} | {$mostRecent->starts_at->format('Y-m-d H:i')}\n";
echo str_repeat('=', 80) . "\n\n";

// Get all student units for this course date
$studentUnits = $mostRecent->StudentUnits;

foreach ($studentUnits as $su) {
    echo "StudentUnit ID: {$su->id} | Auth ID: {$su->course_auth_id}\n";

    // Get student lessons
    $studentLessons = StudentLesson::where('student_unit_id', $su->id)
        ->orderBy('lesson_id')
        ->get();

    if ($studentLessons->isEmpty()) {
        echo "  ❌ No student_lessons found\n";
    } else {
        echo "  Total Lessons: " . $studentLessons->count() . "\n";
        foreach ($studentLessons as $sl) {
            $status = $sl->completed_at ? 'COMPLETED' : 'PENDING';
            $completedText = $sl->completed_at ? $sl->completed_at->format('Y-m-d H:i:s') : 'NULL';
            echo "    Lesson ID: " . $sl->lesson_id . " | " . $status . " | Completed: " . $completedText . "\n";
        }
    }
    echo "\n";
}
