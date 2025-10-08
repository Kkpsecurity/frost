@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-clock"></i>
            {{ $content['title'] }}
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-success" id="run-schedule-btn">
                <i class="fas fa-play"></i>
                Run Schedule Now
            </button>
            <button type="button" class="btn btn-info" id="test-cron-btn">
                <i class="fas fa-check-circle"></i>
                Test Cron
            </button>
            <button type="button" class="btn btn-secondary" id="refresh-btn">
                <i class="fas fa-sync"></i>
                Refresh
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Status Cards -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box {{ $content['cron_status']['cron_installed'] ? 'bg-success' : 'bg-danger' }}">
                    <div class="inner">
                        <h3>{{ $content['cron_status']['cron_installed'] ? 'YES' : 'NO' }}</h3>
                        <p>Cron Installed</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-server"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box {{ $content['cron_status']['is_running'] ? 'bg-success' : 'bg-warning' }}">
                    <div class="inner">
                        <h3>{{ $content['cron_status']['is_running'] ? 'YES' : 'NO' }}</h3>
                        <p>Schedule Running</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ count($content['scheduled_tasks']) }}</h3>
                        <p>Scheduled Tasks</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $content['cron_status']['last_run'] ? \Carbon\Carbon::parse($content['cron_status']['last_run'])->diffForHumans() : 'Never' }}</h3>
                        <p>Last Run</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-history"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="card card-outline card-info mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    System Information
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
                                    <td>{{ $content['system_info']['php_version'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Laravel Version:</strong></td>
                                    <td>{{ $content['system_info']['laravel_version'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Timezone:</strong></td>
                                    <td>{{ $content['system_info']['timezone'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Current Time:</strong></td>
                                    <td>{{ $content['system_info']['current_time'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td><strong>Cron User:</strong></td>
                                    <td>{{ $content['system_info']['cron_user'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Artisan Path:</strong></td>
                                    <td><code>{{ $content['system_info']['artisan_path'] }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Schedule Command:</strong></td>
                                    <td><code>{{ $content['system_info']['schedule_run_command'] }}</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        @if(!empty($content['cron_status']['recommendations']))
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> Recommendations:</h5>
                <ul class="mb-0">
                    @foreach($content['cron_status']['recommendations'] as $recommendation)
                        <li>{{ $recommendation }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Scheduled Tasks -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Scheduled Tasks
                </h3>
            </div>
            <div class="card-body table-responsive p-0">
                @if(count($content['scheduled_tasks']) > 0)
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Command</th>
                                <th>Schedule</th>
                                <th>Next Run</th>
                                <th>Timezone</th>
                                <th>Background</th>
                                <th>Output</th>
                                <th style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($content['scheduled_tasks'] as $task)
                            <tr>
                                <td>
                                    <div class="task-info">
                                        <strong>{{ $task['description'] }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <code>{{ $task['command'] }}</code>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <code>{{ $task['expression'] }}</code>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $task['next_run'] }}
                                    </span>
                                </td>
                                <td>{{ $task['timezone'] }}</td>
                                <td>
                                    @if($task['runs_in_background'])
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task['output_file'])
                                        <code>{{ $task['output_file'] }}</code>
                                    @else
                                        <span class="text-muted">None</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm btn-primary run-task-btn"
                                            data-command="{{ $task['command'] }}"
                                            data-description="{{ $task['description'] }}"
                                            title="Run Task">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Scheduled Tasks Found</h4>
                        <p class="text-muted">
                            No Laravel scheduled tasks are currently configured.
                            <br>
                            Tasks are defined in <code>app/Console/Kernel.php</code>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Log Viewer -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt"></i>
                    Recent Logs
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-primary" id="load-logs-btn">
                        <i class="fas fa-sync"></i>
                        Load Logs
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="logs-container">
                    <p class="text-muted">Click "Load Logs" to view recent schedule-related log entries.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Output Modal -->
    <div class="modal fade" id="taskOutputModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-terminal"></i>
                        Task Output
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="task-output-content">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin"></i>
                            Running task...
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

    // Run individual task
    $('.run-task-btn').click(function() {
        const command = $(this).data('command');
        const description = $(this).data('description');
        
        $('#taskOutputModal .modal-title').html('<i class="fas fa-terminal"></i> ' + description);
        $('#task-output-content').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Running task...</div>');
        $('#taskOutputModal').modal('show');
        
        $.post('{{ route("admin.services.cron-manager.run-task") }}', {
            command: command
        }).done(function(response) {
            let content = '<div class="alert alert-' + (response.success ? 'success' : 'danger') + '">';
            content += '<strong>' + response.message + '</strong>';
            if (response.exit_code !== undefined) {
                content += ' (Exit Code: ' + response.exit_code + ')';
            }
            content += '</div>';
            
            if (response.output) {
                content += '<h6>Output:</h6>';
                content += '<pre class="bg-dark text-light p-3" style="max-height: 300px; overflow-y: auto;">' + response.output + '</pre>';
            }
            
            $('#task-output-content').html(content);
        }).fail(function(xhr) {
            $('#task-output-content').html('<div class="alert alert-danger">Failed to run task: ' + xhr.responseText + '</div>');
        });
    });

    // Run full schedule
    $('#run-schedule-btn').click(function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Running...');
        
        $.post('{{ route("admin.services.cron-manager.run-schedule") }}').done(function(response) {
            toastr.success(response.message);
            setTimeout(() => location.reload(), 1000);
        }).fail(function(xhr) {
            toastr.error('Failed to run schedule');
        }).always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-play"></i> Run Schedule Now');
        });
    });

    // Test cron functionality
    $('#test-cron-btn').click(function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Testing...');
        
        $.post('{{ route("admin.services.cron-manager.test") }}').done(function(response) {
            if (response.success) {
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        }).fail(function(xhr) {
            toastr.error('Test failed');
        }).always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Test Cron');
        });
    });

    // Load logs
    $('#load-logs-btn').click(function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        
        $.get('{{ route("admin.services.cron-manager.logs") }}', {
            lines: 50
        }).done(function(response) {
            if (response.success && response.logs.length > 0) {
                let content = '<pre class="bg-dark text-light p-3" style="max-height: 400px; overflow-y: auto;">';
                response.logs.forEach(function(log) {
                    content += log + '\n';
                });
                content += '</pre>';
                $('#logs-container').html(content);
            } else {
                $('#logs-container').html('<p class="text-muted">No schedule-related logs found.</p>');
            }
        }).fail(function(xhr) {
            $('#logs-container').html('<div class="alert alert-danger">Failed to load logs</div>');
        }).always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-sync"></i> Load Logs');
        });
    });

    // Refresh page
    $('#refresh-btn').click(function() {
        location.reload();
    });

    // Auto-refresh every 30 seconds
    setInterval(function() {
        $('#refresh-btn').click();
    }, 30000);
});
</script>
@stop

@section('css')
<style>
.task-info strong {
    color: #495057;
}

.small-box .inner h3 {
    font-size: 2.2rem;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm > .btn, .btn-sm {
    padding: .375rem .5rem;
    font-size: .875rem;
}

pre {
    font-size: 0.85rem;
    line-height: 1.4;
}
</style>
@stop