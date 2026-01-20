<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Frost\RangeController;

Route::group(['prefix' => 'ranges', 'as' => 'ranges.'], function () {
    Route::get('/', [RangeController::class, 'index'])->name('index');
    Route::get('/create', [RangeController::class, 'create'])->name('create');
    Route::post('/', [RangeController::class, 'store'])->name('store');
    Route::get('/{range}', [RangeController::class, 'show'])->name('show');
    Route::get('/{range}/edit', [RangeController::class, 'edit'])->name('edit');
    Route::put('/{range}', [RangeController::class, 'update'])->name('update');
    Route::delete('/{range}', [RangeController::class, 'destroy'])->name('destroy');
    Route::post('/{range}/toggle-active', [RangeController::class, 'toggleActive'])->name('toggle-active');
});
