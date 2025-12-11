{{-- Site Layout Component for Frontend Pages --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Frost') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS (for compatibility) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Core Theme Styles (Global) --}}
    @vite(['resources/css/style.css'])

    {{-- Panel-Specific CSS Section --}}
    @yield('panel-css')
    @stack('component-styles')

    <!-- Meta Data -->
    <meta name="description" content="{{ $description ?? 'Professional Security Training Platform' }}">
    <meta name="keywords" content="{{ $keywords ?? 'security training, cybersecurity, courses' }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:description" content="{{ $description ?? 'Professional Security Training Platform' }}">
    <meta property="og:image" content="{{ asset('images/og-image.png') }}">
    <meta property="og:url" content="{{ request()->url() }}">

    {{-- Page-specific head content --}}
    {{ $head ?? '' }}
</head>

<body class="antialiased {{ $bodyClass ?? '' }}">
    <!-- Professional Page Loader -->
    <x-frontend.ui.page-loader />

    {{-- Main content area --}}
    <main class="main-content">
        {{ $slot }}
    </main>

    <!-- Scroll to Top Button -->
    <button class="scrollUp" data-tooltip="Back to top">
        <i class="fa fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Page-specific scripts --}}
    {{ $scripts ?? '' }}

    <!-- Professional Site Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Professional page loader with smooth transition
            window.addEventListener('load', function() {
                const preloader = document.getElementById('preloaders');
                if (preloader) {
                    preloader.classList.add('loaded');
                    setTimeout(() => {
                        preloader.style.display = 'none';
                    }, 500);
                }
            });

            // Smooth scroll-to-top functionality
            const scrollUpBtn = document.querySelector('.scrollUp');
            if (scrollUpBtn) {
                // Show/hide based on scroll position
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 100) {
                        scrollUpBtn.classList.add('show');
                    } else {
                        scrollUpBtn.classList.remove('show');
                    }
                });

                // Smooth scroll to top
                scrollUpBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }

            // Mobile menu functionality
            const mobileMenuButton = document.querySelector('.topbar-mobile-toggle');
            const mobileMenu = document.querySelector('#navbarNav');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('show');
                });
            }

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    // Only process valid selectors (not empty or just #)
                    if (href && href !== '#' && href.length > 1) {
                        try {
                            const target = document.querySelector(href);
                            if (target) {
                                target.scrollIntoView({
                                    behavior: 'smooth'
                                });
                            }
                        } catch (error) {
                            console.warn('Invalid selector:', href);
                        }
                    }
                });
            });

            // Professional form enhancements
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
        });
    </script>
</body>
</html>
