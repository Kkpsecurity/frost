<?php

// Simple test to check onboarding endpoint
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\User;
use Carbon\Carbon;

echo "🔍 TESTING ONBOARDING DETECTION\n";
echo "================================\n\n";

// Check for CourseDate for today
$today = Carbon::today();
echo "📅 Today: " . $today->toDateString() . "\n";

$courseDates = CourseDate::whereDate('starts_at', '<=', $today)
    ->whereDate('ends_at', '>=', $today)
    ->get(['id', 'course_id', 'starts_at', 'ends_at']);

echo "📊 Found " . $courseDates->count() . " active CourseDate(s) for today:\n";

foreach ($courseDates as $courseDate) {
    echo "  - ID: {$courseDate->id}, Course: {$courseDate->course_id}, Starts: {$courseDate->starts_at}, Ends: {$courseDate->ends_at}\n";

    // Check for InstUnit
    $instUnit = InstUnit::where('course_date_id', $courseDate->id)
        ->whereNull('ended_at')
        ->first();

    if ($instUnit) {
        echo "    ✅ Has active InstUnit: {$instUnit->id}\n";
    } else {
        echo "    ❌ No active InstUnit found\n";
    }
}

// Test with a sample user (assuming user ID 1 exists)
$user = User::find(1);
if ($user) {
    echo "\n👤 Testing with User: {$user->name} (ID: {$user->id})\n";

    // Test the actual controller logic
    foreach ($courseDates as $courseDate) {
        echo "\n🧪 Testing CourseDate {$courseDate->id}:\n";

        // Check if user has CourseAuth for this course
        $courseAuth = $courseDate->Course->CourseAuths()
            ->where('user_id', $user->id)
            ->first();

        if ($courseAuth) {
            echo "  ✅ User has CourseAuth: {$courseAuth->id}\n";
        } else {
            echo "  ❌ User has no CourseAuth for this course\n";
        }
    }
} else {
    echo "\n❌ No test user found (ID: 1)\n";
}

echo "\n🎯 CONCLUSION:\n";
echo "==============\n";
if ($courseDates->isEmpty()) {
    echo "❌ No active CourseDate found - dashboard will show offline mode\n";
} else {
    echo "✅ Active CourseDate(s) found - should redirect to onboarding\n";
    echo "💡 If still showing offline, check:\n";
    echo "   1. User authentication\n";
    echo "   2. CourseAuth relationship\n";
    echo "   3. Browser console for errors\n";
    echo "   4. Network tab in DevTools\n";
}
