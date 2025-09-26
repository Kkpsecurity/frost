<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Orders\OrderController;

/**
 * Admin Order Management Routes
 * Loaded with 'admin' prefix and middleware from admin.php
 */

Route::middleware(['admin'])->prefix('orders')->name('orders.')->group(function () {

    // Main order management routes
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/create', [OrderController::class, 'create'])->name('create');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
    Route::put('/{order}', [OrderController::class, 'update'])->name('update');
    Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');

    // Order status management
    Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
    Route::patch('/{order}/complete', [OrderController::class, 'markComplete'])->name('complete');
    Route::patch('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    Route::post('/{order}/refund', [OrderController::class, 'refund'])->name('refund');

    // Order duplication
    Route::get('/{order}/duplicate', [OrderController::class, 'duplicate'])->name('duplicate');

    // AJAX endpoints
    Route::get('/api/customer-search', [OrderController::class, 'customerSearch'])->name('api.customer-search');
    Route::get('/api/course-search', [OrderController::class, 'courseSearch'])->name('api.course-search');
    Route::get('/api/order-stats', [OrderController::class, 'orderStats'])->name('api.stats');

    // Bulk operations
    Route::post('/bulk/update-status', [OrderController::class, 'bulkUpdateStatus'])->name('bulk.update-status');
    Route::post('/bulk/delete', [OrderController::class, 'bulkDelete'])->name('bulk.delete');
    Route::post('/bulk/export', [OrderController::class, 'bulkExport'])->name('bulk.export');

    // Financial operations
    Route::get('/finance/sales-report', [OrderController::class, 'salesReport'])->name('finance.sales-report');
    Route::get('/finance/revenue-analytics', [OrderController::class, 'revenueAnalytics'])->name('finance.revenue-analytics');

    // Import/Export functionality
    Route::get('/import', [OrderController::class, 'import'])->name('import');
    Route::post('/import', [OrderController::class, 'processImport'])->name('import.process');
    Route::get('/export', [OrderController::class, 'export'])->name('export');

    // Invoice management
    Route::get('/{order}/invoice', [OrderController::class, 'generateInvoice'])->name('invoice');
    Route::get('/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('invoice.download');
    Route::post('/{order}/invoice/send', [OrderController::class, 'sendInvoice'])->name('invoice.send');

    // Receipt management
    Route::get('/{order}/receipt', [OrderController::class, 'generateReceipt'])->name('receipt');
    Route::get('/{order}/receipt/download', [OrderController::class, 'downloadReceipt'])->name('receipt.download');
});
