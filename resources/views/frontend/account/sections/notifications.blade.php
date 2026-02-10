{{-- Notifications Section --}}
<div class="notifications-section">
    <h3 class="text-white mb-4">
        <i class="fas fa-bell me-2"></i>Notification Preferences
    </h3>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('account.notifications.update') }}" method="POST" class="notifications-form">
        @csrf

        {{-- Global Notification Settings --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-toggle-on me-2"></i>Global Settings
            </h5>
            <p class="text-white-50 mb-3">Control how you receive notifications across all channels</p>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card bg-dark border-secondary h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-desktop fa-2x text-info mb-3"></i>
                            <h6 class="text-white mb-2">In-App Notifications</h6>
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" id="enableInApp"
                                    name="channels[database]"
                                    {{ $notificationsData['channels']['database'] ?? true ? 'checked' : '' }}>
                            </div>
                            <small class="text-white-50 mt-2 d-block">Show notifications in the app</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark border-secondary h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-envelope fa-2x text-warning mb-3"></i>
                            <h6 class="text-white mb-2">Email Notifications</h6>
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" id="enableEmail" name="channels[mail]"
                                    {{ $notificationsData['channels']['mail'] ?? true ? 'checked' : '' }}>
                            </div>
                            <small class="text-white-50 mt-2 d-block">Receive email notifications</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark border-secondary h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-bell fa-2x text-danger mb-3"></i>
                            <h6 class="text-white mb-2">Browser Push</h6>
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" id="enableBrowser"
                                    name="channels[browser]"
                                    {{ $notificationsData['channels']['browser'] ?? true ? 'checked' : '' }}>
                            </div>
                            <small class="text-white-50 mt-2 d-block">Browser push notifications</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Account & Registration Notifications --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-user-circle me-2 text-primary"></i>Account & Registration
            </h5>
            @php
                $accountNotifications = config('user_notifications.notifications.account', []);
            @endphp
            @foreach ($accountNotifications as $key => $notification)
                @if ($notification['user_controllable'])
                    <div class="d-flex align-items-start mb-3 p-3 rounded bg-dark border border-secondary">
                        <div class="form-check form-switch me-3">
                            <input class="form-check-input" type="checkbox" id="notif_{{ $notification['key'] }}"
                                name="notifications[{{ $notification['key'] }}]"
                                {{ $notificationsData['notifications'][$notification['key']] ?? true ? 'checked' : '' }}>
                        </div>
                        <label class="form-check-label text-white flex-grow-1" for="notif_{{ $notification['key'] }}">
                            <strong>{{ $notification['name'] }}</strong>
                            <span
                                class="badge bg-{{ config('user_notifications.priorities.' . $notification['priority'] . '.color') }} ms-2">
                                {{ ucfirst($notification['priority']) }}
                            </span>
                            <small class="d-block text-white-50 mt-1">
                                Channels: {{ implode(', ', $notification['channels']) }}
                            </small>
                        </label>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Course Enrollment & Purchase Notifications --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-graduation-cap me-2 text-success"></i>Course Enrollment & Purchase
            </h5>
            @php
                $enrollmentNotifications = config('user_notifications.notifications.enrollment', []);
            @endphp
            @foreach ($enrollmentNotifications as $key => $notification)
                @if ($notification['user_controllable'])
                    <div class="d-flex align-items-start mb-3 p-3 rounded bg-dark border border-secondary">
                        <div class="form-check form-switch me-3">
                            <input class="form-check-input" type="checkbox" id="notif_{{ $notification['key'] }}"
                                name="notifications[{{ $notification['key'] }}]"
                                {{ $notificationsData['notifications'][$notification['key']] ?? true ? 'checked' : '' }}>
                        </div>
                        <label class="form-check-label text-white flex-grow-1" for="notif_{{ $notification['key'] }}">
                            <strong>{{ $notification['name'] }}</strong>
                            <span
                                class="badge bg-{{ config('user_notifications.priorities.' . $notification['priority'] . '.color') }} ms-2">
                                {{ ucfirst($notification['priority']) }}
                            </span>
                            <small class="d-block text-white-50 mt-1">
                                Channels: {{ implode(', ', $notification['channels']) }}
                            </small>
                        </label>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Classroom Notifications --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-chalkboard-teacher me-2 text-info"></i>Classroom Experience
            </h5>
            @php
                $classroomNotifications = config('user_notifications.notifications.classroom', []);
            @endphp
            @foreach ($classroomNotifications as $key => $notification)
                @if ($notification['user_controllable'])
                    <div class="d-flex align-items-start mb-3 p-3 rounded bg-dark border border-secondary">
                        <div class="form-check form-switch me-3">
                            <input class="form-check-input" type="checkbox" id="notif_{{ $notification['key'] }}"
                                name="notifications[{{ $notification['key'] }}]"
                                {{ $notificationsData['notifications'][$notification['key']] ?? true ? 'checked' : '' }}>
                        </div>
                        <label class="form-check-label text-white flex-grow-1" for="notif_{{ $notification['key'] }}">
                            <strong>{{ $notification['name'] }}</strong>
                            <span
                                class="badge bg-{{ config('user_notifications.priorities.' . $notification['priority'] . '.color') }} ms-2">
                                {{ ucfirst($notification['priority']) }}
                            </span>
                            <small class="d-block text-white-50 mt-1">
                                Channels: {{ implode(', ', $notification['channels']) }}
                            </small>
                        </label>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Progress & Completion Notifications --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-chart-line me-2 text-warning"></i>Course Progress & Completion
            </h5>
            @php
                $progressNotifications = config('user_notifications.notifications.progress', []);
            @endphp
            @foreach ($progressNotifications as $key => $notification)
                @if ($notification['user_controllable'])
                    <div class="d-flex align-items-start mb-3 p-3 rounded bg-dark border border-secondary">
                        <div class="form-check form-switch me-3">
                            <input class="form-check-input" type="checkbox" id="notif_{{ $notification['key'] }}"
                                name="notifications[{{ $notification['key'] }}]"
                                {{ $notificationsData['notifications'][$notification['key']] ?? true ? 'checked' : '' }}>
                        </div>
                        <label class="form-check-label text-white flex-grow-1"
                            for="notif_{{ $notification['key'] }}">
                            <strong>{{ $notification['name'] }}</strong>
                            <span
                                class="badge bg-{{ config('user_notifications.priorities.' . $notification['priority'] . '.color') }} ms-2">
                                {{ ucfirst($notification['priority']) }}
                            </span>
                            <small class="d-block text-white-50 mt-1">
                                Channels: {{ implode(', ', $notification['channels']) }}
                            </small>
                        </label>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Payment & Billing Notifications --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-credit-card me-2 text-success"></i>Payment & Billing
            </h5>
            @php
                $paymentNotifications = config('user_notifications.notifications.payment', []);
            @endphp
            @foreach ($paymentNotifications as $key => $notification)
                @if ($notification['user_controllable'])
                    <div class="d-flex align-items-start mb-3 p-3 rounded bg-dark border border-secondary">
                        <div class="form-check form-switch me-3">
                            <input class="form-check-input" type="checkbox" id="notif_{{ $notification['key'] }}"
                                name="notifications[{{ $notification['key'] }}]"
                                {{ $notificationsData['notifications'][$notification['key']] ?? true ? 'checked' : '' }}>
                        </div>
                        <label class="form-check-label text-white flex-grow-1"
                            for="notif_{{ $notification['key'] }}">
                            <strong>{{ $notification['name'] }}</strong>
                            <span
                                class="badge bg-{{ config('user_notifications.priorities.' . $notification['priority'] . '.color') }} ms-2">
                                {{ ucfirst($notification['priority']) }}
                            </span>
                            <small class="d-block text-white-50 mt-1">
                                Channels: {{ implode(', ', $notification['channels']) }}
                            </small>
                        </label>
                    </div>
                @endif
            @endforeach
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <small><strong>Note:</strong> Critical payment notifications (payment failures, refunds, balance due)
                    are always sent for your security and cannot be disabled.</small>
            </div>
        </div>

        {{-- Exam Notifications --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-clipboard-check me-2 text-danger"></i>Exams & Assessments
            </h5>
            @php
                $examNotifications = config('user_notifications.notifications.exams', []);
            @endphp
            @foreach ($examNotifications as $key => $notification)
                @if ($notification['user_controllable'])
                    <div class="d-flex align-items-start mb-3 p-3 rounded bg-dark border border-secondary">
                        <div class="form-check form-switch me-3">
                            <input class="form-check-input" type="checkbox" id="notif_{{ $notification['key'] }}"
                                name="notifications[{{ $notification['key'] }}]"
                                {{ $notificationsData['notifications'][$notification['key']] ?? true ? 'checked' : '' }}>
                        </div>
                        <label class="form-check-label text-white flex-grow-1"
                            for="notif_{{ $notification['key'] }}">
                            <strong>{{ $notification['name'] }}</strong>
                            <span
                                class="badge bg-{{ config('user_notifications.priorities.' . $notification['priority'] . '.color') }} ms-2">
                                {{ ucfirst($notification['priority']) }}
                            </span>
                            <small class="d-block text-white-50 mt-1">
                                Channels: {{ implode(', ', $notification['channels']) }}
                            </small>
                        </label>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- System Notifications --}}
        <div class="mb-4">
            <h5 class="text-white mb-3">
                <i class="fas fa-cog me-2 text-secondary"></i>System & Administrative
            </h5>
            @php
                $systemNotifications = config('user_notifications.notifications.system', []);
            @endphp
            @foreach ($systemNotifications as $key => $notification)
                @if ($notification['user_controllable'])
                    <div class="d-flex align-items-start mb-3 p-3 rounded bg-dark border border-secondary">
                        <div class="form-check form-switch me-3">
                            <input class="form-check-input" type="checkbox" id="notif_{{ $notification['key'] }}"
                                name="notifications[{{ $notification['key'] }}]"
                                {{ $notificationsData['notifications'][$notification['key']] ?? true ? 'checked' : '' }}>
                        </div>
                        <label class="form-check-label text-white flex-grow-1"
                            for="notif_{{ $notification['key'] }}">
                            <strong>{{ $notification['name'] }}</strong>
                            <span
                                class="badge bg-{{ config('user_notifications.priorities.' . $notification['priority'] . '.color') }} ms-2">
                                {{ ucfirst($notification['priority']) }}
                            </span>
                            <small class="d-block text-white-50 mt-1">
                                Channels: {{ implode(', ', $notification['channels']) }}
                            </small>
                        </label>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Submit Button --}}
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save me-2"></i>Save Notification Preferences
            </button>
        </div>
    </form>
</div>
