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
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 8px;
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
    @guest()
        <a href="{{ url('login') }}" class="btn btn-sm btn-primary m-1 login-button" data-toggle="tooltip" data-placement="top"
            title="Login">
            <i class="fas fa-sign-in"></i> <span class="button-text">Login</span>
        </a>
        <a href="{{ url('register') }}" class="btn btn-sm btn-secondary m-1 register-button" data-toggle="tooltip"
            data-placement="top" title="Register">
            <i class="fas fa-lock"></i> <span class="button-text">Register</span>
        </a>
    @endguest


    @auth()
        {{-- Impersonation Notice --}}
        @impersonating($guard = null)
            <a href="{{ route('impersonate.account.leave') }}" class="me-3">
                <button class="btn btn-sm btn-danger" data-toggle="tooltip" data-placement="top"
                    title="Leave Impersonation">
                    <i class="fas fa-user-secret"></i> <span class="button-text">Leave Impersonation</span>
                </button>
            </a>
        @endImpersonating

        {{-- User Section with Avatar and Dropdown --}}
        <div class="user-section">
            {{-- User Avatar --}}
            <div>
                @if(Auth::user()->hasCustomAvatar() || Auth::user()->shouldUseGravatar())
                    <img src="{{ Auth::user()->getAvatar('small') }}"
                         alt="{{ Auth::user()->fname }} {{ Auth::user()->lname }}"
                         class="user-avatar"
                         title="{{ Auth::user()->fname }} {{ Auth::user()->lname }}">
                @else
                    <div class="user-avatar-fallback"
                         title="{{ Auth::user()->fname }} {{ Auth::user()->lname }}">
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
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ url('account') }}">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ url('account') . '?section=settings' }}">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ url('account') . '?section=orders' }}">
                            <i class="fas fa-shopping-bag me-2"></i>Orders
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ url('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    @endauth
</div>
