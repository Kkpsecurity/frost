<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;

#use App\Http\Controllers\Web\Payments\PaymentController;

/**
 * Fixes te payment route we need the uuid here so we just redirect back to courses
 */
Route::redirect('/payments', '/courses')->name('payments');

Route::namespace( 'App\Http\Controllers\Web\Payments' )
     ->prefix( 'payments' )->name( 'payments' )->group(function () {


    /**
     *
     * PayFlowPro
     *
     */
    Route::controller( PayFlowProController::class )
         ->prefix( '/payflowpro' )->name( '.payflowpro' )->group(function () {


        Route::middleware([ 'auth' ])->group(function () {

            // CC form view
            Route::get( '/{pay_flow_pro:uuid}', 'index' )->name( '' );

            // AJAX
            Route::post( '/{pay_flow_pro:uuid}/get_token', 'GetToken' )->name( '.get_token' );

        });


        // rePOST from PayPal
        Route::withoutMiddleware([ VerifyCsrfToken::class, 'auth', 'verified' ])->group(function () {

            Route::post( '/{pay_flow_pro:uuid}/payment_return', 'HandleReturn' )->name( '.payment_return' );

        });

    });


    /**
     *
     * Future payment types
     *
     */

});
