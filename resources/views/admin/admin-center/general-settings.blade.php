@extends('adminlte::page')

@section('title', 'General Settings')

@section('content_header')
    <h1>General Settings</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Application Settings -->
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> Application Settings
                    </h3>
                </div>
                <form action="{{ route('admin.admin-center.general-settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group" value="app">

                    <div class="card-body">
                        <div class="form-group">
                            <label for="app_name">Application Name</label>
                            <input type="text" class="form-control" id="app_name"
                                   name="settings[app_name]"
                                   value="{{ config('app.name', 'Frost') }}"
                                   placeholder="Frost">
                            <small class="form-text text-muted">The name of your application</small>
                        </div>

                        <div class="form-group">
                            <label for="app_url">Application URL</label>
                            <input type="url" class="form-control" id="app_url"
                                   name="settings[app_url]"
                                   value="{{ config('app.url', url('/')) }}"
                                   placeholder="https://frost.test">
                            <small class="form-text text-muted">The base URL of your application</small>
                        </div>

                        <div class="form-group">
                            <label for="app_env">Environment</label>
                            <select class="form-control" id="app_env" name="settings[app_env]">
                                <option value="local" {{ config('app.env') == 'local' ? 'selected' : '' }}>Local (Development)</option>
                                <option value="staging" {{ config('app.env') == 'staging' ? 'selected' : '' }}>Staging</option>
                                <option value="production" {{ config('app.env') == 'production' ? 'selected' : '' }}>Production</option>
                            </select>
                            <small class="form-text text-muted">Current environment mode</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="app_debug"
                                       name="settings[app_debug]" value="1"
                                       {{ config('app.debug') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="app_debug">
                                    Debug Mode
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Disable in production!</span>
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="app_timezone">Timezone</label>
                            <select class="form-control" id="app_timezone" name="settings[app_timezone]">
                                <option value="UTC" {{ config('app.timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ config('app.timezone') == 'America/New_York' ? 'selected' : '' }}>America/New York (EST)</option>
                                <option value="America/Chicago" {{ config('app.timezone') == 'America/Chicago' ? 'selected' : '' }}>America/Chicago (CST)</option>
                                <option value="America/Denver" {{ config('app.timezone') == 'America/Denver' ? 'selected' : '' }}>America/Denver (MST)</option>
                                <option value="America/Los_Angeles" {{ config('app.timezone') == 'America/Los_Angeles' ? 'selected' : '' }}>America/Los Angeles (PST)</option>
                            </select>
                            <small class="form-text text-muted">Default timezone for the application</small>
                        </div>

                        @if($appSettings->count() > 0)
                            <hr>
                            <h5>Custom App Settings</h5>
                            @foreach($appSettings as $setting)
                                <div class="form-group">
                                    <label>{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                    <input type="text" class="form-control"
                                           name="custom_settings[{{ $setting->key }}]"
                                           value="{{ $setting->value }}">
                                    <small class="form-text text-muted">Key: {{ $setting->key }}</small>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save App Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Authentication Settings -->
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lock"></i> Authentication Settings
                    </h3>
                </div>
                <form action="{{ route('admin.admin-center.general-settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="group" value="auth">

                    <div class="card-body">
                        <h5>Password Requirements</h5>

                        <div class="form-group">
                            <label for="password_min_length">Minimum Password Length</label>
                            <input type="number" class="form-control" id="password_min_length"
                                   name="settings[password_min_length]"
                                   value="{{ $authSettings->where('key', 'password_min_length')->first()->value ?? 8 }}"
                                   min="6" max="32">
                            <small class="form-text text-muted">Minimum characters required (6-32)</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="password_require_uppercase"
                                       name="settings[password_require_uppercase]" value="1"
                                       {{ ($authSettings->where('key', 'password_require_uppercase')->first()->value ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="password_require_uppercase">
                                    Require Uppercase Letters
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="password_require_lowercase"
                                       name="settings[password_require_lowercase]" value="1"
                                       {{ ($authSettings->where('key', 'password_require_lowercase')->first()->value ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="password_require_lowercase">
                                    Require Lowercase Letters
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="password_require_numbers"
                                       name="settings[password_require_numbers]" value="1"
                                       {{ ($authSettings->where('key', 'password_require_numbers')->first()->value ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="password_require_numbers">
                                    Require Numbers
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="password_require_symbols"
                                       name="settings[password_require_symbols]" value="1"
                                       {{ ($authSettings->where('key', 'password_require_symbols')->first()->value ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="password_require_symbols">
                                    Require Special Characters
                                </label>
                            </div>
                        </div>

                        <hr>
                        <h5>Session Settings</h5>

                        <div class="form-group">
                            <label for="session_lifetime">Session Lifetime (minutes)</label>
                            <input type="number" class="form-control" id="session_lifetime"
                                   name="settings[session_lifetime]"
                                   value="{{ config('session.lifetime', 120) }}"
                                   min="5" max="1440">
                            <small class="form-text text-muted">How long sessions remain active (5-1440 minutes)</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="expire_on_close"
                                       name="settings[expire_on_close]" value="1"
                                       {{ config('session.expire_on_close') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="expire_on_close">
                                    Expire Session on Browser Close
                                </label>
                            </div>
                        </div>

                        <hr>
                        <h5>Security Settings</h5>

                        <div class="form-group">
                            <label for="max_login_attempts">Max Login Attempts</label>
                            <input type="number" class="form-control" id="max_login_attempts"
                                   name="settings[max_login_attempts]"
                                   value="5" min="3" max="10">
                            <small class="form-text text-muted">Failed attempts before lockout (3-10)</small>
                        </div>

                        <div class="form-group">
                            <label for="lockout_duration">Lockout Duration (minutes)</label>
                            <input type="number" class="form-control" id="lockout_duration"
                                   name="settings[lockout_duration]"
                                   value="15" min="5" max="60">
                            <small class="form-text text-muted">How long users are locked out (5-60 minutes)</small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save"></i> Save Auth Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row">
        <div class="col-12">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> System Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">PHP Version:</dt>
                                <dd class="col-sm-7">{{ PHP_VERSION }}</dd>

                                <dt class="col-sm-5">Laravel Version:</dt>
                                <dd class="col-sm-7">{{ app()->version() }}</dd>

                                <dt class="col-sm-5">Database:</dt>
                                <dd class="col-sm-7">{{ config('database.default') }} ({{ config('database.connections.'.config('database.default').'.driver') }})</dd>

                                <dt class="col-sm-5">Cache Driver:</dt>
                                <dd class="col-sm-7">{{ config('cache.default') }}</dd>

                                <dt class="col-sm-5">Queue Driver:</dt>
                                <dd class="col-sm-7">{{ config('queue.default') }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Mail Driver:</dt>
                                <dd class="col-sm-7">{{ config('mail.default') }}</dd>

                                <dt class="col-sm-5">Session Driver:</dt>
                                <dd class="col-sm-7">{{ config('session.driver') }}</dd>

                                <dt class="col-sm-5">Broadcast Driver:</dt>
                                <dd class="col-sm-7">{{ config('broadcasting.default') }}</dd>

                                <dt class="col-sm-5">Filesystem Driver:</dt>
                                <dd class="col-sm-7">{{ config('filesystems.default') }}</dd>

                                <dt class="col-sm-5">App Environment:</dt>
                                <dd class="col-sm-7">
                                    <span class="badge badge-{{ config('app.env') == 'production' ? 'success' : (config('app.env') == 'staging' ? 'warning' : 'info') }}">
                                        {{ config('app.env') }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Note:</strong> Some settings require .env file changes and application restart to take effect.
                        Database settings are managed through the <code>settings</code> table.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Mode -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Maintenance Mode
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        $inMaintenance = app()->isDownForMaintenance();
                    @endphp

                    @if($inMaintenance)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <strong>Application is currently in maintenance mode!</strong>
                        </div>
                        <button class="btn btn-success btn-block" onclick="disableMaintenanceMode()">
                            <i class="fas fa-toggle-on"></i> Disable Maintenance Mode
                        </button>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            Application is currently online and accessible.
                        </div>
                        <button class="btn btn-warning btn-block" onclick="enableMaintenanceMode()">
                            <i class="fas fa-toggle-off"></i> Enable Maintenance Mode
                        </button>
                    @endif

                    <hr>
                    <p class="text-muted">
                        <small>
                            Maintenance mode prevents access to the application for all users except admins.
                            Use this when performing system updates or maintenance.
                        </small>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trash-alt"></i> Clear Cache
                    </h3>
                </div>
                <div class="card-body">
                    <p>Clear system caches to apply configuration changes or free up resources.</p>

                    <div class="btn-group-vertical btn-block">
                        <button class="btn btn-outline-danger" onclick="clearCache('config')">
                            <i class="fas fa-cog"></i> Clear Config Cache
                        </button>
                        <button class="btn btn-outline-danger" onclick="clearCache('route')">
                            <i class="fas fa-route"></i> Clear Route Cache
                        </button>
                        <button class="btn btn-outline-danger" onclick="clearCache('view')">
                            <i class="fas fa-eye"></i> Clear View Cache
                        </button>
                        <button class="btn btn-outline-danger" onclick="clearCache('all')">
                            <i class="fas fa-broom"></i> Clear All Caches
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function enableMaintenanceMode() {
        if (confirm('Are you sure you want to enable maintenance mode? This will make the site inaccessible to users.')) {
            alert('Maintenance mode feature requires artisan command implementation.');
            // TODO: Implement via AJAX to run: php artisan down
        }
    }

    function disableMaintenanceMode() {
        if (confirm('Are you sure you want to disable maintenance mode and bring the site back online?')) {
            alert('Maintenance mode feature requires artisan command implementation.');
            // TODO: Implement via AJAX to run: php artisan up
        }
    }

    function clearCache(type) {
        if (confirm('Are you sure you want to clear the ' + type + ' cache?')) {
            alert('Cache clearing feature requires artisan command implementation for: php artisan ' + type + ':clear');
            // TODO: Implement via AJAX to run appropriate artisan command
        }
    }
</script>
@stop
