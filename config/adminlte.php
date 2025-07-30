<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load the base configuration
$config = include('adminlte_config.php');

// Only try to merge database settings if we're in a web request context
// and not during config caching or early bootstrap
try {
    if (function_exists('app') && app()->bound('db') && Schema::hasTable('settings')) {
        // Safely get adminlte settings from database
        $adminlteSettings = DB::table('settings')
            ->where('key', 'LIKE', 'adminlte.%')
            ->pluck('value', 'key');

        // Merge database settings with config
        foreach ($adminlteSettings as $key => $value) {
            $configKey = str_replace('adminlte.', '', $key);
            $config[$configKey] = $value;
        }
    }
} catch (\Exception $e) {
    // Silently fail if database/settings aren't available yet
    // This prevents errors during migrations, cache clearing, etc.
}

return $config;
