@extends('layouts.app')

@section('content')
    @include('frontend/partials/breadcrumbs')

    <div class="login-area">
        <div class="login-overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-12">
                    <div class="border-1 shadow-lg">
                        <div class="login-form-container p-5">
                            <h3 class="login-title mb-4">Reset Password</h3>
                            <div class="row">
                                <div id="message-console"></div>
                                @include('frontend.partials.messages')
                                @include('frontend.forms.reset-password-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
