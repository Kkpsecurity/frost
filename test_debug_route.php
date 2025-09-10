<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Boot the app
$app->boot();

try {
    // Simulate authentication - get a user
    $user = App\Models\User::first();

    if (!$user) {
        echo "No users found in database\n";
        exit;
    }

    echo "Testing with User ID: {$user->id}\n";
    echo "User Email: {$user->email}\n\n";

    // Test the service directly
    $service = new App\Services\StudentDashboardService($user);
    $dashboardData = $service->getDashboardData();

    echo "Dashboard Data Structure:\n";
    echo "========================\n";

    foreach ($dashboardData as $key => $value) {
        echo "{$key}: ";
        if (is_object($value) && method_exists($value, 'count')) {
            echo "Collection with " . $value->count() . " items\n";
        } elseif (is_array($value)) {
            echo "Array with " . count($value) . " items\n";
            if ($key === 'stats') {
                echo "  Stats content: " . json_encode($value, JSON_PRETTY_PRINT) . "\n";
            }
        } else {
            echo gettype($value) . "\n";
        }
    }

    echo "\nUser Methods Check:\n";
    echo "==================\n";
    echo "Has ActiveCourseAuths: " . (method_exists($user, 'ActiveCourseAuths') ? 'YES' : 'NO') . "\n";
    echo "Has InActiveCourseAuths: " . (method_exists($user, 'InActiveCourseAuths') ? 'YES' : 'NO') . "\n";

    // Test the relationships
    if (method_exists($user, 'ActiveCourseAuths')) {
        try {
            $activeAuths = $user->ActiveCourseAuths()->get();
            echo "Active Course Auths Count: " . $activeAuths->count() . "\n";
        } catch (Exception $e) {
            echo "Error getting ActiveCourseAuths: " . $e->getMessage() . "\n";
        }
    }

    if (method_exists($user, 'InActiveCourseAuths')) {
        try {
            $inactiveAuths = $user->InActiveCourseAuths()->get();
            echo "Inactive Course Auths Count: " . $inactiveAuths->count() . "\n";
        } catch (Exception $e) {
            echo "Error getting InActiveCourseAuths: " . $e->getMessage() . "\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
