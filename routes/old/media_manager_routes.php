<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaStreamController;
use App\Http\Controllers\Admin\AdminCenter\MediaManagerController;
use App\Http\Controllers\Admin\MediaController;

// Original Admin Center Media route (maintains backward compatibility)
Route::prefix('admin-center')->name('admin-center.')->group(function () {
    Route::get('/media', [MediaManagerController::class, 'index'])->name('media.index');
});

// Consolidated Media Manager routes with role-based disk access
Route::prefix('media-manager')->name('media-manager.')->group(function () {
    // Main media manager interface
    Route::get('/', [MediaController::class, 'index'])->name('index');

    // File operations
    Route::post('/upload', [MediaController::class, 'upload'])->name('upload');
    Route::get('/files', [MediaController::class, 'listFiles'])->name('files');
    Route::get('/tree', [MediaController::class, 'getTree'])->name('tree');
    Route::post('/create-folder', [MediaController::class, 'createFolder'])->name('create-folder');

    // File management
    Route::delete('/delete/{file}', [MediaController::class, 'deleteFile'])->name('delete');
    Route::delete('/file/{file}', [MediaController::class, 'deleteFile'])->name('delete-file');
    Route::get('/download/{file}', [MediaController::class, 'downloadFile'])->name('download');
    Route::get('/file/{file}', [MediaController::class, 'getFileDetails'])->name('details');
    Route::post('/archive/{file}', [MediaController::class, 'archiveFile'])->name('archive');

    // System information
    Route::get('/disk-statuses', [MediaController::class, 'getDiskStatuses'])->name('disk-statuses');
    Route::get('/stats', [MediaManagerController::class, 'getStats'])->name('stats');
});

// Media streaming routes (for local disk files)
Route::middleware(['auth'])->group(function () {
    Route::get('/media/stream/{file}', [MediaStreamController::class, 'stream'])
        ->name('media.stream');
});
