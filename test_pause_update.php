<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PAUSE UPDATE DIAGNOSTIC ===\n\n";

// Find the inst_lesson record
$instLesson = \App\Models\InstLesson::find(12504);

if (!$instLesson) {
    echo "❌ InstLesson 12504 not found!\n";
    exit(1);
}

echo "✅ Found InstLesson:\n";
echo "   ID: {$instLesson->id}\n";
echo "   Lesson ID: {$instLesson->lesson_id}\n";
echo "   Current is_paused: " . ($instLesson->is_paused ? 'true' : 'false') . "\n";
echo "   Current is_paused (raw): " . var_export($instLesson->is_paused, true) . "\n\n";

echo "📝 Attempting to update is_paused to true...\n";
$result = $instLesson->update(['is_paused' => true]);
echo "   Update result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n\n";

echo "🔍 Checking database directly (raw query):\n";
$raw = \DB::table('inst_lesson')->where('id', 12504)->first();
echo "   is_paused from DB: " . var_export($raw->is_paused, true) . "\n";
echo "   Type: " . gettype($raw->is_paused) . "\n\n";

echo "🔍 Re-fetching model:\n";
$instLesson = \App\Models\InstLesson::find(12504);
echo "   is_paused: " . ($instLesson->is_paused ? 'true' : 'false') . "\n";
echo "   is_paused (raw): " . var_export($instLesson->is_paused, true) . "\n\n";

echo "📝 Attempting to set back to false...\n";
$result = $instLesson->update(['is_paused' => false]);
echo "   Update result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n\n";

echo "✅ Diagnostic complete!\n";
