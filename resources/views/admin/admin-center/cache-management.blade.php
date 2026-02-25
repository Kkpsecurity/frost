@extends('adminlte::page')

@section('title', 'Cache Management')

@section('content_header')
    <h1><i class="fas fa-database"></i> Cache Management</h1>
@stop

@section('content')

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">

        {{-- ====================== --}}
        {{-- Laravel Cache Section  --}}
        {{-- ====================== --}}
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Laravel Cache
                    </h3>
                </div>
                <div class="card-body">

                    <table class="table table-sm table-borderless mb-3">
                        <tr>
                            <th style="width:40%">Cache Driver</th>
                            <td>
                                <span class="badge badge-info text-uppercase">{{ $cacheDriver }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Environment</th>
                            <td>
                                @if (app()->environment('production'))
                                    <span class="badge badge-danger">Production</span>
                                @else
                                    <span class="badge badge-warning">{{ app()->environment() }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <hr>
                    <h6 class="text-muted mb-2"><i class="fas fa-broom"></i> Clear Laravel Caches</h6>

                    <div class="row">
                        <div class="col-6 mb-2">
                            <form action="{{ route('admin.admin-center.cache-management.clear') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="config">
                                <button type="submit" class="btn btn-warning btn-block btn-sm"
                                    onclick="return confirm('Clear config cache?')">
                                    <i class="fas fa-cogs"></i> Config Cache
                                </button>
                            </form>
                        </div>
                        <div class="col-6 mb-2">
                            <form action="{{ route('admin.admin-center.cache-management.clear') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="route">
                                <button type="submit" class="btn btn-warning btn-block btn-sm"
                                    onclick="return confirm('Clear route cache?')">
                                    <i class="fas fa-route"></i> Route Cache
                                </button>
                            </form>
                        </div>
                        <div class="col-6 mb-2">
                            <form action="{{ route('admin.admin-center.cache-management.clear') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="view">
                                <button type="submit" class="btn btn-warning btn-block btn-sm"
                                    onclick="return confirm('Clear view cache?')">
                                    <i class="fas fa-eye-slash"></i> View Cache
                                </button>
                            </form>
                        </div>
                        <div class="col-6 mb-2">
                            <form action="{{ route('admin.admin-center.cache-management.clear') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="all">
                                <button type="submit" class="btn btn-danger btn-block btn-sm"
                                    onclick="return confirm('Clear ALL Laravel caches (config, route, view, application)?')">
                                    <i class="fas fa-trash-alt"></i> Clear All
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ===================== --}}
        {{-- Redis Memory Section  --}}
        {{-- ===================== --}}
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-memory"></i> Redis Memory
                    </h3>
                </div>
                <div class="card-body">
                    @if ($redisMemory)
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width:55%">Total Used Memory</th>
                                <td>{{ $redisMemory->total_human }}</td>
                            </tr>
                            <tr>
                                <th>Dataset Memory</th>
                                <td>{{ $redisMemory->data_human }}</td>
                            </tr>
                            <tr>
                                <th>Laravel Keys</th>
                                <td>
                                    <span
                                        class="badge badge-secondary">{{ number_format($redisMemory->laravel_keys) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>RCache Keys</th>
                                <td>
                                    <span class="badge badge-primary">{{ number_format($redisMemory->rcache_keys) }}</span>
                                </td>
                            </tr>
                        </table>
                    @else
                        <div class="callout callout-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Redis is not available or not configured.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>{{-- /row --}}

    {{-- ==================== --}}
    {{-- RCache Model Section  --}}
    {{-- ==================== --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group"></i> RCache Model Caches
                    </h3>
                    <form action="{{ route('admin.admin-center.cache-management.reload') }}" method="POST" class="mb-0">
                        @csrf
                        <input type="hidden" name="model" value="all">
                        <button type="submit" class="btn btn-success btn-sm"
                            onclick="return confirm('Reload ALL model caches from the database?')">
                            <i class="fas fa-sync-alt"></i> Reload All from DB
                        </button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Model</th>
                                <th>Class</th>
                                <th class="text-center">In Redis</th>
                                <th class="text-center">Records</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rcacheModels as $model)
                                <tr>
                                    <td>
                                        <strong>{{ $model['name'] }}</strong>
                                    </td>
                                    <td>
                                        <code style="font-size:0.8em">{{ class_basename($model['class']) }}</code>
                                    </td>
                                    <td class="text-center">
                                        @if ($model['inRedis'])
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Cached
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-times"></i> Not Cached
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($model['inRedis'])
                                            <span class="badge badge-info">{{ number_format($model['count']) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($model['static'])
                                            <span class="badge badge-warning"
                                                title="Read-only; auto-loaded from DB on boot">
                                                <i class="fas fa-lock"></i> Static
                                            </span>
                                        @else
                                            <span class="badge badge-light text-muted">Dynamic</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.admin-center.cache-management.reload') }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="model" value="{{ $model['name'] }}">
                                            <button type="submit" class="btn btn-xs btn-outline-primary"
                                                onclick="return confirm('Reload {{ $model['name'] }} from database?')">
                                                <i class="fas fa-sync-alt"></i> Reload
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-muted small">
                    <i class="fas fa-info-circle"></i>
                    <strong>Static</strong> models are read-only and pre-loaded at boot.
                    <strong>Dynamic</strong> models are cached on first access and stored in Redis.
                    Reloading forces a fresh database query and overwrites the Redis hash.
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    <style>
        .btn-xs {
            padding: 0.2rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.4;
            border-radius: 0.2rem;
        }
    </style>
@stop
