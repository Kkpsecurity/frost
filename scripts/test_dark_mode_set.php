<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingHelper;
use Illuminate\Support\Facades\DB;

$helper = new SettingHelper('adminlte');

echo "Before: " . $helper->get('layout_dark_mode') . "\n";
$helper->set('layout_dark_mode', '0');
echo "After: " . $helper->get('layout_dark_mode') . "\n";

// Verify from database directly
echo "DB Check: " . DB::table('settings')->where('key', 'adminlte.layout_dark_mode')->value('value') . "\n";
