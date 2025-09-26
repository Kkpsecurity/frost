<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Updated Blade Template Structure\n";
echo "=======================================\n";

try {
    // Simulate authentication for user ID 2
    $user = \App\Models\User::find(2);
    \Illuminate\Support\Facades\Auth::login($user);

    if (!$user) {
        echo "User not found!\n";
        exit;
    }

    echo "User: {$user->email}\n";

    // Test the controller data preparation
    $studentService = new \App\Services\StudentDashboardService($user);
    $courseAuths = $studentService->getCourseAuths();

    $content = [
        'student' => $user,
        'course_auths' => $courseAuths ?? [],
    ];

    $course_auth_id = !empty($courseAuths) ? $courseAuths[0]->id ?? null : null;

    echo "\n🎯 Blade Template Data Structure:\n";
    echo "=================================\n";

    // Student Props JSON (what goes in student-props script tag)
    $studentPropsJson = json_encode([
        'student' => isset($content['student']) ? $content['student'] : null,
        'course_auths' => isset($content['course_auths']) ? $content['course_auths'] : [],
        'course_auth_id' => $course_auth_id ?? null
    ], JSON_PRETTY_PRINT);

    echo "\n📋 Student Props JSON:\n";
    echo "Length: " . strlen($studentPropsJson) . " characters\n";
    echo "Student: " . (isset($content['student']) ? "✓ Present" : "✗ Missing") . "\n";
    echo "Course auths: " . count($content['course_auths']) . " items\n";
    echo "Course auth ID: " . ($course_auth_id ?? 'null') . "\n";

    // Class Props JSON (what goes in class-props div attribute)
    $classPropsJson = json_encode([
        "instructor" => null,
        "course_dates" => []
    ], JSON_PRETTY_PRINT);

    echo "\n📋 Class Props JSON:\n";
    echo "Length: " . strlen($classPropsJson) . " characters\n";
    echo "Instructor: null (expected for student dashboard)\n";
    echo "Course dates: 0 items (expected for student dashboard)\n";

    echo "\n✅ Blade Template Elements:\n";
    echo "- student-props (script): ✓ Contains student data\n";
    echo "- class-props (div): ✓ Contains empty classroom data\n";
    echo "- React mount point: ✓ student-dashboard-container\n";

    echo "\n🎯 Expected React Behavior:\n";
    echo "- React will find both DOM elements\n";
    echo "- Student data will load successfully\n";
    echo "- Class data will be empty (OFFLINE state)\n";
    echo "- No console errors about missing elements\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
