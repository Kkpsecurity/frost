<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Authentication - ' . siteConfig('site.company_name', 'Frost'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, #06b6d4 100%);
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
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo h1 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .auth-logo p {
            color: var(--secondary-color);
            margin: 0;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark-color);
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 500;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
        }

        .auth-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            color: var(--secondary-color);
            font-size: 0.875rem;
        }

        .alert {
            border-radius: 8px;
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
            font-weight: 500;
            opacity: 0.9;
            transition: opacity 0.3s ease;
        }

        .home-link:hover {
            color: white;
            opacity: 1;
        }
    </style>
</head>
<body>
    <a href="{{ route('home') }}" class="home-link">
        <i class="fas fa-arrow-left me-2"></i>Back to Home
    </a>

    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-logo">
                    <h1><i class="fas fa-snowflake"></i> {{ siteConfig('site.company_name', 'Frost') }}</h1>
                    <p>@yield('subtitle', authConfig()->getLoginSubtitle())</p>
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
