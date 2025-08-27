@props(['user'])

@if($user)
    {{-- User is logged in - Show Quick Profile --}}
    <div class="quick-profile-card">
        <div class="profile-header text-center mb-3">
            <div class="profile-avatar mb-3">
                <img src="{{ $user->getAvatarUrl('small') }}" alt="{{ $user->name }}" class="profile-img">
            </div>
            <h5 class="text-white mb-1">Welcome back!</h5>
            <p class="text-white-50 mb-0">{{ $user->name }}</p>
        </div>

        <div class="profile-actions">
            <div class="row g-2">
                <div class="col-6">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </div>
                <div class="col-6">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm w-100">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@else
    {{-- User is not logged in - Show Quick Login Form --}}
    <div class="quick-login-card">
        <form method="POST" action="{{ route('login') }}" class="quick-login-form">
            @csrf

            {{-- Email Field --}}
            <div class="mb-3">
                <label for="quick-email" class="form-label d-flex justify-start text-white-50 small">Email</label>
                <input id="quick-email" type="email"
                       class="form-control form-control-sm @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autocomplete="email"
                       placeholder="your@email.com">
                @error('email')
                    <div class="invalid-feedback small">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password Field --}}
            <div class="mb-3">
                <label for="quick-password" class="form-label d-flex justify-start text-white-50 small">Password</label>
                <input id="quick-password" type="password"
                       class="form-control form-control-sm @error('password') is-invalid @enderror"
                       name="password"
                       required
                       autocomplete="current-password"
                       placeholder="••••••••">
                @error('password')
                    <div class="invalid-feedback small">{{ $message }}</div>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="quick-remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label text-white-50 small" for="quick-remember">
                        Remember me
                    </label>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-sign-in-alt me-1"></i>Sign In
                </button>
            </div>

            {{-- Quick Links --}}
            <div class="text-center quick-login-links">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('register') }}" class="text-decoration-none">
                        <i class="fas fa-user-plus me-1"></i>Register
                    </a>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-decoration-none">
                            <i class="fas fa-question-circle me-1"></i>Forgot?
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endif

@push('component-styles')
<style>
.quick-profile-card, .quick-login-card {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 1.25rem;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    max-width: 300px;
    margin: 0 auto;
}

.profile-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, 0.3);
    object-fit: cover;
    margin: 0 auto;
}

.quick-login-form .form-control {
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #333;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 8px;
}

.quick-login-form .form-control:focus {
    background: rgba(255, 255, 255, 1);
    border-color: #3498db;
    box-shadow: 0 0 0 0.15rem rgba(52, 152, 219, 0.25);
    outline: none;
}

.quick-login-form .form-control::placeholder {
    color: #999;
    font-size: 0.8rem;
}

.quick-login-form .form-check-input {
    background-color: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    margin-right: 0.5rem;
    width: 14px;
    height: 14px;
}

.quick-login-form .form-check-input:checked {
    background-color: #3498db;
    border-color: #3498db;
}

.quick-login-form .form-label {
    margin-bottom: 0.375rem;
    font-weight: 500;
    font-size: 1rem;
}

.quick-login-form .mb-3 {
    margin-bottom: 0.875rem !important;
}

.quick-login-form .btn-primary {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    border: none;
    padding: 0.6rem 1rem;
    font-weight: 600;
    font-size: 0.875rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.quick-login-form .btn-primary:hover {
    background: linear-gradient(135deg, #2980b9 0%, #1f4e79 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.quick-login-links {
    margin-top: 0.75rem;
}

.quick-login-links a {
    color: #74b9ff;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 500;
    transition: color 0.3s ease;
}

.quick-login-links a:hover {
    color: #0984e3;
    text-decoration: underline;
}

.profile-actions .btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Compact the form spacing */
.login-form-inner .profile-detail h4 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.login-form-inner .profile-detail p {
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .quick-profile-card, .quick-login-card {
        padding: 1rem;
        max-width: 280px;
    }

    .profile-img {
        width: 50px;
        height: 50px;
    }

    .quick-login-form .form-control {
        padding: 0.45rem 0.65rem;
        font-size: 0.8rem;
    }

    .login-form-inner .profile-detail h4 {
        font-size: 1.1rem;
    }

    .login-form-inner .profile-detail p {
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .quick-profile-card, .quick-login-card {
        max-width: 100%;
        margin: 0;
    }
}
</style>
@endpush
