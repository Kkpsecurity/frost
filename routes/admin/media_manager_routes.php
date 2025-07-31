<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCenter\MediaManagerController;
use App\Http\Controllers\MediaStreamController;
use App\Http\Controllers\MediaManagerController as NewMediaManagerController;

// Admin Center - Media Manager routes (existing)
Route::prefix('admin-center/media')->name('media-manager.')->group(function () {
    Route::get('/', [MediaManagerController::class, 'index'])->name('index');
});

// AJAX API routes for Media Manager - these will have admin.media.* names (existing)
Route::prefix('media')->name('media.')->group(function () {
    Route::post('/upload', [MediaManagerController::class, 'upload'])->name('upload');
    Route::get('/files', [MediaManagerController::class, 'listFiles'])->name('files');
    Route::delete('/delete', [MediaManagerController::class, 'deleteFiles'])->name('delete');
    Route::post('/migrate', [MediaManagerController::class, 'migrateFiles'])->name('migrate');
    Route::post('/update-disk', [MediaManagerController::class, 'updateDisk'])->name('update-disk');
    Route::get('/stats', [MediaManagerController::class, 'getStats'])->name('stats');
});

// NEW Media Manager with Role-Based Disks + Unified Media Player
Route::prefix('media-manager')->name('new-media.')->group(function () {
    Route::get('/', [NewMediaManagerController::class, 'index'])->name('index');
    Route::post('/upload', [NewMediaManagerController::class, 'upload'])->name('upload');
    Route::get('/files', [NewMediaManagerController::class, 'listFiles'])->name('files');
    Route::get('/tree', [NewMediaManagerController::class, 'getTree'])->name('tree');
    Route::delete('/delete/{file}', [NewMediaManagerController::class, 'deleteFile'])->name('delete');
    Route::delete('/file/{file}', [NewMediaManagerController::class, 'deleteFile'])->name('delete-file');
    Route::post('/archive/{file}', [NewMediaManagerController::class, 'archiveFile'])->name('archive');
    Route::get('/download/{file}', [NewMediaManagerController::class, 'downloadFile'])->name('download');
    Route::post('/create-folder', [NewMediaManagerController::class, 'createFolder'])->name('create-folder');
    Route::get('/file/{file}', [NewMediaManagerController::class, 'getFileDetails'])->name('details');
});

// Media streaming routes (for local disk files)
Route::middleware(['auth'])->group(function () {
    Route::get('/media/stream/{file}', [MediaStreamController::class, 'stream'])
        ->name('media.stream');
});
