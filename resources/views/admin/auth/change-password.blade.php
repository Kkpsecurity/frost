@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        /* FORCE Dark mode styling - Override everything */
        * {
            box-sizing: border-box !important;
        }

        body,
        html,
        .wrapper,
        .content-wrapper,
        .main-content {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%) !important;
            min-height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: flex-start !important;
            padding-top: 0 !important;
        }

        /* FORCE container sizing - Override AdminLTE */
        .login-page .login-box,
        .login-page > div,
        .login-page .container,
        .login-page .container-fluid,
        .login-box,
        .container,
        .container-fluid,
        body > div,
        body > .container,
        .content,
        .main-content,
        div[class*="container"] {
            width: 360px !important;
            max-width: 360px !important;
            margin: 0 auto !important;
            position: relative !important;
            background: transparent !important;
        }

        /* FORCE Logo positioning */
        .login-logo,
        .auth-logo,
        .change_password-logo,
        .change-password-logo,
        body .login-logo,
        body .auth-logo,
        div[class*="logo"],
        h1,
        .brand-link {
            text-align: center !important;
            margin: 120px auto 25px auto !important;
            width: 100% !important;
            position: relative !important;
            z-index: 10 !important;
            display: block !important;
            background: transparent !important;
        }

        .login-logo a,
        .auth-logo a,
        .change_password-logo a,
        .change-password-logo a,
        body .login-logo a,
        body .auth-logo a,
        div[class*="logo"] a,
        h1 a,
        .brand-link {
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
        .change_password-logo a:hover,
        .change-password-logo a:hover,
        body .login-logo a:hover,
        body .auth-logo a:hover,
        div[class*="logo"] a:hover,
        h1 a:hover {
            color: #5dade2 !important;
            text-decoration: none !important;
        }

        /* FORCE Card styling */
        .card,
        .login-box .card,
        div[class*="card"],
        .box,
        .panel,
        .widget {
            background-color: #2c3e50 !important;
            border: 1px solid #34495e !important;
            box-shadow: 0 0 20px rgba(0,0,0,0.5) !important;
            border-radius: 10px !important;
            width: 100% !important;
            max-width: 360px !important;
            margin: 0 auto !important;
        }

        .card-body,
        .login-card-body,
        .box-body,
        .panel-body {
            background-color: #2c3e50 !important;
            color: #ecf0f1 !important;
            padding: 20px !important;
            border-radius: 10px !important;
        }

        /* FORCE Message styling */
        .login-box-msg,
        p.login-box-msg,
        .auth-header,
        h3,
        h4 {
            color: #bdc3c7 !important;
            margin: 0 0 15px 0 !important;
            text-align: center !important;
            font-size: 14px !important;
            background: transparent !important;
        }

        /* FORCE Form controls */
        .form-control,
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input {
            background-color: #34495e !important;
            border-color: #4a6741 !important;
            color: #ecf0f1 !important;
            border-radius: 5px !important;
            border: 1px solid #4a6741 !important;
        }

        .form-control::placeholder,
        input::placeholder {
            color: #95a5a6 !important;
        }

        .form-control:focus,
        input:focus {
            background-color: #34495e !important;
            border-color: #3498db !important;
            color: #ecf0f1 !important;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25) !important;
            outline: none !important;
        }

        .input-group-text,
        .input-group-addon {
            background-color: #4a6741 !important;
            border-color: #4a6741 !important;
            color: #ecf0f1 !important;
            border-radius: 0 5px 5px 0 !important;
        }

        /* FORCE Button styling */
        .btn-primary,
        .btn,
        button[type="submit"] {
            background-color: #3498db !important;
            border-color: #3498db !important;
            border-radius: 5px !important;
            font-weight: 600 !important;
            color: white !important;
        }

        .btn-primary:hover,
        .btn:hover,
        button[type="submit"]:hover {
            background-color: #2980b9 !important;
            border-color: #2980b9 !important;
        }

        .btn-block {
            width: 100% !important;
        }

        /* FORCE Link styling */
        a {
            color: #3498db !important;
            text-decoration: none !important;
        }

        a:hover {
            color: #5dade2 !important;
            text-decoration: underline !important;
        }

        /* FORCE Alert styling */
        .alert-success {
            background-color: #27ae60 !important;
            border-color: #229954 !important;
            color: #fff !important;
            border-radius: 5px !important;
            margin-bottom: 15px !important;
        }

        .alert-danger {
            background-color: #e74c3c !important;
            border-color: #c0392b !important;
            color: white !important;
        }

        /* FORCE Invalid feedback */
        .invalid-feedback {
            color: #e74c3c !important;
        }

        .is-invalid {
            border-color: #e74c3c !important;
        }

        /* FORCE Utility classes */
        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .text-center {
            text-align: center !important;
        }

        .my-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        /* Remove any white backgrounds */
        div, section, main, article {
            background: transparent !important;
        }
    </style>
@stop
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
        .change_password-logo a:hover,
        body.login-page .login-logo a:hover,
        body.change_password-page .login-logo a:hover,
        body.change_password-page .change_password-logo a:hover {
            color: #5dade2 !important;
            text-decoration: none !important;
        }

        /* Force proper body layout with top padding for logo */
        body.login-page,
        body.change_password-page {
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

        .input-group-text {
            background-color: #4a6741 !important;
            border-color: #4a6741 !important;
            color: #ecf0f1 !important;
            border-radius: 0 5px 5px 0 !important;
        }

        /* Button styling */
        .btn-primary {
            background-color: #3498db !important;
            border-color: #3498db !important;
            border-radius: 5px !important;
            font-weight: 600 !important;
        }

        .btn-primary:hover {
            background-color: #2980b9 !important;
            border-color: #2980b9 !important;
        }

        .btn-block {
            width: 100% !important;
        }

        /* Link styling */
        a {
            color: #3498db !important;
            text-decoration: none !important;
        }

        a:hover {
            color: #5dade2 !important;
            text-decoration: underline !important;
        }

        /* Alert styling */
        .alert-success {
            background-color: #27ae60 !important;
            border-color: #229954 !important;
            color: #fff !important;
            border-radius: 5px !important;
            margin-bottom: 15px !important;
        }

        .alert-danger {
            background-color: #e74c3c !important;
            border-color: #c0392b !important;
            color: white !important;
        }

        /* Invalid feedback */
        .invalid-feedback {
            color: #e74c3c !important;
        }

        .is-invalid {
            border-color: #e74c3c !important;
        }

        /* Utility classes */
        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .text-center {
            text-align: center !important;
        }

        .my-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
    </style>
@stop

@php
    $changePasswordUrl = route('admin.profile.update-password');
    $dashboardUrl = route('admin.dashboard');
@endphp

@section('auth_header', 'Change Admin Password')

@section('auth_body')
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <p class="login-box-msg">Update your admin password</p>

    <form action="{{ $changePasswordUrl }}" method="post">
        @csrf

        {{-- Current Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror"
                placeholder="Current Password" required>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>

            @error('current_password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- New Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="New Password" required>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-key"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm New Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" class="form-control"
                placeholder="Confirm New Password" required>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-key"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="fas fa-sync-alt"></span>
                    Update Password
                </button>
            </div>
        </div>

    </form>
@stop

@section('auth_footer')
    <p class="my-0 text-center">
        <a href="{{ $dashboardUrl }}" class="text-center">
            Back to Dashboard
        </a>
    </p>
@stop
