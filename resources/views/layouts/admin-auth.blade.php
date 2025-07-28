<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Authentication - Frost')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --admin-primary: #dc3545;
            --admin-secondary: #6c757d;
            --admin-success: #198754;
            --admin-danger: #dc3545;
            --admin-warning: #ffc107;
            --admin-info: #0dcaf0;
            --admin-dark: #212529;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, var(--admin-primary) 0%, #fd7e14 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .auth-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .auth-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            padding: 2.5rem;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo h1 {
            color: var(--admin-primary);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .auth-logo .admin-badge {
            background: var(--admin-primary);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .auth-logo p {
            color: var(--admin-secondary);
            margin: 0.5rem 0 0 0;
        }

        .form-label {
            font-weight: 600;
            color: var(--admin-dark);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
            background-color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--admin-primary) 0%, #fd7e14 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
        }

        .auth-links a {
            color: var(--admin-primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .auth-links a:hover {
            color: #bb2d3b;
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 1.5rem;
        }

        .invalid-feedback {
            display: block;
        }

        .home-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            opacity: 0.9;
            transition: opacity 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            backdrop-filter: blur(10px);
        }

        .home-link:hover {
            color: white;
            opacity: 1;
        }

        .form-check-input:checked {
            background-color: var(--admin-primary);
            border-color: var(--admin-primary);
        }

        .form-check-label {
            color: var(--admin-secondary);
            font-weight: 500;
        }

        .security-notice {
            background: #fff3cd;
            border: 1px solid #ffecb5;
            color: #664d03;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .security-notice i {
            margin-right: 0.5rem;
        }
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
