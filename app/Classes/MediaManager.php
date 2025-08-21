<?php

namespace App\Classes;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * Media Manager Helper Class
 * 
 * Provides unified access to media files using the configured media structure
 * Integrates with config/media.php for centralized media management
 */
class MediaManager
{
    /**
     * Get the configured media disk
     */
    public static function disk()
    {
        return Storage::disk(config('media.default_disk', 'media'));
    }

    /**
     * Get media configuration
     */
    public static function config(string $key = null)
    {
        return $key ? config("media.{$key}") : config('media');
    }

    /**
     * Generate media URL for a given path
     */
    public static function url(string $path): string
    {
        $config = self::config('urls');
        
        if ($config['cdn_enabled'] && $config['cdn_domain']) {
            return rtrim($config['cdn_domain'], '/') . '/' . ltrim($path, '/');
        }

        if ($config['signed_urls']) {
            return URL::temporarySignedRoute(
                'media.serve',
                now()->addMinutes($config['signed_url_expiry']),
                ['path' => $path]
            );
        }

        return self::disk()->url($path);
    }

    /**
     * Store a file in the appropriate media directory with validation
     */
    public static function store(string $category, string $subcategory, $file, ?string $filename = null): string|false
    {
        // Validate category and subcategory
        $categoryConfig = self::config("categories.{$category}");
        if (!$categoryConfig) {
            throw new \InvalidArgumentException("Invalid media category: {$category}");
        }

        $subcategoryConfig = $categoryConfig['subdirectories'][$subcategory] ?? null;
        if (!$subcategoryConfig) {
            throw new \InvalidArgumentException("Invalid subcategory: {$subcategory} for category: {$category}");
        }

        // Validate file type and size
        self::validateFile($file, $subcategoryConfig);

        // Generate path
        $path = self::buildPath($category, $subcategory);
        
        // Generate filename if not provided
        if (!$filename) {
            $filename = self::generateFilename($file);
        }

        return self::disk()->putFileAs($path, $file, $filename);
    }

