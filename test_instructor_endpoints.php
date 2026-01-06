<?php
require_once 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate auth admin session
auth('admin')->loginUsingId(4);

echo "=== Testing Instructor Data Endpoint ===\n";
$controller = new App\Http\Controllers\Admin\Instructors\InstructorDashboardController();

// Call the getInstructorData method
$response = $controller->getInstructorData();
$data = $response->getData(true);

echo "Instructor Data Response:\n";
echo json_encode($data, JSON_PRETTY_PRINT) . "\n";

echo "\n=== Testing Classroom Data Endpoint ===\n";
$response2 = $controller->getClassroomData();
$data2 = $response2->getData(true);

echo "Classroom Data Response:\n";
echo json_encode($data2, JSON_PRETTY_PRINT) . "\n";
