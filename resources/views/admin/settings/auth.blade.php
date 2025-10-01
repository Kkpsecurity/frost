@extends('adminlte::page')

@section('title', 'Authentication Settings')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Authentication Configuration</h1>
        <div>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Settings
            </a>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Authentication Settings</h3>
                </div>
                <form action="{{ route('admin.settings.update-auth') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <!-- Login Page Settings -->
                        <h5>Login Page Configuration</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="login_title">Login Title</label>
                                    <input type="text" class="form-control @error('login_title') is-invalid @enderror"
                                           id="login_title" name="login_title"
                                           value="{{ old('login_title', $authSettings['login_title'] ?? '') }}"
                                           placeholder="Enter login page title">
                                    @error('login_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Main title displayed on login page</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="login_subtitle">Login Subtitle</label>
                                    <input type="text" class="form-control @error('login_subtitle') is-invalid @enderror"
                                           id="login_subtitle" name="login_subtitle"
                                           value="{{ old('login_subtitle', $authSettings['login_subtitle'] ?? '') }}"
                                           placeholder="Enter login page subtitle">
                                    @error('login_subtitle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Subtitle or description on login page</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Authentication Features -->
                        <h5>Authentication Features</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="password_reset_enabled"
                                               name="password_reset_enabled" value="1"
                                               {{ old('password_reset_enabled', $authSettings['password_reset_enabled'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="password_reset_enabled">
                                            Enable Password Reset
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Allow users to reset their passwords via email</small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="registration_enabled"
                                               name="registration_enabled" value="1"
                                               {{ old('registration_enabled', $authSettings['registration_enabled'] ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="registration_enabled">
                                            Enable Registration
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Allow new users to register accounts</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="remember_me_enabled"
                                               name="remember_me_enabled" value="1"
                                               {{ old('remember_me_enabled', $authSettings['remember_me_enabled'] ?? true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="remember_me_enabled">
                                            Enable "Remember Me"
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Show "Remember Me" checkbox on login</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Password Requirements -->
                        <h5>Password Requirements</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_min_length">Minimum Length</label>
                                    <input type="number" class="form-control @error('password_min_length') is-invalid @enderror"
                                           id="password_min_length" name="password_min_length"
                                           value="{{ old('password_min_length', $authSettings['password_min_length'] ?? 8) }}"
                                           min="6" max="128">
                                    @error('password_min_length')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum password length (6-128 characters)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Character Requirements</label>
                                    <div class="mt-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="password_require_uppercase"
                                                   name="password_require_uppercase" value="1"
                                                   {{ old('password_require_uppercase', $authSettings['password_require_uppercase'] ?? false) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="password_require_uppercase">
                                                Require uppercase letters (A-Z)
                                            </label>
                                        </div>

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="password_require_lowercase"
                                                   name="password_require_lowercase" value="1"
                                                   {{ old('password_require_lowercase', $authSettings['password_require_lowercase'] ?? false) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="password_require_lowercase">
                                                Require lowercase letters (a-z)
                                            </label>
                                        </div>

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="password_require_numbers"
                                                   name="password_require_numbers" value="1"
                                                   {{ old('password_require_numbers', $authSettings['password_require_numbers'] ?? false) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="password_require_numbers">
                                                Require numbers (0-9)
                                            </label>
                                        </div>

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="password_require_symbols"
                                                   name="password_require_symbols" value="1"
                                                   {{ old('password_require_symbols', $authSettings['password_require_symbols'] ?? false) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="password_require_symbols">
                                                Require symbols (!@#$%^&*)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Authentication Settings
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Configuration</h3>
                </div>
                <div class="card-body">
                    <h6>Authentication Status</h6>
                    <ul class="list-unstyled">
                        <li>
                            <i class="fas fa-{{ ($authSettings['password_reset_enabled'] ?? false) ? 'check text-success' : 'times text-danger' }}"></i>
                            Password Reset: {{ ($authSettings['password_reset_enabled'] ?? false) ? 'Enabled' : 'Disabled' }}
                        </li>
                        <li>
                            <i class="fas fa-{{ ($authSettings['registration_enabled'] ?? false) ? 'check text-success' : 'times text-danger' }}"></i>
                            Registration: {{ ($authSettings['registration_enabled'] ?? false) ? 'Enabled' : 'Disabled' }}
                        </li>
                        <li>
                            <i class="fas fa-{{ ($authSettings['remember_me_enabled'] ?? true) ? 'check text-success' : 'times text-danger' }}"></i>
                            Remember Me: {{ ($authSettings['remember_me_enabled'] ?? true) ? 'Enabled' : 'Disabled' }}
                        </li>
                    </ul>

                    <h6>Password Policy</h6>
                    <ul class="list-unstyled">
                        <li><strong>Minimum Length:</strong> {{ $authSettings['password_min_length'] ?? 8 }} characters</li>
                        <li>
                            <i class="fas fa-{{ ($authSettings['password_require_uppercase'] ?? false) ? 'check text-success' : 'times text-muted' }}"></i>
                            Uppercase required
                        </li>
                        <li>
                            <i class="fas fa-{{ ($authSettings['password_require_lowercase'] ?? false) ? 'check text-success' : 'times text-muted' }}"></i>
                            Lowercase required
                        </li>
                        <li>
                            <i class="fas fa-{{ ($authSettings['password_require_numbers'] ?? false) ? 'check text-success' : 'times text-muted' }}"></i>
                            Numbers required
                        </li>
                        <li>
                            <i class="fas fa-{{ ($authSettings['password_require_symbols'] ?? false) ? 'check text-success' : 'times text-muted' }}"></i>
                            Symbols required
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Security Information</h3>
                </div>
                <div class="card-body">
                    <h6>Laravel Configuration</h6>
                    <ul class="list-unstyled text-muted" style="font-size: 12px;">
                        <li><strong>Auth Guard:</strong> {{ config('auth.defaults.guard') }}</li>
                        <li><strong>Auth Provider:</strong> {{ config('auth.defaults.provider') }}</li>
                        <li><strong>Hash Driver:</strong> {{ config('hashing.driver') }}</li>
                        <li><strong>Session Driver:</strong> {{ config('session.driver') }}</li>
                        <li><strong>Session Lifetime:</strong> {{ config('session.lifetime') }} minutes</li>
                    </ul>

                    <h6 class="mt-3">Security Recommendations</h6>
                    <ul class="text-muted" style="font-size: 12px;">
                        <li>• Use strong password requirements</li>
                        <li>• Enable password reset for user convenience</li>
                        <li>• Consider two-factor authentication</li>
                        <li>• Regular security audits</li>
                        <li>• Monitor login attempts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function resetForm() {
            if (confirm('Are you sure you want to reset the authentication settings?')) {
                document.querySelector('form').reset();
            }
        }

        // Update password requirements preview
        function updatePasswordPreview() {
            const minLength = document.getElementById('password_min_length').value;
            const requireUpper = document.getElementById('password_require_uppercase').checked;
            const requireLower = document.getElementById('password_require_lowercase').checked;
            const requireNumbers = document.getElementById('password_require_numbers').checked;
            const requireSymbols = document.getElementById('password_require_symbols').checked;

            // This could be expanded to show a live preview of password requirements
            console.log('Password requirements updated:', {
                minLength,
                requireUpper,
                requireLower,
                requireNumbers,
                requireSymbols
            });
        }

        // Add event listeners for real-time preview
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('password_min_length').addEventListener('input', updatePasswordPreview);
            document.getElementById('password_require_uppercase').addEventListener('change', updatePasswordPreview);
            document.getElementById('password_require_lowercase').addEventListener('change', updatePasswordPreview);
            document.getElementById('password_require_numbers').addEventListener('change', updatePasswordPreview);
            document.getElementById('password_require_symbols').addEventListener('change', updatePasswordPreview);
        });
    </script>
@stop
