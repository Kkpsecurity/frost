<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use App\Services\MediaManagerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MediaManagerController extends Controller
{
    public function __construct(
        private MediaManagerService $mediaManagerService
    ) {}

    /**
     * Display the media manager interface
     */
    public function index()
    {
        return view('admin.media-manager.index');
    }

    /**
     * Upload file to media manager
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:51200', // 50MB
            'disk' => 'required|string|in:public,local,media_s3',
            'collection' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mediaFile = $this->mediaManagerService->uploadFileToMediaManager(
                $request->file('file'),
                $request->input('disk'),
                $request->input('collection')
            );

            return response()->json([
                'success' => true,
                'file' => [
                    'id' => $mediaFile->id,
                    'name' => $mediaFile->name,
                    'original_name' => $mediaFile->original_name,
                    'path' => $mediaFile->path,
                    'url' => $mediaFile->url,
                    'size' => $mediaFile->formatted_size,
                    'mime_type' => $mediaFile->mime_type,
                    'collection' => $mediaFile->collection,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List files for a specific disk
     */
    public function listFiles(Request $request): JsonResponse
    {
        $disk = $request->input('disk', 'public');
        $path = $request->input('path', '');

        try {
            $files = $this->mediaManagerService->listMediaFiles($disk, $path);

            return response()->json([
                'success' => true,
                'files' => $files,
                'disk' => $disk,
                'path' => $path
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get directory tree for disk
     */
    public function getTree(Request $request): JsonResponse
    {
        $disk = $request->input('disk', 'public');

        try {
            $tree = $this->mediaManagerService->getDirectoryTree($disk);

            return response()->json([
                'success' => true,
                'tree' => $tree
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file
     */
    public function deleteFile(Request $request, MediaFile $file): JsonResponse
    {
        try {
            $success = $this->mediaManagerService->deleteMediaFile($file);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to delete file'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive file to S3
     */
    public function archiveFile(Request $request, MediaFile $file): JsonResponse
    {
        try {
            $archivedFile = $this->mediaManagerService->archiveToS3($file);

            return response()->json([
                'success' => true,
                'message' => 'File archived to S3 successfully',
                'file' => [
                    'id' => $archivedFile->id,
                    'disk' => $archivedFile->disk,
                    'path' => $archivedFile->path,
                    'url' => $archivedFile->url
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file details
     */
    public function getFileDetails(MediaFile $file): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'file' => [
                    'id' => $file->id,
                    'name' => $file->name,
                    'original_name' => $file->original_name,
                    'path' => $file->path,
                    'disk' => $file->disk,
                    'mime_type' => $file->mime_type,
                    'size' => $file->size,
                    'formatted_size' => $file->formatted_size,
                    'collection' => $file->collection,
                    'url' => $file->url,
                    'metadata' => $file->metadata,
                    'created_at' => $file->created_at,
                    'updated_at' => $file->updated_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a file
     */
    public function downloadFile(MediaFile $file)
    {
        try {
            return $this->mediaManagerService->downloadFile($file);
        } catch (\Exception $e) {
            abort(404, 'File not found');
        }
    }

    /**
     * Create a new folder
     */
    public function createFolder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'disk' => 'required|string|in:public,local,media_s3',
            'path' => 'required|string',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->mediaManagerService->createFolder(
                $request->input('disk'),
                $request->input('path'),
                $request->input('name')
            );

            return response()->json([
                'success' => true,
                'folder' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
