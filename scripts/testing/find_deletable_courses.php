<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Finding Course Dates Safe to Delete\n";
echo "====================================\n\n";

try {
    // Find course dates with no students and no InstUnits
    $safeCourses = \App\Models\CourseDate::whereDoesntHave('StudentUnits')
        ->whereDoesntHave('InstUnit')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();

    echo "📋 Safe Course Dates (No Students, No InstUnit):\n";

    if ($safeCourses->isEmpty()) {
        echo "   None found - all course dates have students or instructor sessions\n";

        // Show some examples with InstUnits
        echo "\n📊 Course Dates with InstUnits (blocked from deletion):\n";
        $blockedCourses = \App\Models\CourseDate::whereHas('InstUnit')
            ->limit(3)
            ->get();

        foreach ($blockedCourses as $course) {
            $courseName = $course->CourseUnit ? $course->CourseUnit->Course->course_name ?? 'Unknown' : 'No Course Unit';
            echo "   ID: {$course->id} - {$courseName} (has InstUnit)\n";
        }

    } else {
        foreach ($safeCourses as $course) {
            $courseName = $course->CourseUnit ? $course->CourseUnit->Course->course_name ?? 'Unknown' : 'No Course Unit';
            echo "   ID: {$course->id} - {$courseName}\n";
        }

        echo "\n✅ These course dates can be safely deleted via the UI.\n";
    }

    // Show statistics
    echo "\n📊 Course Date Statistics:\n";
    $totalCourses = \App\Models\CourseDate::count();
    $withStudents = \App\Models\CourseDate::whereHas('StudentUnits')->count();
    $withInstUnits = \App\Models\CourseDate::whereHas('InstUnit')->count();
    $safeDeletable = \App\Models\CourseDate::whereDoesntHave('StudentUnits')->whereDoesntHave('InstUnit')->count();

    echo "   Total Course Dates: {$totalCourses}\n";
    echo "   With Students: {$withStudents}\n";
    echo "   With InstUnits: {$withInstUnits}\n";
    echo "   Safe to Delete: {$safeDeletable}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🏁 Search completed.\n";
