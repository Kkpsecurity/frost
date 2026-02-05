<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentLesson;
use App\Classes\Challenger;

echo "🔍 Debugging Active Challenge System\n";
echo "====================================\n\n";

// Find the active student lesson
$studentLesson = StudentLesson::where('id', 209060)->first();

if (!$studentLesson) {
    echo "❌ Student lesson 209060 not found\n";
    exit;
}

echo "📊 Student Lesson Info:\n";
echo "  ID: {$studentLesson->id}\n";
echo "  Lesson ID: {$studentLesson->lesson_id}\n";
echo "  Created At: {$studentLesson->created_at}\n";
echo "  Created At Type: " . get_class($studentLesson->created_at) . "\n";
echo "  Age (seconds): " . now()->diffInSeconds($studentLesson->created_at) . "\n";
echo "  Completed: " . ($studentLesson->completed_at ? 'Yes' : 'No') . "\n\n";

// Get completed lesson IDs
$completedLessonIds = StudentLesson::where('student_unit_id', $studentLesson->student_unit_id)
    ->whereNotNull('completed_at')
    ->whereDate('created_at', now()->toDateString())
    ->pluck('lesson_id')
    ->toArray();

echo "📚 Completed Lesson IDs Today: " . json_encode($completedLessonIds) . "\n\n";

try {
    echo "🎯 Calling Challenger::init() and Ready()...\n";
    Challenger::init($studentLesson);
    $challenge = Challenger::Ready($studentLesson, $completedLessonIds);

    if ($challenge) {
        echo "✅ Challenge Ready!\n";
        echo "  Challenge ID: {$challenge->challenge_id}\n";
        echo "  Is Final: " . ($challenge->is_final ? 'Yes' : 'No') . "\n";
        echo "  Is EOL: " . ($challenge->is_eol ? 'Yes' : 'No') . "\n";
        echo "  Expires At: {$challenge->expires_at}\n";
    } else {
        echo "⏳ No Challenge Ready Yet\n";
        echo "  (This is expected if timing window hasn't been reached)\n";
    }
} catch (\Exception $e) {
    echo "❌ Error calling Challenger::Ready():\n";
    echo "  Message: {$e->getMessage()}\n";
    echo "  File: {$e->getFile()}:{$e->getLine()}\n";
    echo "  Trace:\n";
    echo $e->getTraceAsString();
}

echo "\n\n";
echo "🔧 Dev Mode Configuration:\n";
echo "  Enabled: " . (config('challenger.dev_mode') ? 'Yes' : 'No') . "\n";
echo "  First Challenge Min: " . config('challenger.dev_lesson_start_min') . "s\n";
echo "  First Challenge Max: " . config('challenger.dev_lesson_start_max') . "s\n";
