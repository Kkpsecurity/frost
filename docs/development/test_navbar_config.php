<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Helpers\SettingHelper;
use App\Support\AdminLteConfigElements;

echo "Testing AdminLTE Navbar Configuration...\n\n";

try {
    $settingHelper = new SettingHelper();
    $configElements = new AdminLteConfigElements($settingHelper);
    $menuConfig = $configElements->getMenuConfig();

    $navbarItems = array_filter($menuConfig, function($item) {
        return isset($item['topnav_right']) && $item['topnav_right'] === true;
    });

    echo "Found " . count($navbarItems) . " navbar items:\n";

    foreach ($navbarItems as $key => $item) {
        echo "  Item $key:\n";
        echo "    Type: " . ($item['type'] ?? 'unknown') . "\n";
        echo "    Icon: " . ($item['icon'] ?? 'no icon') . "\n";
        if (isset($item['id'])) {
            echo "    ID: " . $item['id'] . "\n";
        }
        echo "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