    /**
     * Validate uploaded file against configuration
     */
    protected static function validateFile($file, array $config): void
    {
        // Check file size
        if ($file->getSize() > $config['max_size']) {
            throw new \InvalidArgumentException(
                'File size exceeds maximum allowed size of ' . number_format($config['max_size'] / 1024 / 1024, 2) . 'MB'
            );
        }

        // Check file type
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, $config['allowed_types']) && !in_array('*', $config['allowed_types'])) {
            throw new \InvalidArgumentException(
                'File type not allowed. Allowed types: ' . implode(', ', $config['allowed_types'])
            );
        }

        // Additional security checks
        $securityConfig = self::config('security');
        if (in_array($extension, $securityConfig['forbidden_extensions'])) {
            throw new \InvalidArgumentException('File type is forbidden for security reasons');
        }
    }

    /**
     * Build storage path for a category and subcategory
     */
    protected static function buildPath(string $category, string $subcategory, ?string $identifier = null): string
    {
        $categoryConfig = self::config("categories.{$category}");
        $subcategoryConfig = $categoryConfig['subdirectories'][$subcategory];
        
        $path = $categoryConfig['directory'] . '/' . $subcategoryConfig['path'];
        
        // Add dynamic identifier if configured (e.g., user-id, course-id)
        if ($identifier && ($subcategoryConfig['dynamic_structure'] ?? false)) {
            $path .= '/' . $identifier;
        }
        
        return $path;
    }

    /**
     * Generate unique filename
     */
    protected static function generateFilename($file): string
    {
        $config = self::config('helpers');
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        if ($config['use_uuid_filenames']) {
            $filename = (string) Str::uuid();
        } else {
            $filename = Str::slug($basename);
        }
        
        if ($config['timestamp_filenames']) {
            $filename .= '_' . now()->format('YmdHis');
        }
        
        return $filename . '.' . $extension;
    }

    /**
     * Delete a media file
     */
    public static function delete(string $path): bool
    {
        return self::disk()->delete($path);
    }

    /**
     * Check if a media file exists
     */
    public static function exists(string $path): bool
    {
        return self::disk()->exists($path);
    }

    /**
     * Get media file contents
     */
    public static function get(string $path): string|null
    {
        return self::disk()->get($path);
    }

    // ================================
    // CATEGORY-SPECIFIC HELPERS
    // ================================

    /**
     * Asset category helpers
     */
    public static function assetImage(string $path, string $subtype = 'backgrounds'): string
    {
        return self::url("assets/images/{$subtype}/{$path}");
    }

    public static function assetIcon(string $path, string $subtype = 'custom'): string
    {
        return self::url("assets/icons/{$subtype}/{$path}");
    }

    public static function assetLogo(string $path, string $subtype = 'primary'): string
    {
        return self::url("assets/logos/{$subtype}/{$path}");
    }

    /**
     * Content category helpers
     */
    public static function courseContent(string $courseId, string $filename, string $type = 'videos'): string
    {
        return self::url("content/courses/{$courseId}/{$type}/{$filename}");
    }

    public static function document(string $filename, string $subtype = 'guides'): string
    {
        return self::url("content/documents/{$subtype}/{$filename}");
    }

    public static function video(string $filename, string $subtype = 'tutorials'): string
    {
        return self::url("content/videos/{$subtype}/{$filename}");
    }

    /**
     * User category helpers
     */
    public static function avatar(string $filename, string $size = 'original'): string
    {
        return self::url("user/avatars/{$size}/{$filename}");
    }

    public static function userUpload(string $userId, string $filename, string $type = 'personal'): string
    {
        return self::url("user/uploads/{$userId}/{$type}/{$filename}");
    }

    public static function certificate(string $filename, string $type = 'generated'): string
    {
        return self::url("user/certificates/{$type}/{$filename}");
    }

    /**
     * System category helpers
     */
    public static function cached(string $filename, string $type = 'images'): string
    {
        return self::url("system/cache/{$type}/{$filename}");
    }

    public static function temp(string $filename, string $type = 'uploads'): string
    {
        return self::url("system/temp/{$type}/{$filename}");
    }

    // ================================
    // STORAGE HELPERS
    // ================================

    /**
     * Store asset file
     */
    public static function storeAsset($file, string $subtype = 'images', ?string $filename = null): string|false
    {
        return self::store('assets', $subtype, $file, $filename);
    }

    /**
     * Store course content
     */
    public static function storeCourseContent($file, string $courseId, string $type = 'videos', ?string $filename = null): string|false
    {
        $path = "content/courses/{$courseId}/{$type}";
        return self::disk()->putFileAs($path, $file, $filename ?: self::generateFilename($file));
    }

    /**
     * Store user avatar
     */
    public static function storeAvatar($file, ?string $filename = null): string|false
    {
        return self::store('user', 'avatars', $file, $filename);
    }

    /**
     * Store user upload
     */
    public static function storeUserUpload($file, string $userId, string $type = 'personal', ?string $filename = null): string|false
    {
        $categoryConfig = self::config('categories.user');
        $path = "user/uploads/{$userId}/{$type}";
        return self::disk()->putFileAs($path, $file, $filename ?: self::generateFilename($file));
    }

    // ================================
    // UTILITY METHODS
    // ================================

    /**
     * Get media categories configuration
     */
    public static function getCategories(): array
    {
        return self::config('categories');
    }

    /**
     * Get category structure for a specific category
     */
    public static function getCategoryStructure(string $category): array
    {
        return self::config("categories.{$category}") ?? [];
    }

    /**
     * Create complete directory structure based on configuration
     */
    public static function ensureDirectoryStructure(): void
    {
        $disk = self::disk();
        $categories = self::config('categories');
        
        foreach ($categories as $categoryName => $categoryConfig) {
            foreach ($categoryConfig['subdirectories'] as $subName => $subConfig) {
                $path = $categoryConfig['directory'] . '/' . $subConfig['path'];
                
                // Create main subdirectory
                if (!$disk->exists($path)) {
                    $disk->makeDirectory($path);
                }
                
                // Create nested subdirectories if defined
                if (isset($subConfig['subdirs'])) {
                    foreach ($subConfig['subdirs'] as $nestedDir => $description) {
                        $nestedPath = $path . '/' . $nestedDir;
                        if (!$disk->exists($nestedPath)) {
                            $disk->makeDirectory($nestedPath);
                        }
                    }
                }
            }
        }
    }

    /**
     * Clean up temporary and cache files based on configuration
     */
    public static function cleanup(): void
    {
        $cleanupConfig = self::config('cleanup');
        
        if (!$cleanupConfig['enabled']) {
            return;
        }

        $disk = self::disk();
        
        // Clean temporary files
        $tempPath = 'system/temp';
        $tempRetention = now()->subHours($cleanupConfig['temp_file_retention']);
        self::cleanOldFiles($disk, $tempPath, $tempRetention);
        
        // Clean cache files
        $cachePath = 'system/cache';
        $cacheRetention = now()->subDays($cleanupConfig['cache_retention']);
        self::cleanOldFiles($disk, $cachePath, $cacheRetention);
    }

    /**
     * Clean old files from a directory
     */
    protected static function cleanOldFiles($disk, string $path, $before): void
    {
        if (!$disk->exists($path)) {
            return;
        }

        $files = $disk->allFiles($path);
        
        foreach ($files as $file) {
            if ($disk->lastModified($file) < $before->timestamp) {
                $disk->delete($file);
            }
        }
    }
}
