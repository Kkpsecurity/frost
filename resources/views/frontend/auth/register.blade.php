{{-- Register Page --}}
<x-frontend.site.site-wrapper :title="'Register - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Create your account to access security training courses and certification programs">
        <meta name="keywords" content="register, security training, course enrollment, certification signup">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0 frost-secondary-bg" style="min-height: calc(100vh - 200px);">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-10">
                    {{-- Register Card --}}
                    <div class="card border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.95); border-radius: 15px;">
                        <div class="card-header text-center py-4" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); border-radius: 15px 15px 0 0;">
                            <h3 class="text-white mb-2">
                                <i class="fas fa-user-plus me-2"></i>
                                Create Account
                            </h3>
                            <p class="text-white-50 mb-0">Join our professional security training program</p>
                        </div>

                        <div class="card-body p-5">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                {{-- Name --}}
                                <div class="mb-4">
                                    <label for="name" class="form-label text-dark">
                                        <i class="fas fa-user me-2 text-info"></i>Full Name
                                    </label>
                                    <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                           placeholder="Enter your full name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email Address --}}
                                <div class="mb-4">
                                    <label for="email" class="form-label text-dark">
                                        <i class="fas fa-envelope me-2 text-info"></i>Email Address
                                    </label>
                                    <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email') }}" required autocomplete="email"
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
                                    <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                                           name="password" required autocomplete="new-password"
                                           placeholder="Create a secure password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Confirm Password --}}
                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label text-dark">
                                        <i class="fas fa-lock me-2 text-info"></i>Confirm Password
                                    </label>
                                    <input id="password_confirmation" type="password" class="form-control form-control-lg"
                                           name="password_confirmation" required autocomplete="new-password"
                                           placeholder="Confirm your password">
                                </div>

                                {{-- Terms and Conditions --}}
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                        <label class="form-check-label text-dark" for="terms">
                                            I agree to the <a href="#" class="text-info">Terms of Service</a> and <a href="#" class="text-info">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>

                                {{-- Submit Button --}}
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>Create Account
                                    </button>
                                </div>
                            </form>

                            {{-- Login Link --}}
                            <div class="text-center mt-4 pt-4" style="border-top: 1px solid rgba(0,0,0,0.1);">
                                <p class="text-muted mb-2">Already have an account?</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Info --}}
                    <div class="text-center mt-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-graduation-cap fa-2x text-info mb-2"></i>
                                <div class="text-white">
                                    <small>Professional Training</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-certificate fa-2x text-info mb-2"></i>
                                <div class="text-white">
                                    <small>State Certification</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-users fa-2x text-info mb-2"></i>
                                <div class="text-white">
                                    <small>Expert Instructors</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-frontend.site.partials.footer />

</x-frontend.site.site-wrapper>
