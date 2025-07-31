<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MediaController extends Controller
{
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
     * Handle FilePond file upload
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        try {
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
            $url = Storage::disk($disk)->url($path);

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
            $tempUploads[$uploadData['id']] = $uploadData;
            session(['temp_uploads' => $tempUploads]);

            // Return the temporary ID for FilePond
            return response()->json($uploadData['id'], 200, [
                'Content-Type' => 'text/plain'
            ]);

        } catch (\Exception $e) {
            Log::error('FilePond upload error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
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
            'url' => $uploadData['url'] ?? Storage::disk($disk)->url($uploadData['path']),
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
                return Storage::disk($disk)->url($path);
            } catch (\Exception $e) {
                Log::warning('Failed to generate S3 URL for path: ' . $path, [
                    'error' => $e->getMessage()
                ]);
                return '';
            }
        }

        // For local storage
        return Storage::disk($disk)->url($path);
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
}
