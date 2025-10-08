<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Finding Course Dates with Students to Test Delete Validation\n";
echo "==============================================================\n\n";

try {
    // Find course dates WITH students
    $coursesWithStudents = \App\Models\CourseDate::whereHas('StudentUnits')
        ->withCount('StudentUnits')
        ->orderBy('student_units_count', 'desc')
        ->limit(5)
        ->get();

    echo "📋 Course Dates WITH Students (should be blocked from deletion):\n";

    if ($coursesWithStudents->isEmpty()) {
        echo "   None found - no course dates have enrolled students\n";
    } else {
        foreach ($coursesWithStudents as $course) {
            $courseName = 'Unknown';
            try {
                if ($course->CourseUnit && $course->CourseUnit->Course) {
                    $courseName = $course->CourseUnit->Course->course_name;
                }
            } catch (Exception $e) {
                // Ignore relationship errors
            }

            echo "   ID: {$course->id} - {$courseName} ({$course->student_units_count} students)\n";
        }
    }

    // Find course dates WITHOUT students but WITH InstUnits
    echo "\n📋 Course Dates with NO Students but WITH InstUnits:\n";
    $coursesWithInstUnits = \App\Models\CourseDate::whereDoesntHave('StudentUnits')
        ->whereHas('InstUnit')  // This might not work due to date filtering
        ->limit(3)
        ->get();

    if ($coursesWithInstUnits->isEmpty()) {
        echo "   None found using relationship\n";

        // Try direct query
        $instUnitCourseIds = \App\Models\InstUnit::pluck('course_date_id')->unique();
        $coursesWithInstUnits = \App\Models\CourseDate::whereDoesntHave('StudentUnits')
            ->whereIn('id', $instUnitCourseIds)
            ->limit(3)
            ->get();

        echo "   Using direct InstUnit query:\n";
        foreach ($coursesWithInstUnits as $course) {
            echo "   ID: {$course->id} - No students, but has InstUnits\n";
        }
    }

    // Find truly deletable courses
    echo "\n📋 Course Dates SAFE to Delete (no students, no InstUnits):\n";
    $instUnitCourseIds = \App\Models\InstUnit::pluck('course_date_id')->unique();
    $safeCourses = \App\Models\CourseDate::whereDoesntHave('StudentUnits')
        ->whereNotIn('id', $instUnitCourseIds)
        ->limit(5)
        ->get();

    if ($safeCourses->isEmpty()) {
        echo "   None found - all courses have students or InstUnits\n";
    } else {
        foreach ($safeCourses as $course) {
            echo "   ID: {$course->id} - SAFE TO DELETE\n";
        }
    }

    // Summary statistics
    echo "\n📊 Database Statistics:\n";
    $totalCourseDates = \App\Models\CourseDate::count();
    $coursesWithStudentUnits = \App\Models\CourseDate::whereHas('StudentUnits')->count();
    $totalStudentUnits = \App\Models\StudentUnit::count();
    $totalInstUnits = \App\Models\InstUnit::count();

    echo "   Total Course Dates: {$totalCourseDates}\n";
    echo "   Course Dates with Students: {$coursesWithStudentUnits}\n";
    echo "   Total Student Enrollments: {$totalStudentUnits}\n";
    echo "   Total Instructor Sessions: {$totalInstUnits}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🏁 Analysis completed.\n";
