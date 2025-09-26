<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing One Course Per Type Per Day Rule\n";
echo "==========================================\n\n";

use App\Services\Frost\Scheduling\CourseDateGeneratorService;
use Carbon\Carbon;

try {
    $service = new CourseDateGeneratorService();

    // Test the course type detection
    $courses = \App\Models\Course::where('is_active', true)->get();

    echo "📚 Course Type Detection:\n";
    foreach ($courses as $course) {
        $type = $service->getCourseType($course);
        echo "   • Course {$course->id}: \"{$course->title}\" → Type: {$type}\n";
    }

    echo "\n🔍 Testing Conflict Detection:\n";

    // Test with specific dates
    $testDate = Carbon::parse('2025-09-24'); // Tuesday

    echo "   Test Date: {$testDate->format('Y-m-d l')}\n";

    foreach ($courses as $course) {
        $hasConflict = $service->courseTypeConflictExists($course, $testDate);
        $courseType = $service->getCourseType($course);
        echo "   • {$course->title} (Type {$courseType}): " . ($hasConflict ? "❌ CONFLICT" : "✅ OK") . "\n";
    }

    // Test the generation preview with new rules
    echo "\n📅 Testing Generation with One-Per-Type Rule:\n";
    $preview = $service->previewGeneration(
        Carbon::parse('2025-09-24'),
        Carbon::parse('2025-09-30')
    );

    echo "   Period: {$preview['period']['start']} to {$preview['period']['end']}\n";
    echo "   Estimated Total: {$preview['estimated_total']} CourseDate records\n";

    if (!empty($preview['courses'])) {
        echo "\n📊 Courses and Estimated Dates:\n";
        foreach ($preview['courses'] as $course) {
            $type = $service->getCourseType(\App\Models\Course::find($course['id']));
            echo "   • Course {$course['id']} (Type {$type}): {$course['title']} - {$course['estimated_dates']} dates\n";
        }
    }

    echo "\n💡 Expected Behavior:\n";
    echo "   • Only ONE D-type course per day (even if multiple D courses exist)\n";
    echo "   • Only ONE G-type course per day (even if multiple G courses exist)\n";
    echo "   • D and G can be on the same day (different types)\n";
    echo "   • This week: No G classes due to bi-weekly rule\n";
    echo "   • Next week: G classes Mon-Wed only\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
    exit(1);
}
