<?php

/**
 * Test script to check scheduled tasks detection
 */

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== SCHEDULED TASKS TEST ===\n";

try {
    $controller = new App\Http\Controllers\Admin\Services\CronManagerController();
    $reflection = new ReflectionMethod($controller, 'getScheduledTasks');
    $reflection->setAccessible(true);
    $tasks = $reflection->invoke($controller);
    
    echo "Found " . count($tasks) . " scheduled tasks:\n\n";
    
    if (empty($tasks)) {
        echo "❌ No tasks found! This indicates an issue with task detection.\n";
    } else {
        foreach ($tasks as $task) {
            echo "✅ Task: " . $task['description'] . "\n";
            echo "   Command: " . $task['command'] . "\n";
            echo "   Schedule: " . $task['expression'] . "\n";
            echo "   Next Run: " . $task['next_run'] . "\n";
            echo "   Timezone: " . $task['timezone'] . "\n";
            echo "   Background: " . ($task['runs_in_background'] ? 'Yes' : 'No') . "\n";
            echo "   Output: " . ($task['output_file'] ?: 'None') . "\n";
            echo "\n";
        }
    }
    
    // Also test Laravel's schedule:list command for comparison
    echo "=== ARTISAN SCHEDULE:LIST COMPARISON ===\n";
    ob_start();
    Artisan::call('schedule:list');
    $scheduleListOutput = ob_get_clean();
    echo $scheduleListOutput . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "=== TEST COMPLETED ===\n";