<?php

use Illuminate\Support\Facades\Route;


Route::namespace( 'App\Classes\Certificates' )
     ->middleware([ 'auth' ])
     ->prefix( 'certificate' )->name( 'certificate' )->group(function () {


    Route::controller( CertificatePDF::class )->group(function () {

        Route::get( 'g20h_pdf/{course_auth}', 'G20HourPDF'     )->name( '.g20h_pdf' );

        // Route::get( 'printpdf/{course_auth}', 'CertificatePDF' )->name( '.printpdf' );

    });


});
