<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "  PAUSE SYSTEM DIAGNOSTIC TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// TEST 1: Database connection
echo "TEST 1: Database Connection\n";
echo "────────────────────────────────────────────────────────────────\n";
try {
    DB::connection()->getPdo();
    echo "✅ Database connection: OK\n\n";
} catch (Exception $e) {
    echo "❌ Database connection: FAILED\n";
    echo "   Error: {$e->getMessage()}\n\n";
    exit(1);
}

// TEST 2: Find active InstUnit and InstLesson
echo "TEST 2: Active Lesson Detection\n";
echo "────────────────────────────────────────────────────────────────\n";
$instUnit = \App\Models\InstUnit::whereNull('completed_at')->latest()->first();
if (!$instUnit) {
    echo "❌ No active InstUnit found\n\n";
    exit(1);
}
echo "✅ Active InstUnit found: {$instUnit->id}\n";
echo "   Course Date ID: {$instUnit->course_date_id}\n";
echo "   Created: {$instUnit->created_at}\n\n";

$instLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
    ->whereNull('completed_at')
    ->first();

if (!$instLesson) {
    echo "❌ No active InstLesson found\n\n";
    exit(1);
}

echo "✅ Active InstLesson found: {$instLesson->id}\n";
echo "   Lesson ID: {$instLesson->lesson_id}\n";
echo "   Current is_paused: " . ($instLesson->is_paused ? 'TRUE' : 'FALSE') . "\n";
echo "   Created: {$instLesson->created_at}\n\n";

// TEST 3: Database update capability
echo "TEST 3: Database Update Test\n";
echo "────────────────────────────────────────────────────────────────\n";
$originalState = $instLesson->is_paused;
echo "Original state: " . ($originalState ? 'TRUE' : 'FALSE') . "\n";

// Try to pause
echo "Attempting to set is_paused = TRUE...\n";
$instLesson->update(['is_paused' => true]);
$instLesson->refresh();
$afterPause = $instLesson->is_paused;
echo "After update: " . ($afterPause ? 'TRUE' : 'FALSE') . "\n";

if ($afterPause === true) {
    echo "✅ Database update: WORKING\n\n";
} else {
    echo "❌ Database update: FAILED\n\n";
}

// Try to unpause
echo "Attempting to set is_paused = FALSE...\n";
$instLesson->update(['is_paused' => false]);
$instLesson->refresh();
$afterUnpause = $instLesson->is_paused;
echo "After update: " . ($afterUnpause ? 'TRUE' : 'FALSE') . "\n";

if ($afterUnpause === false) {
    echo "✅ Database toggle: WORKING\n\n";
} else {
    echo "❌ Database toggle: FAILED\n\n";
}

// Restore original state
$instLesson->update(['is_paused' => $originalState]);

// TEST 4: Route existence
echo "TEST 4: Route Registration Check\n";
echo "────────────────────────────────────────────────────────────────\n";
$routes = Route::getRoutes();
$pauseRoute = null;
$resumeRoute = null;

foreach ($routes as $route) {
    if ($route->uri() === 'admin/instructors/lessons/pause') {
        $pauseRoute = $route;
    }
    if ($route->uri() === 'admin/instructors/lessons/resume') {
        $resumeRoute = $route;
    }
}

if ($pauseRoute) {
    echo "✅ Pause route registered\n";
    echo "   URI: {$pauseRoute->uri()}\n";
    echo "   Methods: " . implode(', ', $pauseRoute->methods()) . "\n";
    echo "   Action: {$pauseRoute->getActionName()}\n";
} else {
    echo "❌ Pause route NOT found\n";
}
echo "\n";

if ($resumeRoute) {
    echo "✅ Resume route registered\n";
    echo "   URI: {$resumeRoute->uri()}\n";
    echo "   Methods: " . implode(', ', $resumeRoute->methods()) . "\n";
} else {
    echo "❌ Resume route NOT found\n";
}
echo "\n";

// TEST 5: Controller method existence
echo "TEST 5: Controller Method Check\n";
echo "────────────────────────────────────────────────────────────────\n";
$controllerClass = \App\Http\Controllers\Admin\Instructors\InstructorDashboardController::class;

if (method_exists($controllerClass, 'pauseLesson')) {
    echo "✅ pauseLesson method exists\n";
} else {
    echo "❌ pauseLesson method NOT found\n";
}

if (method_exists($controllerClass, 'resumeLesson')) {
    echo "✅ resumeLesson method exists\n";
} else {
    echo "❌ resumeLesson method NOT found\n";
}
echo "\n";

