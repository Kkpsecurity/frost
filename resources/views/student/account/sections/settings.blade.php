{{-- Modern Settings Section Content --}}
<div class="row">
    <div class="col-lg-8">
        {{-- Email Preferences --}}
        <div class="modern-card mb-4">
            <div class="card-body">
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-envelope"></i>Email Preferences
                    </div>

                    <form action="{{ route('account.settings.update') }}" method="POST" id="settingsForm">
                        @csrf
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="email_opt_in" name="email_opt_in"
                                   {{ $settingsData['email_preferences']['email_opt_in'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_opt_in">
                                <strong>Email Notifications</strong>
                                <br><small class="text-muted">Receive email notifications about course updates and announcements</small>
                            </label>
                        </div>

                        <hr class="my-4">

                        {{-- Notification Preferences --}}
                        <h6 class="mb-3">Notification Settings</h6>

                        @foreach($settingsData['notification_settings'] as $key => $enabled)
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox"
                                       id="notification_{{ $key }}" name="notifications[{{ $key }}]"
                                       {{ $enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="notification_{{ $key }}">
                                    <strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong>
                                    <br><small class="text-muted">
                                        @switch($key)
                                            @case('course_updates')
                                                Get notified when there are updates to your enrolled courses
                                                @break
                                            @case('assignment_reminders')
                                                Receive reminders about upcoming assignments and deadlines
                                                @break
                                            @case('system_announcements')
                                                Important system-wide announcements and maintenance notices
                                                @break
                                            @default
                                                Configure {{ str_replace('_', ' ', $key) }} notifications
                                        @endswitch
                                    </small>
                                </label>
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn-modern btn-modern-primary">
                                <i class="fas fa-save"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Privacy & Security --}}
        <div class="modern-card">
            <div class="card-body">
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-shield-alt"></i>Privacy & Security
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Profile Visibility</label>
                            <select class="form-control" disabled>
                                <option value="private" selected>Private</option>
                                <option value="public">Public</option>
                            </select>
                            <small class="text-muted">Currently set to private (feature coming soon)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Two-Factor Authentication</label>
                            <div class="d-flex align-items-center mb-2">
                                <span class="summary-badge" style="background: #fef3c7; color: #92400e;">Not Enabled</span>
                                <button class="btn btn-outline-primary btn-sm ms-2" disabled>
                                    <i class="fas fa-plus me-1"></i>Enable 2FA
                                </button>
                            </div>
                            <small class="text-muted">Feature coming soon</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Password & Security</h6>
                    <div class="mb-3">
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="fas fa-key me-1"></i>Change Password
                        </button>
                        <small class="text-muted ms-2">Password management coming soon</small>
                    </div>

                    <div class="mb-3">
                        <button class="btn btn-outline-danger" disabled>
                            <i class="fas fa-sign-out-alt me-1"></i>Log Out All Devices
                        </button>
                        <small class="text-muted ms-2">Security feature coming soon</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- User Preferences Summary --}}
        <div class="modern-card mb-4">
            <div class="card-body">
                <div class="section-title">
                    <i class="fas fa-user-cog"></i>Current Preferences
                </div>

                @if(!empty($settingsData['preferences']))
                    @foreach($settingsData['preferences'] as $key => $value)
                        <div class="summary-item">
                            <span class="summary-label">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                            <span class="summary-value">{{ $value }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted small mb-0">No custom preferences set</p>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="modern-card">
            <div class="card-body">
                <div class="section-title">
                    <i class="fas fa-bolt"></i>Quick Actions
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" disabled>
                        <i class="fas fa-download me-1"></i>Export My Data
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" disabled>
                        <i class="fas fa-history me-1"></i>Activity Log
                    </button>
                    <button class="btn btn-outline-warning btn-sm" disabled>
                        <i class="fas fa-trash me-1"></i>Delete Account
                    </button>
                </div>
                <small class="text-muted d-block mt-2 text-center">Features coming soon</small>
            </div>
        </div>
    </div>
</div>
