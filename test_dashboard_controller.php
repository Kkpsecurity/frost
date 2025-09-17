<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Dashboard Controller Data\n";
echo "=================================\n";

try {
    // Simulate authentication for user ID 2
    $user = \App\Models\User::find(2);
    \Illuminate\Support\Facades\Auth::login($user);

    if (!$user) {
        echo "User not found!\n";
        exit;
    }

    echo "User: {$user->email}\n";

    // Create controller and test dashboard method
    $controller = new \App\Http\Controllers\Student\StudentDashboardController();
    $studentService = new \App\Services\StudentDashboardService($user);

    // Test the service directly first
    echo "\nTesting service directly:\n";
    $courseAuths = $studentService->getCourseAuths();
    echo "Service getCourseAuths count: " . $courseAuths->count() . "\n";

    if ($courseAuths->count() > 0) {
        foreach ($courseAuths as $index => $courseAuth) {
            echo "Course Auth #{$index}: ID={$courseAuth->id}, Course={$courseAuth->course_id}, User={$courseAuth->user_id}\n";
        }
    }

    // Test the controller content preparation
    echo "\nTesting controller content preparation:\n";

    $content = [
        'student' => $user,
        'course_auths' => $courseAuths ?? [],
    ];

    echo "Content structure:\n";
    echo "- student: " . (isset($content['student']) ? "User #{$content['student']->id}" : 'null') . "\n";
    echo "- course_auths: " . (isset($content['course_auths']) ? count($content['course_auths']) . " items" : 'null') . "\n";

    // Show what would be passed to the blade template
    $templateData = [
        'student' => isset($content['student']) ? $content['student'] : null,
        'course_auths' => isset($content['course_auths']) ? $content['course_auths'] : [],
    ];

    echo "\nBlade template would receive:\n";
    echo "- student: " . ($templateData['student'] ? "User #{$templateData['student']->id} ({$templateData['student']->email})" : 'null') . "\n";
    echo "- course_auths: " . count($templateData['course_auths']) . " items\n";

    if (count($templateData['course_auths']) > 0) {
        echo "\nCourse auths details:\n";
        foreach ($templateData['course_auths'] as $index => $auth) {
            echo "  #{$index}: ID={$auth->id}, Course={$auth->course_id}, User={$auth->user_id}, Created={$auth->created_at}\n";
        }
    }

    // Test the JSON data that would be passed to React
    $reactProps = [
        'student' => isset($content['student']) ? $content['student'] : null,
        'course_auths' => isset($content['course_auths']) ? $content['course_auths'] : [],
        'course_auth_id' => !empty($content['course_auths']) ? $content['course_auths'][0]->id ?? null : null
    ];

    echo "\nReact props JSON structure:\n";
    echo "- student: " . ($reactProps['student'] ? "User #{$reactProps['student']->id}" : 'null') . "\n";
    echo "- course_auths: " . count($reactProps['course_auths']) . " items\n";
    echo "- course_auth_id: " . ($reactProps['course_auth_id'] ?? 'null') . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
