<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

/**
 * @file RCacheModelTrait.php
 * @brief Trait for adding cache functionality to models.
 * @details Provides methods for caching model data using Laravel Cache.
 */

trait RCacheModelTrait
{
    /**
     * Clear cache for this model
     *
     * @return void
     */
    public function clearCache()
    {
        $cacheKey = $this->getCacheKey();
        Cache::forget($cacheKey);
    }

    /**
     * Get cache key for this model
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return get_class($this) . ':' . $this->getKey();
    }

    /**
     * Cache this model
     *
     * @param int $minutes
     * @return $this
     */
    public function cache($minutes = 60)
    {
        $cacheKey = $this->getCacheKey();
        Cache::put($cacheKey, $this, $minutes);
        return $this;
    }

    /**
     * Get model from cache or database
     *
     * @param mixed $id
     * @param int $minutes
     * @return mixed
     */
    public static function cached($id, $minutes = 60)
    {
        $cacheKey = static::class . ':' . $id;

        return Cache::remember($cacheKey, $minutes, function () use ($id) {
            return static::find($id);
        });
    }

    /**
     * Boot the cache trait
     */
    protected static function bootRCacheModelTrait()
    {
        static::updated(function ($model) {
            $model->clearCache();
        });

        static::deleted(function ($model) {
            $model->clearCache();
        });
    }
}
