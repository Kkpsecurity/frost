<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Helpers\SettingHelper;
use Illuminate\Support\Facades\DB;

echo "Updating Media Manager menu configuration...\n";

// Initialize the helper
$helper = new SettingHelper();
$helper->setPrefix('adminlte');

// Check if there's a custom menu in the database
$existingMenu = $helper->get('menu', null);

if ($existingMenu) {
    echo "Found custom menu configuration in database.\n";
    echo "Current menu (first 200 chars): " . substr($existingMenu, 0, 200) . "...\n";

    // Parse the JSON
    $menuArray = json_decode($existingMenu, true);

    if ($menuArray) {
        // Find and update the Media Manager menu item
        $updated = false;

        function updateMenuRecursive(&$menu, &$updated) {
            foreach ($menu as &$item) {
                if (isset($item['text']) && $item['text'] === 'Media Manager') {
                    echo "Found Media Manager menu item\n";
                    $item['route'] = 'admin.admin-center.media-manager.index';
                    $item['icon'] = 'fas fa-fw fa-hdd';
                    $updated = true;
                    echo "Updated Media Manager route and icon\n";
                }

                if (isset($item['submenu'])) {
                    updateMenuRecursive($item['submenu'], $updated);
                }
            }
        }

        updateMenuRecursive($menuArray, $updated);

        if ($updated) {
            // Save the updated menu back to database
            $helper->set('menu', json_encode($menuArray, JSON_PRETTY_PRINT));
            echo "Menu configuration updated successfully!\n";
        } else {
            echo "Media Manager menu item not found in custom menu.\n";
        }
    } else {
        echo "Failed to parse menu JSON.\n";
    }
} else {
    echo "No custom menu configuration found in database.\n";
    echo "The system will use the static configuration from adminlte_config.php\n";
    echo "Static configuration has been updated to use the correct route.\n";
}

// Check if we can access the new route
try {
    $route = route('admin.media-manager.index');
    echo "Media Manager route: $route\n";
} catch (Exception $e) {
    echo "Warning: Route 'admin.media-manager.index' not found: " . $e->getMessage() . "\n";
}

echo "Update complete!\n";
