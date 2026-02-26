<style>
    .top_right_nav {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .top_right_nav .btn {
        margin-left: 5px;
    }

    .top_right_nav .acircle {
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .top_right_nav .acircle i {
        font-size: .8rem;
    }

    .top_right_nav .user-profile {
        font-weight: 500;
    }

    .top_right_nav .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .top_right_nav .user-avatar-fallback {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 14px;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .top_right_nav .user-section {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .top_right_nav .user-name {
        color: white;
        font-weight: 500;
        margin-right: 8px;
    }

    .top_right_nav .user-dropdown {
        position: relative;
    }

    .top_right_nav .user-dropdown .dropdown-menu {
        right: 0;
        left: auto;
        min-width: 200px;
        margin-top: 8px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        border-radius: 8px;
        background: #1a1a2e;
        color: white;
    }

    .top_right_nav .user-dropdown .dropdown-header {
        background: #0f3460;
        color: white;
        padding: 10px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .top_right_nav .user-dropdown .dropdown-header small {
        color: rgba(255, 255, 255, 0.55) !important;
    }

    .top_right_nav .user-dropdown .dropdown-item {
        color: rgba(255, 255, 255, 0.85);
        transition: background 0.2s;
    }

    .top_right_nav .user-dropdown .dropdown-item:hover,
    .top_right_nav .user-dropdown .dropdown-item:focus {
        background: rgba(255, 255, 255, 0.08);
        color: white;
    }

    .top_right_nav .user-dropdown .dropdown-item.text-danger {
        color: #ff6b6b !important;
    }

    .top_right_nav .user-dropdown .dropdown-item.text-danger:hover {
        background: rgba(220, 53, 69, 0.15);
        color: #ff6b6b !important;
    }

    .top_right_nav .user-dropdown .dropdown-divider {
        border-color: rgba(255, 255, 255, 0.1);
    }

    .top_right_nav .notification-icon {
        position: relative;
        margin-right: 15px;
        color: white;
        font-size: 18px;
        cursor: pointer;
        transition: color 0.3s;
        background: none;
        border: none;
        padding: 5px;
    }

    .top_right_nav .notification-icon:hover {
        color: #ffc107;
    }

    .top_right_nav .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        border: 2px solid #1a1a2e;
    }

    .top_right_nav .notification-dropdown {
        position: relative;
    }

    .top_right_nav .notification-dropdown .dropdown-menu {
        right: 0;
        left: auto;
        min-width: 350px;
        max-width: 400px;
        margin-top: 8px;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        max-height: 500px;
        overflow-y: auto;
        background: #1a1a2e;
        color: white;
    }

    .top_right_nav .notification-dropdown .dropdown-header {
        background: #0f3460;
        color: white;
        font-weight: 600;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .top_right_nav .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        cursor: pointer;
        transition: background 0.2s;
        color: white;
        text-decoration: none;
        display: block;
    }

    .top_right_nav .notification-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: white;
    }

    .top_right_nav .notification-item.unread {
        background: rgba(23, 162, 184, 0.1);
    }

    .top_right_nav .notification-item .notification-icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 16px;
    }

    .top_right_nav .notification-item .notification-content {
        flex: 1;
    }

    .top_right_nav .notification-item .notification-title {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .top_right_nav .notification-item .notification-message {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 4px;
    }

    .top_right_nav .notification-item .notification-time {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.5);
    }

    .top_right_nav .notification-footer {
        padding: 10px 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }

    .top_right_nav .notification-footer a,
    .top_right_nav .notification-footer button {
        font-size: 13px;
        padding: 5px 10px;
    }

    .top_right_nav .empty-notifications {
        padding: 40px 20px;
        text-align: center;
        color: rgba(255, 255, 255, 0.5);
    }

    .top_right_nav .empty-notifications i {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.3;
    }

    .top_right_nav .button-text {
        display: none;
    }

    @media (min-width: 768px) {
        .top_right_nav .button-text {
            display: inline;
        }

        .top_right_nav .user-name {
            display: inline;
        }
    }

    @media (max-width: 767px) {
        .top_right_nav .user-name {
            display: none;
        }
    }
</style>

<div class="top_right_nav">

    {{-- Locale Switcher (always visible) --}}
    @php
        $currentLocale = app()->getLocale();
        $nextLocale = $currentLocale === 'en' ? 'es' : 'en';
    @endphp
    <div class="me-2">
        <form action="{{ route('user.locale') }}" method="POST" style="display:inline;margin:0;">
            @csrf
            <input type="hidden" name="locale" value="{{ $nextLocale }}">
            <button type="submit"
                class="btn btn-sm {{ $currentLocale === 'en' ? 'btn-outline-light' : 'btn-outline-warning' }}"
                style="font-size: 11px; padding: 2px 8px; letter-spacing: .5px;"
                title="{{ $currentLocale === 'en' ? __('frontend.nav.switch_to_spanish') : __('frontend.nav.switch_to_english') }}">
                {{ strtoupper($nextLocale) }}
            </button>
        </form>
    </div>

    @guest()
        <a href="{{ url('login') }}" class="btn btn-sm btn-primary m-1 login-button" data-toggle="tooltip"
            data-placement="top" title="{{ __('frontend.nav.login') }}">
            <i class="fas fa-sign-in"></i> <span class="button-text">{{ __('frontend.nav.login') }}</span>
        </a>
        <a href="{{ url('register') }}" class="btn btn-sm btn-secondary m-1 register-button" data-toggle="tooltip"
            data-placement="top" title="{{ __('frontend.nav.register') }}">
            <i class="fas fa-lock"></i> <span class="button-text">{{ __('frontend.nav.register') }}</span>
        </a>
    @endguest


    @auth()
        {{-- Impersonation Notice --}}
        @impersonating($guard = null)
            <a href="{{ route('impersonate.account.leave') }}" class="me-3">
                <button class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top"
                    title="{{ __('frontend.nav.leave_impersonation') }}">
                    <i class="fas fa-user-secret"></i> <span
                        class="button-text">{{ __('frontend.nav.leave_impersonation') }}</span>
                </button>
            </a>
        @endImpersonating

        {{-- User Section with Avatar and Dropdown --}}
        <div class="user-section">
            {{-- Notification Dropdown --}}
            <div class="notification-dropdown dropdown">
                <button class="notification-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                    title="{{ __('frontend.nav.notifications') }}">
                    <i class="fas fa-bell"></i>
                    @php
                        $unreadCount = Auth::user()->unreadNotifications->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="notification-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <div class="dropdown-header">
                            <span>{{ __('frontend.nav.notifications') }}</span>
                            @if ($unreadCount > 0)
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm text-white p-0"
                                        style="text-decoration: none; font-size: 12px;">
                                        {{ __('frontend.nav.mark_all_read') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </li>
                    @php
                        $notifications = Auth::user()->unreadNotifications()->take(6)->get();
                    @endphp
                    @forelse($notifications as $notification)
                        <li>
                            <a href="{{ route('notifications.mark-read', $notification->id) }}"
                                class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}"
                                style="text-decoration: none;">
                                <div class="d-flex">
                                    <div
                                        class="notification-icon-wrapper bg-{{ $notification->data['priority_color'] ?? 'primary' }}">
                                        <i class="fas fa-{{ $notification->data['icon'] ?? 'bell' }}"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title">
                                            {{ $notification->data['title'] ?? __('frontend.nav.notifications') }}
                                        </div>
                                        <div class="notification-message">
                                            {{ Str::limit($notification->data['message'] ?? '', 60) }}
                                        </div>
                                        <div class="notification-time">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li>
                            <div class="empty-notifications">
                                <i class="fas fa-bell-slash"></i>
                                <div>{{ __('frontend.nav.no_notifications') }}</div>
                            </div>
                        </li>
                    @endforelse
                    @if ($notifications->count() > 0)
                        <li>
                            <div class="notification-footer">
                                <a href="{{ route('account.index', ['section' => 'inbox']) }}"
                                    class="btn btn-sm btn-primary flex-grow-1">
                                    {{ __('frontend.nav.view_all_notifications') }}
                                </a>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- User Avatar --}}
            <div>
                @if (Auth::user()->hasCustomAvatar() || Auth::user()->shouldUseGravatar())
                    <img src="{{ Auth::user()->getAvatar('small') }}"
                        alt="{{ Auth::user()->fname }} {{ Auth::user()->lname }}" class="user-avatar"
                        title="{{ Auth::user()->fname }} {{ Auth::user()->lname }}">
                @else
                    <div class="user-avatar-fallback" title="{{ Auth::user()->fname }} {{ Auth::user()->lname }}">
                        {{ substr(Auth::user()->fname, 0, 1) }}{{ substr(Auth::user()->lname, 0, 1) }}
                    </div>
                @endif
            </div>

            {{-- User Name --}}
            <span class="user-name">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</span>

            {{-- User Dropdown Menu --}}
            <div class="user-dropdown dropdown">
                <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                    style="border: none; background: none;">
                    <i class="fas fa-chevron-down"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <div class="dropdown-header">
                            <strong>{{ Auth::user()->fname }} {{ Auth::user()->lname }}</strong>
                            <small class="text-muted d-block">{{ Auth::user()->email }}</small>
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ url('account') }}">
                            <i class="fas fa-user me-2"></i>{{ __('frontend.nav.profile') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ url('account') . '?section=inbox' }}">
                            <i class="fas fa-bell me-2"></i>{{ __('frontend.nav.notifications') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ url('account') . '?section=orders' }}">
                            <i class="fas fa-shopping-bag me-2"></i>{{ __('frontend.nav.orders') }}
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ url('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>{{ __('frontend.nav.logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    @endauth
</div>
