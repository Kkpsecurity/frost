<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InstLesson;
use App\Models\StudentLesson;

echo "=== MARKING LESSON 6 AS FAILED (FOR TESTING) ===\n\n";

// Find the completed InstLesson for lesson 6
$instLesson = InstLesson::where('lesson_id', 6)
    ->where('inst_unit_id', 10676)
    ->first();

if (!$instLesson) {
    echo "❌ InstLesson not found\n";
    exit;
}

echo "Found InstLesson ID: {$instLesson->id}\n";
echo "Status: " . ($instLesson->completed_at ? 'COMPLETED' : 'ACTIVE') . "\n\n";

// Mark as failed by setting completed_at to NULL and adding a failed flag
$instLesson->update([
    'completed_at' => null,
    'started_at' => null,
    'completed_by' => null,
]);

echo "✅ Lesson marked as NOT COMPLETED (reset for testing)\n\n";

// Also clean up any StudentLesson records that might have been created
$deletedCount = StudentLesson::where('lesson_id', 6)
    ->whereHas('StudentUnit', function($q) {
        $q->where('course_date_id', 10757);
    })
    ->delete();

echo "🗑️  Deleted {$deletedCount} StudentLesson records\n\n";
echo "Ready to test: Start lesson 6 again and verify StudentLesson records are created\n";
