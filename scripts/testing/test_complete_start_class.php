<?php
/**
 * Test the complete start class flow
 */

require_once __DIR__ . '/vendor/autoload.php';

// Boot Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING COMPLETE START CLASS FLOW ===\n";

// Clean up any existing InstUnits for this CourseDate (for testing)
echo "1. CLEANUP - REMOVING EXISTING INSTUNITS:\n";
$deletedCount = \App\Models\InstUnit::where('course_date_id', 10556)->delete();
echo "   Deleted {$deletedCount} existing InstUnit(s)\n\n";

// Mock authentication
$admin = \App\Models\Admin::find(13);
auth('admin')->login($admin);
echo "2. AUTHENTICATED AS: " . auth('admin')->user()->name . "\n\n";

// Test the full controller flow
echo "3. TESTING CONTROLLER startClass() METHOD:\n";
$controller = new \App\Http\Controllers\Admin\Instructors\InstructorDashboardController(
    new \App\Services\Frost\Instructors\InstructorDashboardService(),
    new \App\Services\Frost\Instructors\CourseDatesService(),
    new \App\Services\Frost\Instructors\ClassroomService(),
    new \App\Services\Frost\Students\BackendStudentService(),
    new \App\Services\ClassroomSessionService()
);

try {
    $response = $controller->startClass(10556);
    $responseData = $response->getData(true);

    echo "   Controller Response:\n";
    echo "   Success: " . ($responseData['success'] ? 'YES' : 'NO') . "\n";

    if ($responseData['success']) {
        echo "   InstUnit ID: " . $responseData['inst_unit_id'] . "\n";
        echo "   Course Date ID: " . $responseData['course_date_id'] . "\n";
        echo "   Redirect URL: " . $responseData['redirect_url'] . "\n";
        echo "   Message: " . $responseData['message'] . "\n";
    } else {
        echo "   Error: " . $responseData['message'] . "\n";
    }

} catch (Exception $e) {
    echo "   ❌ CONTROLLER EXCEPTION: " . $e->getMessage() . "\n";
}

echo "\n4. VERIFICATION - CHECK COURSEDATE STATUS:\n";
$courseDate = \App\Models\CourseDate::find(10556);
if ($courseDate->instUnit) {
    echo "   ✅ CourseDate now has InstUnit: ID {$courseDate->instUnit->id}\n";
    echo "   Created By: {$courseDate->instUnit->created_by}\n";
    echo "   Created At: {$courseDate->instUnit->created_at}\n";
    echo "   Status: " . ($courseDate->instUnit->completed_at ? 'COMPLETED' : 'ACTIVE') . "\n";
} else {
    echo "   ❌ CourseDate still has no InstUnit\n";
}

echo "\n5. TEST getTodaysLessons() AFTER INSTUNIT CREATION:\n";
$service = new \App\Services\Frost\Instructors\CourseDatesService();
$lessons = $service->getTodaysLessons();

foreach ($lessons['lessons'] as $lesson) {
    if ($lesson['id'] == 10556) {
        echo "   FL-D40-D2 Class Status: {$lesson['class_status']}\n";
        echo "   Buttons: " . json_encode($lesson['buttons']) . "\n";
        echo "   Instructor: " . ($lesson['instructor_name'] ?? 'NONE') . "\n";
        break;
    }
}

echo "\n=== END TEST ===\n";
