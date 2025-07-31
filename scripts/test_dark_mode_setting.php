<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingHelper;
use Akaunting\Setting\Facade as Setting;

echo "=== DARK MODE SETTING DEBUG ===\n\n";

// Create SettingHelper with AdminLTE prefix
$settingHelper = new SettingHelper('adminlte');

echo "1. CURRENT DARK MODE VALUE:\n";
$currentValue = $settingHelper->get('layout_dark_mode');
echo "   Value: " . var_export($currentValue, true) . "\n";
echo "   Type: " . gettype($currentValue) . "\n\n";

echo "2. DIRECT SETTING CHECK:\n";
$directValue = Setting::get('adminlte.layout_dark_mode');
echo "   Direct Value: " . var_export($directValue, true) . "\n";
echo "   Direct Type: " . gettype($directValue) . "\n\n";

echo "3. ALL ADMINLTE SETTINGS:\n";
$allSettings = $settingHelper->all();
foreach ($allSettings as $key => $value) {
    if (str_contains($key, 'dark') || str_contains($key, 'layout_dark')) {
        echo "   {$key}: " . var_export($value, true) . " (" . gettype($value) . ")\n";
    }
}
echo "\n";

echo "4. TEST SETTING DARK MODE TO 1:\n";
$settingHelper->set('layout_dark_mode', '1');
echo "   Set to '1' (string)\n";
$newValue = $settingHelper->get('layout_dark_mode');
echo "   Retrieved: " . var_export($newValue, true) . " (" . gettype($newValue) . ")\n\n";

echo "5. TEST SETTING DARK MODE TO 0:\n";
$settingHelper->set('layout_dark_mode', '0');
echo "   Set to '0' (string)\n";
$newValue = $settingHelper->get('layout_dark_mode');
echo "   Retrieved: " . var_export($newValue, true) . " (" . gettype($newValue) . ")\n\n";

echo "6. DATABASE CHECK:\n";
$dbValue = \Illuminate\Support\Facades\DB::table('settings')
    ->where('key', 'adminlte.layout_dark_mode')
    ->first();

if ($dbValue) {
    echo "   DB Key: " . $dbValue->key . "\n";
    echo "   DB Value: " . var_export($dbValue->value, true) . "\n";
    echo "   DB Type: " . gettype($dbValue->value) . "\n";
} else {
    echo "   No database record found for 'adminlte.layout_dark_mode'\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
