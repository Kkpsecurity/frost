@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        /* Dark mode styling for login page */
        .login-page {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%) !important;
        }

        .login-box .card {
            background-color: #2c3e50 !important;
            border: 1px solid #34495e !important;
            box-shadow: 0 0 20px rgba(0,0,0,0.5) !important;
        }

        .login-box .card-body {
            background-color: #2c3e50 !important;
            color: #ecf0f1 !important;
        }

        .login-box-msg {
            color: #bdc3c7 !important;
        }

        .form-control {
            background-color: #34495e !important;
            border-color: #4a6741 !important;
            color: #ecf0f1 !important;
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
        }

        .btn-primary {
            background-color: #3498db !important;
            border-color: #3498db !important;
        }

        .btn-primary:hover {
            background-color: #2980b9 !important;
            border-color: #2980b9 !important;
        }

        .login-logo a {
            color: #3498db !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5) !important;
        }

        .login-logo a:hover {
            color: #5dade2 !important;
        }

        .icheck-primary input[type=checkbox]:checked + label::before {
            background-color: #3498db !important;
            border-color: #3498db !important;
        }

        .icheck-primary label {
            color: #bdc3c7 !important;
        }

        a {
            color: #3498db !important;
        }

        a:hover {
            color: #5dade2 !important;
        }
    </style>
@stop

@php
    $loginUrl = route('admin.login');
    $homeUrl = route('home');
@endphp

@section('auth_header', 'Admin Login')

@section('auth_body')
    <form action="{{ $loginUrl }}" method="post">
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

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Password">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Login field --}}
        <div class="row">
            <div class="col-7">
                <div class="icheck-primary" title="Remember me">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Remember Me</label>
                </div>
            </div>

            <div class="col-5">
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="fas fa-sign-in-alt"></span>
                    Sign In
                </button>
            </div>
        </div>

    </form>
@stop

@section('auth_footer')
    <p class="my-0 text-center">
        <a href="{{ route('admin.password.request') }}" class="text-center">
            Forgot Your Password?
        </a>
    </p>
    <p class="my-0 text-center mt-2">
        <a href="{{ $homeUrl }}" class="text-center">
            Back to Main Site
        </a>
    </p>
@stop
