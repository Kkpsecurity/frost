<?php

namespace App\Helpers;

use Akaunting\Setting\Facade as Setting;
use Illuminate\Support\Facades\DB;

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
    protected $prefix = '';

    /**
     * Constructor
     */
    public function __construct(string $prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Set the prefix for settings keys
     *
     * @param string $prefix
     * @return void
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Get the current prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Get a setting value with prefix
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $prefixedKey = $this->prefix ? $this->prefix . '.' . $key : $key;
        return Setting::get($prefixedKey, $default);
    }

    /**
     * Set a setting value with prefix
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set(string $key, $value)
    {
        $prefixedKey = $this->prefix ? $this->prefix . '.' . $key : $key;
        Setting::set($prefixedKey, $value);
        Setting::save(); // Force save to database
        return true;
    }

    /**
     * Check if a setting exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        $prefixedKey = $this->prefix ? $this->prefix . '.' . $key : $key;
        return Setting::has($prefixedKey);
    }

    /**
     * Delete a setting
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key)
    {
        $prefixedKey = $this->prefix ? $this->prefix . '.' . $key : $key;
        $result = Setting::forget($prefixedKey);
        Setting::save(); // Force save to database
        return $result;
    }

    /**
     * Get all settings with the current prefix
     *
     * @return array
     */
    public function all()
    {
        if (!$this->prefix) {
            return Setting::all();
        }

        // Query database directly for prefixed settings since Setting::all()
        // doesn't seem to load all settings from database
        $settings = DB::table('settings')
            ->where('key', 'like', $this->prefix . '.%')
            ->orderBy('key')
            ->pluck('value', 'key')
            ->toArray();

        // Remove prefix from keys
        $prefixedSettings = [];
        $prefixLength = strlen($this->prefix . '.');

        foreach ($settings as $key => $value) {
            $unprefixedKey = substr($key, $prefixLength);
            $prefixedSettings[$unprefixedKey] = $value;
        }

        return $prefixedSettings;
    }

    /**
     * Set multiple settings at once
     *
     * @param array $settings
     * @return bool
     */
    public function setMany(array $settings)
    {
        $success = true;
        foreach ($settings as $key => $value) {
            if (!$this->set($key, $value)) {
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
    public function getMany(array $keys, $default = null)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }
        return $results;
    }

    /**
     * Clear all settings with the current prefix
     *
     * @return bool
     */
    public function clear()
    {
        $settings = $this->all();
        $success = true;

        foreach (array_keys($settings) as $key) {
            if (!$this->forget($key)) {
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
    public function getGrouped()
    {
        $settings = $this->all();
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
