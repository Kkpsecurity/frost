<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use App\Support\RoleManager;

class MediaFileService
{
    /**
     * List files for a specific disk with role-based access control
     *
     * @param string $disk
     * @param string $path
     * @return array
     */
    public function listFiles(string $disk, string $path = '/'): array
    {
        try {
            // Check access permissions based on user role
            if (!$this->canAccessDisk($disk)) {
                return [
                    'success' => false,
                    'error' => 'Access denied to this storage disk'
                ];
            }

            // Check disk connectivity status
            $diskStatus = $this->checkDiskStatus($disk);
            if (!$diskStatus['connected']) {
                return [
                    'success' => false,
                    'error' => $diskStatus['message'],
                    'disk_status' => $diskStatus,
                    'show_connection_screen' => true
                ];
            }

            // Map disk names to Laravel storage disks
            $storageDisk = $this->mapDiskName($disk);

            // For public disk, default to media folder structure if at root
            if ($disk === 'public' && ($path === '/' || $path === '')) {
                $path = '/media';
            }

            // Sanitize path
            $path = $this->sanitizePath($path);

            // Get files from the disk
            $files = $this->getFilesFromDisk($storageDisk, $path);
            $directories = $this->getDirectoriesFromDisk($storageDisk, $path);

            return [
                'success' => true,
                'files' => $files,
                'directories' => $directories,
                'current_path' => $path,
                'disk' => $disk,
                'disk_status' => $diskStatus
            ];

        } catch (\Exception $e) {
            Log::error('Media file listing error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Failed to load files'
            ];
        }
    }

