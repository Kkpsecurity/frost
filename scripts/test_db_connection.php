<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\CourseAuth;
use Illuminate\Support\Facades\DB;

echo "=== Database Connection Test ===" . PHP_EOL;

try {
    // Test basic connection
    DB::connection()->getPdo();
    echo "✓ Database connected successfully" . PHP_EOL;

    // Check if user exists
    $user = User::find(2);
    if ($user) {
        echo "✓ User ID 2 found: " . $user->name . " (" . $user->email . ")" . PHP_EOL;

        // Check course auths
        $activeCourseAuths = $user->ActiveCourseAuths()->get();
        echo "✓ Active course auths: " . $activeCourseAuths->count() . PHP_EOL;

        $inactiveCourseAuths = $user->InActiveCourseAuths()->get();
        echo "✓ Inactive course auths: " . $inactiveCourseAuths->count() . PHP_EOL;

        // Test the service directly
        $service = new App\Services\StudentDashboardService($user);
        $dashboardData = $service->getDashboardData();

        echo "✓ Dashboard data generated successfully" . PHP_EOL;
        echo "  - Incomplete auths: " . (is_countable($dashboardData['incompleteAuths']) ? count($dashboardData['incompleteAuths']) : 0) . PHP_EOL;
        echo "  - Completed auths: " . (is_countable($dashboardData['completedAuths']) ? count($dashboardData['completedAuths']) : 0) . PHP_EOL;
        echo "  - Stats: " . json_encode($dashboardData['stats']) . PHP_EOL;

    } else {
        echo "✗ User ID 2 not found" . PHP_EOL;
    }

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . PHP_EOL;
    echo "Stack trace: " . $e->getTraceAsString() . PHP_EOL;
}
