{{-- Register Page --}}
<x-frontend.site.site-wrapper :title="'Register - ' . config('app.name')">
    <x-slot:head>
        <meta name="description"
            content="Create your account to access security training courses and certification programs">
        <meta name="keywords" content="register, security training, course enrollment, certification signup">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0 frost-secondary-bg" style="min-height: calc(100vh - 200px);">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-10">
                    {{-- Register Card --}}
                    <div class="card border-0 shadow-lg"
                        style="background: rgba(255, 255, 255, 0.95); border-radius: 15px;">
                        <div class="card-header text-center py-4"
                            style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); border-radius: 15px 15px 0 0;">
                            <h3 class="text-white mb-2">
                                <i class="fas fa-user-plus me-2"></i>
                                {{ __('frontend.auth.create_account') }}
                            </h3>
                            <p class="text-white-50 mb-0">{{ __('frontend.auth.create_account_subtitle') }}</p>
                        </div>

                        <div class="card-body p-5">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                {{-- Name --}}
                                <div class="mb-4">
                                    <label for="name" class="form-label text-dark">
                                        <i class="fas fa-user me-2 text-info"></i>{{ __('frontend.auth.full_name') }}
                                    </label>
                                    <input id="name" type="text"
                                        class="form-control form-control-lg @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name') }}" required autocomplete="name"
                                        autofocus placeholder="{{ __('frontend.auth.full_name_placeholder') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email Address --}}
                                <div class="mb-4">
                                    <label for="email" class="form-label text-dark">
                                        <i
                                            class="fas fa-envelope me-2 text-info"></i>{{ __('frontend.auth.email_address') }}
                                    </label>
                                    <input id="email" type="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email"
                                        placeholder="{{ __('frontend.auth.email_placeholder') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Password --}}
                                <div class="mb-4">
                                    <label for="password" class="form-label text-dark">
                                        <i class="fas fa-lock me-2 text-info"></i>{{ __('frontend.auth.password') }}
                                    </label>
                                    <input id="password" type="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        name="password" required autocomplete="new-password"
                                        placeholder="{{ __('frontend.auth.new_password_placeholder') }}">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Confirm Password --}}
                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label text-dark">
                                        <i
                                            class="fas fa-lock me-2 text-info"></i>{{ __('frontend.auth.confirm_password') }}
                                    </label>
                                    <input id="password_confirmation" type="password"
                                        class="form-control form-control-lg" name="password_confirmation" required
                                        autocomplete="new-password"
                                        placeholder="{{ __('frontend.auth.confirm_password_placeholder') }}">
                                </div>

                                {{-- Terms and Conditions --}}
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="terms" name="terms"
                                            required>
                                        <label class="form-check-label text-dark" for="terms">
                                            {!! __('frontend.auth.terms_agree', [
                                                'terms' => '<a href="#" class="text-info">' . __('frontend.auth.terms_of_service') . '</a>',
                                                'privacy' => '<a href="#" class="text-info">' . __('frontend.auth.privacy_policy') . '</a>',
                                            ]) !!}
                                        </label>
                                    </div>
                                </div>

                                {{-- Submit Button --}}
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>{{ __('frontend.auth.create_account') }}
                                    </button>
                                </div>
                            </form>

                            {{-- Login Link --}}
                            <div class="text-center mt-4 pt-4" style="border-top: 1px solid rgba(0,0,0,0.1);">
                                <p class="text-muted mb-2">{{ __('frontend.auth.already_have_account') }}</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>{{ __('frontend.auth.sign_in') }}
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
                                    <small>{{ __('frontend.auth.professional_training') }}</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-certificate fa-2x text-info mb-2"></i>
                                <div class="text-white">
                                    <small>{{ __('frontend.auth.state_certification') }}</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-users fa-2x text-info mb-2"></i>
                                <div class="text-white">
                                    <small>{{ __('frontend.auth.expert_instructors') }}</small>
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
