{{-- Student Account Dashboard --}}
<x-frontend.site.site-wrapper :title="'My Account - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Manage your student account, profile, settings, and course enrollments.">
        <meta name="keywords" content="student account, profile, settings, courses">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="frost-secondary-bg account-page"
        style="min-height: calc(100vh - 200px); padding-top: 2rem; padding-bottom: 4rem;">
        <div class="container">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (isset($errors) && is_object($errors) && method_exists($errors, 'any') && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Please correct the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach (is_object($errors) && method_exists($errors, 'all') ? $errors->all() : [] as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                {{-- Sidebar Navigation --}}
                <div class="col-lg-3 mb-4">
                    <div class="card shadow-sm border-0 account-panel account-panel--sidebar">
                        <div class="card-body">
                            <h5 class="text-white mb-3">
                                <i class="fas fa-user-circle me-2"></i>My Account
                            </h5>
                            <nav class="nav flex-column account-nav">
                                <a class="nav-link {{ $activeSection === 'profile' ? 'active' : '' }}"
                                    href="{{ route('account.index', ['section' => 'profile']) }}">
                                    <i class="fas fa-user me-2"></i>Profile
                                </a>
                                <a class="nav-link {{ $activeSection === 'settings' ? 'active' : '' }}"
                                    href="{{ route('account.index', ['section' => 'settings']) }}">
                                    <i class="fas fa-cog me-2"></i>Settings
                                </a>
                                <a class="nav-link {{ $activeSection === 'notifications' ? 'active' : '' }}"
                                    href="{{ route('account.index', ['section' => 'notifications']) }}">
                                    <i class="fas fa-bell me-2"></i>Notifications
                                </a>
                                <a class="nav-link {{ $activeSection === 'orders' ? 'active' : '' }}"
                                    href="{{ route('account.index', ['section' => 'orders']) }}">
                                    <i class="fas fa-graduation-cap me-2"></i>My Courses
                                </a>
                                <a class="nav-link {{ $activeSection === 'payments' ? 'active' : '' }}"
                                    href="{{ route('account.index', ['section' => 'payments']) }}">
                                    <i class="fas fa-credit-card me-2"></i>Payments
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Main Content Area --}}
                <div class="col-lg-9">
                    <div class="card shadow-sm border-0 account-panel account-panel--content">
                        <div class="card-body p-4">
                            @if ($activeSection === 'profile')
                                @include('frontend.account.sections.profile', [
                                    'data' => $profileData,
                                    'user' => $user,
                                ])
                            @elseif($activeSection === 'settings')
                                @include('frontend.account.sections.settings', [
                                    'data' => $settingsData,
                                    'user' => $user,
                                ])
                            @elseif($activeSection === 'notifications')
                                @include('frontend.account.sections.notifications', [
                                    'data' => $notificationsData ?? [],
                                    'user' => $user,
                                ])
                            @elseif($activeSection === 'orders')
                                @include('frontend.account.sections.orders', [
                                    'data' => $ordersData,
                                    'user' => $user,
                                ])
                            @elseif($activeSection === 'payments')
                                @include('frontend.account.sections.payments', [
                                    'data' => $paymentsData,
                                    'user' => $user,
                                    'stripeEnabled' => $stripeEnabled,
                                    'paypalEnabled' => $paypalEnabled,
                                ])
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-frontend.site.partials.footer />

    <style>
        /*
            Account page theme fix (Laravel 11 upgrade):
            - Page background uses the secondary color.
            - Sidebar + content panels use the primary color.
            - Add clear borders/contrast so panels don't blend into the page.
        */
        .account-page {
            background-color: var(--frost-secondary-color) !important;
        }

        .account-panel {
            background-color: var(--frost-primary-color) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        }

        .account-nav .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .account-nav .nav-link:hover {
            background: rgba(0, 0, 0, 0.12);
            border-color: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .account-nav .nav-link.active {
            background: rgba(0, 0, 0, 0.22);
            border-color: rgba(255, 255, 255, 0.18);
            color: #fff;
            font-weight: 500;
        }
    </style>
</x-frontend.site.site-wrapper>
