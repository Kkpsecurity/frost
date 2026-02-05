<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Simulate authenticated user
Auth::loginUsingId(1); // Richard Clark

$request = new Illuminate\Http\Request();
$request->merge(['course_auth_id' => 2]);

$controller = new App\Http\Controllers\Student\StudentDashboardController();
$response = $controller->getClassData($request);

$data = json_decode($response->getContent(), true);

echo "API Response:\n";
echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
if (!$data['success']) {
    echo "Error: " . ($data['error'] ?? 'Unknown error') . "\n";
    echo "\nFull Response:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    exit;
}
echo "Active Lesson ID: " . json_encode($data['data']['active_lesson_id'] ?? null) . "\n";
echo "\nLessons:\n";

foreach ($data['data']['lessons'] ?? [] as $lesson) {
    echo sprintf(
        "  - ID: %d | %s | Status: %s | Active: %s | Completed: %s\n",
        $lesson['id'],
        str_pad($lesson['title'], 30),
        str_pad($lesson['status'], 15),
        $lesson['is_active'] ? 'YES' : 'NO ',
        $lesson['is_completed'] ? 'YES' : 'NO '
    );
}
