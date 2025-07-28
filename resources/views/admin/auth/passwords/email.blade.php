@extends('adminlte::auth.auth-page', ['authType' => 'password_request'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        /* Dark mode styling for password reset page - Force proper sizing */
        body.login-page {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%) !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: flex-start !important;
            min-height: 100vh !important;
            padding-top: 0 !important;
            box-sizing: border-box !important;
        }

        /* Force container to be centered and limited width */
        .login-page .login-box,
        .login-page > div,
        .login-page .container,
        .login-page .container-fluid {
            width: 360px !important;
            max-width: 360px !important;
            margin: 0 auto !important;
            position: relative !important;
        }

        /* Logo styling - Center and position properly */
        .login-logo,
        .auth-logo,
        .password_reset-logo,
        .password_request-logo,
        body.login-page .login-logo,
        body.password-reset-page .login-logo,
        body.password_request-page .password_request-logo,
        body.password_reset-page .password_reset-logo {
            text-align: center !important;
            margin: 120px auto 25px auto !important;
            width: 100% !important;
            position: relative !important;
            z-index: 10 !important;
            display: block !important;
        }

        .login-logo a,
        .auth-logo a,
        .password_reset-logo a,
        .password_request-logo a,
        body.login-page .login-logo a,
        body.password-reset-page .login-logo a,
        body.password_request-page .password_request-logo a,
        body.password_reset-page .password_reset-logo a {
            color: #3498db !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5) !important;
            font-size: 35px !important;
            font-weight: 300 !important;
            text-decoration: none !important;
            display: block !important;
            width: 100% !important;
            text-align: center !important;
        }

        .login-logo a:hover,
        .auth-logo a:hover,
        .password_reset-logo a:hover,
        .password_request-logo a:hover,
        body.login-page .login-logo a:hover,
        body.password-reset-page .login-logo a:hover,
        body.password_request-page .password_request-logo a:hover,
        body.password_reset-page .password_reset-logo a:hover {
            color: #5dade2 !important;
            text-decoration: none !important;
        }

        /* Force proper body layout with top padding for logo */
        body.login-page,
        body.password-reset-page,
        body.password_request-page {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%) !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: flex-start !important;
            min-height: 100vh !important;
            padding-top: 0 !important;
            box-sizing: border-box !important;
        }

        /* Card styling */
        .card,
        .login-box .card,
        div[class*="card"] {
            background-color: #2c3e50 !important;
            border: 1px solid #34495e !important;
            box-shadow: 0 0 20px rgba(0,0,0,0.5) !important;
            border-radius: 10px !important;
            width: 100% !important;
            max-width: 360px !important;
            margin: 0 auto !important;
        }

        .card-body,
        .login-card-body {
            background-color: #2c3e50 !important;
            color: #ecf0f1 !important;
            padding: 20px !important;
            border-radius: 10px !important;
        }

        /* Message styling */
        .login-box-msg,
        p.login-box-msg {
            color: #bdc3c7 !important;
            margin: 0 0 15px 0 !important;
            text-align: center !important;
            font-size: 14px !important;
        }

        /* Form controls */
        .form-control {
            background-color: #34495e !important;
            border-color: #4a6741 !important;
            color: #ecf0f1 !important;
            border-radius: 5px !important;
        }

        .form-control::placeholder {
            color: #95a5a6 !important;
        }

        .form-control:focus {
            background-color: #34495e !important;
            border-color: #3498db !important;
            color: #ecf0f1 !important;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25) !important;
        }

        /* Input group styling */
        .input-group .input-group-text {
            background-color: #34495e !important;
            border-color: #4a6741 !important;
            color: #95a5a6 !important;
        }

        /* Button styling */
        .btn-primary {
            background-color: #3498db !important;
            border-color: #3498db !important;
            color: white !important;
            font-weight: 400 !important;
        }

        .btn-primary:hover {
            background-color: #2980b9 !important;
            border-color: #2980b9 !important;
        }

        .btn-primary:focus {
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25) !important;
        }

        /* Alert styling */
        .alert-danger {
            background-color: #e74c3c !important;
            border-color: #c0392b !important;
            color: white !important;
        }

        .alert-success {
            background-color: #27ae60 !important;
            border-color: #229954 !important;
            color: white !important;
        }

        /* Link styling */
        a {
            color: #3498db !important;
        }

        a:hover {
            color: #5dade2 !important;
            text-decoration: none !important;
        }

        /* Invalid feedback */
        .invalid-feedback {
            color: #e74c3c !important;
        }

        .is-invalid {
            border-color: #e74c3c !important;
        }

        /* Ensure proper centering for all content */
        .login-page {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* Override any AdminLTE specific full-width classes */
        .content-wrapper,
        .main-content,
        .container-fluid {
            width: auto !important;
            max-width: 400px !important;
            margin: 0 auto !important;
        }
    </style>
@stop

@php
    $passwordEmailUrl = route('admin.password.email');
    $loginUrl = route('admin.login');
@endphp

@section('auth_header', 'Reset Admin Password')



@section('auth_body')
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <p class="login-box-msg">Enter your admin email to receive a password reset link</p>

    <form action="{{ $passwordEmailUrl }}" method="post">
        @csrf

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="Admin Email" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="fas fa-paper-plane"></span>
                    Send Password Reset Link
                </button>
            </div>
        </div>

    </form>
@stop

@section('auth_footer')
    <p class="my-0 text-center">
        <a href="{{ $loginUrl }}" class="text-center">
            Back to Login
        </a>
    </p>
@stop
