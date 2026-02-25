@extends('adminlte::page')

@section('title', 'System Health')

@section('content_header')
    <h1><i class="fas fa-heartbeat"></i> System Health</h1>
@stop

@section('content')

    @php
        $dbOk = str_starts_with($health['database'], 'Connected');
        $cacheOk = str_starts_with($health['cache'], 'Working');
        $storageOk = $health['storage_writable'];

        $allOk = $dbOk && $cacheOk && $storageOk;
    @endphp

    {{-- Overall status banner --}}
    <div class="alert alert-{{ $allOk ? 'success' : 'warning' }} alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h5>
            <i class="fas fa-{{ $allOk ? 'check-circle' : 'exclamation-triangle' }}"></i>
            {{ $allOk ? 'All systems operational' : 'One or more systems need attention' }}
        </h5>
        Checked at {{ now()->format('D, d M Y H:i:s T') }}
    </div>

    <div class="row">

        {{-- ======================== --}}
        {{-- Runtime Info             --}}
        {{-- ======================== --}}
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-code"></i> Runtime</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody>
                            <tr>
                                <th style="width:45%">PHP Version</th>
                                <td>
                                    <span class="badge badge-info">{{ $health['php_version'] }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Laravel Version</th>
                                <td>
                                    <span class="badge badge-primary">{{ $health['laravel_version'] }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Environment</th>
                                <td>
                                    @if (app()->environment('production'))
                                        <span class="badge badge-danger">production</span>
                                    @else
                                        <span class="badge badge-warning">{{ app()->environment() }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Debug Mode</th>
                                <td>
                                    @if (config('app.debug'))
                                        <span class="badge badge-warning"><i class="fas fa-bug"></i> Enabled</span>
                                    @else
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Disabled</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>App Locale</th>
                                <td><code>{{ config('app.locale') }}</code></td>
                            </tr>
                            <tr>
                                <th>Timezone</th>
                                <td><code>{{ config('app.timezone') }}</code></td>
                            </tr>
                            <tr>
                                <th>PHP Memory Limit</th>
                                <td><code>{{ ini_get('memory_limit') }}</code></td>
                            </tr>
                            <tr>
                                <th>Max Execution Time</th>
                                <td><code>{{ ini_get('max_execution_time') }}s</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ======================== --}}
        {{-- Services Status          --}}
        {{-- ======================== --}}
        <div class="col-md-6">
            <div class="card card-{{ $allOk ? 'success' : 'warning' }}">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-server"></i> Services</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody>

                            {{-- Database --}}
                            <tr>
                                <th style="width:45%">
                                    <i class="fas fa-database"></i> Database
                                </th>
                                <td>
                                    @if ($dbOk)
                                        <span class="badge badge-success"><i class="fas fa-check"></i>
                                            {{ $health['database'] }}</span>
                                        <small class="text-muted ml-1">{{ config('database.default') }}</small>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-times"></i> Error</span>
                                        <br><small class="text-danger">{{ $health['database'] }}</small>
                                    @endif
                                </td>
                            </tr>

                            {{-- Cache --}}
                            <tr>
                                <th>
                                    <i class="fas fa-bolt"></i> Cache
                                </th>
                                <td>
                                    @if ($cacheOk)
                                        <span class="badge badge-success"><i class="fas fa-check"></i>
                                            {{ $health['cache'] }}</span>
                                        <small class="text-muted ml-1">{{ config('cache.default') }}</small>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-times"></i> Error</span>
                                        <br><small class="text-danger">{{ $health['cache'] }}</small>
                                    @endif
                                </td>
                            </tr>

                            {{-- Storage --}}
                            <tr>
                                <th>
                                    <i class="fas fa-hdd"></i> Storage Writable
                                </th>
                                <td>
                                    @if ($storageOk)
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Writable</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-times"></i> Not Writable</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- Queue driver --}}
                            <tr>
                                <th>
                                    <i class="fas fa-tasks"></i> Queue Driver
                                </th>
                                <td>
                                    <span class="badge badge-secondary">{{ config('queue.default') }}</span>
                                </td>
                            </tr>

                            {{-- Session --}}
                            <tr>
                                <th>
                                    <i class="fas fa-user-clock"></i> Session Driver
                                </th>
                                <td>
                                    <span class="badge badge-secondary">{{ config('session.driver') }}</span>
                                </td>
                            </tr>

                            {{-- Config cached --}}
                            <tr>
                                <th>
                                    <i class="fas fa-cogs"></i> Config Cached
                                </th>
                                <td>
                                    @if (app()->configurationIsCached())
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- Routes cached --}}
                            <tr>
                                <th>
                                    <i class="fas fa-route"></i> Routes Cached
                                </th>
                                <td>
                                    @if (app()->routesAreCached())
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Yes</span>
                                    @else
                                        <span class="badge badge-secondary">No</span>
                                    @endif
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- /row --}}

    {{-- ======================== --}}
    {{-- Disk Usage               --}}
    {{-- ======================== --}}
    @php
        $diskTotal = disk_total_space(base_path());
        $diskFree = disk_free_space(base_path());
        $diskUsed = $diskTotal - $diskFree;
        $diskPct = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100) : 0;
        $diskColor = $diskPct >= 90 ? 'danger' : ($diskPct >= 70 ? 'warning' : 'success');

        $storagePct = 0;
        $storageInfo = '';
        try {
            $storageFree = disk_free_space(storage_path());
            $storageTotal = disk_total_space(storage_path());
            $storagePct = $storageTotal > 0 ? round((($storageTotal - $storageFree) / $storageTotal) * 100) : 0;
        } catch (\Exception $e) {
        }
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-hdd"></i> Disk Usage</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-{{ $diskColor }}">
                                    <i class="fas fa-hdd"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Disk Used / Total</span>
                                    <span class="info-box-number">
                                        {{ number_format($diskUsed / 1073741824, 1) }} GB
                                        / {{ number_format($diskTotal / 1073741824, 1) }} GB
                                    </span>
                                    <div class="progress">
                                        <div class="progress-bar bg-{{ $diskColor }}"
                                            style="width: {{ $diskPct }}%"></div>
                                    </div>
                                    <span class="progress-description">{{ $diskPct }}% used</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-folder-open"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Storage Path</span>
                                    <span class="info-box-number text-sm">
                                        {{ number_format(($diskTotal - $diskFree) / 1073741824, 1) }} GB free
                                    </span>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" style="width: {{ $storagePct }}%"></div>
                                    </div>
                                    <span class="progress-description">{{ $storagePct }}% used</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-secondary">
                                    <i class="fas fa-microchip"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">PHP Memory Limit</span>
                                    <span class="info-box-number">{{ ini_get('memory_limit') }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-secondary" style="width:0%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Peak: {{ number_format(memory_get_peak_usage(true) / 1048576, 1) }} MB this request
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-default collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tools"></i> Quick Actions</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.admin-center.cache-management') }}" class="btn btn-warning mr-2">
                        <i class="fas fa-broom"></i> Cache Management
                    </a>
                    <a href="{{ route('admin.admin-center.database-tools') }}" class="btn btn-info mr-2">
                        <i class="fas fa-database"></i> Database Tools
                    </a>
                </div>
            </div>
        </div>
    </div>

@stop
