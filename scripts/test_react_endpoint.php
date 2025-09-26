<?php

// Test script to check the exact API endpoint that React is calling
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Admin\Instructors\InstructorDashboardController;
use App\Services\Frost\Instructors\CourseDatesService;
use App\Services\Frost\Instructors\InstructorDashboardService;
use App\Services\Frost\Instructors\ClassroomService;
use App\Services\Frost\Students\BackendStudentService;

echo "🔍 Testing React API Endpoint\n";
echo "==============================\n\n";

// Create controller instance
$courseDatesService = new CourseDatesService();
$dashboardService = new InstructorDashboardService();
$classroomService = new ClassroomService();
$studentService = new BackendStudentService();

$controller = new InstructorDashboardController(
    $dashboardService,
    $courseDatesService,
    $classroomService,
    $studentService
);

// Test getTodayLessons method
echo "📅 Testing getTodayLessons() method:\n";
echo "====================================\n";

// We need to mock the auth, but let's see what the raw service returns
$lessons = $courseDatesService->getTodaysLessons();
echo json_encode($lessons, JSON_PRETTY_PRINT);

echo "\n\n✅ This should be what the React component receives!\n";
