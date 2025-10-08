<?php

require_once 'bootstrap/app.php';

use App\Models\User;
use App\Models\Course;

$app = app();

echo "=== Payment Route Debug ===\n";

// Test course exists
$course = Course::find(3);
if ($course) {
    echo "✓ Course found: {$course->title}\n";
    echo "✓ Course active: " . ($course->is_active ? 'YES' : 'NO') . "\n";
    echo "✓ Course price: \${$course->price}\n";
} else {
    echo "✗ Course 3 not found\n";
    exit;
}

// Test user authentication
$user = User::first();
if ($user) {
    echo "✓ User found: {$user->name}\n";

    // Check enrollment status
    $enrolled = $user->ActiveCourseAuths->firstWhere('course_id', $course->id);
    if ($enrolled) {
        echo "⚠ User is ALREADY ENROLLED - this causes redirect!\n";
        echo "  Enrollment ID: {$enrolled->id}\n";
        echo "  Enrolled at: {$enrolled->enrolled_at}\n";
    } else {
        echo "✓ User is NOT enrolled - payment page should work\n";
    }
} else {
    echo "✗ No users found\n";
}

echo "\n=== Route Test ===\n";
try {
    $url = route('payments.course', 3);
    echo "✓ Route generates: {$url}\n";
} catch (Exception $e) {
    echo "✗ Route error: {$e->getMessage()}\n";
}

echo "\n=== End Debug ===\n";
