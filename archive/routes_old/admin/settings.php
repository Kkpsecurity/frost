<?php

/**
 * Admin Settings Routes
 * Routes for admin settings management
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCenter\SettingsController;

// Settings Routes
Route::prefix('settings')
    ->name('settings.')
    ->group(function () {
        
        // Settings Index/Dashboard
        Route::get('/', [
            SettingsController::class,
            'index'
        ])->name('index');
        
        // Settings Create
        Route::get('/create', [
            SettingsController::class,
            'create'
        ])->name('create');
        
        // Settings Store
        Route::post('/', [
            SettingsController::class,
            'store'
        ])->name('store');
        
        // Settings Show
        Route::get('/{setting}', [
            SettingsController::class,
            'show'
        ])->name('show');
        
        // Settings Edit
        Route::get('/{setting}/edit', [
            SettingsController::class,
            'edit'
        ])->name('edit');
        
        // Settings Update
        Route::put('/{setting}', [
            SettingsController::class,
            'update'
        ])->name('update');
        
        // Settings Delete
        Route::delete('/{setting}', [
            SettingsController::class,
            'destroy'
        ])->name('destroy');
        
    });
