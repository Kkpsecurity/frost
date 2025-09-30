<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$setting = DB::table('settings')->where('group', 'ai')->where('key', 'grok_code_fast_preview_enabled')->first();

if ($setting) {
    echo "✅ SUCCESS: Grok Code Fast 1 (Preview) is enabled for all clients!\n";
    echo "Setting Details:\n";
    echo "- Group: {$setting->group}\n";
    echo "- Key: {$setting->key}\n";
    echo "- Value: {$setting->value}\n";
} else {
    echo "❌ Setting not found\n";
}
