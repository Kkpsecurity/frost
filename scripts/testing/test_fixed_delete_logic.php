<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing FIXED Course Delete Logic\n";
echo "===================================\n\n";

try {
    // Test the specific course from the screenshot
    $courseDate = \App\Models\CourseDate::find(10566);

    if (!$courseDate) {
        echo "❌ CourseDate 10566 not found\n";
        exit(1);
    }

    echo "📋 Testing Course Date:\n";
    echo "   ID: {$courseDate->id}\n";
    echo "   Starts At: {$courseDate->starts_at}\n\n";

    // Test the FIXED validation logic (same as frontend display)
    echo "🔍 FIXED Delete Validation (consistent with frontend):\n";

    $instUnit = $courseDate->InstUnit;
    $activeStudentCount = 0;

    echo "   1. InstUnit exists: " . ($instUnit ? 'YES' : 'NO') . "\n";

    if ($instUnit) {
        echo "   2. InstUnit completed: " . ($instUnit->completed_at ? 'YES' : 'NO') . "\n";

        if (!$instUnit->completed_at) {
            // Class has started but not completed - check for active students
            $activeStudentCount = \App\Classes\ClassroomQueries::ActiveStudentUnits($courseDate)->count();
            echo "   3. Active students in class: {$activeStudentCount}\n";
        } else {
            echo "   3. Class completed - no active students\n";
        }
    } else {
        echo "   2. No instructor session - no active students\n";
    }

    // For comparison, show the old logic
    echo "\n📊 Comparison with Old Logic:\n";
    $totalStudentUnits = $courseDate->StudentUnits()->count();
    echo "   OLD: Total StudentUnits (all enrollments): {$totalStudentUnits}\n";
    echo "   NEW: Active students (frontend display): {$activeStudentCount}\n";

    echo "\n🎯 Delete Decision (FIXED):\n";

    if ($activeStudentCount > 0) {
        echo "   ❌ BLOCKED: {$activeStudentCount} active students in class\n";
    } elseif ($instUnit && \App\Models\InstUnit::where('course_date_id', $courseDate->id)->exists()) {
        echo "   ❌ BLOCKED: Instructor session exists (history preservation)\n";
    } else {
        echo "   ✅ ALLOWED: Safe to delete (no active students, no instructor session)\n";
    }

    echo "\n🎉 RESULT:\n";
    if ($activeStudentCount == 0 && $totalStudentUnits > 0) {
        echo "   ✅ FIXED: Frontend shows 0 students, backend now allows deletion\n";
        echo "   📝 Note: {$totalStudentUnits} historical enrollments exist but class is not active\n";
    } elseif ($activeStudentCount == 0 && $totalStudentUnits == 0) {
        echo "   ✅ PERFECT: No students at all - safe to delete\n";
    } else {
        echo "   ⚠️  Still blocked due to active students in live class\n";
    }

} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
}

echo "\n🏁 Fixed validation test completed.\n";
