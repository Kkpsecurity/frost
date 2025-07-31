<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCenter\MediaManagerController;

// Admin Center - Media Manager routes
Route::prefix('admin-center/media')->name('media-manager.')->group(function () {
    Route::get('/', [MediaManagerController::class, 'index'])->name('index');
});
