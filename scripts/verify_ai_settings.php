<?php

// Verify AI Settings
echo "🔍 Verifying AI Settings...\n\n";

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$aiSettings = DB::table('settings')
    ->where('group', 'ai')
    ->get(['key', 'value']);

if ($aiSettings->isEmpty()) {
    echo "❌ No AI settings found in database!\n";
    exit(1);
}

echo "✅ Found " . $aiSettings->count() . " AI settings:\n\n";

foreach ($aiSettings as $setting) {
    echo "📋 {$setting->key}: {$setting->value}\n";
}

// Check specifically for Grok Code Fast setting
$grokSetting = $aiSettings->where('key', 'grok_code_fast_preview_enabled')->first();

if ($grokSetting && $grokSetting->value == '1') {
    echo "\n🎉 SUCCESS: Grok Code Fast 1 (Preview) is ENABLED for all clients!\n";
    echo "   Setting: {$grokSetting->key} = {$grokSetting->value}\n";
} else {
    echo "\n❌ ERROR: Grok Code Fast setting not found or disabled!\n";
    if ($grokSetting) {
        echo "   Current value: {$grokSetting->value}\n";
    }
}

echo "\n✅ AI Settings verification complete!\n";
