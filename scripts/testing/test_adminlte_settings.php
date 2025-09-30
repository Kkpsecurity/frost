<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingHelper;

echo "=== Testing AdminLTE Settings Retrieval ===\n";

try {
    $settingHelper = new SettingHelper('adminlte');

    echo "1. Getting all AdminLTE settings...\n";
    $settings = $settingHelper->all();
    echo "✓ Found " . count($settings) . " AdminLTE settings\n";

    if (count($settings) > 0) {
        echo "\nFirst 5 settings:\n";
        $count = 0;
        foreach ($settings as $key => $value) {
            echo "  {$key} = " . (is_string($value) ? $value : json_encode($value)) . "\n";
            $count++;
            if ($count >= 5) break;
        }

        echo "\n2. Testing grouped settings...\n";
        $grouped = $settingHelper->getGrouped();
        echo "✓ Found " . count($grouped) . " setting groups\n";

        foreach ($grouped as $group => $groupSettings) {
            echo "  Group '{$group}': " . count($groupSettings) . " settings\n";
        }
    } else {
        echo "❌ No AdminLTE settings found!\n";
        echo "Need to run the seeder first.\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
