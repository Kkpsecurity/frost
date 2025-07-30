<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check sidebar settings in database
echo "=== SIDEBAR SETTINGS IN DATABASE ===\n";
$settings = DB::table('settings')->where('key', 'like', 'adminlte.sidebar_%')->get();

if ($settings->isEmpty()) {
    echo "No sidebar settings found in database!\n";
} else {
    foreach ($settings as $setting) {
        echo sprintf("%-30s = %-10s (%s)\n",
            $setting->key,
            $setting->value,
            gettype($setting->value)
        );
    }
}

echo "\n=== ALL ADMINLTE SETTINGS COUNT ===\n";
$allAdminlte = DB::table('settings')->where('key', 'like', 'adminlte.%')->count();
echo "Total AdminLTE settings: $allAdminlte\n";

// Test SettingHelper
echo "\n=== TESTING SETTINGHELPER ===\n";
use App\Helpers\SettingHelper;
$helper = new SettingHelper('adminlte');
$sidebarCollapsed = $helper->get('sidebar_collapsed');
echo "sidebar_collapsed via SettingHelper: " . var_export($sidebarCollapsed, true) . " (" . gettype($sidebarCollapsed) . ")\n";

// Test direct Setting facade
echo "\n=== TESTING SETTING FACADE ===\n";
use Akaunting\Setting\Facade as Setting;
$sidebarCollapsedDirect = Setting::get('adminlte.sidebar_collapsed');
echo "adminlte.sidebar_collapsed via Setting: " . var_export($sidebarCollapsedDirect, true) . " (" . gettype($sidebarCollapsedDirect) . ")\n";

echo "\n=== TEST COMPLETE ===\n";