    /**
     * Upload a file to specified disk
     *
     * @param UploadedFile $file
     * @param string $disk
     * @param string $path
     * @return array
     */
    public function uploadFile(UploadedFile $file, string $disk = 'public', string $path = ''): array
    {
        try {
            if (!$file->isValid()) {
                return [
                    'success' => false,
                    'error' => 'Invalid file upload'
                ];
            }

            // Check access permissions
            if (!$this->canAccessDisk($disk)) {
                return [
                    'success' => false,
                    'error' => 'Access denied to this storage disk'
                ];
            }

            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = pathinfo($originalName, PATHINFO_FILENAME);
            $uniqueFilename = Str::slug($filename) . '_' . time() . '.' . $extension;

            // Determine storage path based on file type
            $storagePath = $this->getStoragePath($file->getMimeType(), $path);

            // Map to Laravel storage disk
            $storageDisk = $this->mapDiskName($disk);

            // Store the file
            $filePath = $file->storeAs($storagePath, $uniqueFilename, $storageDisk);

            if (!$filePath) {
                return [
                    'success' => false,
                    'error' => 'Failed to store file'
                ];
            }

            // Get file URL
            $url = $this->getFileUrl(Storage::disk($storageDisk), $filePath, $storageDisk);

            return [
                'success' => true,
                'file' => [
                    'id' => Str::uuid(),
                    'filename' => $uniqueFilename,
                    'original_name' => $originalName,
                    'path' => $filePath,
                    'disk' => $disk,
                    'url' => $url,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'size_formatted' => $this->formatBytes($file->getSize()),
                    'uploaded_at' => now()->format('c')
                ]
            ];

        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a file from specified disk
     *
     * @param string $disk
     * @param string $filePath
     * @return array
     */
    public function deleteFile(string $disk, string $filePath): array
    {
        try {
            // Check access permissions
            if (!$this->canAccessDisk($disk)) {
                return [
                    'success' => false,
                    'error' => 'Access denied to this storage disk'
                ];
            }

            // Map to Laravel storage disk
            $storageDisk = $this->mapDiskName($disk);
            $storage = Storage::disk($storageDisk);

            if (!$storage->exists($filePath)) {
                return [
                    'success' => false,
                    'error' => 'File not found'
                ];
            }

            $storage->delete($filePath);

            return [
                'success' => true,
                'message' => 'File deleted successfully'
            ];

        } catch (\Exception $e) {
            Log::error('File deletion error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Failed to delete file: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check the connection status of a storage disk
     *
     * @param string $disk
     * @return array
     */
    public function checkDiskStatus(string $disk): array
    {
        try {
            $storageDisk = $this->mapDiskName($disk);
            $storage = Storage::disk($storageDisk);

            switch ($disk) {
                case 'public':
                    // Check if public disk is accessible
                    try {
                        $storage->files('/');
                        return [
                            'connected' => true,
                            'message' => 'Public storage is connected',
                            'disk' => $disk,
                            'type' => 'local'
                        ];
                    } catch (\Exception $e) {
                        return [
                            'connected' => false,
                            'message' => 'Public storage is not accessible',
                            'disk' => $disk,
                            'type' => 'local',
                            'error' => $e->getMessage()
                        ];
                    }

                case 'local':
                    // Check if local private disk is accessible
                    try {
                        $storage->files('/');
                        return [
                            'connected' => true,
                            'message' => 'Private storage is connected',
                            'disk' => $disk,
                            'type' => 'local'
                        ];
                    } catch (\Exception $e) {
                        return [
                            'connected' => false,
                            'message' => 'Private storage is not accessible',
                            'disk' => $disk,
                            'type' => 'local',
                            'error' => $e->getMessage()
                        ];
                    }

                case 's3':
                    // Check S3 connection and configuration
                    try {
                        // Test S3 connection by attempting to list objects
                        $storage->files('/');

                        return [
                            'connected' => true,
                            'message' => 'S3 storage is connected',
                            'disk' => $disk,
                            'type' => 's3',
                            'bucket' => config("filesystems.disks.{$storageDisk}.bucket", 'Unknown'),
                            'region' => config("filesystems.disks.{$storageDisk}.region", 'Unknown')
                        ];
                    } catch (\Exception $e) {
                        return [
                            'connected' => false,
                            'message' => 'S3 storage is not connected',
                            'disk' => $disk,
                            'type' => 's3',
                            'error' => $e->getMessage(),
                            'config_status' => $this->checkS3Configuration($storageDisk)
                        ];
                    }

                default:
                    return [
                        'connected' => false,
                        'message' => 'Unknown disk type',
                        'disk' => $disk,
                        'type' => 'unknown'
                    ];
            }
        } catch (\Exception $e) {
            return [
                'connected' => false,
                'message' => 'Failed to check disk status: ' . $e->getMessage(),
                'disk' => $disk,
                'type' => 'unknown',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get disk status for all accessible disks
     *
     * @return array
     */
    public function getAllDiskStatuses(): array
    {
        $user = auth('admin')->user();
        if (!$user) {
            return ['error' => 'Unauthorized'];
        }

        $statuses = [];
        $disks = ['public'];

        // Add accessible disks based on user role
        if ($user->IsInstructor()) {
            $disks[] = 'local';
        }

        if ($user->IsAdministrator()) {
            $disks[] = 's3';
        }

        foreach ($disks as $disk) {
            $statuses[$disk] = $this->checkDiskStatus($disk);
        }

        return $statuses;
    }

    /**
     * Create a new folder in the specified disk
     *
     * @param string $disk
     * @param string $path
     * @param string $folderName
     * @return array
     */
    public function createFolder(string $disk, string $path, string $folderName): array
    {
        try {
            // Check access permissions
            if (!$this->canAccessDisk($disk)) {
                return [
                    'success' => false,
                    'error' => 'Access denied to this storage disk'
                ];
            }

            // Sanitize inputs
            $path = $this->sanitizePath($path);
            $folderName = $this->sanitizeFolderName($folderName);

            if (empty($folderName)) {
                return [
                    'success' => false,
                    'error' => 'Invalid folder name'
                ];
            }

            // Map to Laravel storage disk
            $storageDisk = $this->mapDiskName($disk);
            $storage = Storage::disk($storageDisk);

            // Create full folder path
            $folderPath = trim($path, '/') . '/' . $folderName;
            $folderPath = trim($folderPath, '/');

            // Check if folder already exists
            if ($storage->exists($folderPath)) {
                return [
                    'success' => false,
                    'error' => 'Folder already exists'
                ];
            }

            // Create the folder by creating a placeholder file
            $placeholderFile = $folderPath . '/.placeholder';
            $storage->put($placeholderFile, '');

            return [
                'success' => true,
                'message' => 'Folder created successfully',
                'folder' => [
                    'name' => $folderName,
                    'path' => $folderPath,
                    'type' => 'folder'
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Folder creation error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'Failed to create folder: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if current user can access the specified disk
     *
     * @param string $disk
     * @return bool
     */
    private function canAccessDisk(string $disk): bool
    {
        $user = auth('admin')->user();
        if (!$user) {
            return false;
        }

        switch ($disk) {
            case 'public':
                // All authenticated admin users can access public disk
                return $user->IsAnyAdmin();

            case 'local':
                // Private disk: Admin roles (1-4) have access
                // Using IsInstructor() which returns true for Instructor and higher roles
                return $user->IsInstructor();

            case 's3':
                // Archive S3: Only System Admin and Admin have access
                // Using IsAdministrator() which returns true for Admin and SysAdmin
                return $user->IsAdministrator();

            default:
                return false;
        }
    }

    /**
     * Map frontend disk names to Laravel storage disk names
     *
     * @param string $disk
     * @return string
     */
    private function mapDiskName(string $disk): string
    {
        $mapping = [
            'public' => 'public',
            'local' => 'local',
            's3' => 'media_s3'
        ];

        return $mapping[$disk] ?? 'public';
    }

    /**
     * Sanitize file path to prevent directory traversal
     *
     * @param string $path
     * @return string
     */
    private function sanitizePath(string $path): string
    {
        // Remove any ../ or .\ patterns
        $path = preg_replace('/\.\.\//', '', $path);
        $path = preg_replace('/\.\.\\\\/', '', $path);

        // Ensure path starts with /
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        // Remove double slashes
        $path = preg_replace('/\/+/', '/', $path);

        return $path;
    }

    /**
     * Sanitize folder name
     *
     * @param string $folderName
     * @return string
     */
    private function sanitizeFolderName(string $folderName): string
    {
        // Remove invalid characters
        $folderName = preg_replace('/[^a-zA-Z0-9\-_\s]/', '', $folderName);
        $folderName = trim($folderName);
        $folderName = preg_replace('/\s+/', '_', $folderName);

        return $folderName;
    }

    /**
     * Get storage path based on file mime type
     *
     * @param string $mimeType
     * @param string $customPath
     * @return string
     */
    private function getStoragePath(string $mimeType, string $customPath = ''): string
    {
        if (!empty($customPath)) {
            return trim($customPath, '/');
        }

        // Use new simplified media folder structure
        $basePath = 'media';

        if (str_starts_with($mimeType, 'image/')) {
            return $basePath . '/images';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return $basePath . '/videos';
        }

        if (in_array($mimeType, ['application/pdf'])) {
            return $basePath . '/documents';
        }

        if (in_array($mimeType, [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-zip-compressed'
        ])) {
            return $basePath . '/archives';
        }

        // For CSS, JS, JSON files
        if (
            in_array($mimeType, [
                'text/css',
                'application/javascript',
                'application/json'
            ])
        ) {
            return $basePath . '/assets';
        }

        return $basePath . '/files';
    }

    /**
     * Get files from specified disk and path
     *
     * @param string $disk
     * @param string $path
     * @return array
     */
    private function getFilesFromDisk(string $disk, string $path): array
    {
        try {
            $storage = Storage::disk($disk);
            $basePath = trim($path, '/');

            // Get all files in the directory (not recursive)
            $files = $storage->files($basePath);

            $fileList = [];

            foreach ($files as $file) {
                $filename = basename($file);

                // Skip hidden files and thumbnails
                if (str_starts_with($filename, '.') || str_starts_with($filename, 'thumb_')) {
                    continue;
                }

                $fileInfo = [
                    'name' => $filename,
                    'path' => $file,
                    'url' => $this->getFileUrl($storage, $file, $disk),
                    'size' => $storage->size($file),
                    'size_formatted' => $this->formatBytes($storage->size($file)),
                    'modified' => $storage->lastModified($file),
                    'modified_formatted' => date('M j, Y g:i A', $storage->lastModified($file)),
                    'type' => $this->getFileType($filename),
                    'mime_type' => $this->getFileMimeType($storage, $file),
                    'is_image' => $this->isImageFile($filename),
                ];

                $fileList[] = $fileInfo;
            }

            // Sort files by name
            usort($fileList, function($a, $b) {
                return strcasecmp($a['name'], $b['name']);
            });

            return $fileList;

        } catch (\Exception $e) {
            Log::error("Failed to get files from disk {$disk}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get directories from specified disk and path
     *
     * @param string $disk
     * @param string $path
     * @return array
     */
    private function getDirectoriesFromDisk(string $disk, string $path): array
    {
        try {
            $storage = Storage::disk($disk);
            $basePath = trim($path, '/');

            // Get all directories in the current path
            $directories = $storage->directories($basePath);

            $dirList = [];

            foreach ($directories as $directory) {
                $dirName = basename($directory);

                // Skip hidden directories
                if (str_starts_with($dirName, '.')) {
                    continue;
                }

                $dirList[] = [
                    'name' => $dirName,
                    'path' => $directory,
                    'type' => 'folder',
                ];
            }

            // Sort directories by name
            usort($dirList, function($a, $b) {
                return strcasecmp($a['name'], $b['name']);
            });

            return $dirList;

        } catch (\Exception $e) {
            Log::error("Failed to get directories from disk {$disk}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Determine file type based on extension
     *
     * @param string $filename
     * @return string
     */
    private function getFileType(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $types = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'],
            'document' => ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt'],
            'video' => ['mp4', 'webm', 'avi', 'mov', 'wmv', 'flv', 'mkv'],
            'audio' => ['mp3', 'wav', 'ogg', 'aac', 'm4a'],
            'archive' => ['zip', 'rar', '7z', 'tar', 'gz'],
            'spreadsheet' => ['xls', 'xlsx', 'csv', 'ods'],
            'presentation' => ['ppt', 'pptx', 'odp'],
        ];

        foreach ($types as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                return $type;
            }
        }

        return 'file';
    }

    /**
     * Get file URL safely across different storage drivers
     *
     * @param mixed $storage
     * @param string $file
     * @param string $diskName
     * @return string
     */
    private function getFileUrl($storage, string $file, string $diskName): string
    {
        // For public disk, use asset URL
        if ($diskName === 'public') {
            return asset('storage/' . $file);
        }

        // For other disks, use download endpoint for now
        // We'll implement direct URLs later when we set up proper S3/cloud storage
        return '/admin/media-manager/download?disk=' . $diskName . '&file=' . urlencode($file);
    }

    /**
     * Get file MIME type safely
     *
     * @param mixed $storage
     * @param string $file
     * @return string
     */
    private function getFileMimeType($storage, string $file): string
    {
        try {
            // Try getting MIME type from storage
            $content = $storage->get($file);
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            return $finfo->buffer($content);
        } catch (\Exception $e) {
            // Fallback to extension-based MIME type detection
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
                'zip' => 'application/zip',
                'txt' => 'text/plain',
            ];

            return $mimeTypes[$extension] ?? 'application/octet-stream';
        }
    }

    /**
     * Check if file is an image
     *
     * @param string $filename
     * @return bool
     */
    private function isImageFile(string $filename): bool
    {
        return $this->getFileType($filename) === 'image';
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Check S3 configuration status
     *
     * @param string $storageDisk
     * @return array
     */
    private function checkS3Configuration(string $storageDisk): array
    {
        $config = config("filesystems.disks.{$storageDisk}", []);

        $requiredFields = ['key', 'secret', 'region', 'bucket'];
        $missingFields = [];
        $configuredFields = [];

        foreach ($requiredFields as $field) {
            if (empty($config[$field])) {
                $missingFields[] = $field;
            } else {
                $configuredFields[] = $field;
            }
        }

        return [
            'configured_fields' => $configuredFields,
            'missing_fields' => $missingFields,
            'is_complete' => empty($missingFields),
            'bucket' => $config['bucket'] ?? null,
            'region' => $config['region'] ?? null,
            'endpoint' => $config['endpoint'] ?? null
        ];
    }
}
