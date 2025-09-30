<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingHelper;
use Illuminate\Support\Facades\DB;

echo "=== Testing SettingHelper ===\n";

try {
    $settingHelper = new SettingHelper();

    // Test setting a value
    echo "1. Setting test value...\n";
    $settingHelper->set('test.setting', 'test_value');
    echo "✓ Setting saved\n";

    // Test retrieving the value
    echo "2. Retrieving test value...\n";
    $result = $settingHelper->get('test.setting');
    echo "✓ Retrieved: {$result}\n";

    // Check the settings table structure
    echo "3. Checking settings table structure...\n";
    $settings = DB::table('settings')->limit(5)->get();
    echo "✓ Settings table has " . count($settings) . " records\n";

    if (count($settings) > 0) {
        echo "Sample setting:\n";
        foreach ($settings as $setting) {
            echo "  Key: {$setting->key}, Value: {$setting->value}\n";
            break;
        }
    }

    // Check if AdminLTE settings exist
    echo "4. Checking AdminLTE settings...\n";
    $adminlteSettings = DB::table('settings')->where('key', 'like', 'adminlte.%')->count();
    echo "✓ AdminLTE settings count: {$adminlteSettings}\n";

    if ($adminlteSettings > 0) {
        $sampleAdminlte = DB::table('settings')->where('key', 'like', 'adminlte.%')->first();
        echo "Sample AdminLTE setting: {$sampleAdminlte->key} = {$sampleAdminlte->value}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
