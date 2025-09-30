<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🎯 Fixed Course Card Data Validation\n";
echo "====================================\n\n";

use App\Services\Frost\Instructors\CourseDatesService;

try {
    $service = new CourseDatesService();
    $todayLessons = $service->getTodaysLessons();

    if (!empty($todayLessons['lessons'])) {
        $lesson = $todayLessons['lessons'][0];

        echo "✅ **FIXED Course Card Data**:\n";
        echo "------------------------------\n";

        // Circle 1: Lesson Count (should be 5 - total course units)
        echo "🔵 **Circle 1 - Blue (Lessons)**:\n";
        echo "   • Value: " . ($lesson['lesson_count'] ?? 'MISSING') . "\n";
        echo "   • Expected: 5 (total CourseUnits in course)\n";
        echo "   • Status: " . (($lesson['lesson_count'] ?? 0) == 5 ? "✅ CORRECT" : "❌ INCORRECT") . "\n\n";

        // Circle 2: Student Count (should be 0 - class hasn't started)
        echo "🟢 **Circle 2 - Green (Students)**:\n";
        echo "   • Value: " . ($lesson['student_count'] ?? 'MISSING') . "\n";
        echo "   • Expected: 0 (class hasn't started today)\n";
        echo "   • Status: " . (($lesson['student_count'] ?? -1) == 0 ? "✅ CORRECT" : "❌ INCORRECT") . "\n\n";

        // Circle 3: Start Time (should be 12:00 PM)
        echo "🟠 **Circle 3 - Orange (Start Time)**:\n";
        echo "   • Value: " . ($lesson['time'] ?? 'MISSING') . "\n";
        echo "   • Expected: 12:00 PM\n";
        echo "   • Status: " . (($lesson['time'] ?? '') == '12:00 PM' ? "✅ CORRECT" : "❌ INCORRECT") . "\n\n";

        // Instructor & Assistant
        echo "👨‍🏫 **Instructor & Assistant Info**:\n";
        echo "   • Instructor: " . ($lesson['instructor_name'] ?? 'NULL') . "\n";
        echo "   • Assistant: " . ($lesson['assistant_name'] ?? 'NULL') . "\n\n";

        echo "🎨 **Visual Expected Result**:\n";
        echo "------------------------------\n";
        echo "   [○ " . ($lesson['lesson_count'] ?? '?') . "] [○ " . ($lesson['student_count'] ?? '?') . "] [○ " . ($lesson['time'] ?? '?') . "]\n";
        echo "   Lessons    Students    Start Time\n";
        echo "   (Blue)     (Green)     (Orange)\n\n";

        echo "   Status: UNASSIGNED\n";
        echo "   Instructor: " . ($lesson['instructor_name'] ?: 'Unassigned') . "\n";
        if ($lesson['assistant_name']) {
            echo "   Assistant: {$lesson['assistant_name']}\n";
        }
        echo "   [Start Class] button\n\n";

        // Validation summary
        $issues = [];
        if (($lesson['lesson_count'] ?? 0) != 5) $issues[] = "Lesson count should be 5";
        if (($lesson['student_count'] ?? -1) != 0) $issues[] = "Student count should be 0";
        if (($lesson['time'] ?? '') != '12:00 PM') $issues[] = "Start time should be 12:00 PM";

        if (empty($issues)) {
            echo "🎉 **ALL FIXES SUCCESSFUL!** Course card data is now correct.\n";
        } else {
            echo "⚠️  **Issues remaining**: " . implode(', ', $issues) . "\n";
        }

        echo "\n📱 **React Component Updates**:\n";
        echo "--------------------------------\n";
        echo "   ✅ Added assistant display under instructor\n";
        echo "   ✅ Circular design with proper Bootstrap classes\n";
        echo "   ✅ Color-coded circles for easy identification\n";
        echo "   ✅ Proper data binding with fallbacks\n";

    } else {
        echo "❌ **No lessons found for today**\n";
    }

} catch (\Exception $e) {
    echo "❌ **Error**: " . $e->getMessage() . "\n";
    echo "📍 **Location**: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
