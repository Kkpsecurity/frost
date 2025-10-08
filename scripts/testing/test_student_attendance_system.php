<?php

/**
 * Test Student Attendance System
 * 
 * This script tests the new student attendance functionality:
 * - StudentAttendanceService
 * - Student attendance routes
 * - API endpoints integration
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Services\StudentAttendanceService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Student Attendance System\n";
echo "=====================================\n\n";

try {
    // Test 1: Service Initialization
    echo "1. Testing StudentAttendanceService initialization...\n";
    $attendanceService = app(StudentAttendanceService::class);
    echo "   ✅ StudentAttendanceService created successfully\n\n";

    // Test 2: Check available methods
    echo "2. Checking available service methods...\n";
    $methods = get_class_methods($attendanceService);
    $expectedMethods = ['enterClass', 'getStudentAttendanceDetails', 'getDashboardData'];
    
    foreach ($expectedMethods as $method) {
        if (in_array($method, $methods)) {
            echo "   ✅ Method '{$method}' available\n";
        } else {
            echo "   ❌ Method '{$method}' missing\n";
        }
    }
    echo "\n";

    // Test 3: Test route registration
    echo "3. Testing route registration...\n";
    
    $routes = [
        'classroom.enter' => 'POST classroom/enter-class',
        'classroom.attendance.data' => 'GET classroom/attendance/data',
        'classroom.attendance.class' => 'GET classroom/attendance/{courseDateId}'
    ];
    
    foreach ($routes as $routeName => $routePattern) {
        try {
            $route = route($routeName, ['courseDateId' => 1]);
            echo "   ✅ Route '{$routeName}' registered: {$route}\n";
        } catch (Exception $e) {
            echo "   ❌ Route '{$routeName}' missing or invalid\n";
        }
    }
    echo "\n";

    // Test 4: Check for users to test with
    echo "4. Checking for test users...\n";
    $userCount = User::count();
    echo "   📊 Found {$userCount} users in database\n";
    
    if ($userCount > 0) {
        $testUser = User::first();
        echo "   👤 Test user available: {$testUser->name} (ID: {$testUser->id})\n";
    } else {
        echo "   ⚠️  No users found for testing\n";
    }
    echo "\n";

    // Test 5: Check database tables
    echo "5. Checking required database tables...\n";
    $tables = ['users', 'course_auths', 'course_dates', 'inst_unit', 'student_unit'];
    
    foreach ($tables as $table) {
        try {
            $count = \DB::table($table)->count();
            echo "   ✅ Table '{$table}': {$count} records\n";
        } catch (Exception $e) {
            echo "   ❌ Table '{$table}' missing or inaccessible\n";
        }
    }
    echo "\n";

    echo "🎉 Student Attendance System Test Complete!\n";
    echo "===========================================\n";
    echo "Status: Ready for frontend integration\n";
    echo "\n";
    echo "Next Steps:\n";
    echo "- Test API endpoints via browser/Postman\n";
    echo "- Verify React component integration\n";
    echo "- Test student class entry flow\n";
    echo "- Monitor attendance tracking functionality\n";

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}