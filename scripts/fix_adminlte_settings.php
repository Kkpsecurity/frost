<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

echo "=== Fixing Corrupted AdminLTE Settings ===\n\n";

// Fix corrupted values
$fixes = [
    'adminlte.preloader_enabled' => '1',
    'adminlte.preloader_mode' => 'fullscreen',
    'adminlte.classes_topnav_nav' => 'navbar-expand',
    'adminlte.sidebar_mini' => 'lg',
    'adminlte.preloader_img_height' => '60',
    'adminlte.preloader_img_width' => '60',
    'adminlte.preloader_img_effect' => 'animation__shake',
    'adminlte.classes_auth_card' => 'card-outline card-dark',
    'adminlte.classes_auth_header' => 'bg-dark text-light',
    'adminlte.classes_auth_body' => 'bg-dark text-light',
    'adminlte.classes_auth_footer' => 'bg-dark text-light',
    'adminlte.classes_auth_btn' => 'btn-flat btn-light',
    'adminlte.classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'adminlte.sidebar_scrollbar_auto_hide' => '0',
    'adminlte.right_sidebar_scrollbar_auto_hide' => 'l'
];

foreach ($fixes as $key => $value) {
    DB::table('settings')->updateOrInsert(['key' => $key], ['value' => $value]);
    echo "Fixed: {$key} = {$value}\n";
}

echo "\n✓ Corrupted settings have been fixed!\n";
echo "Please refresh your browser to see the changes.\n";
