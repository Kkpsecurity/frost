<?php

namespace App\Traits;

/**
 * @file ExpirationTrait.php
 * @brief Trait for handling expiration logic in models.
 * @details Provides common methods for models that have expiration functionality.
 */

trait ExpirationTrait
{
    /**
     * Check if the model has expired
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->expires_at) {
            return false;
        }

        return now()->isAfter($this->expires_at);
    }

    /**
     * Get models that are not expired
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Get models that are expired
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}
