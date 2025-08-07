<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MediaFileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    protected MediaFileService $mediaFileService;

    public function __construct(MediaFileService $mediaFileService)
    {
        $this->mediaFileService = $mediaFileService;
    }
    /**
     * Get the configured storage disk for uploads
     */
    private function getUploadDisk(): string
    {
        return env('FILEPOND_USE_S3', false) ? env('FILEPOND_S3_DISK', 's3') : 'public';
    }

    /**
     * Get the base path for uploads
     */
    private function getUploadBasePath(): string
    {
        return env('FILEPOND_S3_PATH', 'uploads');
    }

    /**
     * Handle file upload with new simplified folder structure
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            // Check if this is the new media manager upload (with disk and folder parameters)
            if ($request->has(['disk', 'folder', 'files'])) {
                return $this->handleMediaManagerUpload($request);
            }

            // Fallback to original FilePond upload for backward compatibility
            return $this->handleFilePondUpload($request);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to maintain proper HTTP status codes
            throw $e;
        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle new media manager uploads with simplified folder structure
     *
     * @param Request $request
     * @return JsonResponse
     */
    private function handleMediaManagerUpload(Request $request): JsonResponse
    {
        // Define folder-specific validation rules
        $folderRules = [
            'images' => [
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'max_size' => 10240, // 10MB in KB
                'mime_types' => ['image/jpeg', 'image/png', 'image/gif']
            ],
            'documents' => [
                'extensions' => ['pdf', 'doc', 'docx'],
                'max_size' => 25600, // 25MB in KB
                'mime_types' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
            ],
            'assets' => [
                'extensions' => ['css', 'js', 'json'],
                'max_size' => 5120, // 5MB in KB
                'mime_types' => ['text/css', 'application/javascript', 'application/json']
            ],
            'validations' => [
                'extensions' => ['jpg', 'jpeg', 'png'],
                'max_size' => 8192, // 8MB in KB
                'mime_types' => ['image/jpeg', 'image/png']
            ]
        ];

        // Validate basic request structure with custom error handling
        try {
            $request->validate([
                'disk' => 'required|string|in:public,local,s3',
                'folder' => 'required|string',
                'files' => 'required|array|min:1',
                'files.*' => 'required|file'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        }

        $disk = $request->input('disk');
        $folder = $request->input('folder');
        $files = $request->file('files');

        // Only implement public disk for now
        if ($disk !== 'public') {
            return response()->json([
                'success' => false,
                'message' => 'Only public disk is currently supported'
            ], 422);
        }

        // Get validation rules for the folder
        $ruleKey = $folder;
        if (Str::startsWith($folder, 'validations/')) {
            $ruleKey = 'validations';
        }

        $rules = $folderRules[$ruleKey] ?? null;

        if (!$rules) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid folder specified: ' . $folder
            ], 422);
        }

        // Validate files against folder rules
        $validator = Validator::make($request->all(), [
            'files.*' => [
                'required',
                'file',
                'max:' . $rules['max_size'],
                'mimes:' . implode(',', $rules['extensions'])
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'File validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $uploadedFiles = [];
        $errors = [];

        foreach ($files as $index => $file) {
            try {
                $result = $this->processMediaManagerFile($file, $disk, $folder);
                $uploadedFiles[] = $result;
            } catch (\Exception $e) {
                $errors["file_{$index}"] = [$e->getMessage()];
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Some files failed to upload',
                'uploaded' => $uploadedFiles,
                'errors' => $errors
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
            'files' => $uploadedFiles
        ]);
    }

    /**
     * Handle original FilePond upload for backward compatibility
     *
     * @param Request $request
     * @return JsonResponse
     */
    private function handleFilePondUpload(Request $request): JsonResponse
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'max:51200', // 50MB in kilobytes
                'mimes:jpeg,jpg,png,gif,webp,pdf,doc,docx,txt,zip,rar,mp4,webm,avi,mov'
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        $file = $request->file('file');

        if (!$file || !$file->isValid()) {
            return response()->json([
                'error' => 'Invalid file upload'
            ], 422);
        }

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = pathinfo($originalName, PATHINFO_FILENAME);
        $uniqueFilename = Str::slug($filename) . '_' . time() . '.' . $extension;

        // Determine storage path based on file type
        $storagePath = $this->getStoragePath($file->getMimeType());

        // Get the storage disk (S3 or local)
        $disk = $this->getUploadDisk();

        // Store the file
        $path = $file->storeAs($storagePath, $uniqueFilename, $disk);

        if (!$path) {
            return response()->json([
                'error' => 'Failed to store file'
            ], 500);
        }

        // Get file URL
        $url = $this->generateFileUrl($path, $disk);

        // Create temporary upload record for FilePond
        $uploadData = [
            'id' => Str::uuid(),
            'filename' => $uniqueFilename,
            'original_name' => $originalName,
            'path' => $path,
            'disk' => $disk,
            'url' => $url,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_at' => now()->toISOString(),
            'temp' => true
        ];

        // Store temporary upload data in session
        $tempUploads = session('temp_uploads', []);
        $tempUploads[$uploadData['id']->toString()] = $uploadData;
        session(['temp_uploads' => $tempUploads]);

        // Return the temporary ID for FilePond
        return response()->json($uploadData['id'], 200, [
            'Content-Type' => 'text/plain'
        ]);
    }

    /**
     * Process individual file upload for media manager
     */
    private function processMediaManagerFile($file, $disk, $folder)
    {
        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $randomString = Str::random(6);
        $filename = pathinfo($originalName, PATHINFO_FILENAME) . "_{$timestamp}_{$randomString}.{$extension}";

        // Build storage path for new folder structure
        $storagePath = "media/{$folder}";

        // Store the file
        $filePath = $file->storeAs($storagePath, $filename, $disk);

        // Get file info
        $fileInfo = [
            'original_name' => $originalName,
            'filename' => $filename,
            'path' => $filePath,
            'size' => $file->getSize(),
            'size_formatted' => $this->formatBytes($file->getSize()),
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'disk' => $disk,
            'folder' => $folder,
            'url' => $this->getMediaFileUrl($disk, $filePath),
            'type' => $this->getFileTypeFromExtension($extension),
            'uploaded_at' => now()->toISOString()
        ];

        return $fileInfo;
    }

    /**
     * Get file URL for media manager files
     */
    private function getMediaFileUrl($disk, $filePath)
    {
        if ($disk === 'public') {
            return asset('storage/' . $filePath);
        }

        return null; // Private files need special handling
    }

    /**
     * Get file type from extension
     */
    private function getFileTypeFromExtension($extension)
    {
        $extension = strtolower($extension);

        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $documentTypes = ['pdf', 'doc', 'docx'];
        $assetTypes = ['css', 'js', 'json'];

        if (in_array($extension, $imageTypes)) {
            return 'image';
        } elseif (in_array($extension, $documentTypes)) {
            return 'document';
        } elseif (in_array($extension, $assetTypes)) {
            return 'asset';
        }

        return 'file';
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
     * Handle FilePond file revert (deletion)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function revert(Request $request): JsonResponse
    {
        try {
            $uploadId = $request->getContent();

            if (empty($uploadId)) {
                return response()->json(['error' => 'No upload ID provided'], 422);
            }

            // Get temporary upload data from session
            $tempUploads = session('temp_uploads', []);

            if (!isset($tempUploads[$uploadId])) {
                return response()->json(['error' => 'Upload not found'], 404);
            }

            $uploadData = $tempUploads[$uploadId];
            $disk = $uploadData['disk'] ?? $this->getUploadDisk();

            // Delete the file from storage
            if (Storage::disk($disk)->exists($uploadData['path'])) {
                Storage::disk($disk)->delete($uploadData['path']);
            }

            // Remove from session
            unset($tempUploads[$uploadId]);
            session(['temp_uploads' => $tempUploads]);

            return response()->json(['message' => 'File deleted successfully']);

        } catch (\Exception $e) {
            Log::error('FilePond revert error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Revert failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file information for a temporary upload
     *
     * @param Request $request
     * @param string $uploadId
     * @return JsonResponse
     */
    public function getUploadInfo(Request $request, string $uploadId): JsonResponse
    {
        $tempUploads = session('temp_uploads', []);

        if (!isset($tempUploads[$uploadId])) {
            return response()->json(['error' => 'Upload not found'], 404);
        }

        $uploadData = $tempUploads[$uploadId];
        $disk = $uploadData['disk'] ?? $this->getUploadDisk();

        return response()->json([
            'id' => $uploadData['id'],
            'filename' => $uploadData['filename'],
            'original_name' => $uploadData['original_name'],
            'mime_type' => $uploadData['mime_type'],
            'size' => $uploadData['size'],
            'size_human' => $this->formatBytes($uploadData['size']),
            'url' => $uploadData['url'] ?? $this->generateFileUrl($uploadData['path'], $disk),
            'uploaded_at' => $uploadData['uploaded_at']
        ]);
    }

    /**
     * Finalize uploaded files and optionally attach to a model
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function finalize(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'uploads' => 'required|array',
            'uploads.*' => 'string',
            'model_type' => 'nullable|string',
            'model_id' => 'nullable|integer',
            'collection' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        try {
            $uploadIds = $request->input('uploads');
            $tempUploads = session('temp_uploads', []);
            $finalizedFiles = [];

            foreach ($uploadIds as $uploadId) {
                if (!isset($tempUploads[$uploadId])) {
                    continue;
                }

                $uploadData = $tempUploads[$uploadId];
                $disk = $uploadData['disk'] ?? $this->getUploadDisk();

                // If model specified, attach to model using Media Library
                if ($request->filled(['model_type', 'model_id'])) {
                    $modelClass = $request->input('model_type');
                    $model = $modelClass::find($request->input('model_id'));

                    if ($model) {
                        $collection = $request->input('collection', 'default');
                        $mediaItem = $model
                            ->addMediaFromDisk($uploadData['path'], $disk)
                            ->usingName($uploadData['original_name'])
                            ->usingFileName($uploadData['filename'])
                            ->toMediaCollection($collection);

                        $finalizedFiles[] = [
                            'id' => $mediaItem->id,
                            'uuid' => $mediaItem->uuid,
                            'name' => $mediaItem->name,
                            'file_name' => $mediaItem->file_name,
                            'mime_type' => $mediaItem->mime_type,
                            'size' => $mediaItem->size,
                            'url' => $mediaItem->getUrl(),
                            'collection' => $mediaItem->collection_name
                        ];
                    }
                } else {
                    // Just mark as finalized without attaching to model
                    $finalizedFiles[] = [
                        'id' => $uploadData['id'],
                        'filename' => $uploadData['filename'],
                        'original_name' => $uploadData['original_name'],
                        'path' => $uploadData['path'],
                        'disk' => $disk,
                        'url' => $uploadData['url'] ?? $this->generateFileUrl($uploadData['path'], $disk)
                    ];
                }

                // Remove from temporary uploads
                unset($tempUploads[$uploadId]);
            }

            session(['temp_uploads' => $tempUploads]);

            return response()->json([
                'message' => 'Files finalized successfully',
                'files' => $finalizedFiles
            ]);

        } catch (\Exception $e) {
            Log::error('FilePond finalize error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Finalization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate file URL based on storage disk
     *
     * @param string $path
     * @param string $disk
     * @return string
     */
    private function generateFileUrl(string $path, string $disk): string
    {
        if ($disk === 's3' || str_starts_with($disk, 's3')) {
            // For S3, generate a public URL
            try {
                // Use the asset helper for consistency
                return asset('storage/' . $path);
            } catch (\Exception $e) {
                Log::warning('Failed to generate S3 URL for path: ' . $path, [
                    'error' => $e->getMessage()
                ]);
                return '';
            }
        }

        // For local storage
        return asset('storage/' . $path);
    }

        /**
     * Get storage path based on file mime type
     *
     * @param string $mimeType
     * @return string
     */
    private function getStoragePath(string $mimeType): string
    {
        $basePath = $this->getUploadBasePath();

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

        return $basePath . '/files';
    }

    /**
     * List files for a specific disk with role-based access control
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listFiles(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'disk' => 'required|string|in:public,local,s3',
            'path' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid parameters: ' . $validator->errors()->first()
            ], 422);
        }

        $disk = $request->input('disk');
        $path = $request->input('path', '/');

        // Ensure path is always a string, fallback to '/' if null or empty
        if (empty($path) || $path === null) {
            $path = '/';
        }

        $result = $this->mediaFileService->listFiles($disk, $path);

        if (!$result['success']) {
            $statusCode = isset($result['show_connection_screen']) ? 503 :
                         (str_contains($result['error'], 'Access denied') ? 403 : 500);
            return response()->json($result, $statusCode);
        }

        return response()->json($result);
    }

    /**
     * Create a new folder
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createFolder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'disk' => 'required|string|in:public,local,s3',
            'path' => 'nullable|string',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid parameters: ' . $validator->errors()->first()
            ], 422);
        }

        $result = $this->mediaFileService->createFolder(
            $request->input('disk'),
            $request->input('path', '/'),
            $request->input('name')
        );

        if (!$result['success']) {
            $statusCode = str_contains($result['error'], 'Access denied') ? 403 : 422;
            return response()->json($result, $statusCode);
        }

        return response()->json($result);
    }

    /**
     * Delete a file
     *
     * @param Request $request
     * @param string $file
     * @return JsonResponse
     */
    public function deleteFile(Request $request, string $file): JsonResponse
    {
        $validator = Validator::make(array_merge($request->all(), ['file' => $file]), [
            'disk' => 'required|string|in:public,local,s3',
            'file' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid parameters: ' . $validator->errors()->first()
            ], 422);
        }

        $result = $this->mediaFileService->deleteFile(
            $request->input('disk'),
            $file
        );

        if (!$result['success']) {
            $statusCode = str_contains($result['error'], 'Access denied') ? 403 : 404;
            return response()->json($result, $statusCode);
        }

        return response()->json($result);
    }

    /**
     * Placeholder methods for future implementation
     */
    public function getTree(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Tree view not yet implemented'
        ], 501);
    }

    public function archiveFile(Request $request, string $file): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Archive functionality not yet implemented'
        ], 501);
    }

    public function downloadFile(Request $request, string $file): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Download functionality not yet implemented'
        ], 501);
    }

    public function getFileDetails(Request $request, string $file): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'File details functionality not yet implemented'
        ], 501);
    }

    /**
     * Get disk status for all accessible disks
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDiskStatuses(Request $request): JsonResponse
    {
        $result = $this->mediaFileService->getAllDiskStatuses();

        // If result is an array with error key, it's an error response
        if (is_array($result) && isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 401);
        }

        return response()->json([
            'success' => true,
            'disk_statuses' => $result
        ]);
    }

    public function index(): \Illuminate\View\View
    {
        return view('admin.admin-center.media.index');
    }
}
