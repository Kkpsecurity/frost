<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Authentication - Frost')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

    <style>
        .login-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .card-header {
            background: transparent;
            border-bottom: none;
            text-align: center;
            padding-bottom: 0;
        }
        .login-logo a {
            color: #ffffff;
            font-size: 2.1rem;
            font-weight: 300;
            text-decoration: none;
        }
        .login-box-msg {
            margin: 0;
            padding: 20px;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body class="hold-transition login-page">
<div class="login-box">
    <!-- Logo -->
    <div class="login-logo">
        <a href="{{ route('home') }}"><b>Frost</b> Admin</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">@yield('subtitle', 'Sign in to start your admin session')</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

        <!-- jQuery -->
<script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

</body>
</html>
    </style>
</head>
<body>
    <a href="{{ route('home') }}" class="home-link">
        <i class="fas fa-home me-2"></i>Main Site
    </a>

    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-logo">
                    <h1><i class="fas fa-snowflake"></i> Frost</h1>
                    <div class="admin-badge">Admin Panel</div>
                    <p>@yield('subtitle', 'Administrative Access')</p>
                </div>

                <div class="security-notice">
                    <i class="fas fa-shield-alt"></i>
                    This is a secure admin area. Only authorized personnel should access this page.
                </div>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
