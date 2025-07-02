<?php

use Illuminate\Support\Facades\Route;

Route::controller( 'App\Http\Controllers\Web\RangeDateController' )
     ->prefix( 'range_date' )->name( 'range_date' )->group(function () {

    Route::get(  '/{course_auth}/select',              'Select' )->name( '.select' );
    Route::get(  '/{course_auth}/show',                'Show'   )->name( '.show'   );
    Route::post( '/{course_auth}/{range_date}/update', 'Update' )->name( '.update' );

});
