<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate authenticated user
$user = App\Models\User::find(2);
Auth::login($user);

echo "=== Student Poll Response for User ID 2 ===" . PHP_EOL . PHP_EOL;

// Call the actual controller method
$controller = new App\Http\Controllers\Student\StudentDashboardController();
$request = new Illuminate\Http\Request();

$response = $controller->getStudentPollData($request);
$data = json_decode($response->getContent(), true);

echo "Student Poll Response:" . PHP_EOL;
echo json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;

// Specifically check validations for courseAuth 2
if (isset($data['data']['validations_by_course_auth'][2])) {
    echo "=== Validations for CourseAuth 2 ===" . PHP_EOL;
    echo json_encode($data['data']['validations_by_course_auth'][2], JSON_PRETTY_PRINT) . PHP_EOL;
} else {
    echo "No validations found for CourseAuth 2" . PHP_EOL;
}

// Check courses array for agreed_at
echo PHP_EOL . "=== Courses Array ===" . PHP_EOL;
foreach ($data['data']['courses'] as $course) {
    echo "Course Auth {$course['course_auth_id']}: ";
    echo "agreed_at=" . ($course['agreed_at'] ?? 'null') . ", ";
    echo "status=" . $course['status'] . PHP_EOL;
}
