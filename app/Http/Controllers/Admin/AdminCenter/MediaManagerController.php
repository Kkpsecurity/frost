<?php

namespace App\Http\Controllers\Admin\AdminCenter;

use App\Http\Controllers\Controller;
use App\Services\MediaManagerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaManagerController extends Controller
{
    protected $mediaManager;

    public function __construct(MediaManagerService $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    /**
     * Display the media manager interface
     */
    public function index(Request $request)
    {
        $categories = MediaManagerService::getCategories();
        $currentDisk = $request->get('disk', config('filesystems.default'));

        // Switch to requested disk
        $this->mediaManager->switchDisk($currentDisk);

        // Ensure directory structure exists
        $this->mediaManager->ensureDirectoryStructure();

        // Get file statistics
        $stats = $this->getStorageStats();

        return view('admin.admin-center.media.index', [
            'categories' => $categories,
            'currentDisk' => $currentDisk,
            'availableDisks' => $this->getAvailableDisks(),
            'stats' => $stats
        ]);
    }

    /**
     * Upload file via AJAX
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:25600', // 25MB max
            'category' => 'required|string',
            'disk' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $disk = $request->get('disk', config('filesystems.default'));
        $this->mediaManager->switchDisk($disk);

        $result = $this->mediaManager->uploadFile(
            $request->file('file'),
            $request->get('category')
        );

        return response()->json($result);
    }

    /**
     * Upload student validation file
     */
    public function uploadValidation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max
            'user_id' => 'required|integer',
            'type' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->mediaManager->uploadStudentValidation(
            $request->file('file'),
            $request->get('user_id'),
            $request->get('type', 'validation')
        );

        return response()->json($result);
    }

    /**
     * Upload student avatar
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpg,jpeg,png|max:5120', // 5MB max
            'user_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->mediaManager->uploadStudentAvatar(
            $request->file('file'),
            $request->get('user_id')
        );

        return response()->json($result);
    }

    /**
     * List files in category
     */
    public function listFiles(Request $request): JsonResponse
    {
        $category = $request->get('category');
        $disk = $request->get('disk', config('filesystems.default'));

        $this->mediaManager->switchDisk($disk);

        $files = $this->mediaManager->listFiles($category);

        return response()->json([
            'success' => true,
            'files' => $files,
            'category' => $category,
            'disk' => $disk
        ]);
    }

    /**
     * Delete file
     */
    public function deleteFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
            'disk' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $disk = $request->get('disk', config('filesystems.default'));
        $this->mediaManager->switchDisk($disk);

        $success = $this->mediaManager->deleteFile($request->get('path'));

        return response()->json([
            'success' => $success,
            'message' => $success ? 'File deleted successfully' : 'Failed to delete file'
        ]);
    }

    /**
     * Delete multiple files (bulk deletion)
     */
    public function deleteFiles(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file_ids' => 'required|array',
            'file_ids.*' => 'string',
            'disk' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $disk = $request->get('disk', config('filesystems.default'));
        $this->mediaManager->switchDisk($disk);

        $deleted = 0;
        $failed = 0;

        foreach ($request->get('file_ids') as $fileId) {
            // Assuming file_ids are actually file paths for now
            if ($this->mediaManager->deleteFile($fileId)) {
                $deleted++;
            } else {
                $failed++;
            }
        }

        return response()->json([
            'success' => $deleted > 0,
            'deleted' => $deleted,
            'failed' => $failed,
            'message' => "Deleted {$deleted} file(s). Failed: {$failed}"
        ]);
    }

    /**
     * Update storage disk setting
     */
    public function updateDisk(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'disk' => 'required|string|in:public,s3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update session or user preference
        session(['media_manager_disk' => $request->get('disk')]);

        return response()->json([
            'success' => true,
            'message' => 'Storage disk updated successfully',
            'disk' => $request->get('disk')
        ]);
    }

    /**
     * Get storage statistics (AJAX endpoint)
     */
    public function getStats(Request $request): JsonResponse
    {
        $disk = $request->get('disk', config('filesystems.default'));
        $this->mediaManager->switchDisk($disk);

        $stats = $this->getStorageStats();

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Move file to different category
     */
    public function moveFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_path' => 'required|string',
            'new_category' => 'required|string',
            'new_filename' => 'sometimes|string',
            'disk' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $disk = $request->get('disk', config('filesystems.default'));
        $this->mediaManager->switchDisk($disk);

        $result = $this->mediaManager->moveFile(
            $request->get('current_path'),
            $request->get('new_category'),
            $request->get('new_filename')
        );

        return response()->json($result);
    }

    /**
     * Get file info
     */
    public function fileInfo(Request $request): JsonResponse
    {
        $path = $request->get('path');
        $disk = $request->get('disk', config('filesystems.default'));

        $this->mediaManager->switchDisk($disk);

        $info = $this->mediaManager->getFileInfo($path);

        return response()->json([
            'success' => $info !== null,
            'file' => $info
        ]);
    }

    /**
     * Get storage statistics
     */
    private function getStorageStats(): array
    {
        $stats = [];
        $categories = MediaManagerService::getCategories();

        foreach ($categories as $key => $name) {
            $files = $this->mediaManager->listFiles($key);
            $stats[$key] = [
                'name' => $name,
                'file_count' => count($files),
                'total_size' => array_sum(array_column($files, 'size'))
            ];
        }

        return $stats;
    }

    /**
     * Get available storage disks
     */
    private function getAvailableDisks(): array
    {
        return [
            'public' => 'Local Public Storage',
            's3' => 'Amazon S3 Storage'
        ];
    }

    /**
     * Migrate files from old structure to new structure
     */
    public function migrateFiles(Request $request): JsonResponse
    {
        try {
            $migrated = [];

            // Migrate validation files
            if (Storage::disk('public')->exists('validations')) {
                $oldFiles = Storage::disk('public')->files('validations');
                foreach ($oldFiles as $file) {
                    $newPath = 'media/students/validations/' . basename($file);
                    Storage::disk('public')->move($file, $newPath);
                    $migrated[] = $file . ' -> ' . $newPath;
                }
            }

            // Migrate avatar files
            if (Storage::disk('public')->exists('avatars')) {
                $oldFiles = Storage::disk('public')->files('avatars');
                foreach ($oldFiles as $file) {
                    $newPath = 'media/students/avatars/' . basename($file);
                    Storage::disk('public')->move($file, $newPath);
                    $migrated[] = $file . ' -> ' . $newPath;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Files migrated successfully',
                'migrated_files' => $migrated,
                'count' => count($migrated)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
