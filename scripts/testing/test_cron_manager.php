<?php

/**
 * Test script for Cron Manager functionality
 * Run: php test_cron_manager.php
 */

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

try {
    // Bootstrap Laravel application  
    $app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    // Create a test request for the cron manager
    $request = Request::create('/admin/services/cron-manager', 'GET');
    $response = $kernel->handle($request);

    echo "=== CRON MANAGER TEST ===\n";
    echo "Status Code: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 200) {
        echo "✅ SUCCESS: Cron Manager page loads successfully\n";
        
        // Test individual components
        echo "\n=== TESTING COMPONENTS ===\n";
        
        // Test controller instantiation
        $controller = new App\Http\Controllers\Admin\Services\CronManagerController();
        echo "✅ Controller instantiated successfully\n";
        
        // Test scheduled tasks retrieval
        try {
            $reflection = new ReflectionMethod($controller, 'getScheduledTasks');
            $reflection->setAccessible(true);
            $tasks = $reflection->invoke($controller);
            
            echo "✅ Scheduled tasks retrieved: " . count($tasks) . " tasks found\n";
            
            if (count($tasks) > 0) {
                echo "Tasks found:\n";
                foreach ($tasks as $task) {
                    echo "  - " . $task['description'] . " (" . $task['expression'] . ")\n";
                }
            }
        } catch (Exception $e) {
            echo "❌ Error retrieving scheduled tasks: " . $e->getMessage() . "\n";
        }
        
        // Test cron status check
        try {
            $reflection = new ReflectionMethod($controller, 'getCronStatus');
            $reflection->setAccessible(true);
            $status = $reflection->invoke($controller);
            
            echo "✅ Cron status retrieved successfully\n";
            echo "  - Cron installed: " . ($status['cron_installed'] ? 'Yes' : 'No') . "\n";
            echo "  - Schedule running: " . ($status['is_running'] ? 'Yes' : 'No') . "\n";
            echo "  - Last run: " . ($status['last_run'] ?: 'Never') . "\n";
        } catch (Exception $e) {
            echo "❌ Error checking cron status: " . $e->getMessage() . "\n";
        }
        
        // Test routes
        echo "\n=== TESTING ROUTES ===\n";
        $routes = [
            'admin.services.cron-manager.index' => 'GET /admin/services/cron-manager',
            'admin.services.cron-manager.run-task' => 'POST /admin/services/cron-manager/run-task',
            'admin.services.cron-manager.run-schedule' => 'POST /admin/services/cron-manager/run-schedule',
            'admin.services.cron-manager.logs' => 'GET /admin/services/cron-manager/logs',
            'admin.services.cron-manager.test' => 'POST /admin/services/cron-manager/test',
        ];
        
        foreach ($routes as $name => $description) {
            try {
                $url = route($name);
                echo "✅ Route '$name' -> $url\n";
            } catch (Exception $e) {
                echo "❌ Route '$name' error: " . $e->getMessage() . "\n";
            }
        }
        
    } else {
        echo "❌ FAILED: HTTP Status " . $response->getStatusCode() . "\n";
        echo "Response: " . $response->getContent() . "\n";
    }

} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== SYSTEM INFO ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Laravel Version: " . (class_exists('Illuminate\\Foundation\\Application') ? app()->version() : 'Unknown') . "\n";
echo "Current Time: " . date('Y-m-d H:i:s') . "\n";
echo "Timezone: " . date_default_timezone_get() . "\n";

echo "\n=== FILE CHECKS ===\n";
$files = [
    'app/Http/Controllers/Admin/Services/CronManagerController.php',
    'routes/admin/services.php',
    'resources/views/admin/services/cron-manager/index.blade.php'
];

foreach ($files as $file) {
    if (file_exists(dirname(__DIR__, 2) . '/' . $file)) {
        echo "✅ " . $file . " exists\n";
    } else {
        echo "❌ " . $file . " missing\n";
    }
}

echo "\n=== TEST COMPLETED ===\n";