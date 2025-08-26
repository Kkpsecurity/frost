@php
    $companyPhone = \App\RCache::SiteConfig('site_company_phone') ?? '(555) 123-4567';
    $companyEmail = \App\RCache::SiteConfig('site_company_email') ?? 'info@floridaonlinesecurity.com';
@endphp

<div class="topbar-right">
    <ul class="top-right-nav mb-0">
        <li class="top-nav-item">
            <i class="fas fa-phone me-1"></i>
            <a href="tel:{{ str_replace(['(', ')', ' ', '-'], '', $companyPhone) }}" class="top-nav-link">
                {{ $companyPhone }}
            </a>
        </li>
        <li class="top-nav-item">
            <i class="fas fa-envelope me-1"></i>
            <a href="mailto:{{ $companyEmail }}" class="top-nav-link">
                {{ $companyEmail }}
            </a>
        </li>
        @guest
            <li class="top-nav-item">
                <a href="{{ route('login') }}" class="top-nav-link">
                    <i class="fas fa-sign-in-alt me-1"></i>Login
                </a>
            </li>
            @if (Route::has('register'))
                <li class="top-nav-item">
                    <a href="{{ route('register') }}" class="top-nav-link">
                        <i class="fas fa-user-plus me-1"></i>Register
                    </a>
                </li>
            @endif
        @endguest
        
        @auth
            <li class="top-nav-item">
                <span class="top-nav-text">
                    <i class="fas fa-user me-1"></i>Welcome, {{ Auth::user()->fname }}
                </span>
            </li>
            <li class="top-nav-item">
                <a href="{{ route('logout') }}" class="top-nav-link"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        @endauth
    </ul>
</div>
