<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing Enhanced Course Delete Logic\n";
echo "=====================================\n\n";

try {
    // Find a course date to test with
    $courseDate = \App\Models\CourseDate::whereDoesntHave('StudentUnits')
        ->orderBy('id', 'desc')
        ->first();

    if (!$courseDate) {
        echo "❌ No course dates found without students\n";
        exit(1);
    }

    echo "📋 Testing Course Date:\n";
    echo "   ID: {$courseDate->id}\n";
    echo "   Starts At: {$courseDate->starts_at}\n";
    echo "   Current Date: " . now()->toDateString() . "\n\n";

    // Test the enhanced logic
    echo "🔍 Enhanced Delete Validation Checks:\n";

    // 1. Student check
    $studentCount = $courseDate->StudentUnits()->count();
    echo "   1. Students enrolled: {$studentCount}\n";

    // 2. Today's InstUnit check (primary safety)
    $hasInstUnitToday = \App\Models\InstUnit::where('course_date_id', $courseDate->id)
        ->whereDate('created_at', now()->toDateString())
        ->exists();
    echo "   2. InstUnit today: " . ($hasInstUnitToday ? 'YES (blocked)' : 'no') . "\n";

    // 3. Any InstUnit check (secondary safety)
    $hasAnyInstUnit = \App\Models\InstUnit::where('course_date_id', $courseDate->id)->exists();
    echo "   3. Any InstUnit: " . ($hasAnyInstUnit ? 'YES (blocked)' : 'no') . "\n";

    // 4. Old relationship check (for comparison)
    $oldRelationshipCheck = $courseDate->InstUnit()->exists();
    echo "   4. Old relationship check: " . ($oldRelationshipCheck ? 'YES' : 'no') . "\n";

    echo "\n🎯 Delete Decision:\n";

    if ($studentCount > 0) {
        echo "   ❌ BLOCKED: Students are enrolled\n";
    } elseif ($hasInstUnitToday) {
        echo "   ❌ BLOCKED: Active instructor session from today\n";
    } elseif ($hasAnyInstUnit) {
        echo "   ❌ BLOCKED: Existing instructor sessions\n";
    } else {
        echo "   ✅ ALLOWED: Safe to delete\n";
    }

    // Show InstUnit details if any exist
    if ($hasAnyInstUnit) {
        echo "\n📊 InstUnit Details:\n";
        $instUnits = \App\Models\InstUnit::where('course_date_id', $courseDate->id)->get();
        foreach ($instUnits as $instUnit) {
            echo "   - ID: {$instUnit->id}, Created: {$instUnit->created_at}\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "   Exception: " . get_class($e) . "\n";
}

echo "\n🏁 Enhanced validation test completed.\n";
