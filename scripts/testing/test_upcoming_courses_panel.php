<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🎯 Testing Upcoming Courses Panel\n";
echo "==================================\n\n";

use App\Services\Frost\Instructors\InstructorDashboardService;

try {
    $dashboardService = new InstructorDashboardService();
    $upcomingCoursesPanel = $dashboardService->getUpcomingCoursesPanel();

    echo "✅ **Upcoming Courses Panel Data**:\n";
    echo "-----------------------------------\n";

    // Summary
    $summary = $upcomingCoursesPanel['summary'];
    echo "📊 **Summary**: {$summary['message']}\n";
    echo "📈 **Has Upcoming**: " . ($summary['has_upcoming'] ? '✅ Yes' : '❌ No') . "\n";
    echo "⚠️  **Needs Generation**: " . ($summary['needs_generation'] ? '⚠️ Yes' : '✅ No') . "\n\n";

    // Upcoming courses details
    $upcoming = $upcomingCoursesPanel['upcoming_courses'];
    echo "📚 **Course Statistics**:\n";
    echo "   • Total Upcoming: {$upcoming['total_upcoming']}\n";
    echo "   • This Week: {$upcoming['this_week_count']}\n";
    echo "   • Next Week: {$upcoming['next_week_count']}\n";
    echo "   • Date Range: {$upcoming['date_range']['formatted_range']}\n\n";

    // Upcoming dates summary
    if (!empty($upcoming['upcoming_dates'])) {
        echo "📅 **Upcoming Class Dates**:\n";
        foreach (array_slice($upcoming['upcoming_dates'], 0, 5) as $dateInfo) {
            echo "   • {$dateInfo['formatted_date']}: {$dateInfo['course_count']} classes\n";
            foreach (array_slice($dateInfo['courses'], 0, 2) as $course) {
                echo "     - {$course['time']}: {$course['course_name']} ({$course['unit_title']})\n";
            }
        }

        if (count($upcoming['upcoming_dates']) > 5) {
            echo "   ... and " . (count($upcoming['upcoming_dates']) - 5) . " more dates\n";
        }
        echo "\n";
    }

    // Course breakdown
    if (!empty($upcoming['course_breakdown'])) {
        echo "🏫 **Course Type Breakdown**:\n";
        foreach ($upcoming['course_breakdown'] as $courseType) {
            echo "   • {$courseType['course_name']}: {$courseType['count']} classes (Next: {$courseType['next_class']})\n";
        }
        echo "\n";
    }

    echo "🎯 **Web Route Testing**:\n";
    echo "Route: GET /admin/instructors/data/upcoming-courses-panel\n";
    echo "Controller: InstructorDashboardController@getUpcomingCoursesPanel\n";
    echo "Middleware: admin\n\n";

    echo "✅ **Test Results**: Data structure is complete and ready for React component!\n";

} catch (\Exception $e) {
    echo "❌ **Error**: " . $e->getMessage() . "\n";
    echo "📍 **File**: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
