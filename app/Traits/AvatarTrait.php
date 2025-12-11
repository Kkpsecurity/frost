<?php

namespace App\Traits;

/**
 * Avatar Trait
 * @version: 2.0.0
 * @author: Richard Clark
 *
 * @dependencies
 * --- Laravolt\Avatar\Avatar
 * --- Illuminate\Support\Facades\Storage
 * --- Illuminate\Support\Facades\Cache
 */

use Laravolt\Avatar\Avatar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait AvatarTrait
{
    /**
     * Avatar size configurations
     */
    protected static array $avatarSizes = [
        'thumb' => ['width' => 100, 'height' => 100],
        'regular' => ['width' => 220, 'height' => 220],
        'large' => ['width' => 400, 'height' => 400],
        'small' => ['width' => 50, 'height' => 50],
    ];

    /**
     * Get user avatar URL with caching and proper error handling
     *
     * @param string $size Avatar size (thumb, regular, large, small)
     * @return string Avatar URL
     */
    public function getAvatar(string $size = 'thumb'): string
    {
        // Validate size
        if (!array_key_exists($size, self::$avatarSizes)) {
            $size = 'thumb';
        }

        // Create secure cache key using hashed user ID
        $cacheKey = 'avatar_' . md5($this->id . '_' . get_class($this)) . '_' . $size;

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($size) {
            try {
                // Priority: Custom avatar -> Gravatar -> Generated avatar
                if ($this->hasCustomAvatar()) {
                    return $this->getCustomAvatarUrl();
                }

                if ($this->shouldUseGravatar()) {
                    return $this->getGravatarUrl(self::$avatarSizes[$size]['width']);
                }

                return $this->generateDefaultAvatar($size);
            } catch (\Exception $e) {
                Log::warning('Avatar generation failed for user ' . $this->id, [
                    'error' => $e->getMessage(),
                    'size' => $size
                ]);
                return $this->getFallbackAvatar();
            }
        });
    }

    /**
     * Check if user has a custom uploaded avatar
     *
     * @return bool
     */
    public function hasCustomAvatar(): bool
    {
        if (empty($this->avatar)) {
            return false;
        }

        // Handle both JSON and string formats for backwards compatibility
        $avatarPath = $this->getAvatarPath();

        return !empty($avatarPath) && Storage::disk('public')->exists($avatarPath);
    }

    /**
     * Get the avatar path from the avatar field
     *
     * @return string|null
     */
    protected function getAvatarPath(): ?string
    {
        if (empty($this->avatar)) {
            return null;
        }

        // Handle JSON format (legacy)
        if ($this->isJson($this->avatar)) {
            $avatarData = json_decode($this->avatar, true);
            return isset($avatarData['filename']) ? 'avatars/' . $avatarData['filename'] : null;
        }

        // Handle direct path format (current)
        return $this->avatar;
    }

    /**
     * Get custom avatar URL
     *
     * @return string
     */
    protected function getCustomAvatarUrl(): string
    {
        $avatarPath = $this->getAvatarPath();
        return asset('storage/' . $avatarPath);
    }

    /**
     * Check if user should use Gravatar
     *
     * @return bool
     */
    public function shouldUseGravatar(): bool
    {
        return (bool) ($this->use_gravatar ?? false) && !empty($this->email);
    }

    /**
     * Get Gravatar URL with proper parameters
     *
     * @param int $size
     * @return string
     */
    protected function getGravatarUrl(int $size = 100): string
    {
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=identicon&r=pg";
    }

    /**
     * Generate a default avatar using the Avatar package
     *
     * @param string $size
     * @return string
     */
    protected function generateDefaultAvatar(string $size): string
    {
        $avatar = new Avatar();
        $fullName = $this->getDisplayName();
        $dimensions = self::$avatarSizes[$size];

        return $avatar->create($fullName)
            ->setDimension($dimensions['width'], $dimensions['height'])
            ->toBase64();
    }

    /**
     * Get display name for avatar generation
     *
     * @return string
     */
    protected function getDisplayName(): string
    {
        // Try different methods to get the full name
        if (method_exists($this, 'fullname')) {
            return $this->fullname();
        }

        if (method_exists($this, 'getFullNameAttribute')) {
            return $this->getFullNameAttribute();
        }

        if (isset($this->full_name)) {
            return $this->full_name;
        }

        // Fallback to combining fname and lname
        $fname = $this->fname ?? $this->first_name ?? '';
        $lname = $this->lname ?? $this->last_name ?? '';

        if ($fname || $lname) {
            return trim($fname . ' ' . $lname);
        }

        // Final fallback to email or 'User'
        return $this->email ?? 'User';
    }

    /**
     * Get fallback avatar for error cases
     *
     * @return string
     */
    protected function getFallbackAvatar(): string
    {
        return asset('assets/img/icon/headshot.png');
    }

    /**
     * Check if string is valid JSON
     *
     * @param string $string
     * @return bool
     */
    protected function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Clear avatar cache for this user
     *
     * @return void
     */
    public function clearAvatarCache(): void
    {
        $baseKey = 'avatar_' . md5($this->id . '_' . get_class($this));

        foreach (array_keys(self::$avatarSizes) as $size) {
            Cache::forget($baseKey . '_' . $size);
        }
    }

    /**
     * Get avatar URL for specific size (public interface)
     *
     * @param string $size
     * @return string
     */
    public function getAvatarUrl(string $size = 'thumb'): string
    {
        return $this->getAvatar($size);
    }

    /**
     * Get all available avatar sizes
     *
     * @return array
     */
    public static function getAvailableSizes(): array
    {
        return array_keys(self::$avatarSizes);
    }

    /**
     * Update avatar and clear cache
     *
     * @param string|null $avatarPath
     * @param bool $useGravatar
     * @return void
     */
    public function updateAvatar(?string $avatarPath = null, bool $useGravatar = false): void
    {
        // Delete old avatar file if exists and we're updating to a new one
        if ($avatarPath && $this->hasCustomAvatar()) {
            $oldPath = $this->getAvatarPath();
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $this->avatar = $avatarPath;
        $this->use_gravatar = $useGravatar;
        $this->save();

        $this->clearAvatarCache();
    }

    /**
     * Legacy method for backwards compatibility
     *
     * @param $user
     * @return string
     * @deprecated Use getGravatarUrl() instead
     */
    public function getGravatarAttribute($user): string
    {
        return $this->getGravatarUrl();
    }
}
