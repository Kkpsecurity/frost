<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Helpers\SettingHelper;
use App\Support\AdminLteConfigElements;

$settingHelper = new SettingHelper('adminlte');

echo "Current sidebar_collapse_remember: " . var_export($settingHelper->get('sidebar_collapse_remember'), true) . "\n";
echo "Default config value: " . var_export(config('adminlte.sidebar_collapse_remember'), true) . "\n";

// Check if it exists in the default config
$allConfig = config('adminlte');
echo "\nSidebar settings in default config:\n";
foreach ($allConfig as $key => $value) {
    if (strpos($key, 'sidebar_collapse') !== false) {
        echo "  $key: " . var_export($value, true) . "\n";
    }
}
