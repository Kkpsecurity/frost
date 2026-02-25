@extends('adminlte::page')

@section('title', 'Database Tools')

@section('content_header')
    <h1><i class="fas fa-database"></i> Database Tools</h1>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $dbInfo['error'])
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle"></i> {{ session('error') ?: $dbInfo['error'] }}
        </div>
    @endif

    {{-- ======================== --}}
    {{-- Connection Summary       --}}
    {{-- ======================== --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-plug"></i> Connection</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <tbody>
                            <tr>
                                <th style="width:30%">Connection</th>
                                <td><span class="badge badge-info">{{ $dbInfo['connection'] }}</span></td>
                            </tr>
                            <tr>
                                <th>Driver</th>
                                <td><code>{{ $dbInfo['driver'] }}</code></td>
                            </tr>
                            <tr>
                                <th>Host</th>
                                <td><code>{{ $dbInfo['host'] }}{{ $dbInfo['port'] ? ':' . $dbInfo['port'] : '' }}</code>
                                </td>
                            </tr>
                            <tr>
                                <th>Database</th>
                                <td><strong>{{ $dbInfo['database'] }}</strong></td>
                            </tr>
                            @if (!empty($dbInfo['pg_version']))
                                <tr>
                                    <th>Server Version</th>
                                    <td><small class="text-muted">{{ Str::before($dbInfo['pg_version'], ' on ') }}</small>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-table"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Tables</span>
                    <span class="info-box-number">{{ $dbInfo['table_count'] ?? count($dbInfo['tables']) }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width:0%"></div>
                    </div>
                    <span class="progress-description">public schema</span>
                </div>
            </div>
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-hdd"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Database Size</span>
                    <span class="info-box-number">{{ $dbInfo['db_size'] ?? '—' }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width:0%"></div>
                    </div>
                    <span class="progress-description">total on disk</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================== --}}
    {{-- Tables                   --}}
    {{-- ======================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-th-list"></i> Tables</h3>
                    <div class="card-tools">
                        <input type="text" id="tableSearch" class="form-control form-control-sm"
                            placeholder="Filter tables..." style="width:200px">
                    </div>
                </div>
                <div class="card-body p-0">
                    @if (empty($dbInfo['tables']))
                        <div class="p-3 text-muted">No tables found.</div>
                    @else
                        <table class="table table-hover table-sm table-striped mb-0" id="tableList">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Table Name</th>
                                    @if (is_array($dbInfo['tables'][0] ?? null))
                                        <th class="text-right">Est. Rows</th>
                                        <th class="text-right">Table Size</th>
                                        <th class="text-right">Index Size</th>
                                        <th class="text-right">Total Size</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dbInfo['tables'] as $i => $table)
                                    @php
                                        $isArray = is_array($table);
                                        $name = $isArray ? $table['table_name'] : $table;
                                    @endphp
                                    <tr class="table-row">
                                        <td class="text-muted">{{ $i + 1 }}</td>
                                        <td>
                                            <code>{{ $name }}</code>
                                        </td>
                                        @if ($isArray)
                                            <td class="text-right">
                                                {{ number_format($table['row_estimate']) }}
                                            </td>
                                            <td class="text-right text-muted small">{{ $table['table_size'] }}</td>
                                            <td class="text-right text-muted small">{{ $table['index_size'] }}</td>
                                            <td class="text-right">
                                                @php
                                                    $bytes = $table['total_bytes'] ?? 0;
                                                    $sizeColor =
                                                        $bytes > 104857600
                                                            ? 'danger' // > 100 MB
                                                            : ($bytes > 10485760
                                                                ? 'warning' // > 10 MB
                                                                : 'secondary');
                                                @endphp
                                                <span class="badge badge-{{ $sizeColor }}">
                                                    {{ $table['total_size'] }}
                                                </span>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                @if (!empty($dbInfo['tables']))
                    <div class="card-footer text-muted small">
                        <i class="fas fa-info-circle"></i>
                        Row counts are PostgreSQL live-tuple estimates from <code>pg_stat_user_tables</code>
                        and may differ slightly from exact counts.
                        Sizes include TOAST and MVCC overhead.
                    </div>
                @endif
            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        document.getElementById('tableSearch').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tableList tbody .table-row').forEach(function(row) {
                const name = row.querySelector('code').textContent.toLowerCase();
                row.style.display = name.includes(q) ? '' : 'none';
            });
        });
    </script>
@stop
