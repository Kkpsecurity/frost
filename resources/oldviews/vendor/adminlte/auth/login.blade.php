@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        /* Enhanced circular logo styling */
        .login-logo img.img-circle,
        .auth-logo img.img-circle {
            border-radius: 50% !important;
            border: 3px solid rgba(255, 255, 255, 0.2) !important;
            box-shadow:
                0 4px 8px rgba(0, 0, 0, 0.2),
                0 0 0 2px rgba(255, 255, 255, 0.1) !important;
            transition: all 0.3s ease-in-out !important;
            object-fit: cover !important;
        }

        /* Hover effect for logo */
        .login-logo img.img-circle:hover,
        .auth-logo img.img-circle:hover {
            transform: scale(1.05) !important;
            box-shadow:
                0 6px 12px rgba(0, 0, 0, 0.3),
                0 0 0 3px rgba(255, 255, 255, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
        }

        /* Logo container styling */
        .login-logo,
        .auth-logo {
            margin-bottom: 25px !important;
        }

        .login-logo a,
        .auth-logo a {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 15px !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .login-logo img.img-circle,
            .auth-logo img.img-circle {
                width: 60px !important;
                height: 60px !important;
            }
        }

        @media (max-width: 480px) {
            .login-logo img.img-circle,
            .auth-logo img.img-circle {
                width: 50px !important;
                height: 50px !important;
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

@section('auth_header', __('adminlte::adminlte.login_message'))

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

                    <label for="remember">
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
    {{-- Password reset link --}}
    @if($passResetUrl)
        <p class="my-0">
            <a href="{{ $passResetUrl }}">
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </p>
    @endif

    {{-- Register link --}}
    @if($registerUrl)
        <p class="my-0">
            <a href="{{ $registerUrl }}">
                {{ __('adminlte::adminlte.register_a_new_membership') }}
            </a>
        </p>
    @endif
@stop
