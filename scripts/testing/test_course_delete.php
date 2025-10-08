<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Application;

// Bootstrap Laravel application
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing Course Delete Functionality\n";
echo "==========================================\n\n";

try {
    // Get a test course date with no students
    $courseDate = \App\Models\CourseDate::whereDoesntHave('studentUnits')
        ->orderBy('id', 'desc')
        ->first();

    if (!$courseDate) {
        echo "❌ No course dates found without students\n";
        exit(1);
    }

    echo "📋 Found Course Date to Test:\n";
    echo "   ID: {$courseDate->id}\n";
    $courseName = $courseDate->course ? $courseDate->course->course_name : 'Unknown';
    echo "   Course: {$courseName}\n";
    echo "   Date: {$courseDate->course_date}\n";
    echo "   Students: " . ($courseDate->studentUnits()->count()) . "\n";
    $hasInstUnitCheck = $courseDate->InstUnit()->exists();
    echo "   InstUnit: " . ($hasInstUnitCheck ? 'exists' : 'none') . "\n\n";

    // Test the delete conditions
    echo "🔍 Testing Delete Conditions:\n";

    // Check if students exist
    $studentCount = $courseDate->StudentUnits()->count();
    echo "   Students enrolled: {$studentCount}\n";

    // Check if inst unit exists
    $hasInstUnit = $courseDate->InstUnit()->exists();
    echo "   InstUnit exists: " . ($hasInstUnit ? 'yes' : 'no') . "\n";    // Check course unit lessons
    $courseUnit = $courseDate->courseUnit;
    if ($courseUnit) {
        $lessonCount = $courseUnit->courseUnitLessons()->count();
        echo "   Course Unit ID: {$courseUnit->id}\n";
        echo "   Course Unit Lessons: {$lessonCount}\n";
    } else {
        echo "   Course Unit: None\n";
    }

    echo "\n";

    // Simulate the delete logic
    if ($studentCount > 0) {
        echo "❌ Delete blocked: Students are enrolled\n";
    } elseif ($hasInstUnit) {
        echo "❌ Delete blocked: InstUnit exists\n";
    } else {
        echo "✅ Delete should be allowed\n";

        // Try to delete
        echo "\n🚀 Attempting to delete CourseDate ID: {$courseDate->id}\n";

        try {
            $courseDate->delete();
            echo "✅ CourseDate deleted successfully!\n";
        } catch (Exception $e) {
            echo "❌ Delete failed with error: " . $e->getMessage() . "\n";
            echo "   Exception type: " . get_class($e) . "\n";
            echo "   Stack trace:\n";
            echo $e->getTraceAsString() . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "   Exception type: " . get_class($e) . "\n";
}

echo "\n🏁 Test completed.\n";
