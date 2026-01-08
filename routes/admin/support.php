<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FrostSupportController;
use App\Http\Controllers\Admin\SupportController;

// All support routes require admin authentication
Route::middleware(['admin'])->group(function () {
    // Support SPA Dashboard
    Route::get('/frost-support', [FrostSupportController::class, 'index'])->name('admin.frost-support');

    // Support API endpoints
    Route::get('/api/support/search-users', [SupportController::class, 'searchUsers'])->name('admin.support.search-users');
    Route::get('/api/support/poll-data', [SupportController::class, 'pollData'])->name('admin.support.poll-data');
    Route::post('/api/support/update-student/{studentId}', [SupportController::class, 'updateStudentDetails'])->name('admin.support.update-student');
});
