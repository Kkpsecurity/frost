<?php

/**
 * Admin Media Manager Routes
 * Routes for media management functionality including file uploads, galleries, etc.
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaUploadController;

// Media Manager Routes
Route::prefix('media-manager')
    ->name('media-manager.')
    ->group(function () {
        
        // Media Manager Dashboard/Index
        Route::get('/', [
            MediaController::class,
            'index'
        ])->name('index');
        
        // Media Gallery View
        Route::get('/gallery', [
            MediaController::class,
            'gallery'
        ])->name('gallery');
        
        // Media Library Browse
        Route::get('/browse', [
            MediaController::class,
            'browse'
        ])->name('browse');
        
        // Media File Show/Preview
        Route::get('/file/{media}', [
            MediaController::class,
            'show'
        ])->name('file.show');
        
        // Media File Edit
        Route::get('/file/{media}/edit', [
            MediaController::class,
            'edit'
        ])->name('file.edit');
        
        // Media File Update
        Route::put('/file/{media}', [
            MediaController::class,
            'update'
        ])->name('file.update');
        
        // Media File Delete
        Route::delete('/file/{media}', [
            MediaController::class,
            'destroy'
        ])->name('file.destroy');
        
        // Media Upload Routes
        Route::prefix('upload')
            ->name('upload.')
            ->group(function () {
                
                // Show Upload Form
                Route::get('/', [
                    MediaUploadController::class,
                    'index'
                ])->name('index');
                
                // Process File Upload
                Route::post('/', [
                    MediaUploadController::class,
                    'upload'
                ])->name('store');
                
                // Chunk Upload (for large files)
                Route::post('/chunk', [
                    MediaUploadController::class,
                    'uploadChunk'
                ])->name('chunk');
                
                // Upload Complete
                Route::post('/complete', [
                    MediaUploadController::class,
                    'uploadComplete'
                ])->name('complete');
                
                // Revert Upload (FilePond)
                Route::delete('/revert', [
                    MediaUploadController::class,
                    'revert'
                ])->name('revert');
                
                // Get Upload Info
                Route::get('/{uploadId}', [
                    MediaUploadController::class,
                    'getUploadInfo'
                ])->name('info');
                
                // Finalize Upload
                Route::post('/finalize', [
                    MediaUploadController::class,
                    'finalize'
                ])->name('finalize');
                
            });
            
        // Media Folders Management
        Route::prefix('folders')
            ->name('folders.')
            ->group(function () {
                
                // List Folders
                Route::get('/', [
                    MediaController::class,
                    'folders'
                ])->name('index');
                
                // Create Folder
                Route::post('/', [
                    MediaController::class,
                    'createFolder'
                ])->name('store');
                
                // Rename Folder
                Route::put('/{folder}', [
                    MediaController::class,
                    'renameFolder'
                ])->name('update');
                
                // Delete Folder
                Route::delete('/{folder}', [
                    MediaController::class,
                    'deleteFolder'
                ])->name('destroy');
                
            });
            
    });
