<?php

use Illuminate\Support\Facades\Route;


Route::controller( 'App\Http\Controllers\Web\Other\DiscountCodesController' )
     ->prefix( 'discount_codes' )->name( 'discount_codes' )->group(function () {

    Route::get(  '/usage/{discount_code:uuid}',      'Usage'     )->name( '.usage' );
    Route::get(  '/usage/{discount_code:uuid}/csv',  'UsageCSV'  )->name( '.usage.csv' );
    Route::post( '/usage/{discount_code:uuid}/auth', 'AuthCode'  )->name( '.usage.auth' );

    // devel
    Route::get( '/usage/{discount_code:uuid}/forget', 'ForgetCode' )->name( '.usage.forget' );

});
