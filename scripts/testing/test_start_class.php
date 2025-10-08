<?php
/**
 * Test the startClass functionality
 */

require_once __DIR__ . '/vendor/autoload.php';

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING START CLASS FUNCTIONALITY ===\n";
echo "Current Date: " . now()->format('Y-m-d H:i:s') . "\n\n";

// Test the ClassroomSessionService directly
$sessionService = new \App\Services\ClassroomSessionService();

// Get the FL-D40-D2 CourseDate
$courseDate = \App\Models\CourseDate::find(10556);
echo "1. COURSEDATE INFO:\n";
echo "   ID: {$courseDate->id}\n";
echo "   Course: " . ($courseDate->courseUnit->course->title ?? 'Unknown') . "\n";
echo "   Starts At: {$courseDate->starts_at}\n";
echo "   Current InstUnit: " . ($courseDate->instUnit ? 'EXISTS (ID: ' . $courseDate->instUnit->id . ')' : 'NONE') . "\n\n";

// Mock an authenticated admin user (ID 13 was the original instructor)
echo "2. SIMULATING AUTHENTICATED ADMIN (ID 13):\n";
$admin = \App\Models\Admin::find(13);
if ($admin) {
    echo "   Admin Found: {$admin->name} (ID: {$admin->id})\n";
    // Temporarily set the auth guard for testing
    auth('admin')->login($admin);
    echo "   Admin authenticated for testing\n\n";
} else {
    echo "   ERROR: Admin ID 13 not found\n\n";
}

// Test starting classroom session
echo "3. TESTING startClassroomSession():\n";
try {
    $instUnit = $sessionService->startClassroomSession($courseDate->id);

    if ($instUnit) {
        echo "   ✅ SUCCESS: InstUnit created!\n";
        echo "   InstUnit ID: {$instUnit->id}\n";
        echo "   Course Date ID: {$instUnit->course_date_id}\n";
        echo "   Created By: {$instUnit->created_by}\n";
        echo "   Created At: {$instUnit->created_at}\n";
        echo "   Assistant ID: " . ($instUnit->assistant_id ?? 'NULL') . "\n";
        echo "   Completed At: " . ($instUnit->completed_at ?? 'NULL') . "\n\n";

        // Test the redirect URL
        echo "4. REDIRECT URL:\n";
        echo "   Classroom URL: /instructor/classroom/{$courseDate->id}\n";
        echo "   Full URL: " . url("/instructor/classroom/{$courseDate->id}") . "\n\n";

    } else {
        echo "   ❌ FAILED: InstUnit not created\n\n";
    }

} catch (Exception $e) {
    echo "   ❌ EXCEPTION: " . $e->getMessage() . "\n\n";
}

// Verify the CourseDate now has an InstUnit
echo "5. VERIFICATION - CHECK COURSEDATE AGAIN:\n";
$courseDate->refresh();
if ($courseDate->instUnit) {
    echo "   ✅ CourseDate now has InstUnit: ID {$courseDate->instUnit->id}\n";
    echo "   Instructor: " . ($courseDate->instUnit->GetCreatedBy()->name ?? 'Unknown') . "\n";
    echo "   Status: " . ($courseDate->instUnit->completed_at ? 'COMPLETED' : 'ACTIVE') . "\n";
} else {
    echo "   ❌ CourseDate still has no InstUnit\n";
}

echo "\n=== END TEST ===\n";
