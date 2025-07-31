<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AdminLTE Configuration Check ===\n";
echo "Database logo setting: " . config('adminlte.logo') . "\n";
echo "Database title setting: " . config('adminlte.title') . "\n";
echo "Database logo_img setting: " . config('adminlte.logo_img') . "\n";
echo "Database logo_img_alt setting: " . config('adminlte.logo_img_alt') . "\n";

echo "\n=== Direct Database Check ===\n";
use App\Helpers\SettingHelper;
$helper = new SettingHelper('adminlte');
echo "Direct DB logo: " . $helper->get('logo') . "\n";
echo "Direct DB title: " . $helper->get('title') . "\n";

echo "\n=== Raw Settings Table Check ===\n";
use Illuminate\Support\Facades\DB;
$settings = DB::table('settings')
    ->where('key', 'LIKE', 'adminlte.%')
    ->get(['key', 'value']);

foreach ($settings as $setting) {
    echo $setting->key . " = " . $setting->value . "\n";
}
