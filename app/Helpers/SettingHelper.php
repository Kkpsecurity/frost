<?php

namespace App\Helpers;

use Akaunting\Setting\Facade as Setting;

/**
 * Setting Helper
 *
 * This helper provides a global settings system with prefix support
 * using the Akaunting/Laravel-Setting package.
 */
class SettingHelper
{
    /**
     * The prefix for settings
     */
    protected static $prefix = '';

    /**
     * Set the prefix for settings keys
     *
     * @param string $prefix
     * @return void
     */
    public static function setPrefix(string $prefix)
    {
        self::$prefix = $prefix;
    }

    /**
     * Get the current prefix
     *
     * @return string
     */
    public static function getPrefix()
    {
        return self::$prefix;
    }

    /**
     * Get a setting value with prefix
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $prefixedKey = self::$prefix ? self::$prefix . '.' . $key : $key;
        return Setting::get($prefixedKey, $default);
    }

    /**
     * Set a setting value with prefix
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set(string $key, $value)
    {
        $prefixedKey = self::$prefix ? self::$prefix . '.' . $key : $key;
        return Setting::set($prefixedKey, $value);
    }

    /**
     * Check if a setting exists
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key)
    {
        $prefixedKey = self::$prefix ? self::$prefix . '.' . $key : $key;
        return Setting::has($prefixedKey);
    }

    /**
     * Delete a setting
     *
     * @param string $key
     * @return bool
     */
    public static function forget(string $key)
    {
        $prefixedKey = self::$prefix ? self::$prefix . '.' . $key : $key;
        return Setting::forget($prefixedKey);
    }

    /**
     * Get all settings with the current prefix
     *
     * @return array
     */
    public static function all()
    {
        if (!self::$prefix) {
            return Setting::all();
        }

        $allSettings = Setting::all();
        $prefixedSettings = [];
        $prefixLength = strlen(self::$prefix . '.');

        foreach ($allSettings as $key => $value) {
            if (strpos($key, self::$prefix . '.') === 0) {
                $unprefixedKey = substr($key, $prefixLength);
                $prefixedSettings[$unprefixedKey] = $value;
            }
        }

        return $prefixedSettings;
    }

    /**
     * Set multiple settings at once
     *
     * @param array $settings
     * @return bool
     */
    public static function setMany(array $settings)
    {
        $success = true;
        foreach ($settings as $key => $value) {
            if (!self::set($key, $value)) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Get multiple settings at once
     *
     * @param array $keys
     * @param mixed $default
     * @return array
     */
    public static function getMany(array $keys, $default = null)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = self::get($key, $default);
        }
        return $results;
    }

    /**
     * Clear all settings with the current prefix
     *
     * @return bool
     */
    public static function clear()
    {
        $settings = self::all();
        $success = true;

        foreach (array_keys($settings) as $key) {
            if (!self::forget($key)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Get settings grouped by category (using dot notation)
     *
     * Example: 'layout.dark_mode' and 'layout.sidebar_mini' will be grouped under 'layout'
     *
     * @return array
     */
    public static function getGrouped()
    {
        $settings = self::all();
        $grouped = [];

        foreach ($settings as $key => $value) {
            if (strpos($key, '.') !== false) {
                $parts = explode('.', $key, 2);
                $group = $parts[0];
                $subkey = $parts[1];
                $grouped[$group][$subkey] = $value;
            } else {
                $grouped['general'][$key] = $value;
            }
        }

        return $grouped;
    }

    /**
     * Example usage demonstration
     *
     * @return array
     */
    public static function exampleUsage()
    {
        return [
            'set_prefix' => 'SettingHelper::setPrefix("adminlte")',
            'set_value' => 'SettingHelper::set("title", "My Admin Panel")',
            'get_value' => 'SettingHelper::get("title", "Default Title")',
            'set_grouped' => 'SettingHelper::set("layout.dark_mode", true)',
            'get_grouped' => 'SettingHelper::get("layout.dark_mode", false)',
            'storage_key' => 'Stored as: adminlte.title, adminlte.layout.dark_mode',
        ];
    }
}
