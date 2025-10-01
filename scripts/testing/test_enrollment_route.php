<?php

// Test enrollment URL generation
require_once __DIR__ . '/../../bootstrap/app.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Enrollment Route Generation\n";
echo "===================================\n\n";

try {
    // Test if we can generate the enrollment route
    $courseId = 1;
    $enrollUrl = route('courses.enroll', $courseId);
    echo "✓ Route generation works: $enrollUrl\n";
} catch (Exception $e) {
    echo "✗ Route generation failed: " . $e->getMessage() . "\n";
}

// Test if course model exists
try {
    $course = App\Models\Course::find(1);
    if ($course) {
        echo "✓ Course ID 1 exists: " . $course->title . "\n";
        echo "  - Active: " . ($course->is_active ? 'Yes' : 'No') . "\n";
        echo "  - Price: $" . $course->price . "\n";
    } else {
        echo "✗ Course ID 1 not found\n";
    }
} catch (Exception $e) {
    echo "✗ Course lookup failed: " . $e->getMessage() . "\n";
}

// Check if enrollment view exists
$enrollViewPath = __DIR__ . '/../../resources/views/frontend/courses/enroll.blade.php';
echo (file_exists($enrollViewPath) ? "✓" : "✗") . " Enrollment view exists\n";

echo "\nPossible issues with enrollment button:\n";
echo "1. JavaScript preventing navigation (check browser console)\n";
echo "2. Authentication middleware redirecting\n";
echo "3. Course not active or not found\n";
echo "4. View compilation error\n";
echo "\nCheck browser network tab when clicking enroll button.\n";
