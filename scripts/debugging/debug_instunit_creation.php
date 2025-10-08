<?php
/**
 * Debug InstUnit creation issue
 */

require_once __DIR__ . '/vendor/autoload.php';

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING INSTUNIT CREATION ===\n";

// Mock authentication
$admin = \App\Models\Admin::find(13);
auth('admin')->login($admin);
echo "Authenticated as: " . auth('admin')->user()->name . "\n\n";

// Test InstUnit creation directly
echo "1. TESTING DIRECT INSTUNIT CREATION:\n";
try {
    $instUnit = \App\Models\InstUnit::create([
        'course_date_id' => 10556,
        'created_by' => auth('admin')->id(),
        'created_at' => now(),
        'assistant_id' => null,
    ]);

    echo "   ✅ SUCCESS: InstUnit created directly!\n";
    echo "   ID: {$instUnit->id}\n";
    echo "   Course Date ID: {$instUnit->course_date_id}\n";
    echo "   Created By: {$instUnit->created_by}\n";

} catch (Exception $e) {
    echo "   ❌ DIRECT CREATION FAILED: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n2. TESTING WITH ClassroomSessionService:\n";
$sessionService = new \App\Services\ClassroomSessionService();

// Check if the user is properly authenticated
echo "   Auth check - User ID: " . auth('admin')->id() . "\n";
echo "   Auth check - User: " . (auth('admin')->user() ? 'AUTHENTICATED' : 'NOT AUTHENTICATED') . "\n";

try {
    $instUnit = $sessionService->startClassroomSession(10556);

    if ($instUnit) {
        echo "   ✅ SUCCESS via Service!\n";
        echo "   ID: {$instUnit->id}\n";
    } else {
        echo "   ❌ Service returned null\n";
    }

} catch (Exception $e) {
    echo "   ❌ SERVICE EXCEPTION: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== END DEBUG ===\n";
