<?php

namespace App\Services;

use App\Support\AdminLteConfigElements;
use App\Helpers\SettingHelper;

class AdminLteService {

    protected $configElements;

    public function __construct(AdminLteConfigElements $configElements)
    {
        $this->configElements = $configElements;
    }

    public static function initiateConfig(): ?array
    {
        try {
            // Only proceed if the app is fully booted
            if (!function_exists('app') || !app()->bound('db')) {
                return null;
            }

            $configElements = new AdminLteConfigElements(new SettingHelper());

            // Get the base configuration
            $baseConfig = include(config_path('adminlte_config.php'));

            // Merge with database-driven configuration
            return array_merge(
                $baseConfig,
                $configElements->getAllConfiguration()
            );
        } catch (\Exception $e) {
            // Return null if anything fails, let config fall back to static config
            return null;
        }
    }

}
