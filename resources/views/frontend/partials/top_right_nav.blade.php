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
        <span class="user-profile text-white mr-2" style="margin-right: 10px;">Hello, {{ Auth::user()->fname }}!</span>
        @impersonating($guard = null)
            <a href="{{ route('impersonate.account.leave') }}">
                <button class="btn btn-sm btn-danger m-1" data-toggle="tooltip" data-placement="top" title="Leave Impersonation">
                    <i class="fas fa-user-secret"></i> <span class="button-text">Leave Impersonation</span>
                </button>
            </a>
        @endImpersonating
        <a href="{{ url('account') }}" class="btn-primary acircle m-1">
            <i class="fa fa-user"></i>
        </a>
        <a href="{{ url('account/settings') }}" class="btn-primary acircle m-1">
            <i class="fa fa-cog"></i>
        </a>
        <form action="{{ url('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-danger acircle m-1">
                <i class="fas fa-power-off"></i>
            </button>
        </form>
    @endauth
</div>
