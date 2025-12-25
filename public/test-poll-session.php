<?php
/**
 * Quick test to verify polling integration for lesson sessions
 * Tests /classroom/class/data endpoint for active_self_study_session
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== POLLING SESSION TEST ===\n\n";

// Test data
$userId = 2;
$courseAuthId = 2;

echo "Test User ID: $userId\n";
echo "Test Course Auth ID: $courseAuthId\n\n";

// Create a test session
echo "Step 1: Creating test session...\n";
$user = \App\Models\User::find($userId);
$lesson = \App\Models\Lesson::find(1); // Use first lesson

if (!$user || !$lesson) {
    echo "ERROR: User or lesson not found\n";
    exit(1);
}

echo "  User: {$user->name} (ID: {$user->id})\n";
echo "  Lesson: {$lesson->name} (ID: {$lesson->id})\n";

$service = app(\App\Services\LessonSessionService::class);

try {
    $result = $service->startSession(
        student: $user,
        courseAuthId: $courseAuthId,
        lessonId: $lesson->id,
        videoDurationSeconds: 21600 // 6 hours
    );

    if ($result['success']) {
        $session = $result['session'];
        echo "✓ Session created successfully\n";
        echo "  Session ID: {$session->session_id}\n";
        echo "  Lesson ID: {$session->lesson_id}\n";
        echo "  Expires at: {$session->session_expires_at}\n\n";
    } else {
        echo "ERROR: {$result['message']}\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

// Test polling endpoint
echo "Step 2: Testing polling endpoint...\n";
$controller = app(\App\Http\Controllers\Student\StudentDashboardController::class);

// Simulate request
$request = new \Illuminate\Http\Request();
$request->merge(['course_auth_id' => $courseAuthId]);

try {
    $response = $controller->getClassData($request);
    $data = $response->getData(true);

    echo "✓ Poll endpoint responded\n\n";

    if (isset($data['data']['active_self_study_session'])) {
        $activeSession = $data['data']['active_self_study_session'];
        echo "✓ ACTIVE SESSION DETECTED IN POLL:\n";
        echo "  Session ID: {$activeSession['session_id']}\n";
        echo "  Lesson ID: {$activeSession['lesson_id']}\n";
        echo "  Time Remaining: {$activeSession['time_remaining_minutes']} minutes\n";
        echo "  Pause Remaining: {$activeSession['pause_remaining_minutes']} minutes\n";
        echo "  Progress: {$activeSession['completion_percentage']}%\n";
        echo "  Started: {$activeSession['started_at']}\n";
        echo "  Expires: {$activeSession['expires_at']}\n\n";
        echo "✅ POLLING INTEGRATION WORKING!\n\n";
    } else {
        echo "❌ ERROR: active_self_study_session not in poll response\n";
        echo "Response keys: " . implode(', ', array_keys($data['data'])) . "\n\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

// Cleanup
echo "Step 3: Cleanup (marking session as consumed)...\n";
$session->update(['quota_status' => 'consumed']);
echo "✓ Session marked as consumed\n\n";

echo "=== TEST COMPLETE ===\n";
