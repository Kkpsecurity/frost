{{-- Alerts Tab Content - Prepared for Future Implementation --}}
<div class="row">
    <div class="col-lg-8">
        {{-- Placeholder Alert System --}}
        <div class="alert-placeholder mb-4">
            <i class="fas fa-bell fa-3x mb-3"></i>
            <h4>Notifications System Coming Soon!</h4>
            <p class="mb-0">We're building a comprehensive notification system to keep you updated on:</p>
            <ul class="list-unstyled mt-3 mb-0">
                <li><i class="fas fa-check me-2"></i>Course updates and announcements</li>
                <li><i class="fas fa-check me-2"></i>Assignment reminders</li>
                <li><i class="fas fa-check me-2"></i>System maintenance notices</li>
                <li><i class="fas fa-check me-2"></i>Achievement notifications</li>
            </ul>
        </div>

        {{-- Sample Alerts (for demonstration) --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h6>
                <small class="text-muted">Sample notifications</small>
            </div>
            <div class="card-body">
                @foreach($alertsData['recent_alerts'] as $alert)
                    <div class="d-flex align-items-start mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="me-3">
                            @switch($alert['type'])
                                @case('info')
                                    <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    @break
                                @case('warning')
                                    <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    @break
                                @case('success')
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    @break
                                @default
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-bell"></i>
                                    </div>
                            @endswitch
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="mb-0">{{ $alert['title'] }}</h6>
                                <small class="text-muted">{{ $alert['created_at']->diffForHumans() }}</small>
                            </div>
                            <p class="text-muted mb-1">{{ $alert['message'] }}</p>
                            @if(!$alert['read'])
                                <span class="badge bg-primary">New</span>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if($alertsData['recent_alerts']->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-bell-slash fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No notifications yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Alert Preferences --}}
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-cog me-2"></i>Alert Preferences</h6>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="email_alerts"
                           {{ $alertsData['alert_preferences']['email_alerts'] ? 'checked' : '' }} disabled>
                    <label class="form-check-label" for="email_alerts">
                        <strong>Email Alerts</strong>
                        <br><small class="text-muted">Receive alerts via email</small>
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="browser_notifications"
                           {{ $alertsData['alert_preferences']['browser_notifications'] ? 'checked' : '' }} disabled>
                    <label class="form-check-label" for="browser_notifications">
                        <strong>Browser Notifications</strong>
                        <br><small class="text-muted">Push notifications to browser</small>
                    </label>
                </div>

                <small class="text-muted">Settings will be functional when notification system is implemented</small>
            </div>
        </div>

        {{-- Alert Statistics --}}
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Notification Stats</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Total Alerts</span>
                    <span class="fw-bold">{{ $alertsData['recent_alerts']->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Unread</span>
                    <span class="badge bg-danger">{{ $alertsData['unread_count'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">This Week</span>
                    <span class="fw-bold">{{ $alertsData['recent_alerts']->where('created_at', '>=', now()->subWeek())->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
