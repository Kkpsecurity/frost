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

    .top_right_nav .button-text {
        display: none;
    }

    @media (min-width: 768px) {
        .top_right_nav .button-text {
            display: inline;
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
        <span class="user-profile text-white mr-2" style="margin-right: 10px;">Hello, {{ Auth::user()->fname }}!</span>

        @impersonating($guard = null)
            <a href="{{ route('impersonate.account.leave') }}">
                <button class="btn btn-sm btn-danger m-1" data-toggle="tooltip" data-placement="top"
                    title="Leave Impersonation">
                    <i class="fas fa-user-secret"></i> <span class="button-text">Leave Impersonation</span>
                </button>
            </a>
        @endImpersonating

        <a href="{{ url('account') }}" class="btn-primary acircle m-1">
            <i class="fa fa-user"></i>
        </a>
        <a href="{{ url('account') . '?section=settings' }}" class="btn-primary acircle m-1">
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
