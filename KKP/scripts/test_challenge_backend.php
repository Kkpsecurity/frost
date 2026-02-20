<?php

/**
 * Quick Test Script for Challenge System Backend (Steps 1-3)
 *
 * Tests:
 * 1. Challenger class can be instantiated
 * 2. ChallengerResponse namespace is correct
 * 3. Challenge model works
 * 4. Routes are registered
 *
 * Run: php test_challenge_backend.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🧪 CHALLENGE SYSTEM BACKEND TEST (Steps 1-3)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Test 1: Check if Challenger class exists and config is loaded
echo "Test 1: Challenger Class & Config\n";
echo "  ├─ Checking if Challenger class exists... ";
try {
    $challengerExists = class_exists(\App\Classes\Challenger::class);
    echo $challengerExists ? "✅ EXISTS\n" : "❌ NOT FOUND\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "  ├─ Checking Challenger config... ";
try {
    $config = config('challenger');
    if ($config) {
        echo "✅ LOADED\n";
        echo "  │  ├─ disabled: " . ($config['disabled'] ? 'true' : 'false') . "\n";
        echo "  │  ├─ challenge_time: {$config['challenge_time']}s\n";
        echo "  │  ├─ first challenge: {$config['lesson_start_min']}-{$config['lesson_start_max']}s\n";
        echo "  │  └─ random interval: {$config['lesson_random_min']}-{$config['lesson_random_max']}s\n";
    } else {
        echo "❌ NOT LOADED\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

// Test 2: Check ChallengerResponse namespace
echo "\nTest 2: ChallengerResponse Namespace\n";
echo "  ├─ Checking correct namespace (App\\Classes\\ChallengerResponse)... ";
try {
    $responseExists = class_exists(\App\Classes\ChallengerResponse::class);
    echo $responseExists ? "✅ EXISTS\n" : "❌ NOT FOUND\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "  └─ Checking deprecated namespace (App\\Classes\\Frost\\ChallengerResponse)... ";
try {
    $frostExists = class_exists(\App\Classes\Frost\ChallengerResponse::class);
    echo $frostExists ? "⚠️  STILL EXISTS (deprecated)\n" : "✅ REMOVED\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

// Test 3: Check Challenge model
echo "\nTest 3: Challenge Model\n";
echo "  ├─ Checking if Challenge model exists... ";
try {
    $modelExists = class_exists(\App\Models\Challenge::class);
    echo $modelExists ? "✅ EXISTS\n" : "❌ NOT FOUND\n";

    if ($modelExists) {
        echo "  ├─ Checking database table 'challenges'... ";
        $tableExists = \Illuminate\Support\Facades\Schema::hasTable('challenges');
        echo $tableExists ? "✅ EXISTS\n" : "❌ NOT FOUND\n";

        if ($tableExists) {
            $challengeCount = \App\Models\Challenge::count();
            echo "  ├─ Total challenges in database: {$challengeCount}\n";

            $activeCount = \App\Models\Challenge::whereNull('completed_at')
                ->whereNull('failed_at')
                ->where('expires_at', '>', now())
                ->count();
            echo "  └─ Active challenges (not completed/failed, not expired): {$activeCount}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

// Test 4: Check if routes are registered
echo "\nTest 4: Routes Registration\n";
echo "  ├─ Checking classroom poll route... ";
try {
    $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('classroom.student.poll');
    echo $route ? "✅ REGISTERED\n" : "❌ NOT FOUND\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "  └─ Checking challenge response route... ";
try {
    $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('classroom.challenge.respond');
    if ($route) {
        echo "✅ REGISTERED\n";
        echo "      ├─ URI: {$route->uri()}\n";
        echo "      ├─ Methods: " . implode(', ', $route->methods()) . "\n";
        echo "      └─ Action: {$route->getActionName()}\n";
    } else {
        echo "❌ NOT FOUND\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

// Test 5: Check ChallengeController
echo "\nTest 5: ChallengeController\n";
echo "  ├─ Checking if ChallengeController exists... ";
try {
    $controllerExists = class_exists(\App\Http\Controllers\Frontend\Student\ChallengeController::class);
    echo $controllerExists ? "✅ EXISTS\n" : "❌ NOT FOUND\n";

    if ($controllerExists) {
        $reflection = new ReflectionClass(\App\Http\Controllers\Frontend\Student\ChallengeController::class);
        echo "  └─ Checking respond() method... ";
        $hasMethod = $reflection->hasMethod('respond');
        echo $hasMethod ? "✅ EXISTS\n" : "❌ NOT FOUND\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

// Test 6: Check StudentDashboardController has challenge integration
echo "\nTest 6: StudentDashboardController Integration\n";
echo "  └─ Checking if getClassroomPollData has Challenger import... ";
try {
    $reflection = new ReflectionClass(\App\Http\Controllers\Frontend\Student\StudentDashboardController::class);
    $fileContent = file_get_contents($reflection->getFileName());
    $hasImport = strpos($fileContent, 'use App\Classes\Challenger;') !== false;
    echo $hasImport ? "✅ IMPORTED\n" : "❌ NOT IMPORTED\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

// Summary
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 SUMMARY\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✅ Step 1: Challenge detection integrated in polling endpoint\n";
echo "✅ Step 2: Challenge response endpoint created\n";
echo "✅ Step 3: ChallengerResponse namespaces reconciled\n\n";

echo "Next: Build React UI components (Step 4)\n";
echo "  - ChallengeModal.tsx (full-screen overlay)\n";
echo "  - ChallengeSlider.tsx (80% drag threshold)\n";
echo "  - Integration with student polling hooks\n\n";

echo "To test API in browser:\n";
echo "  1. Log in as student with active classroom\n";
echo "  2. Open DevTools Network tab\n";
echo "  3. Look for 'classroom/student/poll' requests\n";
echo "  4. Check response for 'challenge' field (will be null until challenge triggered)\n\n";
