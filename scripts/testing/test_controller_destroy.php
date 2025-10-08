<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing Controller Destroy Method Directly\n";
echo "=============================================\n\n";

try {
    // Test if we can access the CourseDate model and destroy method
    $courseDate = \App\Models\CourseDate::find(10566);

    if (!$courseDate) {
        echo "❌ CourseDate 10566 not found\n";
        exit(1);
    }

    echo "📋 Found Course Date:\n";
    echo "   ID: {$courseDate->id}\n";
    echo "   Course: " . ($courseDate->CourseUnit ? $courseDate->CourseUnit->Course->course_name ?? 'Unknown' : 'No CourseUnit') . "\n\n";

    // Test the validation logic manually (same as controller)
    echo "🔍 Testing Validation Logic:\n";

    // Active students check
    $instUnit = $courseDate->InstUnit;
    $activeStudentCount = 0;

    if ($instUnit && !$instUnit->completed_at) {
        $activeStudentCount = \App\Classes\ClassroomQueries::ActiveStudentUnits($courseDate)->count();
    }

    echo "   Active Students: {$activeStudentCount}\n";

    // InstUnit checks
    $hasInstUnitToday = \App\Models\InstUnit::where('course_date_id', $courseDate->id)
        ->whereDate('created_at', now()->toDateString())
        ->exists();

    echo "   InstUnit Today: " . ($hasInstUnitToday ? 'YES' : 'NO') . "\n";

    $hasAnyInstUnit = \App\Models\InstUnit::where('course_date_id', $courseDate->id)->exists();
    echo "   Any InstUnit: " . ($hasAnyInstUnit ? 'YES' : 'NO') . "\n";

    // Simulate the controller response
    echo "\n🎯 Controller Response Simulation:\n";

    if ($activeStudentCount > 0) {
        $errorMessage = "Cannot delete course date with {$activeStudentCount} active students in class.";
        echo "   Status: 400 (Bad Request)\n";
        echo "   Response: {\"success\": false, \"message\": \"{$errorMessage}\"}\n";
    } elseif ($hasInstUnitToday) {
        $errorMessage = 'Cannot delete course date with active instructor session from today.';
        echo "   Status: 400 (Bad Request)\n";
        echo "   Response: {\"success\": false, \"message\": \"{$errorMessage}\"}\n";
    } elseif ($hasAnyInstUnit) {
        $errorMessage = 'Cannot delete course date with existing instructor sessions.';
        echo "   Status: 400 (Bad Request)\n";
        echo "   Response: {\"success\": false, \"message\": \"{$errorMessage}\"}\n";
    } else {
        echo "   Status: 200 (OK)\n";
        echo "   Response: {\"success\": true, \"message\": \"Course date deleted successfully.\"}\n";
        echo "   ✅ Would proceed with deletion\n";
    }

} catch (Exception $e) {
    echo "❌ Error testing controller logic: " . $e->getMessage() . "\n";
    echo "   Exception: " . get_class($e) . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Controller test completed.\n";
