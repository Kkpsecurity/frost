<?php


Route::controller( 'App\Http\Controllers\Web\EnrollmentController' )
     ->prefix( 'enroll' )->name( 'enroll' )->group(function() {

    //
    // update this in the future
    //

    Route::get( '/{course}', 'AutoPayFlowPro' )->name( '' );

});



Route::namespace( 'App\Http\Controllers\Web' )->group(function () {

    Route::controller( 'OrderController' )
         ->prefix( 'order' )->name( 'order' )->group(function () {

        // add perms wrappers

        Route::get(  '/{order}/completed',         'ShowCompleted'     )->name( '.completed' );
        Route::post( '/{order}/applydiscountcode', 'ApplyDiscountCode' )->name( '.applydiscountcode' );

        #Route::middleware([ 'can:update,order' ])->group(function () {
            #Route::get(  '/{order}/payment',           'Payment'           )->name( '.payment' );
            #Route::post( '/{order}/applydiscountcode', 'ApplyDiscountCode' )->name( '.applydiscountcode' );
        #});


    });

});
