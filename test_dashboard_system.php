#!/usr/bin/env php
<?php
/**
 * Dashboard Testing Script
 * Tests all dashboard routes and functionality
 */

// Check if we can run PHP commands
echo "=== FROST LMS Dashboard Testing Script ===\n\n";

// Test routes configuration
echo "1. Testing Route Configuration...\n";

// Test if route files exist
$routeFiles = [
    'routes/admin/instructors.php' => 'Instructor Dashboard Routes',
    'routes/admin/support.php' => 'Support Dashboard Routes', 
    'routes/frontend/student.php' => 'Student Dashboard Routes'
];

foreach ($routeFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✓ $description: FOUND\n";
    } else {
        echo "✗ $description: MISSING\n";
    }
}

echo "\n";

// Test controller files
echo "2. Testing Controller Files...\n";

$controllers = [
    'app/Http/Controllers/Admin/Instructors/InstructorDashboardController.php' => 'Instructor Dashboard Controller',
    'app/Http/Controllers/Admin/SupportDashboardController.php' => 'Support Dashboard Controller',
    'app/Http/Controllers/Student/StudentDashboardController.php' => 'Student Dashboard Controller'
];

foreach ($controllers as $file => $description) {
    if (file_exists($file)) {
        echo "✓ $description: EXISTS\n";
        
        // Check if class exists
        require_once $file;
        $className = str_replace(['app/Http/Controllers/', '.php'], ['App\\Http\\Controllers\\', ''], $file);
        $className = str_replace('/', '\\', $className);
        
        if (class_exists($className)) {
            echo "  ✓ Class $className: LOADED\n";
        } else {
            echo "  ✗ Class $className: NOT LOADED\n";
        }
    } else {
        echo "✗ $description: MISSING\n";
    }
}

echo "\n";

// Test view files
echo "3. Testing View Files...\n";

$views = [
    'resources/views/dashboards/instructor/offline.blade.php' => 'Instructor Offline Dashboard',
    'resources/views/dashboards/instructor/online.blade.php' => 'Instructor Online Dashboard',
    'resources/views/dashboards/support/index.blade.php' => 'Support Dashboard',
    'resources/views/dashboards/student/index.blade.php' => 'Student Dashboard'
];

foreach ($views as $file => $description) {
    if (file_exists($file)) {
        echo "✓ $description: EXISTS\n";
        $content = file_get_contents($file);
        $lines = substr_count($content, "\n");
        echo "  - Lines: $lines\n";
    } else {
        echo "✗ $description: MISSING\n";
    }
}

echo "\n";

// Test React components
echo "4. Testing React Components...\n";

$reactComponents = [
    'resources/js/React/Instructor/Components/InstructorDashboard.tsx' => 'Instructor Dashboard Component',
    'resources/js/React/Student/StudentDashboard.tsx' => 'Student Dashboard Component'
];

foreach ($reactComponents as $file => $description) {
    if (file_exists($file)) {
        echo "✓ $description: EXISTS\n";
        $content = file_get_contents($file);
        $lines = substr_count($content, "\n");
        echo "  - Lines: $lines\n";
    } else {
        echo "✗ $description: MISSING\n";
    }
}

echo "\n";

// Dashboard routes summary
echo "5. Dashboard Routes Summary...\n";
echo "Admin Routes:\n";
echo "  - /admin/instructors/ (Instructor Dashboard)\n";
echo "  - /admin/instructors/offline (Offline Mode)\n";
echo "  - /admin/instructors/online (Online Class Mode)\n";
echo "  - /admin/support/ (Support Dashboard)\n";
echo "\nStudent Routes:\n";
echo "  - /student/dashboard (Student Dashboard)\n";
echo "  - /classroom/ (Legacy Student Portal)\n";

echo "\n";

// API endpoints summary
echo "6. API Endpoints Summary...\n";
echo "Instructor APIs:\n";
echo "  - GET /admin/instructors/api/stats\n";
echo "  - GET /admin/instructors/api/lessons\n";
echo "  - GET /admin/instructors/api/chat-messages\n";
echo "  - POST /admin/instructors/api/send-message\n";

echo "\nSupport APIs:\n";
echo "  - GET /admin/support/api/stats\n";
echo "  - GET /admin/support/api/tickets\n";
echo "  - POST /admin/support/api/search-students\n";
echo "  - PATCH /admin/support/api/tickets/{id}\n";

echo "\nStudent APIs:\n";
echo "  - GET /student/api/progress/{courseId}\n";
echo "  - POST /student/api/lesson/{id}/progress\n";
echo "  - GET /student/api/assignments\n";
echo "  - GET /student/api/activity\n";

echo "\n";

echo "=== Dashboard System Ready for Testing ===\n";
echo "Next Steps:\n";
echo "1. Run 'php artisan route:list' to verify routes are loaded\n";
echo "2. Test dashboard access with proper authentication\n";
echo "3. Verify API endpoints return expected data\n";
echo "4. Test React component integration\n";
echo "5. Validate responsive design on different devices\n";

?>
