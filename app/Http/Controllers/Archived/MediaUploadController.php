<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MediaUploadController extends Controller
{
    private $publicDiskRules = [
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

    /**
     * Upload files to specified disk and folder
     */
    public function upload(Request $request)
    {
        try {
            // Validate basic request structure
            $request->validate([
                'disk' => 'required|string|in:public,local,s3',
                'folder' => 'required|string',
                'files' => 'required|array|min:1',
                'files.*' => 'required|file'
            ]);

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

            // Validate folder against public disk rules
            $validationRules = $this->getValidationRules($disk, $folder);
            
            // Validate files against folder rules
            $validator = Validator::make($request->all(), $validationRules);
            
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
                    $result = $this->processFileUpload($file, $disk, $folder);
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List files in specified disk and folder
     */
    public function listFiles(Request $request)
    {
        try {
            $request->validate([
                'disk' => 'required|string|in:public,local,s3',
                'folder' => 'string|nullable'
            ]);

            $disk = $request->input('disk');
            $folder = $request->input('folder', '');

            // Only implement public disk for now
            if ($disk !== 'public') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only public disk is currently supported'
                ], 422);
            }

            $path = $this->buildPath($disk, $folder);
            $files = $this->getFilesList($disk, $path);

            return response()->json([
                'success' => true,
                'files' => $files,
                'folder' => $folder,
                'disk' => $disk
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to list files: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a file
     */
    public function deleteFile(Request $request)
    {
        try {
            $request->validate([
                'disk' => 'required|string|in:public,local,s3',
                'path' => 'required|string'
            ]);

            $disk = $request->input('disk');
            $path = $request->input('path');

            // Only implement public disk for now
            if ($disk !== 'public') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only public disk is currently supported'
                ], 422);
            }

            $fullPath = $this->buildPath($disk, $path);

            if (!Storage::disk($disk)->exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            Storage::disk($disk)->delete($fullPath);

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process individual file upload
     */
    private function processFileUpload($file, $disk, $folder)
    {
        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $randomString = Str::random(6);
        $filename = pathinfo($originalName, PATHINFO_FILENAME) . "_{$timestamp}_{$randomString}.{$extension}";

        // Build storage path
        $storagePath = $this->buildPath($disk, $folder);
        
        // Store the file
        $filePath = $file->storeAs($storagePath, $filename, $disk);

        // Get file info
        $fileInfo = [
            'original_name' => $originalName,
            'filename' => $filename,
            'path' => $filePath,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'disk' => $disk,
            'folder' => $folder,
            'url' => $this->getFileUrl($disk, $filePath),
            'uploaded_at' => Carbon::now()->toISOString()
        ];

        return $fileInfo;
    }

    /**
     * Get validation rules for specific disk and folder
     */
    private function getValidationRules($disk, $folder)
    {
        if ($disk === 'public') {
            // Handle validations subfolder
            $ruleKey = $folder;
            if (Str::startsWith($folder, 'validations/')) {
                $ruleKey = 'validations';
            }

            $rules = $this->publicDiskRules[$ruleKey] ?? null;
            
            if (!$rules) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid folder specified: ' . $folder
                ], 422);
            }
            
            return [
                'files.*' => [
                    'required',
                    'file',
                    'max:' . $rules['max_size'],
                    'mimes:' . implode(',', $rules['extensions'])
                ]
            ];
        }

        return response()->json([
            'success' => false,
            'message' => 'Disk not supported: ' . $disk
        ], 422);
    }

    /**
     * Build storage path for disk and folder
     */
    private function buildPath($disk, $folder = '')
    {
        if ($disk === 'public') {
            $basePath = 'media';
            return $folder ? "{$basePath}/{$folder}" : $basePath;
        }

        return $folder;
    }

    /**
     * Get list of files in directory
     */
    private function getFilesList($disk, $path)
    {
        try {
            $files = Storage::disk($disk)->files($path);
            $filesList = [];

            foreach ($files as $filePath) {
                $filename = basename($filePath);
                
                // Skip hidden files
                if (Str::startsWith($filename, '.')) {
                    continue;
                }

                $fileInfo = [
                    'name' => $filename,
                    'path' => $filePath,
                    'size' => Storage::disk($disk)->size($filePath),
                    'modified' => Storage::disk($disk)->lastModified($filePath),
                    'url' => $this->getFileUrl($disk, $filePath),
                    'type' => $this->getFileType($filename)
                ];

                $filesList[] = $fileInfo;
            }

            // Sort by modified date (newest first)
            usort($filesList, function($a, $b) {
                return $b['modified'] - $a['modified'];
            });

            return $filesList;

        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get file URL for display
     */
    private function getFileUrl($disk, $filePath)
    {
        if ($disk === 'public') {
            return asset('storage/' . $filePath);
        }

        return null; // Private files need special handling
    }

    /**
     * Determine file type for frontend display
     */
    private function getFileType($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
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
}
