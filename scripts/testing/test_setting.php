<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Title Field Test ===\n\n";

// Check current title value in database
$currentTitle = DB::table('settings')
    ->where('key', 'adminlte.title')
    ->value('value');

echo "Current title in database: " . ($currentTitle ?: 'NOT SET') . "\n";

// Check current title in Laravel config
$configTitle = config('adminlte.title');
echo "Current title in Laravel config: " . ($configTitle ?: 'NOT SET') . "\n\n";

echo "Now change the title in the form and submit to see if it updates...\n";
