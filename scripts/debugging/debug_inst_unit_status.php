<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Debug InstUnit Status Issue\n";
echo "==============================\n\n";

use App\Models\CourseDate;
use App\Models\InstUnit;
use Carbon\Carbon;

$today = now()->format('Y-m-d');
echo "📅 **Today**: {$today}\n\n";

// Get today's CourseDate
$courseDate = CourseDate::whereDate('starts_at', $today)
    ->where('is_active', true)
    ->with(['instUnit.createdBy'])
    ->first();

if (!$courseDate) {
    echo "❌ No CourseDate found for today\n";
    exit;
}

echo "📚 **CourseDate Details**:\n";
echo "  - ID: {$courseDate->id}\n";
echo "  - Starts at: {$courseDate->starts_at}\n";
echo "  - Ends at: {$courseDate->ends_at}\n";
echo "  - Is Active: " . ($courseDate->is_active ? 'YES' : 'NO') . "\n";

$instUnit = $courseDate->instUnit;

if ($instUnit) {
    echo "\n🎓 **InstUnit Details**:\n";
    echo "  - ID: {$instUnit->id}\n";
    echo "  - Created by: {$instUnit->created_by}\n";
    echo "  - Created at: {$instUnit->created_at}\n";
    echo "  - Completed at: " . ($instUnit->completed_at ?? 'NULL') . "\n";

    if ($instUnit->completed_at) {
        $completedAt = Carbon::parse($instUnit->completed_at);
        $startTime = Carbon::parse($courseDate->starts_at);
        $now = now();

        echo "\n⏰ **Time Analysis**:\n";
        echo "  - Current time: {$now}\n";
        echo "  - Class start time: {$startTime}\n";
        echo "  - Completed at: {$completedAt}\n";
        echo "  - Completed before start? " . ($completedAt->lt($startTime) ? 'YES ⚠️' : 'NO') . "\n";
        echo "  - Is today? " . ($completedAt->isToday() ? 'YES' : 'NO') . "\n";

        if ($completedAt->lt($startTime)) {
            echo "\n🚨 **ISSUE FOUND**: InstUnit shows completed BEFORE the class start time!\n";
            echo "  - This suggests the InstUnit is from a PREVIOUS day's class\n";
            echo "  - The InstUnit should be NULL or from today's session\n";
        }
    }

    echo "\n👨‍🏫 **Instructor Details**:\n";
    if ($instUnit->createdBy) {
        echo "  - Name: {$instUnit->createdBy->fname} {$instUnit->createdBy->lname}\n";
        echo "  - Email: {$instUnit->createdBy->email}\n";
    } else {
        echo "  - No instructor found\n";
    }
} else {
    echo "\n✅ **No InstUnit** - Class is unassigned (correct for future class)\n";
}

echo "\n💡 **Expected Behavior**:\n";
echo "  - For TODAY'S class that hasn't started: InstUnit should be NULL\n";
echo "  - For TODAY'S class in progress: InstUnit should exist, completed_at = NULL\n";
echo "  - For TODAY'S class finished: InstUnit should exist, completed_at = today's timestamp\n";

// Check if there are multiple InstUnits for this CourseDate
$allInstUnits = InstUnit::where('course_date_id', $courseDate->id)->get();
echo "\n🔍 **All InstUnits for this CourseDate**: {$allInstUnits->count()}\n";

foreach ($allInstUnits as $index => $unit) {
    echo "  {$index}: ID {$unit->id}, Created: {$unit->created_at}, Completed: " . ($unit->completed_at ?? 'NULL') . "\n";
}
