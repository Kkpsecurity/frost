@extends('adminlte::page')

@section('title', 'Activity Logs')

@section('content_header')
    <h1><i class="fas fa-history"></i> Activity Logs</h1>
@stop

@section('content')

    @php
        $activeSince = now()->subMinutes(30)->timestamp;
    @endphp

    {{-- Summary info-boxes --}}
    <div class="row">
        <div class="col-md-4">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active Sessions (last 30 min)</span>
                    <span class="info-box-number">{{ $activeCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Authenticated Sessions</span>
                    <span class="info-box-number">{{ $authCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-secondary">
                <span class="info-box-icon"><i class="fas fa-user-secret"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Guest Sessions</span>
                    <span class="info-box-number">{{ $guestCount }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Session table --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-list-alt"></i> Recent Sessions</h3>
                    <div class="card-tools">
                        <input type="text" id="sessionSearch" class="form-control form-control-sm"
                            placeholder="Filter..." style="width:200px">
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover table-striped mb-0" id="sessionTable">
                        <thead class="thead-light">
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>IP Address</th>
                                <th>Browser / Agent</th>
                                <th>Last Activity</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $s)
                                @php
                                    $isActive = $s->last_activity >= $activeSince;
                                    $roleLabel = match ((int) ($s->role_id ?? 0)) {
                                        1 => ['label' => 'SysAdmin', 'color' => 'danger'],
                                        2 => ['label' => 'Admin', 'color' => 'warning'],
                                        3 => ['label' => 'Support', 'color' => 'info'],
                                        4 => ['label' => 'Instructor', 'color' => 'primary'],
                                        5 => ['label' => 'Student', 'color' => 'secondary'],
                                        default => ['label' => 'Guest', 'color' => 'light'],
                                    };
                                    // Shorten UA
                                    $ua = $s->user_agent ?? '';
                                    $browser = 'Unknown';
                                    if (str_contains($ua, 'Firefox')) {
                                        $browser = 'Firefox';
                                    } elseif (str_contains($ua, 'Edg')) {
                                        $browser = 'Edge';
                                    } elseif (str_contains($ua, 'Chrome')) {
                                        $browser = 'Chrome';
                                    } elseif (str_contains($ua, 'Safari')) {
                                        $browser = 'Safari';
                                    } elseif (str_contains($ua, 'curl')) {
                                        $browser = 'curl';
                                    } elseif (str_contains($ua, 'bot')) {
                                        $browser = 'Bot';
                                    }
                                    $os = '';
                                    if (str_contains($ua, 'Windows')) {
                                        $os = 'Windows';
                                    } elseif (str_contains($ua, 'Mac')) {
                                        $os = 'macOS';
                                    } elseif (str_contains($ua, 'Linux')) {
                                        $os = 'Linux';
                                    } elseif (str_contains($ua, 'Android')) {
                                        $os = 'Android';
                                    } elseif (str_contains($ua, 'iPhone')) {
                                        $os = 'iOS';
                                    }
                                @endphp
                                <tr class="session-row">
                                    <td>
                                        @if ($s->user_id)
                                            <strong>{{ $s->fname }} {{ $s->lname }}</strong><br>
                                            <small class="text-muted">{{ $s->email }}</small>
                                        @else
                                            <span class="text-muted"><em>Guest</em></span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $roleLabel['color'] }}">
                                            {{ $roleLabel['label'] }}
                                        </span>
                                    </td>
                                    <td><code>{{ $s->ip_address ?? '—' }}</code></td>
                                    <td>
                                        <span title="{{ $s->user_agent }}">
                                            {{ $browser }}{{ $os ? ' / ' . $os : '' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span title="{{ $s->last_activity_at->toDateTimeString() }}">
                                            {{ $s->last_activity_at->diffForHumans() }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($isActive)
                                            <span class="badge badge-success">
                                                <i class="fas fa-circle" style="font-size:0.6em"></i> Active
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Idle</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No session data found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-muted small">
                    <i class="fas fa-info-circle"></i>
                    Showing up to 150 most recent sessions from the <code>sessions</code> table.
                    "Active" = last activity within the past 30 minutes.
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        document.getElementById('sessionSearch').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#sessionTable tbody .session-row').forEach(function(row) {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>
@stop
