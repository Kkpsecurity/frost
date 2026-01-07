<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Student Poll Validations Structure ===" . PHP_EOL . PHP_EOL;

// Test with student user (ID 2)
$user = App\Models\User::find(2);

if (!$user) {
    echo "Student user not found!" . PHP_EOL;
    exit(1);
}

echo "Student: {$user->fname} {$user->lname} (ID: {$user->id})" . PHP_EOL;
echo "Email: {$user->email}" . PHP_EOL . PHP_EOL;

// Get course auths
$courseAuths = $user->courseAuths()->with(['course'])->get();

echo "Total Course Enrollments: " . $courseAuths->count() . PHP_EOL . PHP_EOL;

foreach ($courseAuths as $courseAuth) {
    echo "--- Course Auth ID: {$courseAuth->id} ---" . PHP_EOL;
    echo "Course: " . ($courseAuth->course?->title ?? 'N/A') . PHP_EOL;
    echo "Agreed At: " . ($courseAuth->agreed_at ? $courseAuth->agreed_at->format('Y-m-d H:i:s') : 'NOT AGREED') . PHP_EOL;

    // Check ID card validation (course_auth level)
    $idValidation = App\Models\Validation::where('course_auth_id', $courseAuth->id)->first();
    if ($idValidation) {
        echo "✓ ID CARD FOUND: " . $idValidation->URL(false) . PHP_EOL;
    } else {
        echo "✗ NO ID CARD" . PHP_EOL;
    }

    // Check student unit for headshot (session level)
    $studentUnit = App\Models\StudentUnit::where('course_auth_id', $courseAuth->id)
        ->orderByDesc('course_date_id')
        ->first();

    if ($studentUnit) {
        echo "Student Unit ID: {$studentUnit->id} (CourseDate: {$studentUnit->course_date_id})" . PHP_EOL;

        $headshotValidation = App\Models\Validation::where('student_unit_id', $studentUnit->id)->first();
        if ($headshotValidation) {
            echo "✓ HEADSHOT FOUND: " . $headshotValidation->URL(false) . PHP_EOL;
        } else {
            echo "✗ NO HEADSHOT" . PHP_EOL;
        }

        // Check verified JSON
        $verified = json_decode($studentUnit->getRawOriginal('verified'), true) ?: [];
        if (!empty($verified['id_card_path'])) {
            echo "ID Card in verified JSON: " . $verified['id_card_path'] . PHP_EOL;
        }
        if (!empty($verified['headshot_path'])) {
            echo "Headshot in verified JSON: " . $verified['headshot_path'] . PHP_EOL;
        }
    } else {
        echo "No StudentUnit found" . PHP_EOL;
    }

    echo PHP_EOL;
}

echo PHP_EOL . "=== Testing buildStudentValidationsForCourseAuth Output ===" . PHP_EOL . PHP_EOL;

// Simulate what the controller returns
$controller = new App\Http\Controllers\Student\StudentDashboardController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('buildStudentValidationsForCourseAuth');
$method->setAccessible(true);

foreach ($courseAuths as $courseAuth) {
    echo "CourseAuth {$courseAuth->id} validations:" . PHP_EOL;
    $validations = $method->invoke($controller, $courseAuth);
    echo json_encode($validations, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;
}
