<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Testing Student Poll Data for Pause Detection\n";
echo str_repeat("=", 80) . "\n\n";

// Find active course date
$courseDate = \App\Models\CourseDate::where('status', 'active')
    ->where('date', today())
    ->first();

if (!$courseDate) {
    echo "❌ No active course date found for today\n";
    exit(1);
}

echo "✅ Found active CourseDate:\n";
echo "   ID: {$courseDate->id}\n";
echo "   Course: {$courseDate->course->name}\n";
echo "   Date: {$courseDate->date}\n\n";

// Find inst_unit
$instUnit = \App\Models\InstUnit::where('course_date_id', $courseDate->id)->first();

if (!$instUnit) {
    echo "❌ No InstUnit found for this course date\n";
    exit(1);
}

echo "✅ Found InstUnit:\n";
echo "   ID: {$instUnit->id}\n";
echo "   Status: {$instUnit->status}\n\n";

// Find active inst_lesson
$activeInstLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
    ->whereNull('completed_at')
    ->whereNull('failed_at')
    ->first();

if (!$activeInstLesson) {
    echo "❌ No active InstLesson found\n";
    exit(1);
}

echo "✅ Found Active InstLesson:\n";
echo "   ID: {$activeInstLesson->id}\n";
echo "   Lesson ID: {$activeInstLesson->lesson_id}\n";
echo "   Started At: {$activeInstLesson->started_at}\n";
echo "   Is Paused: " . ($activeInstLesson->is_paused ? 'TRUE ⏸️' : 'FALSE ▶️') . "\n";
echo "   Completed At: " . ($activeInstLesson->completed_at ?? 'NULL') . "\n\n";

// Test what the API would return
echo "📡 Simulating API Response Structure:\n";
echo str_repeat("-", 80) . "\n";

$apiResponse = [
    'active_lesson_id' => $activeInstLesson->lesson_id,
    'activeLesson' => [
        'id' => $activeInstLesson->id,
        'lesson_id' => $activeInstLesson->lesson_id,
        'inst_unit_id' => $activeInstLesson->inst_unit_id,
        'started_at' => $activeInstLesson->started_at,
        'completed_at' => $activeInstLesson->completed_at,
        'is_paused' => $activeInstLesson->is_paused,
    ],
];

echo json_encode($apiResponse, JSON_PRETTY_PRINT) . "\n\n";

// Check what students would receive
echo "🎓 Student Poll Data Check:\n";
echo str_repeat("-", 80) . "\n";

if ($activeInstLesson->is_paused) {
    echo "✅ PAUSE DATA PRESENT: Student should see pause modal\n";
    echo "   activeLesson.is_paused = true\n";
    echo "   activeLesson.id = {$activeInstLesson->id}\n";
    echo "   activeLesson.lesson_id = {$activeInstLesson->lesson_id}\n";
} else {
    echo "ℹ️  NO PAUSE: Lesson is running normally\n";
    echo "   activeLesson.is_paused = false\n";
}

echo "\n";

// Test the actual controller endpoint
echo "🧪 Testing Actual Controller Response:\n";
echo str_repeat("-", 80) . "\n";

try {
    // Find a student enrolled in this course
    $student = \App\Models\CourseAuth::where('course_date_id', $courseDate->id)
        ->where('status', 'active')
        ->first();

    if (!$student) {
        echo "⚠️  No active student found for this course date\n";
        exit(0);
    }

    echo "✅ Testing with Student:\n";
    echo "   CourseAuth ID: {$student->id}\n";
    echo "   User ID: {$student->user_id}\n";
    echo "   User: {$student->user->fname} {$student->user->lname}\n\n";

    // Simulate the controller method
    $controller = new \App\Http\Controllers\Frontend\Student\StudentDashboardController();

    // Get the student's unit
    $studentUnit = \App\Models\StudentUnit::where('course_auth_id', $student->id)
        ->where('course_unit_id', $instUnit->course_unit_id)
        ->first();

    if (!$studentUnit) {
        echo "⚠️  No StudentUnit found for this student\n";
    } else {
        echo "✅ Student has StudentUnit ID: {$studentUnit->id}\n";
    }

    echo "\n📋 Controller Would Return:\n";
    echo "   activeLesson.id = {$activeInstLesson->id}\n";
    echo "   activeLesson.lesson_id = {$activeInstLesson->lesson_id}\n";
    echo "   activeLesson.is_paused = " . ($activeInstLesson->is_paused ? 'true ⏸️' : 'false ▶️') . "\n";
} catch (\Exception $e) {
    echo "❌ Error testing controller: {$e->getMessage()}\n";
}

echo "\n";
echo str_repeat("=", 80) . "\n";
echo "✅ Test Complete\n";
