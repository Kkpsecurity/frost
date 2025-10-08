<?php
/**
 * Create Test CourseDate Record for Bulletin Board Testing
 * 
 * This script creates a test CourseDate record for today to demonstrate
 * the bulletin board auto-loading functionality.
 */

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CREATING TEST COURSEDATE FOR BULLETIN BOARD ===\n\n";

try {
    // Find the first available course unit to create a test with
    $courseUnit = \App\Models\CourseUnit::first();
    if (!$courseUnit) {
        echo "❌ No CourseUnit records found. Cannot create test CourseDate.\n";
        exit(1);
    }
    
    $today = now();
    $startTime = $today->copy()->setTime(9, 0, 0); // 9:00 AM today
    $endTime = $startTime->copy()->addHours(2);    // 11:00 AM today
    
    // Check if a CourseDate already exists for today with this course unit
    $existingCourseDate = \App\Models\CourseDate::whereDate('starts_at', $today->format('Y-m-d'))
        ->where('course_unit_id', $courseUnit->id)
        ->first();
    
    if ($existingCourseDate) {
        echo "ℹ️  CourseDate already exists for today:\n";
        echo "   ID: {$existingCourseDate->id}\n";
        echo "   Course Unit: {$courseUnit->title}\n";
        echo "   Start Time: {$existingCourseDate->starts_at}\n";
        echo "   Active: " . ($existingCourseDate->is_active ? 'YES' : 'NO') . "\n\n";
        
        // Activate it if not already active
        if (!$existingCourseDate->is_active) {
            $existingCourseDate->is_active = true;
            $existingCourseDate->save();
            echo "✅ Activated existing CourseDate record\n\n";
        }
        
        $courseDate = $existingCourseDate;
    } else {
        // Create new CourseDate record
        $courseDate = new \App\Models\CourseDate();
        $courseDate->course_unit_id = $courseUnit->id;
        $courseDate->starts_at = $startTime;
        $courseDate->ends_at = $endTime;
        $courseDate->is_active = true;
        $courseDate->save();
        
        echo "✅ Created new CourseDate record:\n";
        echo "   ID: {$courseDate->id}\n";
        echo "   Course Unit: {$courseUnit->title}\n";
        echo "   Start Time: {$courseDate->starts_at}\n";
        echo "   End Time: {$courseDate->ends_at}\n";
        echo "   Active: YES\n\n";
    }
    
    // Now test the bulletin board data
    echo "🔍 TESTING BULLETIN BOARD AUTO-LOAD:\n";
    
    $service = new \App\Services\Frost\Instructors\CourseDatesService();
    $bulletinData = $service->getBulletinBoardData();
    
    echo "- Today's Lessons Count: " . count($bulletinData['todays_lessons']) . "\n";
    echo "- Has Lessons Today: " . ($bulletinData['has_lessons_today'] ? 'YES' : 'NO') . "\n";
    echo "- Announcements Count: " . count($bulletinData['announcements']) . "\n";
    echo "- Message: " . ($bulletinData['lessons_message'] ?? 'No message') . "\n\n";
    
    if ($bulletinData['has_lessons_today'] && count($bulletinData['todays_lessons']) > 0) {
        echo "🎉 SUCCESS: Bulletin board is now auto-loading today's courses!\n\n";
        
        echo "📋 COURSE DETAILS:\n";
        foreach ($bulletinData['todays_lessons'] as $lesson) {
            echo "- Time: {$lesson['time']}\n";
            echo "  Course: {$lesson['course_name']}\n";
            echo "  Lesson: {$lesson['lesson_name']}\n";
            echo "  Status: {$lesson['class_status']}\n";
            echo "  Students: {$lesson['student_count']}\n";
            echo "  Buttons: " . implode(', ', array_keys($lesson['buttons'])) . "\n\n";
        }
        
        // Check for the special announcement
        $todaysCoursesAnnouncement = null;
        foreach ($bulletinData['announcements'] as $announcement) {
            if ($announcement['id'] === 'todays_courses') {
                $todaysCoursesAnnouncement = $announcement;
                break;
            }
        }
        
        if ($todaysCoursesAnnouncement) {
            echo "✅ SUCCESS: 'Today's Courses' announcement was automatically added\n";
            echo "   Title: {$todaysCoursesAnnouncement['title']}\n\n";
        }
        
        echo "🔄 AUTOMATIC REFRESH BEHAVIOR:\n";
        echo "   When this data is polled via /admin/instructors/data/bulletin-board\n";
        echo "   every 45 seconds, instructors will automatically see today's courses\n";
        echo "   without any manual intervention required.\n\n";
        
        echo "⏰ 7 AM AUTO-LOAD PROCESS:\n";
        echo "   1. 06:00 AM - course:activate-dates activates CourseDate records\n";
        echo "   2. 07:00 AM - classrooms:auto-create-today creates classroom sessions\n";
        echo "   3. 07:01 AM - Next bulletin board polling cycle shows courses\n";
        echo "   4. Instructors see course cards automatically appear\n\n";
        
    } else {
        echo "❌ Issue: Bulletin board is not loading today's courses\n";
        echo "   Check the getTodaysLessons() method or CourseDate record format\n\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}

echo "=== TEST COURSEDATE CREATION COMPLETE ===\n";