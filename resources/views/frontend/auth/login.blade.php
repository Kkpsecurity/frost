{{-- Login Page --}}
<x-frontend.site.site-wrapper :title="'Login - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Login to access your security training courses and certification programs">
        <meta name="keywords" content="login, security training, course access, certification">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0 frost-secondary-bg" style="min-height: calc(100vh - 200px);">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7 col-sm-9">
                    {{-- Login Card --}}
                    <div class="card border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.95); border-radius: 15px;">
                        <div class="card-header text-center py-4" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); border-radius: 15px 15px 0 0;">
                            <h3 class="text-white mb-2">
                                <i class="fas fa-user-shield me-2"></i>
                                Sign In
                            </h3>
                            <p class="text-white-50 mb-0">Access your security training courses</p>
                        </div>

                        <div class="card-body p-5">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                {{-- Email Address --}}
                                <div class="mb-4">
                                    <label for="email" class="form-label text-dark">
                                        <i class="fas fa-envelope me-2 text-info"></i>Email Address
                                    </label>
                                    <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                           placeholder="Enter your email address">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Password --}}
                                <div class="mb-4">
                                    <label for="password" class="form-label text-dark">
                                        <i class="fas fa-lock me-2 text-info"></i>Password
                                    </label>
                                    <div class="position-relative">
                                        <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                                               name="password" required autocomplete="current-password"
                                               placeholder="Enter your password">
                                        <button type="button" class="btn btn-sm position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none;" onclick="togglePassword()">
                                            <i class="fas fa-eye text-muted" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Remember Me --}}
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                </div>

                                {{-- Submit Button --}}
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                    </button>
                                </div>

                                {{-- Auth Links --}}
                                <div class="text-center">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-decoration-none text-info">
                                            <i class="fas fa-question-circle me-1"></i>Forgot your password?
                                        </a>
                                    @endif
                                </div>
                            </form>

                            {{-- Registration Link --}}
                            @if(Route::has('register'))
                                <div class="text-center mt-4 pt-4" style="border-top: 1px solid rgba(0,0,0,0.1);">
                                    <p class="text-muted mb-2">Don't have an account?</p>
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-user-plus me-2"></i>Create Account
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Additional Info --}}
                    <div class="text-center mt-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-shield-alt fa-2x text-info mb-2"></i>
                                <div class="text-white">
                                    <small>Secure Login</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-certificate fa-2x text-info mb-2"></i>
                                <div class="text-white">
                                    <small>State Certified</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-headset fa-2x text-info mb-2"></i>
                                <div class="text-white">
                                    <small>24/7 Support</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-frontend.site.partials.footer />

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>

</x-frontend.site.site-wrapper>
