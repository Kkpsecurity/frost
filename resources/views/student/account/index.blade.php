{{-- Modern Account Dashboard with Sidebar Navigation --}}
<x-frontend.site.site-wrapper :title="'My Account - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Manage your account profile, settings, and course enrollments">
        <style>
            /* Modern Account Dashboard Styling */
            body {
                background: var(--frost-secondary-color);
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            main {
                background: var(--frost-secondary-color) !important;
                min-height: 100vh;
            }

            /* Account Dashboard Layout */
            .account-dashboard {
                display: flex;
                min-height: calc(100vh - 140px);
                background: white;
            }

            /* Sidebar Navigation */
            .account-sidebar {
                width: 280px;
                background: #1e293b;
                color: white;
                border-right: 1px solid #334155;
                min-height: 100%;
                position: sticky;
                top: 0;
                padding: 0;
            }

            .sidebar-header {
                padding: 2rem 1.5rem;
                border-bottom: 1px solid #334155;
                background: linear-gradient(135deg, var(--frost-primary-color, #212a3e) 0%, var(--frost-secondary-color, #394867) 100%);
            }

            .sidebar-avatar {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid rgba(255,255,255,0.2);
                margin-bottom: 1rem;
            }

            .sidebar-name {
                font-size: 1.125rem;
                font-weight: 700;
                color: white;
                margin-bottom: 0.25rem;
            }

            .sidebar-role {
                color: rgba(255,255,255,0.7);
                font-size: 0.875rem;
                font-weight: 500;
            }

            /* Navigation Links */
            .sidebar-nav {
                list-style: none;
                padding: 1rem 0;
                margin: 0;
            }

            .nav-item {
                margin-bottom: 0;
            }

            .nav-link {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 1rem 1.5rem;
                color: #cbd5e1;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.3s ease;
                border-left: 3px solid transparent;
                position: relative;
            }

            .nav-link:hover {
                background: rgba(255,255,255,0.1);
                color: white;
                border-left-color: rgba(255,255,255,0.3);
            }

            .nav-link.active {
                background: rgba(255,255,255,0.1);
                color: white;
                border-left-color: #10b981;
                position: relative;
            }

            .nav-link.active::before {
                content: '';
                position: absolute;
                right: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                background: #10b981;
            }

            .nav-icon {
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
            }

            /* Main Content Area */
            .account-content {
                flex: 1;
                background: #f8fafc;
                min-height: 100%;
                overflow-x: auto;
            }

            .content-header {
                background: white;
                border-bottom: 1px solid #e2e8f0;
                padding: 2rem;
                margin-bottom: 0;
            }

            .content-title {
                font-size: 1.75rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
                display: flex;
                align-items: center;
                gap: 12px;
                color: #1e293b;
            }

            .content-description {
                color: #64748b;
                font-size: 1rem;
                margin: 0;
            }

            .content-body {
                padding: 2rem;
            }

            /* Modern Cards */
            .modern-card {
                background: white;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                overflow: hidden;
            }

            .modern-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 40px rgba(0,0,0,0.12);
            }

            .modern-card .card-body {
                padding: 2rem;
            }

            /* Profile Avatar Section */
            .profile-avatar-section {
                text-align: center;
                position: relative;
            }

            .profile-avatar {
                width: 120px;
                height: 120px;
                object-fit: cover;
                border-radius: 50%;
                border: 4px solid white;
                box-shadow: 0 8px 32px rgba(0,0,0,0.12);
                margin-bottom: 1.5rem;
            }

            .profile-name {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1e293b;
                margin-bottom: 0.5rem;
            }

            .profile-role {
                color: #64748b;
                font-weight: 500;
                margin-bottom: 1.5rem;
            }

            /* Status Badges */
            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                border-radius: 50px;
                font-size: 0.875rem;
                font-weight: 500;
                margin: 4px;
            }

            .status-badge.verified {
                background: #dcfce7;
                color: #166534;
            }

            .status-badge.action {
                background: #dbeafe;
                color: #1d4ed8;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .status-badge.action:hover {
                background: #bfdbfe;
                transform: translateY(-1px);
            }

            /* Account Summary Card */
            .summary-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 1rem 0;
                border-bottom: 1px solid #f1f5f9;
            }

            .summary-item:last-child {
                border-bottom: 0;
            }

            .summary-label {
                color: #64748b;
                font-weight: 500;
            }

            .summary-value {
                color: #1e293b;
                font-weight: 600;
            }

            .summary-badge {
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .summary-badge.active {
                background: #dcfce7;
                color: #166534;
            }

            .summary-badge.count {
                background: var(--frost-primary-color, #212a3e);
                color: white;
            }

            /* Form Elements */
            .form-section {
                margin-bottom: 2.5rem;
            }

            .section-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: #1e293b;
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .section-title i {
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, var(--frost-primary-color, #212a3e) 0%, var(--frost-secondary-color, #394867) 100%);
                color: white;
                border-radius: 8px;
                font-size: 0.875rem;
            }

            .form-control {
                border: 2px solid #e2e8f0;
                border-radius: 12px;
                padding: 12px 16px;
                font-weight: 500;
                transition: all 0.2s ease;
                background: #f8fafc;
            }

            .form-control:focus {
                border-color: var(--frost-primary-color, #212a3e);
                box-shadow: 0 0 0 3px rgba(33, 42, 62, 0.1);
                background: white;
            }

            .form-label {
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
            }

            /* Modern Buttons */
            .btn-modern {
                padding: 12px 32px;
                border-radius: 12px;
                font-weight: 600;
                border: 0;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-modern-primary {
                background: linear-gradient(135deg, var(--frost-primary-color, #212a3e) 0%, var(--frost-secondary-color, #394867) 100%);
                color: white;
                box-shadow: 0 4px 16px rgba(33, 42, 62, 0.3);
            }

            .btn-modern-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 32px rgba(33, 42, 62, 0.4);
                color: white;
            }

            /* Tab Content */
            .tab-content {
                min-height: 500px;
            }

            /* Responsive Design */
            @media (max-width: 1024px) {
                .account-dashboard {
                    flex-direction: column;
                }

                .account-sidebar {
                    width: 100%;
                    position: relative;
                    top: 0;
                    min-height: auto;
                }

                .sidebar-nav {
                    display: flex;
                    overflow-x: auto;
                    padding: 1rem 0;
                }

                .nav-item {
                    flex-shrink: 0;
                }

                .nav-link {
                    white-space: nowrap;
                    border-left: none;
                    border-bottom: 3px solid transparent;
                    padding: 1rem 1.5rem;
                }

                .nav-link.active {
                    border-left: none;
                    border-bottom-color: #10b981;
                }

                .nav-link.active::before {
                    display: none;
                }
            }

            @media (max-width: 768px) {
                .account-dashboard {
                    min-height: calc(100vh - 120px);
                }

                .sidebar-header {
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    padding: 1.5rem;
                }

                .sidebar-avatar {
                    width: 50px;
                    height: 50px;
                    margin-bottom: 0;
                }

                .content-header {
                    padding: 1.5rem;
                }

                .content-title {
                    font-size: 1.5rem;
                }

                .content-body {
                    padding: 1.5rem;
                }

                .modern-card .card-body {
                    padding: 1.5rem;
                }
            }

            /* Statistics styling */
            .stat-item {
                padding: 1rem;
            }

            .stat-number {
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 0.25rem;
            }

            .stat-label {
                font-size: 0.875rem;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
        </style>
    </x-slot:head>

    <x-frontend.site.partials.header />

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="container-fluid py-2">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container-fluid py-2">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    {{-- Account Dashboard with Sidebar --}}
    <div class="account-dashboard">
        {{-- Sidebar Navigation --}}
        <div class="account-sidebar">
            {{-- User Profile Header --}}
            <div class="sidebar-header">
                <div class="d-flex align-items-center">
                    @if($profileData['avatar']['current_avatar'])
                        <img src="{{ $profileData['avatar']['current_avatar'] }}" alt="Profile" class="sidebar-avatar me-3">
                    @else
                        <div class="sidebar-avatar bg-white bg-opacity-10 d-flex align-items-center justify-content-center me-3">
                            <i class="fas fa-user fa-lg text-white-50"></i>
                        </div>
                    @endif
                    <div>
                        <div class="sidebar-name">{{ $profileData['basic_info']['full_name'] }}</div>
                        <div class="sidebar-role">{{ $profileData['basic_info']['role'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Navigation Menu --}}
            <nav>
                <ul class="sidebar-nav">
                    <li class="nav-item">
                        <a href="{{ route('account.index') }}?section=profile"
                           class="nav-link {{ $activeSection === 'profile' ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('account.index') }}?section=settings"
                           class="nav-link {{ $activeSection === 'settings' ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-cog"></i>
                            </span>
                            Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('account.index') }}?section=orders"
                           class="nav-link {{ $activeSection === 'orders' ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-shopping-bag"></i>
                            </span>
                            Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('account.index') }}?section=payments"
                           class="nav-link {{ $activeSection === 'payments' ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-credit-card"></i>
                            </span>
                            Payments
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        {{-- Main Content Area --}}
        <div class="account-content">
            {{-- Dynamic Content Header --}}
            <div class="content-header">
                @switch($activeSection)
                    @case('profile')
                        <div class="content-title">
                            <i class="fas fa-user"></i>
                            Profile Information
                        </div>
                        <div class="content-description">Manage your personal information and account settings</div>
                        @break
                    @case('settings')
                        <div class="content-title">
                            <i class="fas fa-cog"></i>
                            Account Settings
                        </div>
                        <div class="content-description">Configure your preferences and privacy settings</div>
                        @break
                    @case('orders')
                        <div class="content-title">
                            <i class="fas fa-shopping-bag"></i>
                            Order History
                        </div>
                        <div class="content-description">View your course enrollments and purchase history</div>
                        @break
                    @case('payments')
                        <div class="content-title">
                            <i class="fas fa-credit-card"></i>
                            Payment Methods
                        </div>
                        <div class="content-description">Manage your payment methods and billing information</div>
                        @break
                    @default
                        <div class="content-title">
                            <i class="fas fa-user"></i>
                            Profile Information
                        </div>
                        <div class="content-description">Manage your personal information and account settings</div>
                @endswitch
            </div>

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
