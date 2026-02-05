<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CodexSecureController;

/**
 * Codex Secure Services Routes
 * Prefix: /admin/codex/services
 * Middleware: admin
 */

Route::middleware(['admin'])->prefix('codex/services')->name('codex.services.')->group(function () {
    // Secure services dashboard
    Route::get('/secure', [CodexSecureController::class, 'index'])->name('secure');
    Route::post('/secure/refresh', [CodexSecureController::class, 'refresh'])->name('secure.refresh');
});
