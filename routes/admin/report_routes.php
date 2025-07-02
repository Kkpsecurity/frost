<?php

Route::get('reports/dashboard', [App\Http\Controllers\Admin\Reports\ReportController::class, 'dashboard'])
    ->name('admin.reports.dashboard');

Route::get('reports/services/charts/registration', 
    [
        App\Http\Controllers\Admin\Services\AdminChartServiceController::class, 
        'registration'
    ]
);