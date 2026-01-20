<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StudentsController;

/**
 * Admin Students Management Routes
 * Prefix: /admin
 * Middleware: admin
 */

Route::middleware(['admin'])->group(function () {
    // Students Management
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentsController::class, 'index'])->name('index');
        Route::get('/data', [StudentsController::class, 'getData'])->name('data');
        Route::get('/{id}', [StudentsController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [StudentsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [StudentsController::class, 'update'])->name('update');
    });
});
