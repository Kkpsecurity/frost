<?php

/**
 * Test script for Admin Center Dashboard
 * Run: php test_admin_center_dashboard.php
 */

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

try {
    // Bootstrap Laravel application
    $app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "=== ADMIN CENTER DASHBOARD TEST ===\n";
    
    // Test controller instantiation
    $controller = new App\Http\Controllers\Admin\AdminCenter\CenterController();
    echo "✅ CenterController instantiated successfully\n";
    
    // Test routes
    echo "\n=== TESTING ROUTES ===\n";
    $routes = [
        'admin.admin-center.dashboard' => '/admin/admin-center',
        'admin.admin-center.admin-users.index' => '/admin/admin-center/admin-users',
        'admin.settings.index' => '/admin/settings',
        'admin.payments.index' => '/admin/payments',
        'admin.media-manager.index' => '/admin/media-manager',
        'admin.services.cron-manager.index' => '/admin/services/cron-manager',
    ];
    
    foreach ($routes as $name => $expected) {
        try {
            $url = route($name);
            echo "✅ Route '$name' -> $url\n";
        } catch (Exception $e) {
            echo "❌ Route '$name' error: " . $e->getMessage() . "\n";
        }
    }
    
    // Test data methods
    echo "\n=== TESTING DATA METHODS ===\n";
    
    try {
        $reflection = new ReflectionMethod($controller, 'getServicesData');
        $reflection->setAccessible(true);
        $services = $reflection->invoke($controller);
        
        echo "✅ Services data retrieved: " . count($services) . " services found\n";
        
        foreach ($services as $key => $service) {
            echo "  - " . $service['title'] . " (" . $service['status'] . ")\n";
        }
    } catch (Exception $e) {
        echo "❌ Error retrieving services data: " . $e->getMessage() . "\n";
    }
    
    try {
        $reflection = new ReflectionMethod($controller, 'getSystemOverview');
        $reflection->setAccessible(true);
        $overview = $reflection->invoke($controller);
        
        echo "✅ System overview retrieved successfully\n";
        echo "  - PHP: " . $overview['php_version'] . "\n";
        echo "  - Laravel: " . $overview['laravel_version'] . "\n";
        echo "  - Environment: " . $overview['environment'] . "\n";
        echo "  - Database: " . $overview['database_connection'] . "\n";
    } catch (Exception $e) {
        echo "❌ Error retrieving system overview: " . $e->getMessage() . "\n";
    }
    
    try {
        $reflection = new ReflectionMethod($controller, 'getQuickStats');
        $reflection->setAccessible(true);
        $stats = $reflection->invoke($controller);
        
        echo "✅ Quick stats retrieved successfully\n";
        echo "  - Total Users: " . $stats['total_users'] . "\n";
        echo "  - Admin Users: " . $stats['admin_users'] . "\n";
        echo "  - Settings: " . $stats['total_settings'] . "\n";
    } catch (Exception $e) {
        echo "❌ Error retrieving quick stats: " . $e->getMessage() . "\n";
    }
    
    // Test database connections
    echo "\n=== TESTING DATABASE ===\n";
    
    try {
        $userCount = App\Models\User::count();
        echo "✅ Users table accessible: $userCount users\n";
    } catch (Exception $e) {
        echo "❌ Users table error: " . $e->getMessage() . "\n";
    }
    
    try {
        $adminCount = App\Models\Admin::count();
        echo "✅ Admins table accessible: $adminCount admins\n";
    } catch (Exception $e) {
        echo "❌ Admins table error: " . $e->getMessage() . "\n";
    }
    
    try {
        $settingsCount = DB::table('settings')->count();
        echo "✅ Settings table accessible: $settingsCount settings\n";
    } catch (Exception $e) {
        echo "❌ Settings table error: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== FILE CHECKS ===\n";
$files = [
    'app/Http/Controllers/Admin/AdminCenter/CenterController.php',
    'routes/admin/admin-center.php',
    'resources/views/admin/center/dashboard.blade.php',
    'config/adminlte_config.php'
];

foreach ($files as $file) {
    if (file_exists(dirname(__DIR__, 2) . '/' . $file)) {
        echo "✅ " . $file . " exists\n";
    } else {
        echo "❌ " . $file . " missing\n";
    }
}

echo "\n=== MENU CONFIGURATION ===\n";
$config = include dirname(__DIR__, 2) . '/config/adminlte_config.php';
$adminCenterMenu = null;

foreach ($config['menu'] as $item) {
    if (isset($item['text']) && $item['text'] === 'Admin Center') {
        $adminCenterMenu = $item;
        break;
    }
}

if ($adminCenterMenu) {
    if (isset($adminCenterMenu['submenu'])) {
        echo "❌ Admin Center still has submenu - should be single dashboard link\n";
    } else {
        echo "✅ Admin Center configured as single dashboard link\n";
        echo "  - Route: " . ($adminCenterMenu['route'] ?? 'Not set') . "\n";
        echo "  - Icon: " . ($adminCenterMenu['icon'] ?? 'Not set') . "\n";
    }
} else {
    echo "❌ Admin Center menu item not found\n";
}

echo "\n=== TEST COMPLETED ===\n";