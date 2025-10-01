<?php
/**
 * Deep investigation of the InstUnit stale data issue
 */

require_once __DIR__ . '/vendor/autoload.php';

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEEP INVESTIGATION: STALE INSTUNIT DATA ===\n";
echo "Current Date: " . now()->format('Y-m-d H:i:s') . "\n\n";

// Get the problematic CourseDate
$courseDate = \App\Models\CourseDate::find(10556);
echo "1. COURSEDATE 10556 (FL-D40-D2):\n";
echo "   ID: {$courseDate->id}\n";
echo "   Starts At: {$courseDate->starts_at}\n";
echo "   Ends At: {$courseDate->ends_at}\n";
echo "   Is Active: " . ($courseDate->is_active ? 'YES' : 'NO') . "\n";
echo "   Created At: {$courseDate->created_at}\n";
echo "   Updated At: {$courseDate->updated_at}\n\n";

// Get the InstUnit
$instUnit = $courseDate->instUnit;
echo "2. INSTUNIT {$instUnit->id} (STALE DATA):\n";
echo "   Course Date ID: {$instUnit->course_date_id}\n";
echo "   Created By: {$instUnit->created_by}\n";
echo "   Created At: {$instUnit->created_at} (" . \Carbon\Carbon::parse($instUnit->created_at)->format('Y-m-d H:i:s') . ")\n";
echo "   Completed At: {$instUnit->completed_at} (" . \Carbon\Carbon::parse($instUnit->completed_at)->format('Y-m-d H:i:s') . ")\n";
echo "   Assistant ID: " . ($instUnit->assistant_id ?? 'NULL') . "\n\n";

// Check if this InstUnit should exist for today's class
echo "3. STALE DATA ANALYSIS:\n";
$createdDate = \Carbon\Carbon::parse($instUnit->created_at)->format('Y-m-d');
$completedDate = \Carbon\Carbon::parse($instUnit->completed_at)->format('Y-m-d');
$courseDateDay = \Carbon\Carbon::parse($courseDate->starts_at)->format('Y-m-d');

echo "   InstUnit Created Date: {$createdDate}\n";
echo "   InstUnit Completed Date: {$completedDate}\n";
echo "   CourseDate Scheduled Date: {$courseDateDay}\n";
echo "   Days between completion and today's class: " . \Carbon\Carbon::parse($completedDate)->diffInDays(now()) . " days\n\n";

echo "4. THE PROBLEM:\n";
echo "   - This InstUnit is from April 3rd, 2025 (almost 6 months ago)\n";
echo "   - Today's class (Sept 30th) should NOT have this old InstUnit attached\n";
echo "   - The system is seeing the completed_at field and marking today's class as completed\n";
echo "   - This is why the class shows 'EXPIRED' instead of being available to start\n\n";

// Check if there are other InstUnits for this CourseDate
echo "5. ALL INSTUNITS FOR THIS COURSEDATE:\n";
$allInstUnits = \App\Models\InstUnit::where('course_date_id', $courseDate->id)->get();
echo "   Found " . $allInstUnits->count() . " InstUnit(s) for CourseDate {$courseDate->id}:\n";

foreach ($allInstUnits as $iu) {
    $createdParsed = \Carbon\Carbon::parse($iu->created_at)->format('Y-m-d H:i:s');
    $completedParsed = $iu->completed_at ? \Carbon\Carbon::parse($iu->completed_at)->format('Y-m-d H:i:s') : 'NULL';
    echo "   - InstUnit {$iu->id}: Created {$createdParsed}, Completed {$completedParsed}\n";
}

echo "\n6. RECOMMENDED SOLUTION:\n";
echo "   Option A: Clear the stale InstUnit completed_at field\n";
echo "   Option B: Delete the stale InstUnit entirely\n";
echo "   Option C: Fix the CourseDate relationship to not use stale InstUnits\n\n";

// Check CourseDate model relationship
echo "7. COURSEDATE INSTUNIT RELATIONSHIP:\n";
echo "   The CourseDate model probably has a hasOne or hasMany relationship to InstUnit\n";
echo "   This relationship is picking up the old InstUnit instead of creating a new one\n";
echo "   We need to either:\n";
echo "   - Add date filtering to the relationship\n";
echo "   - Clear stale InstUnit data\n";
echo "   - Modify the logic to ignore stale InstUnits\n\n";

echo "=== END INVESTIGATION ===\n";
