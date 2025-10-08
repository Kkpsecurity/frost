@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        /* Admin Login Page - Professional Styling */
        .login-page {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                        url('{{ asset("images/premium_photo-1661878265739-da90bc1af051.jpg") }}') center/cover fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-box {
            width: 400px;
            margin: 0 auto;
        }

        /* Logo styling */
        .login-logo img {
            width: 60px !important;
            height: 60px !important;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.9);
            object-fit: cover;
            margin-right: 15px;
        }

        .login-logo a {
            color: #ffffff !important;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
            font-size: 28px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 0px;
        }

        /* Card styling */
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: none;
        }

        .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 500;
        }

        .card-body {
            padding: 30px;
        }

        /* Form styling */
        .form-control {
            padding: 12px 15px;
            font-size: 16px;
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #2c3e50;
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
        }

        .input-group-text {
            background: #2c3e50;
            border: 2px solid #2c3e50;
            color: white;
            border-radius: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1a252f 0%, #2c3e50 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(44, 62, 80, 0.4);
        }

        .input-group {
            margin-bottom: 20px;
        }

        /* Remember me styling - more specific selectors */
        .card-body .icheck-primary > label,
        .card-body .icheck-primary label,
        .card-body label[for="remember"] {
            color: #e6e6e7 !important;
            font-size: 15px !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: color 0.3s ease !important;
            padding-left: 10px !important;
        }

        .card-body .icheck-primary > label:hover,
        .card-body .icheck-primary label:hover,
        .card-body label[for="remember"]:hover {
            color: #1a252f !important;
        }

        /* Footer links */
        .card-footer {
            background: rgba(248, 249, 252, 0.9);
            padding: 20px;
            text-align: center;
        }

        .card-footer a {
            color: #ffffff !important;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
            display: inline-block;
            padding: 5px 10px;
        }

        .card-footer a:hover {
            color: #f8f9fa !important;
            text-decoration: underline;
            transform: translateY(-1px);
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-box {
                width: 100%;
                max-width: 350px;
            }

            .card-body {
                padding: 20px;
            }

            .login-logo img {
                width: 50px !important;
                height: 50px !important;
                margin-right: 12px;
            }

            .login-logo a {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .login-logo img {
                width: 45px !important;
                height: 45px !important;
                margin-right: 10px;
            }

            .login-logo a {
                font-size: 22px;
            }
        }
    </style>
@stop

@php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl = $loginUrl ? route($loginUrl) : '';
        $registerUrl = $registerUrl ? route($registerUrl) : '';
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
    } else {
        $loginUrl = $loginUrl ? url($loginUrl) : '';
        $registerUrl = $registerUrl ? url($registerUrl) : '';
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
    }
@endphp

@section('auth_header', 'Enter your credentials')

@section('auth_body')
    <form action="{{ $loginUrl }}" method="post">
        @csrf

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
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
                placeholder="{{ __('adminlte::adminlte.password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
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
                <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label for="remember" style="color: #e3e6f0 !important;">
                        {{ __('adminlte::adminlte.remember_me') }}
                    </label>
                </div>
            </div>

            <div class="col-5">
                <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    {{-- Password reset link - only if enabled in settings --}}
    @if(config('adminlte.password_reset_enabled', true) && $passResetUrl)
        <p class="my-0">
            <a href="{{ $passResetUrl }}">
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </p>
    @endif

    {{-- Registration removed for admin-only authentication --}}
@stop
