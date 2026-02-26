<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminSearchController;

/**
 * Admin Search Routes
 * Prefix: /admin
 * Loaded via glob from routes/admin.php
 */

Route::middleware(['admin'])->group(function () {

    Route::get('/search', [AdminSearchController::class, 'index'])->name('search');
});
