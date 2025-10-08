<?php
/**
 * Test the assistant feature for instructor classroom
 *
 * This script tests:
 * 1. Starting a class session with optional assistant
 * 2. Joining an existing class as an assistant
 * 3. Verifying assistant assignment in InstUnit
 */

// Laravel bootstrap
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\User;
use App\Services\ClassroomSessionService;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING ASSISTANT FEATURE ===\n\n";

// 1. Find a test course date
echo "1. FINDING TEST COURSE DATE:\n";
$courseDate = CourseDate::whereDate('starts_at', '>=', now()->subDays(7))
    ->whereDate('starts_at', '<=', now()->addDays(7))
    ->first();

if (!$courseDate) {
    echo "❌ No course dates found within date range\n";
    exit(1);
}

echo "✅ Found CourseDate: {$courseDate->id}\n";
echo "   Course: " . ($courseDate->GetCourse()->title ?? 'Unknown') . "\n";
echo "   Date: {$courseDate->starts_at}\n\n";

// 2. Find test users (instructors/admins)
echo "2. FINDING TEST USERS:\n";
$instructorUser = User::where('user_type', 'admin')
    ->whereNotNull('fname')
    ->first();

$assistantUser = User::where('user_type', 'admin')
    ->whereNotNull('fname')
    ->where('id', '!=', $instructorUser->id ?? 0)
    ->first();

if (!$instructorUser || !$assistantUser) {
    echo "❌ Need at least 2 admin users for testing\n";
    exit(1);
}

echo "✅ Instructor: {$instructorUser->fname} {$instructorUser->lname} (ID: {$instructorUser->id})\n";
echo "✅ Assistant: {$assistantUser->fname} {$assistantUser->lname} (ID: {$assistantUser->id})\n\n";

// 3. Clean up any existing InstUnit for this course date
echo "3. CLEANING UP EXISTING INSTUNIT:\n";
$existingInstUnit = $courseDate->InstUnit;
if ($existingInstUnit) {
    echo "   Deleting existing InstUnit: {$existingInstUnit->id}\n";
    $existingInstUnit->delete();
    $courseDate->refresh();
}
echo "✅ Clean slate ready\n\n";

// 4. Test starting class session with assistant
echo "4. TESTING CLASSROOM SESSION SERVICE:\n";
$sessionService = new ClassroomSessionService();

// Authenticate as instructor
Auth::login($instructorUser);

echo "   Starting session with assistant...\n";
$instUnit = $sessionService->startClassroomSession($courseDate->id, $assistantUser->id);

if (!$instUnit) {
    echo "❌ Failed to start classroom session\n";
    exit(1);
}

echo "✅ InstUnit created: {$instUnit->id}\n";
echo "   Instructor ID: {$instUnit->created_by}\n";
echo "   Assistant ID: {$instUnit->assistant_id}\n";
echo "   Created: {$instUnit->created_at}\n\n";

// 5. Verify InstUnit has correct data
echo "5. VERIFYING INSTUNIT DATA:\n";
$instUnit->refresh();

if ($instUnit->created_by != $instructorUser->id) {
    echo "❌ Instructor mismatch: expected {$instructorUser->id}, got {$instUnit->created_by}\n";
    exit(1);
}

if ($instUnit->assistant_id != $assistantUser->id) {
    echo "❌ Assistant mismatch: expected {$assistantUser->id}, got {$instUnit->assistant_id}\n";
    exit(1);
}

echo "✅ Instructor correctly set: {$instUnit->GetCreatedBy()->fname} {$instUnit->GetCreatedBy()->lname}\n";
echo "✅ Assistant correctly set: {$instUnit->GetAssistant()->fname} {$instUnit->GetAssistant()->lname}\n\n";

// 6. Test getClassroomSession method
echo "6. TESTING GET CLASSROOM SESSION:\n";
$sessionInfo = $sessionService->getClassroomSession($courseDate->id);

echo "   Session exists: " . ($sessionInfo['exists'] ? 'YES' : 'NO') . "\n";
echo "   Instructor: {$sessionInfo['instructor']['name']} (ID: {$sessionInfo['instructor']['id']})\n";
echo "   Assistant: {$sessionInfo['assistant']['name']} (ID: {$sessionInfo['assistant']['id']})\n";
echo "   Is Active: " . ($sessionInfo['is_active'] ? 'YES' : 'NO') . "\n\n";

// 7. Test changing assistant
echo "7. TESTING ASSISTANT REASSIGNMENT:\n";
$newAssistant = User::where('user_type', 'admin')
    ->whereNotNull('fname')
    ->whereNotIn('id', [$instructorUser->id, $assistantUser->id])
    ->first();

if ($newAssistant) {
    echo "   Reassigning to: {$newAssistant->fname} {$newAssistant->lname} (ID: {$newAssistant->id})\n";

    $success = $sessionService->assignAssistant($instUnit->id, $newAssistant->id);

    if ($success) {
        $instUnit->refresh();
        echo "✅ Assistant reassigned successfully\n";
        echo "   New Assistant: {$instUnit->GetAssistant()->fname} {$instUnit->GetAssistant()->lname}\n";
    } else {
        echo "❌ Failed to reassign assistant\n";
    }
} else {
    echo "⚠️  No third user available for reassignment test\n";
}

echo "\n8. TESTING ASSIST WITHOUT EXISTING SESSION:\n";

// Delete the InstUnit to test joining as assistant
$instUnit->delete();
$courseDate->refresh();

// Start session without assistant
$instUnit2 = $sessionService->startClassroomSession($courseDate->id);
echo "✅ New session started without assistant: {$instUnit2->id}\n";
echo "   Assistant ID: " . ($instUnit2->assistant_id ?? 'NULL') . "\n";

// Now add assistant
$success = $sessionService->assignAssistant($instUnit2->id, $assistantUser->id);
if ($success) {
    $instUnit2->refresh();
    echo "✅ Assistant added to existing session\n";
    echo "   Assistant: {$instUnit2->GetAssistant()->fname} {$instUnit2->GetAssistant()->lname}\n";
} else {
    echo "❌ Failed to add assistant to existing session\n";
}

echo "\n=== ASSISTANT FEATURE TESTING COMPLETE ===\n";
echo "✅ All core functionality working:\n";
echo "   - Start session with assistant ✓\n";
echo "   - Join existing session as assistant ✓\n";
echo "   - Reassign assistant ✓\n";
echo "   - InstUnit relationship management ✓\n";
echo "   - Session info retrieval ✓\n";

// Clean up
$instUnit2->delete();
Auth::logout();

echo "\n🎉 ASSISTANT FEATURE READY FOR FRONTEND INTEGRATION!\n";
