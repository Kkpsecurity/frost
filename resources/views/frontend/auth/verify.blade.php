@extends('layouts.app')

@section('page-title', __('Verify Your Email Address'))
@section('page-keywords', 'Verify Your Email Address')
@section('page-description', 'Verify Your Email Address')

@section('content')
    @include('frontend.partials.breadcrumbs')

    <div class="login-area">
        <div class="login-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-12">
                    <div class="border-1 shadow-lg">
                        <div class="login-form-container p-5">
                            <h3 class="login-title mb-4">Verify Your Email</h3>
                            <div class="row">
                                
                                @if (session('resent'))
                                    <div class="alert alert-success mb-4">
                                        A fresh verification link has been sent to your email address. Please check your
                                        email to complete the verification.
                                    </div>
                                @else
                                    <div id="message-console"></div>
                                    @include('frontend.partials.messages')

                                    <form method="POST" action="{{ route('verification.resend') }}" class="form">
                                        @csrf
                                        <div class="form-group d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Resend Verification Email') }}
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
