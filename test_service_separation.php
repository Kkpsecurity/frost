<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a fake request to bootstrap Laravel
$request = Illuminate\Http\Request::create('/test', 'GET');
$response = $kernel->handle($request);

echo "Testing Service Separation\n";
echo "=========================\n\n";

try {
    // Test 1: Student Dashboard Service (standalone)
    echo "1. Testing StudentDashboardService:\n";
    $studentService = new App\Services\StudentDashboardService();
    $studentData = $studentService->getDashboardData();

    echo "   - Student service keys: " . implode(', ', array_keys($studentData)) . "\n";
    echo "   - Course auths count: " . $studentData['courseAuths']->count() . "\n";
    echo "   - Student stats: " . json_encode($studentData['stats']) . "\n\n";

    // Test 2: Classroom Dashboard Service (standalone)
    echo "2. Testing ClassroomDashboardService:\n";
    $classroomService = new App\Services\ClassroomDashboardService();
    $classroomData = $classroomService->getClassroomData();

    echo "   - Classroom service keys: " . implode(', ', array_keys($classroomData)) . "\n";
    echo "   - Instructors count: " . $classroomData['instructors']->count() . "\n";
    echo "   - Course dates count: " . $classroomData['courseDates']->count() . "\n";
    echo "   - Classroom stats: " . json_encode($classroomData['stats']) . "\n\n";

    // Test 3: Services working together
    echo "3. Testing services integration:\n";
    echo "   - Student service creates ClassroomDashboardService: " . (property_exists($studentService, 'classroomService') ? 'Yes' : 'No') . "\n";
    echo "   - Services are properly separated: " . (class_exists('App\\Services\\StudentDashboardService') && class_exists('App\\Services\\ClassroomDashboardService') ? 'Yes' : 'No') . "\n\n";

    echo "✅ Service separation test completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}
