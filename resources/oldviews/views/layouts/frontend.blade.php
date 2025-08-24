<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Frost'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS (if needed for compatibility) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Our Custom Styles -->
    @vite(['resources/css/style.css', 'resources/js/site.js'])

    <!-- Additional Styles -->
    @stack('styles')

    <!-- Meta Data -->
    <meta name="description" content="@yield('description', 'Professional Security Training Platform')">
    <meta name="keywords" content="@yield('keywords', 'security training, cybersecurity, courses')">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'Professional Security Training Platform')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.png'))">
    <meta property="og:url" content="{{ request()->url() }}">
</head>

<body class="@yield('body_class', '')">
    <!-- Page Loader -->
    <div id="preloaders">
        <div class="frost-loader">
            <div class="inner"></div>
        </div>
        <div class="loading-text">Loading...</div>
        <div class="loading-progress">
            <div class="loading-progress-bar"></div>
        </div>
    </div>

    <!-- Site Header -->
    @include('components.frontend.partials.topbar')

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Site Footer -->
    @include('components.frontend.partials.footer')

    <!-- Scroll to Top Button -->
    <button class="scrollUp" data-tooltip="Back to top">
        <i class="fa fa-arrow-up"></i>
    </button>

    <!-- Modals -->
    @stack('modals')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Additional Scripts -->
    @stack('scripts')

    <!-- Page Loader Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loader when page is fully loaded
            window.addEventListener('load', function() {
                const preloader = document.getElementById('preloaders');
                if (preloader) {
                    preloader.classList.add('loaded');
                    setTimeout(() => {
                        preloader.style.display = 'none';
                    }, 500);
                }
            });

            // Scroll to top functionality
            const scrollUpBtn = document.querySelector('.scrollUp');
            if (scrollUpBtn) {
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 100) {
                        scrollUpBtn.classList.add('show');
                    } else {
                        scrollUpBtn.classList.remove('show');
                    }
                });

                scrollUpBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>
</body>
</html>
