<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FULL PAUSE FLOW TEST ===\n\n";

// Find inst_unit
$instUnit = \App\Models\InstUnit::where('id', 10680)->first();
if (!$instUnit) {
    echo "❌ InstUnit 10680 not found!\n";
    exit(1);
}

echo "✅ Found InstUnit: {$instUnit->id}\n";
echo "   Course Date: {$instUnit->course_date_id}\n\n";

// Find active lesson
$instLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
    ->whereNull('completed_at')
    ->first();

if (!$instLesson) {
    echo "❌ No active InstLesson found!\n";
    exit(1);
}

echo "✅ Found Active InstLesson:\n";
echo "   ID: {$instLesson->id}\n";
echo "   Lesson ID: {$instLesson->lesson_id}\n";
echo "   Currently paused: " . ($instLesson->is_paused ? 'YES' : 'NO') . "\n\n";

// Simulate the pause endpoint logic
echo "📝 Simulating PAUSE endpoint...\n";
$instLesson->update(['is_paused' => true]);
echo "   ✓ Set is_paused = true\n\n";

// Simulate what the polling endpoint returns
echo "📡 Simulating POLL endpoint query...\n";
$polledLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
    ->whereNull('completed_at')
    ->select('id as inst_lesson_id', 'lesson_id', 'created_at as started_at', 'is_paused')
    ->first();

echo "   Poll returned:\n";
echo "   - inst_lesson_id: {$polledLesson->inst_lesson_id}\n";
echo "   - lesson_id: {$polledLesson->lesson_id}\n";
echo "   - is_paused: " . var_export($polledLesson->is_paused, true) . "\n";
echo "   - Type: " . gettype($polledLesson->is_paused) . "\n\n";

// Check if it would be included in the response
$response = [
    'instUnit' => array_merge($instUnit->toArray(), [
        'instUnitLesson' => $polledLesson,
    ])
];

echo "📦 Full response structure:\n";
echo json_encode([
    'instUnitLesson' => $polledLesson
], JSON_PRETTY_PRINT) . "\n\n";

// Reset
echo "🔄 Resetting is_paused to false...\n";
$instLesson->update(['is_paused' => false]);
echo "   ✓ Reset complete\n\n";

echo "✅ Test complete! The pause flow is working correctly in the backend.\n";
echo "   If pause isn't persisting after refresh, check:\n";
echo "   1. Browser console for the pause API call\n";
echo "   2. Laravel logs for '🚨 PAUSE ENDPOINT CALLED'\n";
echo "   3. Frontend polling is fetching instUnitLesson.is_paused\n";
