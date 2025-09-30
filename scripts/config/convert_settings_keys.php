#!/usr/bin/env php
<?php

/**
 * Simple CLI script to convert settings from dot notation to group structure
 * Usage: php scripts/convert_settings_keys.php
 */

// Bootstrap Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Settings Key Converter\n";
echo "====================\n";

try {
    // Find all settings with dot notation
    $dotNotationSettings = DB::table('settings')
        ->where('key', 'like', '%.%')
        ->get();

    if ($dotNotationSettings->isEmpty()) {
        echo "No dot notation settings found to convert.\n";
        exit(0);
    }

    echo "Found {$dotNotationSettings->count()} settings to convert:\n\n";

    $converted = 0;

    foreach ($dotNotationSettings as $setting) {
        // Split the key by the first dot only
        $parts = explode('.', $setting->key, 2);

        if (count($parts) !== 2) {
            echo "SKIP: {$setting->key} - invalid format\n";
            continue;
        }

        $group = $parts[0];
        $newKey = $parts[1];

        echo "Converting: {$setting->key} → group: '{$group}', key: '{$newKey}'\n";

        // Update the setting
        DB::table('settings')
            ->where('id', $setting->id)
            ->update([
                'group' => $group,
                'key' => $newKey
            ]);

        $converted++;
    }

    echo "\n";
    echo "Conversion complete!\n";
    echo "Converted: {$converted} settings\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
