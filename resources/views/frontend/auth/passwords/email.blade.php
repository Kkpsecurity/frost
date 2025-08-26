{{-- Forgot Password Page --}}
<x-frontend.site.site-wrapper :title="'Forgot Password - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Reset your password to regain access to your security training account">
        <meta name="keywords" content="password reset, forgot password, account recovery, security training">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0 frost-secondary-bg" style="min-height: calc(100vh - 200px);">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7 col-sm-10">
                    {{-- Password Reset Card --}}
                    <div class="card border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.95); border-radius: 15px;">
                        <div class="card-header text-center py-4" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); border-radius: 15px 15px 0 0;">
                            <h3 class="text-white mb-2">
                                <i class="fas fa-key me-2"></i>
                                Forgot Password
                            </h3>
                            <p class="text-white-50 mb-0">Reset your account password</p>
                        </div>

                        <div class="card-body p-5">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('status') }}
                                </div>
                            @endif

                            <div class="text-center mb-4">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle me-2 text-info"></i>
                                    Enter your email address and we'll send you a password reset link
                                </div>
                            </div>

                            <form method="POST" action="{{ route('password.email') }}">
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

                                {{-- Submit Button --}}
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                                    </button>
                                </div>
                            </form>

                            {{-- Back to Login --}}
                            <div class="text-center mt-4 pt-4" style="border-top: 1px solid rgba(0,0,0,0.1);">
                                <p class="text-muted mb-2">Remember your password?</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Login
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Security Info --}}
                    <div class="text-center mt-4">
                        <div class="bg-dark text-white rounded p-3" style="background: rgba(0, 0, 0, 0.7) !important;">
                            <i class="fas fa-shield-alt fa-lg text-info mb-2"></i>
                            <div>
                                <strong>Secure Password Recovery</strong>
                            </div>
                            <small class="text-white-50">
                                Password reset links are valid for 60 minutes and can only be used once
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-frontend.site.partials.footer />

</x-frontend.site.site-wrapper>
