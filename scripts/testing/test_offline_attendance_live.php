<?php
/**
 * Test Offline Attendance Recording - Real Data Test
 * 
 * This script performs a live test of offline attendance recording
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\User;
use App\Models\CourseDate;
use App\Models\StudentUnit;
use App\Services\AttendanceService;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== OFFLINE ATTENDANCE RECORDING TEST ===\n\n";

try {
    // Find a student with existing course enrollment
    $studentUnit = StudentUnit::with(['CourseAuth.User', 'CourseDate'])
        ->whereHas('CourseAuth.User', function($query) {
            $query->whereHas('roles', function($roleQuery) {
                $roleQuery->where('name', 'student');
            });
        })
        ->first();

    if (!$studentUnit) {
        echo "❌ No student units found for testing\n";
        exit;
    }

    $student = $studentUnit->CourseAuth->User;
    $courseDate = $studentUnit->CourseDate;
    
    echo "🎯 TEST DATA FOUND:\n";
    echo "Student: {$student->name} (ID: {$student->id})\n";
    echo "Course: {$courseDate->GetCourse()->name}\n";
    echo "Course Date: {$courseDate->id} - " . $courseDate->course_date . "\n";
    echo "Existing Attendance Type: {$studentUnit->attendance_type}\n\n";

    // Test the AttendanceService
    $attendanceService = new AttendanceService();
    
    // Find a course date without existing attendance for this student
    $testCourseDate = CourseDate::whereHas('StudentUnits.CourseAuth', function($query) use ($student) {
        $query->where('user_id', $student->id);
    })
    ->whereDoesntHave('StudentUnits', function($query) use ($student) {
        $query->whereHas('CourseAuth', function($authQuery) use ($student) {
            $authQuery->where('user_id', $student->id);
        });
    })
    ->first();

    if ($testCourseDate) {
        echo "📝 TESTING OFFLINE ATTENDANCE RECORDING:\n";
        echo "Course Date: {$testCourseDate->id} - {$testCourseDate->GetCourse()->name}\n\n";
        
        // Record offline attendance
        $result = $attendanceService->recordOfflineAttendance($student, $testCourseDate->id, [
            'recorded_by' => 'test_script',
            'location' => 'physical_classroom',
            'timestamp' => now()
        ]);
        
        echo "RESULT:\n";
        echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
        echo "Message: {$result['message']}\n";
        echo "Code: {$result['code']}\n";
        
        if ($result['success'] && isset($result['data'])) {
            $newUnit = $result['data'];
            echo "\nNEW STUDENTUNIT CREATED:\n";
            echo "ID: {$newUnit->id}\n";
            echo "Attendance Type: {$newUnit->attendance_type}\n";
            echo "Course Date ID: {$newUnit->course_date_id}\n";
            echo "Course Auth ID: {$newUnit->course_auth_id}\n";
            echo "Created At: {$newUnit->created_at}\n";
            
            // Verify with fresh query
            $verification = StudentUnit::find($newUnit->id);
            echo "\nVERIFICATION:\n";
            echo "Record exists: " . ($verification ? 'YES' : 'NO') . "\n";
            echo "Is offline attendance: " . ($verification && $verification->isOfflineAttendance() ? 'YES' : 'NO') . "\n";
        }
        
    } else {
        echo "⚠ Could not find course date without existing attendance for testing\n";
        echo "📊 Current Statistics:\n";
        echo "- Total StudentUnits: " . StudentUnit::count() . "\n";
        echo "- Online attendance: " . StudentUnit::onlineAttendance()->count() . "\n";
        echo "- Offline attendance: " . StudentUnit::offlineAttendance()->count() . "\n";
    }

    echo "\n=== TEST COMPLETED ===\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}