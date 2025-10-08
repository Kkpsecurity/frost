<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Checking Student Enrollment for Course Date\n";
echo "=============================================\n\n";

try {
    // Check the course date we've been testing
    $courseDate = \App\Models\CourseDate::find(10566);

    if (!$courseDate) {
        echo "❌ CourseDate 10566 not found\n";
        exit(1);
    }

    echo "📋 Course Date Details:\n";
    echo "   ID: {$courseDate->id}\n";
    echo "   Starts At: {$courseDate->starts_at}\n";
    echo "   Course Unit ID: {$courseDate->course_unit_id}\n\n";

    // Check StudentUnits (enrolled students)
    echo "🎓 StudentUnit Check (Enrolled Students):\n";
    $studentUnits = $courseDate->StudentUnits()->get();
    echo "   Count: " . $studentUnits->count() . "\n";

    if ($studentUnits->count() > 0) {
        echo "   Students enrolled:\n";
        foreach ($studentUnits->take(5) as $studentUnit) {
            $student = $studentUnit->user ?? null;
            $studentName = $student ? "{$student->fname} {$student->lname}" : "Unknown Student";
            echo "      - ID: {$studentUnit->id}, Student: {$studentName}, Created: {$studentUnit->created_at}\n";
        }
        if ($studentUnits->count() > 5) {
            echo "      ... and " . ($studentUnits->count() - 5) . " more\n";
        }
    } else {
        echo "   ✅ No students enrolled in this course date\n";
    }

    // For comparison, check if there are CourseAuths (different table)
    echo "\n📚 CourseAuth Check (Course Authorizations - NOT enrollment):\n";
    $courseId = $courseDate->CourseUnit ? $courseDate->CourseUnit->course_id : null;

    if ($courseId) {
        $courseAuthCount = \App\Models\CourseAuth::where('course_id', $courseId)->count();
        echo "   CourseAuths for course ID {$courseId}: {$courseAuthCount}\n";
        echo "   ⚠️  Note: CourseAuth ≠ StudentUnit. CourseAuth is authorization, StudentUnit is actual enrollment.\n";
    } else {
        echo "   No course unit found to check CourseAuths\n";
    }

    // Direct database query to double-check
    echo "\n🔍 Direct Database Query:\n";
    $directCount = \App\Models\StudentUnit::where('course_date_id', $courseDate->id)->count();
    echo "   Direct StudentUnit count: {$directCount}\n";

    // Check if the relationship method exists
    echo "\n🔧 Relationship Verification:\n";
    try {
        $relationshipExists = method_exists($courseDate, 'StudentUnits');
        echo "   CourseDate->StudentUnits() method exists: " . ($relationshipExists ? 'YES' : 'NO') . "\n";

        if ($relationshipExists) {
            $queryResult = $courseDate->StudentUnits()->toSql();
            echo "   SQL Query: {$queryResult}\n";
        }
    } catch (Exception $e) {
        echo "   Error checking relationship: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Exception: " . get_class($e) . "\n";
}

echo "\n🏁 Student enrollment check completed.\n";
