<?php

// Enable Grok Code Fast 1 (Preview) for all clients
echo "Enabling Grok Code Fast 1 (Preview) for all clients...\n\n";

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Check if the setting exists
$existing = DB::table('settings')
    ->where('group', 'ai')
    ->where('key', 'grok_code_fast_preview_enabled')
    ->first();

if ($existing) {
    echo "✅ Setting already exists and is enabled!\n";
    echo "Group: {$existing->group}\n";
    echo "Key: {$existing->key}\n";
    echo "Value: {$existing->value}\n";
} else {
    echo "Setting not found, creating it...\n";

    // Create the setting (without timestamp columns since they don't exist)
    $result = DB::table('settings')->insert([
        'group' => 'ai',
        'key' => 'grok_code_fast_preview_enabled',
        'value' => '1'
    ]);

    if ($result) {
        echo "✅ Successfully created and enabled Grok Code Fast 1 (Preview) setting!\n";
        echo "The feature is now enabled for all clients.\n";
    } else {
        echo "❌ Failed to create the setting.\n";
    }
}

echo "\n🎉 Grok Code Fast 1 (Preview) has been successfully enabled for all clients!\n";
