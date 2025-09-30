<?php

/**
 * Test Script: InstUnit Creation via ClassroomSessionService
 *
 * This script tests the creation of InstUnit records when starting a classroom session.
 * Run this script to verify the database operations work correctly.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\ClassroomSessionService;
use App\Models\User;
use App\Models\CourseDate;
use App\Models\InstUnit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== InstUnit Creation Test ===\n\n";

try {
    // Set up test user (replace with actual instructor user ID)
    $instructorId = 1; // Change this to a valid instructor user ID
    $instructor = User::find($instructorId);

    if (!$instructor) {
        echo "ERROR: Instructor user with ID {$instructorId} not found!\n";
        echo "Please update the \$instructorId variable with a valid user ID.\n";
        exit(1);
    }

    echo "✓ Found instructor: {$instructor->fullname()} (ID: {$instructorId})\n";

    // Set authenticated user for the service
    Auth::login($instructor);
    echo "✓ Authenticated as instructor: {$instructor->fullname()}\n";

    // Find a CourseDate to test with
    $courseDate = CourseDate::whereDoesntHave('InstUnit')->first();

    if (!$courseDate) {
        echo "ERROR: No CourseDate found without an existing InstUnit!\n";
        echo "Please create a CourseDate record first or choose a different test CourseDate.\n";
        exit(1);
    }

    echo "✓ Found test CourseDate: ID {$courseDate->id}\n";
    echo "  - Course: " . ($courseDate->GetCourse()->title ?? 'Unknown') . "\n";
    echo "  - Unit: " . ($courseDate->GetCourseUnit()->title ?? 'Unknown') . "\n";

    // Initialize the service
    $sessionService = new ClassroomSessionService();
    echo "✓ ClassroomSessionService initialized\n";

    // Test: Start a classroom session
    echo "\n--- Testing startClassroomSession ---\n";
    $instUnit = $sessionService->startClassroomSession($courseDate->id);

    if ($instUnit) {
        echo "✅ SUCCESS: InstUnit created successfully!\n";
        echo "  - InstUnit ID: {$instUnit->id}\n";
        echo "  - CourseDate ID: {$instUnit->course_date_id}\n";
        echo "  - Instructor ID: {$instUnit->created_by}\n";
        echo "  - Instructor Name: " . ($instUnit->GetCreatedBy()->fullname() ?? 'Unknown') . "\n";
        echo "  - Created At: {$instUnit->created_at}\n";

        // Test: Get session info
        echo "\n--- Testing getClassroomSession ---\n";
        $sessionInfo = $sessionService->getClassroomSession($courseDate->id);

        if ($sessionInfo['exists']) {
            echo "✅ SUCCESS: Session info retrieved successfully!\n";
            echo "  - Session exists: " . ($sessionInfo['exists'] ? 'YES' : 'NO') . "\n";
            echo "  - Course Name: " . ($sessionInfo['course_name'] ?? 'Unknown') . "\n";
            echo "  - Instructor: " . ($sessionInfo['instructor']['name'] ?? 'Unknown') . "\n";
            echo "  - Is Active: " . ($sessionInfo['is_active'] ? 'YES' : 'NO') . "\n";
        } else {
            echo "❌ FAILED: Could not retrieve session info\n";
        }

        // Test: Complete the session (optional)
        echo "\n--- Testing completeClassroomSession ---\n";
        $completed = $sessionService->completeClassroomSession($instUnit->id);

        if ($completed) {
            echo "✅ SUCCESS: Session completed successfully!\n";

            // Refresh the InstUnit to see the completion
            $instUnit->refresh();
            echo "  - Completed At: {$instUnit->completed_at}\n";
            echo "  - Completed By: " . ($instUnit->GetCompletedBy()->fullname() ?? 'Unknown') . "\n";
        } else {
            echo "❌ FAILED: Could not complete session\n";
        }

    } else {
        echo "❌ FAILED: InstUnit creation failed!\n";
        echo "Check the Laravel logs for more details.\n";
    }

} catch (Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "Check the inst_unit table in your database to verify the records were created.\n";
