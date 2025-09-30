<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingHelper;

$helper = new SettingHelper('adminlte');

echo "Logo setting: " . $helper->get('logo') . "\n";
echo "Title setting: " . $helper->get('title') . "\n";
