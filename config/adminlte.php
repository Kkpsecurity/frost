<?php

// Load the base configuration
$config = include('adminlte_config.php');

// Try to get dynamic configuration from database
try {
    if (function_exists('app') && app()->bound('db')) {
        $dynamicConfig = App\Services\AdminLteService::initiateConfig();
        if ($dynamicConfig) {
            return $dynamicConfig;
        }
    }
} catch (\Exception $e) {
    // Fall back to static config if database isn't available
}

// Return base config as fallback
return $config;
