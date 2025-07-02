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
                            <h3 class="login-title mb-4">Confirm Email</h3>
                            <p class="mb-4 text-white">You must first provide your registered email to reset your password.</p>

                            @if (session('status'))
                                <div class="alert alert-success mb-4" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @include('frontend.forms.email-confirm-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
