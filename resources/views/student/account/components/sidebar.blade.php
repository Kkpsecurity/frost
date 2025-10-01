{{-- Account Sidebar Component --}}
<div class="account-sidebar">
    {{-- User Profile Header --}}
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            @if ($profileData['avatar']['current_avatar'])
                <img src="{{ $profileData['avatar']['current_avatar'] }}" alt="Profile"
                    class="sidebar-avatar me-3">
            @else
                <div
                    class="sidebar-avatar bg-white bg-opacity-10 d-flex align-items-center justify-content-center me-3">
                    <i class="fas fa-user fa-lg text-white-50"></i>
                </div>
            @endif
            <div>
                <div class="sidebar-name text-white">{{ $profileData['basic_info']['full_name'] }}</div>
                <div class="sidebar-role text-white-50">{{ $profileData['basic_info']['role'] }}</div>
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
