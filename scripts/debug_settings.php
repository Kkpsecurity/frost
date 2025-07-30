<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Akaunting\Setting\Facade as Setting;

echo "=== Testing Akaunting Setting Facade ===\n";

try {
    echo "1. Getting all settings via Setting::all()...\n";
    $allSettings = Setting::all();
    echo "✓ Found " . count($allSettings) . " total settings\n";

    echo "\n2. Looking for AdminLTE settings...\n";
    $adminlteCount = 0;
    $sampleAdminlte = [];

    foreach ($allSettings as $key => $value) {
        if (strpos($key, 'adminlte.') === 0) {
            $adminlteCount++;
            if (count($sampleAdminlte) < 3) {
                $sampleAdminlte[$key] = $value;
            }
        }
    }

    echo "✓ Found {$adminlteCount} AdminLTE settings in Setting::all()\n";

    if (count($sampleAdminlte) > 0) {
        echo "\nSample AdminLTE settings:\n";
        foreach ($sampleAdminlte as $key => $value) {
            echo "  {$key} = {$value}\n";
        }
    }

    echo "\n3. Testing direct AdminLTE setting retrieval...\n";
    $titleSetting = Setting::get('adminlte.title', 'NOT_FOUND');
    echo "✓ adminlte.title = {$titleSetting}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
