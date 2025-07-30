<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SettingsController;

// Admin Center - Settings CRUD routes
Route::prefix('admin-center/settings')->name('settings.')->group(function () {
    // Main settings CRUD
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::get('/create', [SettingsController::class, 'create'])->name('create');
    Route::post('/', [SettingsController::class, 'store'])->name('store');
    Route::get('/{key}/edit', [SettingsController::class, 'edit'])->name('edit');
    Route::put('/{key}', [SettingsController::class, 'update'])->name('update');
    Route::delete('/{key}', [SettingsController::class, 'destroy'])->name('destroy');
    Route::get('/{key}', [SettingsController::class, 'show'])->name('show');

    // AdminLTE specific settings
    Route::get('/adminlte/config', [SettingsController::class, 'adminlte'])->name('adminlte');
    Route::put('/adminlte/config', [SettingsController::class, 'updateAdminlte'])->name('adminlte.update');

    // Test settings functionality
    Route::get('/test/functionality', [SettingsController::class, 'test'])->name('test');
});
