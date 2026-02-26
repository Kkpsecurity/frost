{{-- Reset Password Page --}}
<x-frontend.site.site-wrapper :title="'Reset Password - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Create a new password for your security training account">
        <meta name="keywords" content="reset password, new password, account security, password recovery">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0 frost-secondary-bg" style="min-height: calc(100vh - 200px);">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7 col-sm-10">
                    {{-- Password Reset Form Card --}}
                    <div class="card border-0 shadow-lg"
                        style="background: rgba(255, 255, 255, 0.95); border-radius: 15px;">
                        <div class="card-header text-center py-4"
                            style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); border-radius: 15px 15px 0 0;">
                            <h3 class="text-white mb-2">
                                <i class="fas fa-lock me-2"></i>
                                {{ __('frontend.auth.reset_password') }}
                            </h3>
                            <p class="text-white-50 mb-0">{{ __('frontend.auth.create_new_password') }}</p>
                        </div>

                        <div class="card-body p-5">
                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf

                                <input type="hidden" name="token" value="{{ $token }}">

                                {{-- Email Address --}}
                                <div class="mb-4">
                                    <label for="email" class="form-label text-dark">
                                        <i
                                            class="fas fa-envelope me-2 text-info"></i>{{ __('frontend.auth.email_address') }}
                                    </label>
                                    <input id="email" type="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        name="email" value="{{ $email ?? old('email') }}" required
                                        autocomplete="email" autofocus
                                        placeholder="{{ __('frontend.auth.email_placeholder') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- New Password --}}
                                <div class="mb-4">
                                    <label for="password" class="form-label text-dark">
                                        <i class="fas fa-lock me-2 text-info"></i>{{ __('frontend.auth.new_password') }}
                                    </label>
                                    <div class="input-group">
                                        <input id="password" type="password"
                                            class="form-control form-control-lg @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="new-password"
                                            placeholder="{{ __('frontend.auth.new_password_placeholder_reset') }}">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Confirm New Password --}}
                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label text-dark">
                                        <i
                                            class="fas fa-lock me-2 text-info"></i>{{ __('frontend.auth.confirm_new_password') }}
                                    </label>
                                    <input id="password_confirmation" type="password"
                                        class="form-control form-control-lg" name="password_confirmation" required
                                        autocomplete="new-password"
                                        placeholder="{{ __('frontend.auth.confirm_new_password_placeholder') }}">
                                </div>

                                {{-- Submit Button --}}
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check me-2"></i>{{ __('frontend.auth.update_password') }}
                                    </button>
                                </div>
                            </form>

                            {{-- Password Requirements --}}
                            <div class="alert alert-info">
                                <strong><i
                                        class="fas fa-info-circle me-2"></i>{{ __('frontend.auth.password_req_title') }}</strong>
                                <ul class="mb-0 mt-2">
                                    <li>{{ __('frontend.auth.password_req_length') }}</li>
                                    <li>{{ __('frontend.auth.password_req_case') }}</li>
                                    <li>{{ __('frontend.auth.password_req_number') }}</li>
                                    <li>{{ __('frontend.auth.password_req_special') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Security Info --}}
                    <div class="text-center mt-4">
                        <div class="bg-dark text-white rounded p-3" style="background: rgba(0, 0, 0, 0.7) !important;">
                            <i class="fas fa-shield-alt fa-lg text-success mb-2"></i>
                            <div>
                                <strong>{{ __('frontend.auth.secure_password_reset') }}</strong>
                            </div>
                            <small class="text-white-50">
                                {{ __('frontend.auth.password_stored_securely') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-frontend.site.partials.footer />

    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>

</x-frontend.site.site-wrapper>
