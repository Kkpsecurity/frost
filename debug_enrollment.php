<?php

// Quick test script to debug enrollment issues
// Run this from Laravel root: php debug_enrollment.php

require_once 'bootstrap/app.php';

$app = Illuminate\Foundation\Application::getInstance();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test the routes
$routes = collect(app('router')->getRoutes()->getRoutes())->filter(function($route) {
    return str_contains($route->uri, 'enroll');
});

echo "=== ENROLLMENT ROUTES DEBUG ===\n\n";

foreach($routes as $route) {
    echo "URI: " . $route->uri . "\n";
    echo "Methods: " . implode(', ', $route->methods) . "\n";
    echo "Name: " . $route->getName() . "\n";
    echo "Action: " . $route->getActionName() . "\n";
    echo "---\n";
}

// Test course existence
echo "\n=== COURSE DATA TEST ===\n";
try {
    $course = App\Models\Course::where('is_active', true)->first();
    if ($course) {
        echo "Found active course: ID {$course->id}, Title: {$course->title}\n";
        echo "Price: \${$course->price}\n";
        echo "Active: " . ($course->is_active ? 'Yes' : 'No') . "\n";
    } else {
        echo "No active courses found!\n";
    }
} catch (Exception $e) {
    echo "Error fetching course: " . $e->getMessage() . "\n";
}

// Test user authentication (you'll need to run this while logged in)
echo "\n=== AUTH TEST ===\n";
echo "Auth routes loaded: " . (Route::has('login') ? 'Yes' : 'No') . "\n";
echo "Enrollment routes loaded: " . (Route::has('courses.enroll.process') ? 'Yes' : 'No') . "\n";

echo "\n=== ROUTE URL GENERATION TEST ===\n";
try {
    if ($course) {
        echo "Enroll URL: " . route('courses.enroll.process', $course->id) . "\n";
        echo "Show URL: " . route('courses.show', $course->id) . "\n";
    }
} catch (Exception $e) {
    echo "Error generating URLs: " . $e->getMessage() . "\n";
}

echo "\nDebugging complete.\n";
