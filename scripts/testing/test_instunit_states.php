<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing InstUnit Assignment Logic\n\n";

// Get our course date
$courseDate = DB::table('course_dates')->where('id', 10552)->first();
if (!$courseDate) {
    echo "❌ CourseDate 10552 not found\n";
    exit;
}

echo "📅 CourseDate Details:\n";
echo "ID: {$courseDate->id}\n";
echo "Date: {$courseDate->starts_at}\n\n";

// Check if InstUnit exists
$instUnit = DB::table('inst_unit')->where('course_date_id', $courseDate->id)->first();

if (!$instUnit) {
    echo "🔍 No InstUnit found - This should show 'Start Class' button\n";
    echo "State: UNASSIGNED (CourseDate exists but no InstUnit)\n\n";

    // Create a test InstUnit without created_by (unassigned state)
    echo "Creating test InstUnit without created_by...\n";
    $instUnitId = DB::table('inst_unit')->insertGetId([
        'course_date_id' => $courseDate->id,
        'created_at' => now(),
        'created_by' => null, // This is the key - NULL means unassigned
        'completed_at' => null,
        'completed_by' => null,
        'assistant_id' => null
    ]);
    echo "✅ Created InstUnit ID: {$instUnitId} (UNASSIGNED state)\n\n";

} else {
    echo "🔍 InstUnit found:\n";
    echo "ID: {$instUnit->id}\n";
    echo "Created By: " . ($instUnit->created_by ? $instUnit->created_by : 'NULL (UNASSIGNED)') . "\n";
    echo "Assistant ID: " . ($instUnit->assistant_id ? $instUnit->assistant_id : 'NULL') . "\n";
    echo "Completed At: " . ($instUnit->completed_at ? $instUnit->completed_at : 'NULL (ACTIVE)') . "\n\n";

    if (!$instUnit->created_by) {
        echo "State: UNASSIGNED - Should show 'Start Class' button\n\n";

        // Assign an instructor (simulate someone taking the class)
        echo "Simulating instructor assignment...\n";
        DB::table('inst_unit')
            ->where('id', $instUnit->id)
            ->update(['created_by' => 1]); // Assume user ID 1 is an instructor

        echo "✅ InstUnit now assigned to instructor ID 1\n";
        echo "State: ASSIGNED - Should show 'Take Control' and 'Assist' buttons\n\n";

    } else {
        echo "State: ASSIGNED - Should show 'Take Control' and 'Assist' buttons\n\n";

        // Test unassigning
        echo "Testing unassign (set created_by to NULL)...\n";
        DB::table('inst_unit')
            ->where('id', $instUnit->id)
            ->update(['created_by' => null]);

        echo "✅ InstUnit now unassigned\n";
        echo "State: UNASSIGNED - Should show 'Start Class' button\n\n";
    }
}

echo "🧪 Test the instructor dashboard now to see the different button states!\n";
echo "URL: https://frost.test/admin/instructors\n";
