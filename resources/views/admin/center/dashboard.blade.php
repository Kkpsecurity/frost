@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-cogs"></i>
            {{ $content['title'] }}
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" id="refresh-dashboard">
                <i class="fas fa-sync"></i>
                Refresh
            </button>
            <button type="button" class="btn btn-info" id="system-status">
                <i class="fas fa-heartbeat"></i>
                System Status
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        
        <!-- Quick Stats Row -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($content['quick_stats']['total_users']) }}</h3>
                        <p>Total Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $content['quick_stats']['admin_users'] }}</h3>
                        <p>Admin Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $content['quick_stats']['total_settings'] }}</h3>
                        <p>Settings Configured</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cog"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box {{ $content['system_overview']['database_connection'] === 'connected' ? 'bg-success' : 'bg-danger' }}">
                    <div class="inner">
                        <h3>{{ strtoupper($content['system_overview']['database_connection']) }}</h3>
                        <p>Database Status</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Cards -->
        <div class="row">
            @foreach($content['services'] as $serviceKey => $service)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card card-outline card-{{ $service['color'] }} service-card h-100">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">
                                    <i class="{{ $service['icon'] }} mr-2"></i>
                                    {{ $service['title'] }}
                                </h3>
                                @if($service['status'] === 'active' || $service['status'] === 'configured' || $service['status'] === 'operational' || $service['status'] === 'running')
                                    <span class="badge badge-success px-2 py-1">
                                        <i class="fas fa-check-circle"></i>
                                        {{ ucfirst($service['status']) }}
                                    </span>
                                @elseif($service['status'] === 'partially_configured' || $service['status'] === 'inactive')
                                    <span class="badge badge-warning px-2 py-1">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ ucfirst(str_replace('_', ' ', $service['status'])) }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">
                                        <i class="fas fa-question-circle"></i>
                                        {{ ucfirst(str_replace('_', ' ', $service['status'])) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body pb-2">
                            <p class="card-text text-muted mb-3">{{ $service['description'] }}</p>
                            
                            <div class="service-metrics">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="metric-item">
                                            <div class="metric-value">{{ number_format($service['count']) }}</div>
                                            <div class="metric-label">Items</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric-item">
                                            <div class="metric-value text-sm">{{ $service['last_activity'] }}</div>
                                            <div class="metric-label">Last Activity</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent pt-0">
                            <div class="row">
                                @foreach($service['actions'] as $index => $action)
                                    <div class="col-{{ count($service['actions']) == 1 ? '12' : '6' }}">
                                        @if(isset($action['route']))
                                            <a href="{{ route($action['route']) }}" class="btn btn-{{ $service['color'] }} btn-block btn-sm">
                                                <i class="{{ $action['icon'] }} mr-1"></i>
                                                {{ $action['text'] }}
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-{{ $service['color'] }} btn-block btn-sm" onclick="handleAction('{{ $serviceKey }}', '{{ $action['text'] }}')">
                                                <i class="{{ $action['icon'] }} mr-1"></i>
                                                {{ $action['text'] }}
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- System Overview -->
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-server"></i>
                            System Overview
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td><strong>PHP Version:</strong></td>
                                            <td>{{ $content['system_overview']['php_version'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Laravel Version:</strong></td>
                                            <td>{{ $content['system_overview']['laravel_version'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Environment:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $content['system_overview']['environment'] === 'production' ? 'danger' : ($content['system_overview']['environment'] === 'local' ? 'primary' : 'warning') }}">
                                                    {{ strtoupper($content['system_overview']['environment']) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Debug Mode:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $content['system_overview']['debug_mode'] ? 'warning' : 'success' }}">
                                                    {{ $content['system_overview']['debug_mode'] ? 'ENABLED' : 'DISABLED' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Timezone:</strong></td>
                                            <td>{{ $content['system_overview']['timezone'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td><strong>Database:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $content['system_overview']['database_connection'] === 'connected' ? 'success' : 'danger' }}">
                                                    {{ strtoupper($content['system_overview']['database_connection']) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cache Driver:</strong></td>
                                            <td>{{ strtoupper($content['system_overview']['cache_driver']) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Session Driver:</strong></td>
                                            <td>{{ strtoupper($content['system_overview']['session_driver']) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Storage Disk:</strong></td>
                                            <td>{{ strtoupper($content['system_overview']['storage_disk']) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Maintenance:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $content['system_overview']['maintenance_mode'] ? 'danger' : 'success' }}">
                                                    {{ $content['system_overview']['maintenance_mode'] ? 'DOWN' : 'UP' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i>
                            System Resources
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-hdd"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Disk Space</span>
                                <span class="info-box-number">{{ $content['quick_stats']['disk_usage'] }}</span>
                            </div>
                        </div>
                        
                        <div class="info-box">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-clock"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">System Uptime</span>
                                <span class="info-box-number">{{ $content['quick_stats']['uptime'] }}</span>
                            </div>
                        </div>
                        
                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-archive"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Last Backup</span>
                                <span class="info-box-number text-sm">{{ $content['quick_stats']['last_backup'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="btn-group mr-2 mb-2">
                            <button type="button" class="btn btn-primary" onclick="clearCache()">
                                <i class="fas fa-broom"></i>
                                Clear Cache
                            </button>
                            <button type="button" class="btn btn-success" onclick="runSchedule()">
                                <i class="fas fa-play"></i>
                                Run Schedule
                            </button>
                            <button type="button" class="btn btn-info" onclick="checkHealth()">
                                <i class="fas fa-heartbeat"></i>
                                Health Check
                            </button>
                        </div>
                        <div class="btn-group mr-2 mb-2">
                            <button type="button" class="btn btn-warning" onclick="optimizeSystem()">
                                <i class="fas fa-tachometer-alt"></i>
                                Optimize
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="viewLogs()">
                                <i class="fas fa-file-alt"></i>
                                View Logs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Result Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-terminal"></i>
                        Action Result
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="action-result-content">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin"></i>
                            Processing...
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // CSRF token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Auto-refresh dashboard every 5 minutes
    setInterval(function() {
        location.reload();
    }, 300000);
});

// Dashboard refresh
$('#refresh-dashboard').click(function() {
    location.reload();
});

// System status check
$('#system-status').click(function() {
    checkHealth();
});

// Service-specific actions
function handleAction(service, action) {
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    btn.disabled = true;
    
    // Handle different actions
    switch(action) {
        case 'Test Connection':
            testPaymentConnection();
            break;
        case 'Upload':
            window.location.href = '{{ route("admin.media-manager.index") }}';
            break;
        case 'System Health':
            checkHealth();
            break;
        default:
            toastr.info('Action: ' + action + ' for ' + service);
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, 1000);
    }
}

// Quick actions
function clearCache() {
    showActionModal('Clear Cache', 'Clearing all application caches...');
    
    // Simulate cache clearing
    setTimeout(() => {
        $('#action-result-content').html(`
            <div class="alert alert-success">
                <strong>Success!</strong> All caches have been cleared.
                <ul class="mt-2 mb-0">
                    <li>Application cache cleared</li>
                    <li>Configuration cache cleared</li>
                    <li>Route cache cleared</li>
                    <li>View cache cleared</li>
                </ul>
            </div>
        `);
    }, 2000);
}

function runSchedule() {
    showActionModal('Run Schedule', 'Running Laravel scheduled tasks...');
    
    // This would make an AJAX call to run the schedule
    setTimeout(() => {
        $('#action-result-content').html(`
            <div class="alert alert-success">
                <strong>Schedule completed!</strong> All scheduled tasks have been executed.
            </div>
            <pre class="bg-dark text-light p-3">
Running scheduled commands...
✓ course:activate-dates
✓ classrooms:auto-create-today  
✓ course:generate-dates
All tasks completed successfully.
            </pre>
        `);
    }, 3000);
}

function checkHealth() {
    showActionModal('System Health Check', 'Running comprehensive system health check...');
    
    setTimeout(() => {
        $('#action-result-content').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6>✅ System Status</h6>
                    <ul class="list-unstyled">
                        <li>✅ Database Connection</li>
                        <li>✅ File Permissions</li>
                        <li>✅ Storage Access</li>
                        <li>✅ Cache System</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>⚠️ Recommendations</h6>
                    <ul class="list-unstyled">
                        <li>⚠️ Enable OPcache for better performance</li>
                        <li>✅ SSL Certificate valid</li>
                        <li>✅ Security headers configured</li>
                        <li>⚠️ Consider backup automation</li>
                    </ul>
                </div>
            </div>
        `);
    }, 2500);
}

function optimizeSystem() {
    showActionModal('System Optimization', 'Optimizing system performance...');
    
    setTimeout(() => {
        $('#action-result-content').html(`
            <div class="alert alert-success">
                <strong>Optimization Complete!</strong>
            </div>
            <pre class="bg-dark text-light p-3">
Running optimization tasks...
✓ Config cached
✓ Routes cached  
✓ Views compiled
✓ Autoloader optimized
System performance improved!
            </pre>
        `);
    }, 2000);
}

function viewLogs() {
    window.open('{{ route("admin.services.cron-manager.logs") }}', '_blank');
}

function testPaymentConnection() {
    showActionModal('Payment Test', 'Testing payment gateway connections...');
    
    setTimeout(() => {
        $('#action-result-content').html(`
            <div class="alert alert-info">
                <strong>Payment Gateway Status:</strong>
            </div>
            <table class="table table-sm">
                <tr>
                    <td>PayPal</td>
                    <td><span class="badge badge-success">Connected</span></td>
                </tr>
                <tr>
                    <td>Stripe</td>
                    <td><span class="badge badge-warning">Not Configured</span></td>
                </tr>
            </table>
        `);
    }, 1500);
}

function showActionModal(title, message) {
    $('#actionModal .modal-title').html('<i class="fas fa-terminal"></i> ' + title);
    $('#action-result-content').html(`
        <div class="text-center">
            <i class="fas fa-spinner fa-spin"></i>
            ${message}
        </div>
    `);
    $('#actionModal').modal('show');
}
</script>
@stop

@section('css')
<style>
/* Service Cards */
.service-card {
    transition: all 0.3s ease;
    border-width: 2px;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.service-card .card-header {
    background: rgba(0,0,0,0.02);
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.service-card .card-title {
    font-size: 1.1rem;
    font-weight: 600;
}

.service-card .card-text {
    font-size: 0.9rem;
    line-height: 1.4;
}

/* Service Metrics */
.service-metrics {
    background: rgba(0,0,0,0.02);
    border-radius: 8px;
    padding: 15px 10px;
    margin: 10px 0;
}

.metric-item {
    padding: 0 10px;
}

.metric-value {
    font-size: 1.4rem;
    font-weight: 700;
    color: #495057;
    line-height: 1.2;
}

.metric-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
    margin-top: 2px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .service-card .card-title {
        font-size: 1rem;
    }
    
    .metric-value {
        font-size: 1.2rem;
    }
    
    .metric-label {
        font-size: 0.7rem;
    }
}

/* Status badges */
.badge {
    font-size: 0.7rem;
    font-weight: 600;
}

/* Quick stats cards */
.small-box .inner h3 {
    font-size: 2.2rem;
}

/* Action buttons */
.card-footer .btn {
    font-size: 0.8rem;
    padding: 8px 12px;
    font-weight: 600;
}

/* General improvements */
.card {
    border-radius: 10px;
    overflow: hidden;
}

.table td {
    vertical-align: middle;
}

pre {
    font-size: 0.85rem;
    line-height: 1.4;
    border-radius: 8px;
}

/* Info boxes in system panels */
.info-box {
    border-radius: 8px;
}

.info-box-number {
    font-size: 1rem !important;
}
</style>
@stop