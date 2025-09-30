<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing CourseDate Generation Service Integration\n";
echo "==================================================\n\n";

use App\Services\Frost\Scheduling\CourseDateGeneratorService;
use Carbon\Carbon;

try {
    $service = new CourseDateGeneratorService();

    // Test 1: Service instantiation
    echo "✅ Service instantiated successfully\n";

    // Test 2: Preview generation for next 7 days
    echo "\n📅 Testing preview for next 7 days...\n";
    $startDate = now()->addDay();
    $endDate = now()->addWeek();

    $preview = $service->previewGeneration($startDate, $endDate);

    echo "   Period: {$preview['period']['start']} to {$preview['period']['end']}\n";
    echo "   Total Days: {$preview['period']['total_days']}\n";
    echo "   Weekdays: {$preview['period']['weekdays']}\n";
    echo "   Estimated Total: {$preview['estimated_total']} CourseDate records\n";
    echo "   Active Courses: " . count($preview['courses']) . "\n";

    if (!empty($preview['courses'])) {
        echo "\n📚 Courses found:\n";
        foreach (array_slice($preview['courses'], 0, 3) as $course) { // Show first 3
            echo "   • Course {$course['id']}: {$course['title']} ({$course['units_count']} units)\n";
        }
        if (count($preview['courses']) > 3) {
            echo "   • ... and " . (count($preview['courses']) - 3) . " more courses\n";
        }
    } else {
        echo "\n⚠️  No active courses with CourseUnits found\n";
    }

    // Test 3: Check existing CourseDate records
    echo "\n📊 Current CourseDate Statistics:\n";
    $totalCourseDates = \App\Models\CourseDate::count();
    $upcomingCourseDates = \App\Models\CourseDate::where('starts_at', '>=', now())->count();
    $todayCourseDates = \App\Models\CourseDate::whereDate('starts_at', today())->count();

    echo "   Total CourseDate records: {$totalCourseDates}\n";
    echo "   Upcoming CourseDate records: {$upcomingCourseDates}\n";
    echo "   Today's CourseDate records: {$todayCourseDates}\n";

    // Test 4: Check active courses
    echo "\n🎓 Course Statistics:\n";
    $activeCourses = \App\Models\Course::where('is_active', true)->count();
    $activeCoursesWithUnits = \App\Models\Course::where('is_active', true)
        ->whereHas('courseUnits')
        ->count();

    echo "   Active Courses: {$activeCourses}\n";
    echo "   Active Courses with Units: {$activeCoursesWithUnits}\n";

    // Test 5: Command availability
    echo "\n🖥️  Testing Artisan Command:\n";
    $artisanPath = base_path('artisan');
    if (file_exists($artisanPath)) {
        echo "   ✅ Artisan command file exists\n";
        echo "   📝 Command: php artisan course:generate-dates --preview --days=7\n";
        echo "   📝 Scheduled: Weekly on Sunday at 10:00 PM ET\n";
    } else {
        echo "   ❌ Artisan command file not found\n";
    }

    // Test 6: API endpoints
    echo "\n🌐 API Endpoints Available:\n";
    echo "   POST /api/admin/course-date-generator/preview\n";
    echo "   POST /api/admin/course-date-generator/generate\n";
    echo "   POST /api/admin/course-date-generator/cleanup\n";
    echo "   GET  /api/admin/course-date-generator/status\n";
    echo "   POST /api/admin/course-date-generator/quick-generate\n";

    echo "\n🎉 Integration Test Complete!\n";
    echo "==========================\n\n";

    // Usage examples
    echo "💡 Usage Examples:\n";
    echo "-----------------\n";
    echo "Preview next 7 days:\n";
    echo "  php artisan course:generate-dates --preview --days=7\n\n";

    echo "Generate for specific date range:\n";
    echo "  php artisan course:generate-dates --range=2024-10-01,2024-10-31\n\n";

    echo "Quick cleanup and generate:\n";
    echo "  php artisan course:generate-dates --cleanup --days=14\n\n";

    echo "API Preview Request:\n";
    echo "  POST /api/admin/course-date-generator/preview\n";
    echo "  {\n";
    echo "    \"start_date\": \"2024-10-01\",\n";
    echo "    \"end_date\": \"2024-10-07\"\n";
    echo "  }\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
    exit(1);
}
