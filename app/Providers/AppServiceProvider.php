<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register component namespaces
        Blade::componentNamespace('App\\View\\Components\\Admin', 'admin');

        // Load AdminLTE settings from database and merge with config
        $this->loadAdminLteSettings();
    }

    /**
     * Load AdminLTE settings from database and merge with configuration
     */
    private function loadAdminLteSettings(): void
    {
        try {
            // Only load if database and settings table exist
            if (Schema::hasTable('settings')) {
                // Get adminlte settings from database
                $adminlteSettings = DB::table('settings')
                    ->where('key', 'LIKE', 'adminlte.%')
                    ->pluck('value', 'key');

                // Only use database settings if we have any, otherwise use defaults
                if ($adminlteSettings && count($adminlteSettings) > 0) {
                    // Start with base config as fallback
                    $baseConfig = config('adminlte');
                    $newConfig = [];

                    // Process database settings
                    foreach ($adminlteSettings as $key => $value) {
                        $configKey = str_replace('adminlte.', '', $key);

                        // Handle JSON values (like plugins_*_files)
                        if (str_starts_with($configKey, 'plugins_') && str_ends_with($configKey, '_files')) {
                            $decoded = json_decode($value, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $value = $decoded;
                            }
                        }

                        // Handle menu and filters arrays
                        if (in_array($configKey, ['menu', 'menu_filters'])) {
                            $decoded = json_decode($value, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $value = $decoded;
                            }
                        }

                        $newConfig[$configKey] = $value;
                    }

                    // Use database config with base config as fallback for missing keys
                    $finalConfig = array_merge($baseConfig, $newConfig);

                    // Ensure plugins structure is maintained
                    if (!isset($finalConfig['plugins']) || !is_array($finalConfig['plugins'])) {
                        $finalConfig['plugins'] = $baseConfig['plugins'] ?? [];
                    }

                    // Update the configuration
                    config(['adminlte' => $finalConfig]);
                } else {
                    // No database settings found, use defaults from file
                    // This is already loaded, so nothing to do
                }
            }
        } catch (\Exception $e) {
            // Silently fail if database isn't ready yet
            // This prevents errors during migrations, cache clearing, etc.
        }
    }
}
