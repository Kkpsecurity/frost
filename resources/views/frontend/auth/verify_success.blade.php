@extends('layouts.app')

@section('page-title', __('Email Verification Success'))
@section('page-keywords', 'Email Verified, Success')
@section('page-description', 'Your Email Has Been Successfully Verified')

@section('content')
    @include('frontend.partials.breadcrumbs')

    <div class="login-area">
        <div class="login-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-12">
                    <div class="border-1 shadow-lg">
                        <div class="login-form-container p-5">
                            <h3 class="login-title mb-4">Email Verification Success</h3>
                            
                            <div class="alert alert-success text-center" role="alert">
                                <h4 class="alert-heading">Congratulations!</h4>
                                <p>Your email has been verified successfully!</p>
                                <hr>
                                <p class="mb-0">You may now continue using our services.</p>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
