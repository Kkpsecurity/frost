<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Validation File Paths ===" . PHP_EOL . PHP_EOL;

// Check CourseAuth 2 validation
$validation = App\Models\Validation::where('course_auth_id', 2)->first();

if ($validation) {
    echo "Validation ID: {$validation->id}" . PHP_EOL;
    echo "Course Auth ID: {$validation->course_auth_id}" . PHP_EOL;
    echo "Student Unit ID: {$validation->student_unit_id}" . PHP_EOL;
    echo "Status: {$validation->status}" . PHP_EOL;
    echo "Type: {$validation->type}" . PHP_EOL . PHP_EOL;

    echo "RelPathBase(): " . $validation->RelPathBase() . PHP_EOL;
    echo "RelPathResolved(): " . $validation->RelPathResolved() . PHP_EOL;
    echo "Full path: " . storage_path('app/public/' . $validation->RelPathResolved()) . PHP_EOL;
    echo "File exists: " . (file_exists(storage_path('app/public/' . $validation->RelPathResolved())) ? 'YES' : 'NO') . PHP_EOL;
    echo "URL(false): " . ($validation->URL(false) ?? 'NULL') . PHP_EOL;
    echo "URL(true): " . $validation->URL(true) . PHP_EOL;
} else {
    echo "No validation found for course_auth_id = 2" . PHP_EOL;
}

echo PHP_EOL . "=== All Validations for CourseAuth 2 ===" . PHP_EOL;
$allValidations = App\Models\Validation::where('course_auth_id', 2)->get();
foreach ($allValidations as $val) {
    echo "ID: {$val->id}, Type: {$val->type}, Status: {$val->status}, URL: " . ($val->URL(false) ?? 'NULL') . PHP_EOL;
}
