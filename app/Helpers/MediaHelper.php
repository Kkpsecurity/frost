<?php

namespace App\Helpers;

use App\Classes\MediaManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

/**
 * Media Helper Class
 *
 * Standardizes media URL generation to use Laravel's disk system
 * Provides backward compatibility while migrating to proper media management
 */
class MediaHelper
{
    /**
     * Generate media URL using the proper disk system
     *
     * @param string $path The media file path
     * @param string $disk The disk to use (public, media, s3_media)
     * @param string $fallback Fallback image if file doesn't exist
     * @return string
     */
    public static function url(string $path, string $disk = 'public', string $fallback = null): string
    {
        // Remove leading slash and 'assets/' prefix if present for backward compatibility
        $cleanPath = ltrim($path, '/');
        $cleanPath = preg_replace('/^assets\//', '', $cleanPath);

        // For legacy asset paths, map to media disk structure
        if (str_starts_with($path, 'assets/')) {
            $cleanPath = 'assets/' . str_replace('assets/', '', $cleanPath);
            $disk = 'media';
        }

        try {
            // Check if file exists on the specified disk
            if (Storage::disk($disk)->exists($cleanPath)) {
                // Generate URL using Laravel's built-in method
                return self::generateDiskUrl($disk, $cleanPath);
            }

            // Try fallback path if original doesn't exist
            if ($fallback && Storage::disk($disk)->exists($fallback)) {
                return self::generateDiskUrl($disk, $fallback);
            }

            // Return default placeholder or original asset() call as ultimate fallback
            return asset($path);

        } catch (\Exception $e) {
            // Log the error but don't break the page
            Log::warning("MediaHelper: Failed to generate URL for {$path} on disk {$disk}: " . $e->getMessage());

            // Return original asset() call as fallback
            return asset($path);
        }
    }

    /**
     * Generate URL for a file on a specific disk
     */
    private static function generateDiskUrl(string $disk, string $path): string
    {
        $diskConfig = config("filesystems.disks.{$disk}");

        if (!$diskConfig) {
            return asset($path);
        }

        switch ($diskConfig['driver']) {
            case 'local':
                if (isset($diskConfig['url'])) {
                    return rtrim($diskConfig['url'], '/') . '/' . ltrim($path, '/');
                }
                break;

            case 's3':
                // For S3, generate a basic URL based on configuration
                if (isset($diskConfig['url'])) {
                    return rtrim($diskConfig['url'], '/') . '/' . ltrim($path, '/');
                }
                // Fallback to asset URL if no S3 URL configured
                return asset($path);
        }

        return asset($path);
    }

    /**
     * Get blog post featured image URL
     *
     * @param string $slug Blog post slug
     * @param string $fallback Fallback image
     * @return string
     */
    public static function blogImage(string $slug, string $fallback = 'img/blog/b1.jpg'): string
    {
        $imageMap = [
            'florida-gun-laws-2025' => 'florida-gun-laws.jpg',
            'essential-firearms-safety' => 'firearms-safety.jpg',
            'threat-assessment-techniques' => 'threat-assessment.jpg',
            'security-license-renewal' => 'security-license.jpg',
            'concealed-carry-florida' => 'concealed-carry.jpg',
            'security-training' => 'security-training.jpg',
            'security-officer' => 'security-officer-featured.jpg',
            'ensuring-compliance' => 'compliance.jpg',
        ];

        $imagePath = isset($imageMap[$slug])
            ? "content/blog/{$imageMap[$slug]}"
            : "content/blog/{$fallback}";

        return self::url($imagePath, 'media', "content/blog/b1.jpg");
    }

    /**
     * Get course icon URL
     *
     * @param string $type Course type (class-d, class-g, etc.)
     * @return string
     */
    public static function courseIcon(string $type): string
    {
        $iconMap = [
            'class-d' => 'assets/icons/online-course-icon-class-d.png',
            'class-g' => 'assets/icons/online-course-icon-class-g.png',
        ];

        $iconPath = $iconMap[$type] ?? 'assets/icons/default-course.png';

        return self::url($iconPath, 'media');
    }

    /**
     * Get logo URL
     *
     * @param string $variant Logo variant (logo, logo2, etc.)
     * @return string
     */
    public static function logo(string $variant = 'logo'): string
    {
        $logoPath = "assets/logo/{$variant}.png";

        return self::url($logoPath, 'media');
    }

    /**
     * Migrate existing asset URLs to use proper media disk
     * This method helps transition existing hardcoded asset() calls
     *
     * @param string $assetPath Original asset path
     * @return string
     */
    public static function migrateAssetUrl(string $assetPath): string
    {
        // Map common asset patterns to media disk structure
        $migrations = [
            'assets/img/blog/' => 'content/blog/',
            'assets/img/icon/' => 'assets/icons/',
            'assets/img/logo/' => 'assets/logo/',
            'assets/img/' => 'assets/images/',
            'themes/frost/bultifore/img/' => 'assets/theme/',
        ];

        foreach ($migrations as $oldPattern => $newPattern) {
            if (str_contains($assetPath, $oldPattern)) {
                $newPath = str_replace($oldPattern, $newPattern, $assetPath);
                return self::url($newPath, 'media');
            }
        }

        // If no migration pattern matches, try as-is
        return self::url($assetPath, 'media');
    }
}
