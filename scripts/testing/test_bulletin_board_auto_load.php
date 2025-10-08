<?php
/**
 * TEST: Bulletin Board Auto-Load at 7 AM
 * 
 * This script tests the bulletin board auto-loading functionality
 * that should detect and display today's courses when activated.
 */

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING BULLETIN BOARD AUTO-LOAD ===\n\n";

try {
    // Test the updated getBulletinBoardData method
    $service = new \App\Services\Frost\Instructors\CourseDatesService();
    $bulletinData = $service->getBulletinBoardData();
    
    echo "🔍 BULLETIN BOARD DATA STRUCTURE:\n";
    echo "- Today's Lessons: " . (isset($bulletinData['todays_lessons']) ? 'YES' : 'NO') . "\n";
    echo "- Has Lessons Today: " . ($bulletinData['has_lessons_today'] ? 'YES' : 'NO') . "\n";
    echo "- Lessons Count: " . count($bulletinData['todays_lessons']) . "\n";
    echo "- Announcements Count: " . count($bulletinData['announcements']) . "\n";
    echo "- Message: " . ($bulletinData['lessons_message'] ?? 'No message') . "\n\n";
    
    if ($bulletinData['has_lessons_today'] && count($bulletinData['todays_lessons']) > 0) {
        echo "✅ SUCCESS: Today's courses are being loaded in bulletin board!\n\n";
        
        echo "📅 TODAY'S COURSES:\n";
        foreach ($bulletinData['todays_lessons'] as $lesson) {
            echo "- {$lesson['time']} | {$lesson['course_name']} | Status: {$lesson['class_status']}\n";
        }
        echo "\n";
        
        // Check if the special "Today's Courses" announcement was added
        $todaysCoursesAnnouncement = null;
        foreach ($bulletinData['announcements'] as $announcement) {
            if ($announcement['id'] === 'todays_courses') {
                $todaysCoursesAnnouncement = $announcement;
                break;
            }
        }
        
        if ($todaysCoursesAnnouncement) {
            echo "✅ SUCCESS: Special 'Today's Courses' announcement added\n";
            echo "   Title: {$todaysCoursesAnnouncement['title']}\n";
            echo "   Content: {$todaysCoursesAnnouncement['content']}\n\n";
        }
        
    } else {
        echo "ℹ️  INFO: No courses scheduled for today\n";
        echo "   This is normal if no CourseDate records exist for today\n";
        echo "   or if courses haven't been activated yet (6 AM activation)\n\n";
    }
    
    echo "🔄 POLLING VERIFICATION:\n";
    echo "   Route: /admin/instructors/data/bulletin-board\n";
    echo "   Controller: InstructorDashboardController::getBulletinBoardData()\n";
    echo "   Service: CourseDatesService::getBulletinBoardData() [UPDATED]\n";
    echo "   Polling Interval: 45 seconds (classroom_polling)\n\n";
    
    echo "⏰ AUTO-LOAD SCHEDULE:\n";
    echo "   06:00 AM - CourseDate records activated (course:activate-dates)\n";
    echo "   07:00 AM - Classrooms created (classrooms:auto-create-today)\n";
    echo "   07:01 AM - Next polling cycle shows today's courses in bulletin board\n\n";
    
    echo "🎯 EXPECTED BEHAVIOR:\n";
    echo "   1. At 7 AM, courses are activated and classrooms created\n";
    echo "   2. Within 45 seconds, bulletin board polling detects courses\n";
    echo "   3. Bulletin board automatically shows today's course cards\n";
    echo "   4. No manual intervention required from instructors\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}

// Additional verification: Check if CourseDate records exist for today
echo "📊 TODAY'S COURSEDATE RECORDS:\n";
try {
    $today = now()->format('Y-m-d');
    $courseDates = \App\Models\CourseDate::whereDate('starts_at', $today)
        ->orderBy('starts_at')
        ->get(['id', 'starts_at', 'ends_at', 'is_active', 'course_unit_id']);
    
    if ($courseDates->count() > 0) {
        echo "Found {$courseDates->count()} CourseDate records for today:\n";
        foreach ($courseDates as $courseDate) {
            $status = $courseDate->is_active ? 'ACTIVE' : 'INACTIVE';
            $time = \Carbon\Carbon::parse($courseDate->starts_at)->format('H:i');
            echo "- ID:{$courseDate->id} | {$time} | {$status} | Unit:{$courseDate->course_unit_id}\n";
        }
    } else {
        echo "No CourseDate records found for today ({$today})\n";
        echo "To test bulletin board auto-loading:\n";
        echo "1. Run course generation: php artisan course:generate-dates\n";
        echo "2. Run course activation: php artisan course:activate-dates\n";
        echo "3. Test bulletin board again\n";
    }
} catch (\Exception $e) {
    echo "Error checking CourseDate records: " . $e->getMessage() . "\n";
}

echo "\n=== BULLETIN BOARD AUTO-LOAD TEST COMPLETE ===\n";