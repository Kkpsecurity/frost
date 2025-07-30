@extends('adminlte::page')

@section('title', 'Settings Management')

@section('content_header')
    <x-admin.admin-header />
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Settings</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.settings.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Setting
                        </a>
                        <a href="{{ route('admin.settings.test') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-vial"></i> Test Settings
                        </a>
                        <a href="{{ route('admin.settings.adminlte') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-palette"></i> AdminLTE Settings
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(count($groupedSettings) > 0)
                        @foreach($groupedSettings as $prefix => $settings)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-folder"></i> {{ ucfirst($prefix) }} Settings
                                        <span class="badge badge-info">{{ count($settings) }} settings</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Key</th>
                                                    <th>Value</th>
                                                    <th>Full Key</th>
                                                    <th width="150">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($settings as $key => $value)
                                                    @php
                                                        $fullKey = $prefix === 'general' ? $key : $prefix . '.' . $key;
                                                        $displayValue = is_array($value) || is_object($value) ? json_encode($value) : $value;
                                                        $displayValue = strlen($displayValue) > 50 ? substr($displayValue, 0, 50) . '...' : $displayValue;
                                                    @endphp
                                                    <tr>
                                                        <td><code>{{ $key }}</code></td>
                                                        <td>{{ $displayValue }}</td>
                                                        <td><small class="text-muted">{{ $fullKey }}</small></td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('admin.settings.show', $fullKey) }}" class="btn btn-info btn-xs">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.settings.edit', $fullKey) }}" class="btn btn-warning btn-xs">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form action="{{ route('admin.settings.destroy', $fullKey) }}" method="POST" style="display: inline-block;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No settings found. <a href="{{ route('admin.settings.create') }}">Create your first setting</a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    @vite('resources/css/admin.css')
<style>
.table-responsive {
    font-size: 0.9em;
}
.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@stop
