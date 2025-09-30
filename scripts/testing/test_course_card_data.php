<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Updated Course Card Data\n";
echo "===================================\n\n";

use App\Services\Frost\Instructors\CourseDatesService;

$service = new CourseDatesService();
$todaysLessons = $service->getTodaysLessons();

echo "📊 **Service Response**:\n";
echo "  - Has lessons: " . ($todaysLessons['has_lessons'] ? 'YES' : 'NO') . "\n";
echo "  - Lesson count: " . count($todaysLessons['lessons'] ?? []) . "\n";

if (!empty($todaysLessons['lessons'])) {
    echo "\n📝 **Course Card Data**:\n";

    foreach ($todaysLessons['lessons'] as $index => $lesson) {
        echo "  📚 **Lesson " . ($index + 1) . "**:\n";
        echo "    - Course: {$lesson['course_name']}\n";
        echo "    - Start Time: {$lesson['time']}\n";
        echo "    - Student Count: {$lesson['student_count']}\n";
        echo "    - Lesson Count: " . ($lesson['lesson_count'] ?? 'NOT_SET') . "\n";
        echo "    - Class Status: {$lesson['class_status']}\n";
        echo "    - Instructor: " . ($lesson['instructor_name'] ?? 'Unassigned') . "\n";
        echo "    - Duration: {$lesson['duration']}\n";

        if (!empty($lesson['buttons'])) {
            echo "    - Buttons: " . json_encode($lesson['buttons']) . "\n";
        }

        echo "\n";
    }

    echo "🎯 **Course Card Requirements Check**:\n";
    $lesson = $todaysLessons['lessons'][0];

    echo "  ✅ Lesson Count: " . (isset($lesson['lesson_count']) ? "SET ({$lesson['lesson_count']})" : "❌ MISSING") . "\n";
    echo "  ✅ Student Count: " . (isset($lesson['student_count']) ? "SET ({$lesson['student_count']})" : "❌ MISSING") . "\n";
    echo "  ✅ Start Time: " . (isset($lesson['time']) ? "SET ({$lesson['time']})" : "❌ MISSING") . "\n";
    echo "  ✅ Class Status: " . (isset($lesson['class_status']) ? "SET ({$lesson['class_status']})" : "❌ MISSING") . "\n";

    $hasCompletedIncorrectly = false;
    if ($lesson['class_status'] === 'completed' && !empty($lesson['buttons']['info'])) {
        if (str_contains($lesson['buttons']['info'], 'completed at')) {
            echo "  ⚠️  Status Issue: Shows 'completed' but class is scheduled for today\n";
            $hasCompletedIncorrectly = true;
        }
    }

    if (!$hasCompletedIncorrectly) {
        echo "  ✅ Status Logic: Correct for today's class\n";
    }

} else {
    echo "\n❌ **No lessons returned** - this is the original problem!\n";
    echo "  - Message: " . ($todaysLessons['message'] ?? 'No message') . "\n";
}

echo "\n🎨 **Expected Course Card Layout**:\n";
echo "  📍 Top: Course Name (Florida D40 (Dy))\n";
echo "  📍 Left Circle: Lesson Count (e.g., '3 Lessons')\n";
echo "  📍 Right Circle: Start Time (e.g., '12:00 PM')\n";
echo "  📍 Center: Student Count (e.g., '5 Students')\n";
echo "  📍 Status: Instructor assignment status\n";
echo "  📍 Bottom: Action buttons or status message\n";
