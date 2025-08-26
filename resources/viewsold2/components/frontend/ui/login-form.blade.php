{{-- Login Form Component --}}
<form method="POST" action="{{ route('login') }}" class="login-form">
    @csrf
    
    <div class="form-group mb-3">
        <label for="email" class="form-label text-white mb-2">{{ __('Email Address') }}</label>
        <div class="input-group">
            <span class="input-group-text bg-secondary border-0">
                <i class="fas fa-envelope text-dark"></i>
            </span>
            <input id="email" type="email" 
                   class="form-control border-0 login-input @error('email') is-invalid @enderror" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="email" 
                   autofocus
                   style="background-color: #f8e97a; color: #333;"
                   placeholder="richievc@gmail.com">
        </div>
        @error('email')
            <div class="invalid-feedback d-block">
                <strong>{{ $message }}</strong>
            </div>
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="password" class="form-label text-white mb-2">{{ __('Password') }}</label>
        <div class="input-group">
            <span class="input-group-text bg-secondary border-0">
                <i class="fas fa-lock text-dark"></i>
            </span>
            <input id="password" type="password" 
                   class="form-control border-0 login-input @error('password') is-invalid @enderror" 
                   name="password" 
                   required 
                   autocomplete="current-password"
                   style="background-color: #f8e97a; color: #333;"
                   placeholder="••••••••••••">
        </div>
        @error('password')
            <div class="invalid-feedback d-block">
                <strong>{{ $message }}</strong>
            </div>
        @enderror
    </div>

    <div class="form-group mb-3 d-flex justify-content-between align-items-center">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label text-white" for="remember">
                {{ __('Remember Me') }}
            </label>
        </div>
        
        @if (Route::has('password.request'))
            <a class="text-white-50 text-decoration-none small" href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
            </a>
        @endif
    </div>

    <div class="form-group mb-3">
        <button type="submit" class="btn btn-info w-100 py-2">
            <i class="fas fa-sign-in-alt me-2"></i>{{ __('Login') }}
        </button>
    </div>

    @if (Route::has('register'))
        <div class="form-group text-center">
            <span class="text-white-50">Don't have an account? </span>
            <a href="{{ route('register') }}" class="text-info text-decoration-none">{{ __('Register here') }}</a>
        </div>
    @endif
</form>
