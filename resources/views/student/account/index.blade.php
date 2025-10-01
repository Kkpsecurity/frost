{{-- Modern Account Dashboard with Sidebar Navigation --}}
<x-frontend.site.site-wrapper :title="'My Account - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Manage your account profile, settings, and course enrollments">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Include Stripe.js for payment processing --}}
        @if ($activeSection === 'payments')
            <script src="https://js.stripe.com/v3/"></script>
        @endif

        {{-- Include Account Dashboard Styles --}}
        @include('student.account.components.dashboard-styles')
    </x-slot:head>

    <x-frontend.site.partials.header />

    {{-- Display Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Account Dashboard with Sidebar --}}
    <div class="account-dashboard">
        {{-- Include Sidebar Component --}}
        @include('student.account.components.sidebar')

        {{-- Main Content Area --}}
        <div class="account-content">
            {{-- Include Dynamic Content Header --}}
            @include('student.account.components.content-header')

            {{-- Section Content --}}
            <div class="content-body">
                @switch($activeSection)
                    @case('profile')
                        @include('student.account.sections.profile')
                    @break

                    @case('settings')
                        @include('student.account.sections.settings')
                    @break

                    @case('orders')
                        @include('student.account.sections.orders')
                    @break

                    @case('payments')
                        @include('student.account.sections.payments')
                    @break

                    @default
                        @include('student.account.sections.profile')
                @endswitch
            </div>
        </div>
    </div>

    <x-frontend.site.partials.footer />

    <x-slot:scripts>
        <script>
            // Handle section navigation with URL updates
            document.querySelectorAll('.nav-link[href*="section="]').forEach(link => {
                link.addEventListener('click', event => {
                    // Let the normal navigation happen, just add smooth loading effect
                    event.target.closest('.nav-link').style.opacity = '0.7';
                });
            });

            // Auto-dismiss alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert:not(.alert-persistent)');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Add smooth transitions for cards
            document.querySelectorAll('.modern-card').forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-4px)';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0)';
                });
            });
        </script>
    </x-slot:scripts>
</x-frontend.site.site-wrapper>
