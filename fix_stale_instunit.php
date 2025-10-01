<?php
/**
 * EMERGENCY FIX: Clear stale InstUnit completed_at to allow today's class to start
 */

require_once __DIR__ . '/vendor/autoload.php';

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== EMERGENCY FIX: CLEAR STALE INSTUNIT ===\n";
echo "Current Date: " . now()->format('Y-m-d H:i:s') . "\n\n";

// Get the problematic InstUnit
$instUnit = \App\Models\InstUnit::find(10459);

echo "BEFORE FIX:\n";
echo "InstUnit ID: {$instUnit->id}\n";
echo "Course Date ID: {$instUnit->course_date_id}\n";
echo "Created At: " . \Carbon\Carbon::parse($instUnit->created_at)->format('Y-m-d H:i:s') . "\n";
echo "Completed At: " . ($instUnit->completed_at ? \Carbon\Carbon::parse($instUnit->completed_at)->format('Y-m-d H:i:s') : 'NULL') . "\n";
echo "Created By: {$instUnit->created_by}\n\n";

// Clear the completed_at field to "uncomplete" the stale class
echo "APPLYING FIX: Clearing completed_at field...\n";
$instUnit->completed_at = null;
$instUnit->save();

echo "✅ InstUnit {$instUnit->id} completed_at field cleared!\n\n";

echo "AFTER FIX:\n";
$instUnit->refresh();
echo "InstUnit ID: {$instUnit->id}\n";
echo "Course Date ID: {$instUnit->course_date_id}\n";
echo "Created At: " . \Carbon\Carbon::parse($instUnit->created_at)->format('Y-m-d H:i:s') . "\n";
echo "Completed At: " . ($instUnit->completed_at ? \Carbon\Carbon::parse($instUnit->completed_at)->format('Y-m-d H:i:s') : 'NULL') . "\n";
echo "Created By: {$instUnit->created_by}\n\n";

echo "🎯 NOW TEST THE CLASS STATUS:\n";
echo "The FL-D40-D2 class should now show as 'IN_PROGRESS' or 'UNASSIGNED' instead of 'EXPIRED'\n";
echo "Try refreshing the instructor dashboard to see the change.\n\n";

echo "=== FIX COMPLETE ===\n";
