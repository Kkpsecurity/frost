<?php

/**
 * CUSTOM COURSE DATE SCHEDULE GENERATOR TEST
 * 
 * This script helps you generate course dates following custom patterns:
 * 1. Monday/Wednesday every other week
 * 2. Every 3 days pattern
 * 3. Custom scheduling patterns
 * 
 * Based on your requirements: "every 3 day mon wensday every other week"
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Services\Frost\Scheduling\CustomScheduleGeneratorService;
use Carbon\Carbon;

// Initialize Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🗓️  CUSTOM COURSE DATE SCHEDULE GENERATOR\n";
echo "==========================================\n\n";

try {
    $customScheduleService = app(CustomScheduleGeneratorService::class);

    // 1. PREVIEW MONDAY/WEDNESDAY EVERY OTHER WEEK PATTERN
    echo "📋 PREVIEW: Monday/Wednesday Every Other Week Pattern\n";
    echo "---------------------------------------------------\n";
    
    $preview = $customScheduleService->previewPattern('monday_wednesday_biweekly', null, 4);
    
    echo "Date Range: {$preview['date_range']['start']} to {$preview['date_range']['end']}\n";
    echo "Total Weeks: {$preview['date_range']['total_weeks']}\n";
    echo "Estimated Total Dates: {$preview['estimated_total']}\n\n";
    
    foreach ($preview['courses'] as $courseTitle => $courseData) {
        echo "Course: {$courseTitle}\n";
        echo "  Estimated Dates: {$courseData['estimated_dates']}\n";
        echo "  Sample Dates:\n";
        foreach ($courseData['sample_dates'] as $sampleDate) {
            echo "    - {$sampleDate['date']} ({$sampleDate['day_name']})\n";
        }
        echo "\n";
    }

    // 2. PREVIEW EVERY 3 DAYS PATTERN
    echo "📋 PREVIEW: Every 3 Days Pattern\n";
    echo "-------------------------------\n";
    
    $preview2 = $customScheduleService->previewPattern('every_three_days', null, 4);
    
    echo "Date Range: {$preview2['date_range']['start']} to {$preview2['date_range']['end']}\n";
    echo "Estimated Total Dates: {$preview2['estimated_total']}\n\n";
    
    foreach ($preview2['courses'] as $courseTitle => $courseData) {
        echo "Course: {$courseTitle}\n";
        echo "  Estimated Dates: {$courseData['estimated_dates']}\n";
        echo "  Sample Dates:\n";
        foreach (array_slice($courseData['sample_dates'], 0, 5) as $sampleDate) {
            echo "    - {$sampleDate['date']} ({$sampleDate['day_name']})\n";
        }
        echo "\n";
    }

    // 3. INTERACTIVE CHOICE
    echo "🎯 GENERATION OPTIONS:\n";
    echo "1. Generate Monday/Wednesday Every Other Week (8 weeks ahead)\n";
    echo "2. Generate Every 3 Days Pattern (8 weeks ahead)\n";
    echo "3. Preview Only (no generation)\n";
    echo "4. Exit\n\n";

    echo "Enter your choice (1-4): ";
    $choice = trim(fgets(STDIN));

    switch ($choice) {
        case '1':
            echo "\n🚀 GENERATING: Monday/Wednesday Every Other Week Pattern...\n";
            $result = $customScheduleService->generateMondayWednesdayEveryOtherWeek(null, 8);
            break;

        case '2':
            echo "\n🚀 GENERATING: Every 3 Days Pattern...\n";
            $result = $customScheduleService->generateEveryThreeDaysPattern(null, 8);
            break;

        case '3':
            echo "\n📋 PREVIEW MODE - No dates will be generated.\n";
            $result = ['message' => 'Preview completed above'];
            break;

        case '4':
            echo "\n👋 Goodbye!\n";
            exit(0);

        default:
            echo "\n❌ Invalid choice. Please run the script again.\n";
            exit(1);
    }

    // DISPLAY RESULTS
    if (isset($result['dates_created'])) {
        echo "\n✅ GENERATION COMPLETED!\n";
        echo "=======================\n";
        echo "Pattern: {$result['pattern']}\n";
        echo "Date Range: {$result['start_date']} to {$result['end_date']}\n";
        echo "Courses Processed: {$result['courses_processed']}\n";
        echo "Dates Created: {$result['dates_created']}\n";
        echo "Dates Skipped: {$result['dates_skipped']}\n";

        if (!empty($result['errors'])) {
            echo "\n⚠️  ERRORS:\n";
            foreach ($result['errors'] as $error) {
                echo "  - {$error}\n";
            }
        }

        echo "\n📊 COURSE DETAILS:\n";
        foreach ($result['course_details'] as $courseTitle => $details) {
            echo "\nCourse: {$courseTitle}\n";
            echo "  Created: {$details['dates_created']} dates\n";
            echo "  Skipped: {$details['dates_skipped']} dates\n";
            
            if (!empty($details['date_details'])) {
                echo "  Recent Dates:\n";
                foreach (array_slice($details['date_details'], -5) as $dateDetail) {
                    $status = $dateDetail['status'] === 'created' ? '✅' : '⏭️';
                    echo "    {$status} {$dateDetail['date']}";
                    if (isset($dateDetail['day_of_week'])) {
                        echo " ({$dateDetail['day_of_week']})";
                    }
                    if ($dateDetail['status'] === 'created' && isset($dateDetail['course_unit'])) {
                        echo " - {$dateDetail['course_unit']}";
                    } elseif ($dateDetail['status'] === 'skipped') {
                        echo " - {$dateDetail['reason']}";
                    }
                    echo "\n";
                }
            }
        }
    } else {
        echo "\n📋 {$result['message']}\n";
    }

    echo "\n🎯 NEXT STEPS:\n";
    echo "1. Check your calendar view to see the generated dates\n";
    echo "2. Activate dates as needed using the admin panel\n";
    echo "3. Adjust course units and schedules as required\n";
    echo "4. Run this script again to generate more dates\n\n";

    echo "🔍 USEFUL WEB ROUTES:\n";
    echo "- Schedule Generator: /admin/schedule\n";
    echo "- Generate Monday/Wednesday: POST /admin/schedule/generate/monday-wednesday-biweekly\n";
    echo "- Generate Every 3 Days: POST /admin/schedule/generate/every-three-days\n";
    echo "- Preview Pattern: GET /admin/schedule/preview/{pattern}\n";
    echo "- View Schedules: GET /admin/schedule/view\n\n";
    
    echo "🔍 USEFUL COMMANDS:\n";
    echo "- Preview patterns: Use choice 3\n";
    echo "- Check database: SELECT * FROM course_dates ORDER BY starts_at DESC LIMIT 10;\n";
    echo "- View active dates: SELECT * FROM course_dates WHERE is_active = true;\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "✨ Custom Schedule Generation Complete!\n";

?>