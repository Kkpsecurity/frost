<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Frost\DiscountCodeController;

/**
 * Admin Discount Code Routes
 * Loaded with 'admin' prefix and middleware from admin.php
 */

Route::middleware(['admin'])->prefix('discount-codes')->name('discount-codes.')->group(function () {

    Route::get('/',              [DiscountCodeController::class, 'index'])->name('index');
    Route::get('/create',        [DiscountCodeController::class, 'create'])->name('create');
    Route::post('/',             [DiscountCodeController::class, 'store'])->name('store');
    Route::get('/{id}',          [DiscountCodeController::class, 'show'])->name('show');
    Route::get('/{id}/edit',     [DiscountCodeController::class, 'edit'])->name('edit');
    Route::put('/{id}',          [DiscountCodeController::class, 'update'])->name('update');
    Route::delete('/{id}',       [DiscountCodeController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/usage.csv', [DiscountCodeController::class, 'exportCsv'])->name('usage.csv');
});
