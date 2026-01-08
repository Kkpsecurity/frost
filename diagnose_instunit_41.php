<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\StudentUnit;
use Carbon\Carbon;

echo "=== Detailed InstUnit #41 Analysis (Yesterday's Class) ===\n\n";

$instUnit = InstUnit::with(['CourseDate.CourseUnit', 'StudentUnits'])->find(41);

if (!$instUnit) {
    echo "InstUnit #41 not found.\n";
    exit;
}

echo "InstUnit ID: {$instUnit->id}\n";
echo "CourseDate ID: {$instUnit->course_date_id}\n";
echo "Created by (Instructor): {$instUnit->created_by}\n";
echo "Created at: " . Carbon::createFromTimestamp($instUnit->created_at)->format('Y-m-d H:i:s') . "\n";
echo "Completed at: " . ($instUnit->completed_at ? Carbon::createFromTimestamp($instUnit->completed_at)->format('Y-m-d H:i:s') : 'NULL - STILL ACTIVE') . "\n\n";

$courseDate = $instUnit->CourseDate;
if ($courseDate) {
    echo "CourseDate Details:\n";
    echo "  ID: {$courseDate->id}\n";
    echo "  Starts at: {$courseDate->starts_at}\n";
    echo "  Ends at: {$courseDate->ends_at}\n";
    echo "  Course Unit ID: {$courseDate->course_unit_id}\n";

    if ($courseDate->CourseUnit) {
        echo "  Course Unit: {$courseDate->CourseUnit->name}\n";
        echo "  Course ID: {$courseDate->CourseUnit->course_id}\n";
    }
    echo "\n";
}

echo "StudentUnits (students who joined):\n";
$studentUnits = $instUnit->StudentUnits;
if ($studentUnits->isEmpty()) {
    echo "  No students joined this class session.\n";
} else {
    echo "  Total students: {$studentUnits->count()}\n";
    foreach ($studentUnits as $su) {
        echo "  - Student ID: {$su->user_id}, CourseAuth: {$su->course_auth_id}, Joined: {$su->created_at}\n";
    }
}

echo "\n=== DIAGNOSIS ===\n";
echo "This InstUnit (#41) is from YESTERDAY (2026-01-07).\n";
echo "It's still marked as active (completed_at = NULL).\n";
echo "This means the instructor never properly ended the class!\n\n";

echo "PROBLEM IDENTIFIED:\n";
echo "1. Instructor started class on 2026-01-07 (CourseDate #75)\n";
echo "2. InstUnit #41 was created\n";
echo "3. Instructor NEVER completed/ended the class (completed_at still NULL)\n";
echo "4. Today is 2026-01-08 with CourseDate #76\n";
echo "5. NO InstUnit exists for today's CourseDate #76\n\n";

echo "EXPECTED BEHAVIOR:\n";
echo "- Instructor SHOULD be on BULLETIN BOARD (offline mode) for today\n";
echo "- Students SHOULD be in WAITING ROOM for today\n";
echo "- Both should see CourseDate #76 (today) with NO InstUnit\n\n";

echo "If instructor says they are 'in class', check:\n";
echo "1. Are they looking at yesterday's InstUnit #41?\n";
echo "2. Did they properly click 'Start Class' for today's CourseDate #76?\n";
echo "3. Is their frontend showing the correct date?\n";

echo "\n=== Checking CourseDate #76 Relationships ===\n";
$todayCourseDate = CourseDate::with('InstUnit')->find(76);
echo "CourseDate #76 (today):\n";
echo "  Starts at: {$todayCourseDate->starts_at}\n";
echo "  InstUnit: " . ($todayCourseDate->InstUnit ? "EXISTS (ID: {$todayCourseDate->InstUnit->id})" : "NULL - No class started yet") . "\n";
