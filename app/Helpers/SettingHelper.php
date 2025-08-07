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
        // Parse the key to extract group and actual key
        $fullKey = $this->prefix ? $this->prefix . '.' . $key : $key;

        if (str_contains($fullKey, '.')) {
            [$group, $actualKey] = explode('.', $fullKey, 2);
        } else {
            $group = 'general';
            $actualKey = $fullKey;
        }

        // Query the database directly
        $setting = DB::table('settings')
            ->where('group', $group)
            ->where('key', $actualKey)
            ->first();

        return $setting ? $setting->value : $default;
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
        // Parse the key to extract group and actual key
        $fullKey = $this->prefix ? $this->prefix . '.' . $key : $key;

        if (str_contains($fullKey, '.')) {
            [$group, $actualKey] = explode('.', $fullKey, 2);
        } else {
            $group = 'general';
            $actualKey = $fullKey;
        }

        // Use direct database insertion to handle the group column
        DB::table('settings')->updateOrInsert(
            ['group' => $group, 'key' => $actualKey],
            ['value' => $value]
        );

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
        // Parse the key to extract group and actual key
        $fullKey = $this->prefix ? $this->prefix . '.' . $key : $key;

        if (str_contains($fullKey, '.')) {
            [$group, $actualKey] = explode('.', $fullKey, 2);
        } else {
            $group = 'general';
            $actualKey = $fullKey;
        }

        // Query the database directly
        return DB::table('settings')
            ->where('group', $group)
            ->where('key', $actualKey)
            ->exists();
    }

    /**
     * Delete a setting
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key)
    {
        // Parse the key to extract group and actual key
        $fullKey = $this->prefix ? $this->prefix . '.' . $key : $key;

        if (str_contains($fullKey, '.')) {
            [$group, $actualKey] = explode('.', $fullKey, 2);
        } else {
            $group = 'general';
            $actualKey = $fullKey;
        }

        // Delete from database directly
        $deleted = DB::table('settings')
            ->where('group', $group)
            ->where('key', $actualKey)
            ->delete();

        return $deleted > 0;
    }

    /**
     * Get all settings with the current prefix
     *
     * @return array
     */
    public function all()
    {
        if (!$this->prefix) {
            // Return all settings grouped by their original key format
            $settings = DB::table('settings')
                ->orderBy('group')
                ->orderBy('key')
                ->get();

            $result = [];
            foreach ($settings as $setting) {
                if ($setting->group === 'general') {
                    $result[$setting->key] = $setting->value;
                } else {
                    $result[$setting->group . '.' . $setting->key] = $setting->value;
                }
            }
            return $result;
        }

        // Query database directly for prefixed settings
        $settings = DB::table('settings')
            ->where('group', $this->prefix)
            ->orderBy('key')
            ->pluck('value', 'key')
            ->toArray();

        return $settings;
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
