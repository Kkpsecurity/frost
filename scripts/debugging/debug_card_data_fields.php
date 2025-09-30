<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Course Card Data Debug\n";
echo "==========================\n\n";

use App\Services\Frost\Instructors\CourseDatesService;

try {
    $service = new CourseDatesService();
    $todayLessons = $service->getTodaysLessons();

    if (!empty($todayLessons['lessons'])) {
        $lesson = $todayLessons['lessons'][0];

        echo "📊 **Current Lesson Data Structure**:\n";
        echo "------------------------------------\n";

        // Key fields that should appear on course card
        echo "🎯 **Card Display Fields**:\n";
        echo "   • lesson_count: " . ($lesson['lesson_count'] ?? 'MISSING') . "\n";
        echo "   • student_count: " . ($lesson['student_count'] ?? 'MISSING') . "\n";
        echo "   • time: " . ($lesson['time'] ?? 'MISSING') . "\n";
        echo "   • start_time: " . ($lesson['start_time'] ?? 'MISSING') . "\n";
        echo "   • course_name: " . ($lesson['course_name'] ?? 'MISSING') . "\n";
        echo "   • class_status: " . ($lesson['class_status'] ?? 'MISSING') . "\n\n";

        echo "🔍 **Full Data Structure**:\n";
        echo "----------------------------\n";
        foreach ($lesson as $key => $value) {
            if (is_array($value)) {
                echo "   • {$key}: [array with " . count($value) . " items]\n";
            } else {
                echo "   • {$key}: " . (is_null($value) ? 'NULL' : $value) . "\n";
            }
        }

        echo "\n📝 **Analysis**:\n";
        echo "----------------\n";

        if (!isset($lesson['lesson_count'])) {
            echo "❌ **Missing lesson_count** - Need to add total lessons for course unit\n";
        } else {
            echo "✅ **lesson_count present**: {$lesson['lesson_count']}\n";
        }

        if (!isset($lesson['time']) && !isset($lesson['start_time'])) {
            echo "❌ **Missing start time** - Neither 'time' nor 'start_time' present\n";
        } else {
            echo "✅ **Time data present**: time=" . ($lesson['time'] ?? 'null') . ", start_time=" . ($lesson['start_time'] ?? 'null') . "\n";
        }

        if (isset($lesson['student_count'])) {
            echo "✅ **student_count present**: {$lesson['student_count']}\n";
        }

    } else {
        echo "❌ **No lessons found for today**\n";
    }

} catch (\Exception $e) {
    echo "❌ **Error**: " . $e->getMessage() . "\n";
}
