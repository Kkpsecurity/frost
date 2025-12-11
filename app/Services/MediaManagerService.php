<?php

namespace App\Services;

use App\Models\MediaFile;
use App\Models\MediaPermission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class MediaManagerService
{
    protected $disk;
    protected $basePath;

    // File type categories (legacy support)
    const CATEGORY_STUDENT_VALIDATIONS = 'students/validations';
    const CATEGORY_STUDENT_AVATARS = 'students/avatars';
    const CATEGORY_CERTIFICATES = 'certificates/pdf';
    const CATEGORY_DOCUMENTS = 'documents';
    const CATEGORY_IMAGES = 'images';
    const CATEGORY_GENERAL = 'general';

    // New Media Manager Collections
    const COLLECTION_STUDENT_PUBLIC = 'student_public';
    const COLLECTION_FRONTEND_PUBLIC = 'frontend_public';
    const COLLECTION_ADMIN_LOCAL = 'admin_local';
    const COLLECTION_PROTECTED_LOCAL = 'protected_local';
    const COLLECTION_S3_ARCHIVE = 's3_archive';

    public function __construct($disk = null)
    {
        $this->disk = $disk ?: config('filesystems.default');
        $this->basePath = 'media';
    }

    /**
     * Get file URL based on disk type (NEW METHOD)
     */
    public function getFileUrl(MediaFile $file): string
    {
        switch ($file->disk) {
            case 'public':
                return asset('storage/' . $file->path);

            case 'local':
            case 'media_s3':
            case 's3':
                // For private disks, use our secure streaming route
                return route('media.stream', ['file' => $file->id]);

            default:
                throw new \InvalidArgumentException("Unsupported disk: {$file->disk}");
        }
    }

    /**
     * Enhanced upload method with MediaFile support
     */
    public function uploadFileToMediaManager(UploadedFile $file, string $disk, string $collection = null): MediaFile
    {
        $this->validateDiskAccess($disk, 'upload');

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = pathinfo($originalName, PATHINFO_FILENAME);
        $uniqueFilename = Str::slug($filename) . '_' . time() . '.' . $extension;

        // Store file
        $path = $file->storeAs($collection ?? 'uploads', $uniqueFilename, $disk);

        $mediaFile = MediaFile::create([
            'name' => $uniqueFilename,
            'original_name' => $originalName,
            'disk' => $disk,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'collection' => $collection,
            'user_id' => Auth::id(),
            'metadata' => $this->extractMetadata($file)
        ]);

        $this->auditAction('upload', $disk, $path);

        return $mediaFile;
    }

    /**
     * Archive file to S3
     */
    public function archiveToS3(MediaFile $file): MediaFile
    {
        $this->validateDiskAccess('media_s3', 'archive');

        DB::beginTransaction();
        try {
            // Get file content from current disk
            $content = Storage::disk($file->disk)->get($file->path);
            $s3Path = "archive/{$file->collection}/" . basename($file->path);

            // Store in S3
            Storage::disk('media_s3')->put($s3Path, $content);

            // Update file record
            $originalDisk = $file->disk;
            $originalPath = $file->path;

            $file->update([
                'disk' => 'media_s3',
                'path' => $s3Path
            ]);

            // Clean up original (only if not public disk)
            if ($originalDisk !== 'public') {
                Storage::disk($originalDisk)->delete($originalPath);
            }

            $this->auditAction('archive', 'media_s3', $s3Path);

            DB::commit();
            return $file;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Archive to S3 failed', [
                'file_id' => $file->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * List MediaFile records for disk
     */
    public function listMediaFiles(string $disk, string $path = ''): array
    {
        $this->validateDiskAccess($disk, 'view');

        $files = MediaFile::where('disk', $disk)
            ->when($path, function ($query, $path) {
                return $query->where('path', 'like', $path . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return $files->map(function ($file) {
            return [
                'id' => $file->id,
                'name' => $file->name,
                'original_name' => $file->original_name,
                'path' => $file->path,
                'mime_type' => $file->mime_type,
                'size' => $file->size,
                'formatted_size' => $file->formatted_size,
                'collection' => $file->collection,
                'url' => $file->url,
                'is_image' => $file->isImage(),
                'is_video' => $file->isVideo(),
                'is_audio' => $file->isAudio(),
                'is_pdf' => $file->isPdf(),
                'created_at' => $file->created_at,
            ];
        })->toArray();
    }

    /**
     * Get directory tree for disk
     */
    public function getDirectoryTree(string $disk): array
    {
        $this->validateDiskAccess($disk, 'view');

        $collections = MediaFile::where('disk', $disk)
            ->distinct()
            ->pluck('collection')
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        return [
            'disk' => $disk,
            'directories' => $collections
        ];
    }

    /**
     * Delete MediaFile
     */
    public function deleteMediaFile(MediaFile $file): bool
    {
        $this->validateDiskAccess($file->disk, 'delete');

        DB::beginTransaction();
        try {
            // Delete physical file
            Storage::disk($file->disk)->delete($file->path);

            // Delete database record
            $file->delete();

            $this->auditAction('delete', $file->disk, $file->path);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('File deletion failed', [
                'file_id' => $file->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

        /**
     * Validate user has access to disk for given action
     */
    private function validateDiskAccess(string $disk, string $action = 'view'): void
    {
        // Check admin guard first
        $user = Auth::guard('admin')->user();
        
        if (!$user) {
            // Fallback to regular auth for API usage
            $user = Auth::user();
        }
        
        if (!$user) {
            throw new UnauthorizedHttpException('', 'Authentication required');
        }

        // Get the user role
        $userRole = $this->getUserRole($user);

        // Check permissions
        if (!$this->hasPermission($userRole, $disk, $action)) {
            throw new UnauthorizedHttpException('', "No {$action} permission for disk: {$disk}");
        }
    }

    /**
     * Get user role (simplified for now)
     */
    private function getUserRole($user): string
    {
        // For admin guard users, always return admin
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->id === $user->id) {
            return 'admin';
        }

        // Check if user has a role relationship
        if (isset($user->role) && $user->role) {
            return strtolower($user->role->name ?? 'student');
        }

        // Check if user has role_id
        if (isset($user->role_id)) {
            switch ($user->role_id) {
                case 1:
                    return 'admin';
                case 2:
                    return 'staff';
                default:
                    return 'student';
            }
        }

        // Check if this is an admin model (common pattern)
        if (get_class($user) === 'App\\Models\\Admin' || str_contains(get_class($user), 'Admin')) {
            return 'admin';
        }

        // Default to student for regular users
        return 'student';
    }

    /**
     * Check if role has permission for disk and action (simplified)
     */
    private function hasPermission(string $role, string $disk, string $action): bool
    {
        // Basic permission matrix
        $permissions = [
            'admin' => [
                'public' => ['view', 'upload', 'delete', 'move', 'archive'],
                'local' => ['view', 'upload', 'delete', 'move', 'archive'],
                'media_s3' => ['view', 'upload', 'delete', 'move', 'archive'],
                's3' => ['view', 'upload', 'delete', 'move', 'archive'],
            ],
            'staff' => [
                'public' => ['view', 'upload'],
                'local' => ['view', 'upload', 'delete'],
                'media_s3' => ['view'],
                's3' => ['view'],
            ],
            'student' => [
                'public' => ['view', 'upload'],
                'local' => [],
                'media_s3' => [],
                's3' => [],
            ],
        ];

        return in_array($action, $permissions[$role][$disk] ?? []);
    }

    /**
     * Extract metadata from uploaded file
     */
    private function extractMetadata(UploadedFile $file): array
    {
        $metadata = [
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ];

        // Extract image dimensions if it's an image
        if (str_starts_with($file->getMimeType(), 'image/')) {
            try {
                $imageSize = getimagesize($file->getPathname());
                if ($imageSize) {
                    $metadata['width'] = $imageSize[0];
                    $metadata['height'] = $imageSize[1];
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return $metadata;
    }

    /**
     * Log audit action
     */
    private function auditAction(string $action, string $disk, string $path): void
    {
        DB::table('media_manager_audit_logs')->insert([
            'user_id' => Auth::id(),
            'action' => $action,
            'disk' => $disk,
            'file_path' => $path,
            'metadata' => json_encode([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]),
            'created_at' => now(),
        ]);
    }

    // ======= LEGACY METHODS (maintained for backward compatibility) =======

    /**
     * Upload file to specified category (LEGACY)
     */
    public function uploadFile(UploadedFile $file, string $category, string $filename = null): array
    {
        try {
            // Generate filename if not provided
            if (!$filename) {
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            }

            // Ensure filename is safe
            $filename = $this->sanitizeFilename($filename);

            // Store the file
            $storedPath = $file->storeAs(
                $this->basePath . '/' . $category,
                $filename,
                $this->disk
            );

            return [
                'success' => true,
                'path' => $storedPath,
                'url' => $this->getFileUrlLegacy($storedPath),
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'category' => $category,
                'disk' => $this->disk
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Upload student validation file (LEGACY)
     */
    public function uploadStudentValidation(UploadedFile $file, int $userId, string $type = 'validation'): array
    {
        $filename = "user_{$userId}_{$type}_" . time() . '.' . $file->getClientOriginalExtension();
        return $this->uploadFile($file, self::CATEGORY_STUDENT_VALIDATIONS, $filename);
    }

    /**
     * Upload student avatar (LEGACY)
     */
    public function uploadStudentAvatar(UploadedFile $file, int $userId): array
    {
        $filename = "avatar_{$userId}_" . time() . '.' . $file->getClientOriginalExtension();
        return $this->uploadFile($file, self::CATEGORY_STUDENT_AVATARS, $filename);
    }

    /**
     * Upload certificate PDF (LEGACY)
     */
    public function uploadCertificate(UploadedFile $file, int $userId, string $certificateType = 'completion'): array
    {
        $filename = "cert_{$userId}_{$certificateType}_" . time() . '.pdf';
        return $this->uploadFile($file, self::CATEGORY_CERTIFICATES, $filename);
    }

    /**
     * Get file URL (LEGACY)
     */
    public function getFileUrlLegacy(string $path): string
    {
        if ($this->disk === 's3') {
            return config('filesystems.disks.s3.url') . '/' . $path;
        }

        return asset('storage/' . str_replace('public/', '', $path));
    }

    /**
     * Delete file (LEGACY)
     */
    public function deleteFile(string $path): bool
    {
        try {
            return Storage::disk($this->disk)->delete($path);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * List files in category (LEGACY)
     */
    public function listFiles(string $category): array
    {
        $path = $this->basePath . '/' . $category;

        try {
            $files = Storage::disk($this->disk)->files($path);

            return array_map(function($file) use ($category) {
                return [
                    'path' => $file,
                    'url' => $this->getFileUrlLegacy($file),
                    'filename' => basename($file),
                    'size' => Storage::disk($this->disk)->size($file),
                    'last_modified' => Storage::disk($this->disk)->lastModified($file),
                    'category' => $category
                ];
            }, $files);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get file info (LEGACY)
     */
    public function getFileInfo(string $path): ?array
    {
        try {
            if (!Storage::disk($this->disk)->exists($path)) {
                return null;
            }

            return [
                'path' => $path,
                'url' => $this->getFileUrlLegacy($path),
                'filename' => basename($path),
                'size' => Storage::disk($this->disk)->size($path),
                'last_modified' => Storage::disk($this->disk)->lastModified($path),
                'exists' => true
            ];

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Move file to different category (LEGACY)
     */
    public function moveFile(string $currentPath, string $newCategory, string $newFilename = null): array
    {
        try {
            $filename = $newFilename ?: basename($currentPath);
            $newPath = $this->basePath . '/' . $newCategory . '/' . $filename;

            // Copy file to new location
            $content = Storage::disk($this->disk)->get($currentPath);
            Storage::disk($this->disk)->put($newPath, $content);

            // Delete old file
            Storage::disk($this->disk)->delete($currentPath);

            return [
                'success' => true,
                'old_path' => $currentPath,
                'new_path' => $newPath,
                'url' => $this->getFileUrlLegacy($newPath)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Switch storage disk
     */
    public function switchDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * Get available categories (LEGACY)
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_STUDENT_VALIDATIONS => 'Student Validations',
            self::CATEGORY_STUDENT_AVATARS => 'Student Avatars',
            self::CATEGORY_CERTIFICATES => 'Certificates',
            self::CATEGORY_DOCUMENTS => 'Documents',
            self::CATEGORY_IMAGES => 'Images',
            self::CATEGORY_GENERAL => 'General Files'
        ];
    }

    /**
     * Get available collections (NEW)
     */
    public static function getCollections(): array
    {
        return [
            self::COLLECTION_STUDENT_PUBLIC => 'Student Public',
            self::COLLECTION_FRONTEND_PUBLIC => 'Frontend Public',
            self::COLLECTION_ADMIN_LOCAL => 'Admin Local',
            self::COLLECTION_PROTECTED_LOCAL => 'Protected Local',
            self::COLLECTION_S3_ARCHIVE => 'S3 Archive'
        ];
    }

    /**
     * Sanitize filename
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove special characters and spaces
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Remove multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);

        return $filename;
    }

    /**
     * Create directory structure if it doesn't exist (LEGACY)
     */
    public function ensureDirectoryStructure(): void
    {
        $directories = [
            $this->basePath . '/' . self::CATEGORY_STUDENT_VALIDATIONS,
            $this->basePath . '/' . self::CATEGORY_STUDENT_AVATARS,
            $this->basePath . '/' . self::CATEGORY_CERTIFICATES,
            $this->basePath . '/' . self::CATEGORY_DOCUMENTS,
            $this->basePath . '/' . self::CATEGORY_IMAGES,
            $this->basePath . '/' . self::CATEGORY_GENERAL,
        ];

        foreach ($directories as $directory) {
            if (!Storage::disk($this->disk)->exists($directory)) {
                Storage::disk($this->disk)->makeDirectory($directory);
            }
        }
    }

    /**
     * Format file size in human readable format
     */
    public static function formatBytes(int $size, int $precision = 2): string
    {
        if ($size === 0) return '0 B';

        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    /**
     * Download a file
     */
    public function downloadFile(MediaFile $file)
    {
        $disk = Storage::disk($file->disk);
        
        if (!$disk->exists($file->path)) {
            throw new \Exception('File not found');
        }

        // Get file contents and create download response
        $contents = $disk->get($file->path);
        
        return response($contents)
            ->header('Content-Type', $file->mime_type)
            ->header('Content-Disposition', 'attachment; filename="' . ($file->original_name ?? $file->name) . '"');
    }

    /**
     * Create a new folder
     */
    public function createFolder(string $disk, string $path, string $name): array
    {
        $storage = Storage::disk($disk);
        $fullPath = rtrim($path, '/') . '/' . $name;
        
        if ($storage->exists($fullPath)) {
            throw new \Exception('Folder already exists');
        }

        $storage->makeDirectory($fullPath);

        return [
            'name' => $name,
            'path' => $fullPath,
            'type' => 'folder'
        ];
    }
}
