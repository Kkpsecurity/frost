<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "InstUnit Table Analysis\n\n";

// Get table columns
$columns = DB::getSchemaBuilder()->getColumnListing('inst_unit');
echo "📋 Table Columns:\n";
foreach ($columns as $column) {
    echo "  - {$column}\n";
}

echo "\n🔍 Sample InstUnit Record:\n";
$sample = DB::table('inst_unit')->first();
if ($sample) {
    foreach ($sample as $key => $value) {
        echo "  {$key}: " . ($value ?? 'NULL') . "\n";
    }
}

echo "\n🎯 Current Understanding:\n";
echo "If created_by is NOT NULL constraint, then:\n";
echo "  - InstUnit EXISTS = Class is ASSIGNED (show Take Control/Assist)\n";
echo "  - InstUnit MISSING = Class is UNASSIGNED (show Start Class)\n";
echo "  - InstUnit with completed_at = Class was COMPLETED (can restart)\n";

// Check our current CourseDate
echo "\n📅 Current CourseDate 10552 Analysis:\n";
$courseDate = DB::table('course_dates')->where('id', 10552)->first();
$instUnit = DB::table('inst_unit')->where('course_date_id', 10552)->first();

if ($instUnit) {
    echo "✅ InstUnit EXISTS (ID: {$instUnit->id})\n";
    echo "   Created By: {$instUnit->created_by}\n";
    echo "   Completed At: " . ($instUnit->completed_at ?? 'NULL (ACTIVE)') . "\n";

    if ($instUnit->completed_at) {
        echo "   Status: COMPLETED (can restart)\n";
        echo "   Buttons: Start New Session\n";
    } else {
        echo "   Status: ASSIGNED/ACTIVE\n";
        echo "   Buttons: Take Control, Assist\n";
    }
} else {
    echo "❌ NO InstUnit found\n";
    echo "   Status: UNASSIGNED\n";
    echo "   Buttons: Start Class\n";
}
