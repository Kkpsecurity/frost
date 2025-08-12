<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking Settings Database Structure\n";
echo "===================================\n";

$settings = DB::table('settings')->take(10)->get(['id', 'group', 'key', 'value']);

foreach ($settings as $setting) {
    echo "ID: {$setting->id}\n";
    echo "Group: " . ($setting->group ?? 'NULL') . "\n";
    echo "Key: {$setting->key}\n";
    echo "Value: " . substr($setting->value, 0, 50) . "...\n";
    echo "---\n";
}

echo "Total settings: " . DB::table('settings')->count() . "\n";
echo "Settings with dots in key: " . DB::table('settings')->where('key', 'like', '%.%')->count() . "\n";