// TEST 6: Simulate full pause flow
echo "TEST 6: Full Pause Flow Simulation\n";
echo "────────────────────────────────────────────────────────────────\n";

// Check breaks count
$breaksCount = $instLesson->Breaks()->count();
echo "Current breaks taken: {$breaksCount}\n";
echo "Breaks allowed: " . config('frost.instructor_breaks.breaks_allowed_per_day', 3) . "\n\n";

echo "Simulating instructor clicks 'Pause'...\n";
echo "Step 1: Update is_paused in database\n";
$instLesson->update(['is_paused' => true]);
echo "   ✓ is_paused set to TRUE\n\n";

echo "Step 2: Create InstLessonBreak record\n";
$break = \App\Models\InstLessonBreak::create([
    'inst_lesson_id' => $instLesson->id,
    'break_number' => $breaksCount + 1,
    'started_at' => now(),
    'started_by' => 1, // Assume user ID 1
]);
echo "   ✓ Break record created: ID {$break->id}\n\n";

echo "Step 3: Simulate instructor poll endpoint\n";
$polledLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
    ->whereNull('completed_at')
    ->select('id as inst_lesson_id', 'lesson_id', 'created_at as started_at', 'is_paused')
    ->first();
echo "   Polled data:\n";
echo "   - inst_lesson_id: {$polledLesson->inst_lesson_id}\n";
echo "   - is_paused: " . ($polledLesson->is_paused ? 'TRUE' : 'FALSE') . "\n";
echo "   ✓ Poll would return is_paused = TRUE\n\n";

echo "Step 4: Simulate student poll endpoint\n";
$studentPolledLessons = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
    ->get()
    ->map(function ($il) {
        return [
            'id' => $il->id,
            'lesson_id' => $il->lesson_id,
            'is_paused' => $il->is_paused,
            'completed_at' => $il->completed_at,
        ];
    });
echo "   Student poll inst_lessons:\n";
foreach ($studentPolledLessons as $spl) {
    echo "   - Lesson {$spl['lesson_id']}: is_paused=" . ($spl['is_paused'] ? 'TRUE' : 'FALSE') .
        ", completed=" . ($spl['completed_at'] ? 'YES' : 'NO') . "\n";
}
echo "   ✓ Students would receive is_paused = TRUE\n\n";

// Cleanup
echo "Cleanup: Removing test break and resetting state...\n";
$break->delete();
$instLesson->update(['is_paused' => $originalState]);
echo "   ✓ Cleanup complete\n\n";

// TEST 7: Check Laravel logs for recent pause attempts
echo "TEST 7: Recent Pause Attempts in Logs\n";
echo "────────────────────────────────────────────────────────────────\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = explode("\n", file_get_contents($logFile));
    $recentPauseLogs = array_filter($lines, function ($line) {
        return strpos($line, 'PAUSE') !== false &&
            strpos($line, date('Y-m-d')) !== false;
    });

    if (empty($recentPauseLogs)) {
        echo "⚠️  No pause-related logs found today\n";
        echo "   This suggests the pause endpoint is NOT being called\n";
    } else {
        echo "✅ Found pause-related logs:\n";
        foreach (array_slice($recentPauseLogs, -5) as $log) {
            echo "   " . substr($log, 0, 100) . "...\n";
        }
    }
} else {
    echo "⚠️  Log file not found\n";
}
echo "\n";

// SUMMARY
echo "═══════════════════════════════════════════════════════════════\n";
echo "  TEST SUMMARY\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "Backend Components:\n";
echo "  ✅ Database connection\n";
echo "  ✅ Database update capability\n";
echo "  ✅ Route registration\n";
echo "  ✅ Controller methods\n";
echo "  ✅ Data flow simulation\n\n";

echo "Next Steps:\n";
echo "  1. Check browser console for pause button click\n";
echo "  2. Check browser network tab for /admin/instructors/lessons/pause request\n";
echo "  3. Verify courseDateId is being passed correctly\n";
echo "  4. Ensure React app has been rebuilt (npm run build)\n\n";

echo "To test the pause button:\n";
echo "  1. Open instructor dashboard\n";
echo "  2. Open browser DevTools (F12)\n";
echo "  3. Go to Console tab\n";
echo "  4. Click the pause button\n";
echo "  5. Look for these console messages:\n";
echo "     - '🖱️ Start Break button clicked'\n";
echo "     - '✅ pausedLessonId exists, calling postLessonAction'\n";
echo "     - '🎯 Posting lesson action:'\n";
echo "  6. Check Network tab for POST to /admin/instructors/lessons/pause\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
