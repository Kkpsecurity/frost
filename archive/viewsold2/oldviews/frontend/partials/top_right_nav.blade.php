<div class="header-user">
    @guest()
        <div class="user-actions">
            <a href="{{ url('login') }}" class="nav-link login-button" data-toggle="tooltip" data-placement="top"
                title="Login">
                <i class="fas fa-sign-in"></i> <span class="button-text">Login</span>
            </a>
            <a href="{{ url('register') }}" class="nav-link register-button" data-toggle="tooltip"
                data-placement="top" title="Register">
                <i class="fas fa-lock"></i> <span class="button-text">Register</span>
            </a>
        </div>
    @endguest

    @auth()
        <div class="user-greeting">
            <span class="text-light">Hello, {{ Auth::user()->fname }}!</span>
        </div>
        <div class="user-actions">
            @impersonating($guard = null)
                <a href="{{ route('impersonate.account.leave') }}" class="user-btn" data-toggle="tooltip"
                   data-placement="top" title="Leave Impersonation">
                    <i class="fas fa-user-secret"></i>
                </a>
            @endImpersonating
            <a href="{{ url('account') }}" class="user-btn" data-toggle="tooltip" data-placement="top" title="Account">
                <i class="fa fa-user"></i>
            </a>
            <a href="{{ url('account/settings') }}" class="user-btn" data-toggle="tooltip" data-placement="top" title="Settings">
                <i class="fa fa-cog"></i>
            </a>
            <form action="{{ url('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="user-btn" data-toggle="tooltip" data-placement="top" title="Logout">
                    <i class="fas fa-power-off"></i>
                </button>
            </form>
        </div>
    @endauth
</div>
