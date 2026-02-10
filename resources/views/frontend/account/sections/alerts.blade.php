{{-- Alerts Section --}}
<div class="alerts-section">
    <h3 class="text-white mb-4">
        <i class="fas fa-bell me-2"></i>Alerts & Notifications
        @if($data['unread_count'] > 0)
            <span class="badge bg-danger ms-2">{{ $data['unread_count'] }}</span>
        @endif
    </h3>

    {{-- Alert Preferences --}}
    <div class="mb-4 pb-4 border-bottom border-secondary">
        <h5 class="text-white mb-3">Alert Preferences</h5>
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="emailAlerts" {{ $data['alert_preferences']['email_alerts'] ? 'checked' : '' }}>
            <label class="form-check-label text-white-50" for="emailAlerts">
                Receive email alerts
            </label>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="browserNotifications" {{ $data['alert_preferences']['browser_notifications'] ? 'checked' : '' }}>
            <label class="form-check-label text-white-50" for="browserNotifications">
                Enable browser notifications
            </label>
        </div>
    </div>

    {{-- Recent Alerts --}}
    <h5 class="text-white mb-3">Recent Alerts</h5>
    @if($data['recent_alerts']->isNotEmpty())
        <div class="list-group list-group-flush">
            @foreach($data['recent_alerts'] as $alert)
                <div class="list-group-item bg-dark border-secondary {{ $alert['read'] ? 'opacity-75' : '' }}">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            @if($alert['type'] === 'info')
                                <i class="fas fa-info-circle fa-2x text-info"></i>
                            @elseif($alert['type'] === 'warning')
                                <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                            @elseif($alert['type'] === 'success')
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            @else
                                <i class="fas fa-bell fa-2x text-secondary"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-white mb-1">{{ $alert['title'] }}</h6>
                            <p class="text-white-50 mb-2">{{ $alert['message'] }}</p>
                            <small class="text-white-50">
                                <i class="far fa-clock me-1"></i>{{ $alert['created_at']->diffForHumans() }}
                            </small>
                        </div>
                        @if(!$alert['read'])
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary">New</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-secondary">
            <i class="fas fa-inbox me-2"></i>
            No alerts at this time.
        </div>
    @endif
</div>
