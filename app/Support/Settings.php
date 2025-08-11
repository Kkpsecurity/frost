<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Application Settings Helper
 * Provides cacheable settings management
 */
final class Settings
{
    /**
     * Get a setting value with caching
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("settings.$key", 300, function () use ($key, $default) {
            $setting = DB::table('app_settings')->where('key', $key)->first();

            return $setting
                ? json_decode($setting->value, true)
                : $default;
        });
    }

    /**
     * Set a setting value and clear cache
     *
     * @param string $key
     * @param array $value
     * @return void
     */
    public static function put(string $key, array $value): void
    {
        DB::table('app_settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => json_encode($value),
                'updated_at' => now()
            ]
        );

        Cache::forget("settings.$key");
    }

    /**
     * Remove a setting and clear cache
     *
     * @param string $key
     * @return void
     */
    public static function forget(string $key): void
    {
        DB::table('app_settings')->where('key', $key)->delete();
        Cache::forget("settings.$key");
    }

    /**
     * Get all settings (not cached)
     *
     * @return array
     */
    public static function all(): array
    {
        return DB::table('app_settings')
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => json_decode($setting->value, true)];
            })
            ->toArray();
    }
}
