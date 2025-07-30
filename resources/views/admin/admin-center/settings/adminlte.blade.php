@extends('adminlte::page')

@section('title', 'AdminLTE Settings')

@section('content_header')
    <x-admin.admin-header />
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-palette"></i> AdminLTE Theme Settings
                    </h3>
                </div>

                <form action="{{ route('admin.settings.adminlte.update') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @foreach($adminlteSettings as $key => $value)
                        <div class="form-group">
                            <label for="{{ $key }}">{{ ucwords(str_replace(['_', '.'], ' ', $key)) }}</label>

                            @if(in_array($key, ['adminlte.skin', 'adminlte.layout']))
                                <select class="form-control" name="{{ $key }}" id="{{ $key }}">
                                    @if($key === 'adminlte.skin')
                                        <option value="blue" {{ $value === 'blue' ? 'selected' : '' }}>Blue</option>
                                        <option value="black" {{ $value === 'black' ? 'selected' : '' }}>Black</option>
                                        <option value="purple" {{ $value === 'purple' ? 'selected' : '' }}>Purple</option>
                                        <option value="green" {{ $value === 'green' ? 'selected' : '' }}>Green</option>
                                        <option value="red" {{ $value === 'red' ? 'selected' : '' }}>Red</option>
                                        <option value="yellow" {{ $value === 'yellow' ? 'selected' : '' }}>Yellow</option>
                                    @else
                                        <option value="fixed" {{ $value === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                        <option value="layout-fluid" {{ $value === 'layout-fluid' ? 'selected' : '' }}>Fluid</option>
                                        <option value="layout-boxed" {{ $value === 'layout-boxed' ? 'selected' : '' }}>Boxed</option>
                                    @endif
                                </select>
                            @elseif(in_array(strtolower($value), ['true', 'false', '1', '0']))
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"
                                           id="{{ $key }}" name="{{ $key }}" value="1"
                                           {{ in_array(strtolower($value), ['true', '1']) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="{{ $key }}"></label>
                                </div>
                            @else
                                <input type="text" class="form-control"
                                       name="{{ $key }}" id="{{ $key }}"
                                       value="{{ $value }}" placeholder="Enter value">
                            @endif

                            <small class="form-text text-muted">
                                Current: <code>{{ $value ?? 'null' }}</code>
                            </small>
                        </div>
                        @endforeach
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update AdminLTE Settings
                        </button>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Settings
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paint-brush"></i> Theme Preview
                    </h3>
                </div>
                <div class="card-body">
                    <h6><strong>Current Theme:</strong></h6>
                    <div class="mb-3">
                        <span class="badge badge-primary">{{ $adminlteSettings['adminlte.skin'] ?? 'Default' }}</span>
                        <span class="badge badge-secondary">{{ $adminlteSettings['adminlte.layout'] ?? 'Default' }}</span>
                    </div>

                    <h6><strong>Settings Groups:</strong></h6>
                    @foreach($groupedSettings as $group => $settings)
                        @if($group === 'adminlte')
                        <div class="mb-2">
                            <span class="badge badge-info">{{ ucfirst($group) }}</span>
                            <small class="text-muted">({{ count($settings) }} settings)</small>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Important Note
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-warning">
                        <strong>Theme changes may require:</strong>
                    </p>
                    <ul class="list-unstyled">
                        <li>• Browser refresh</li>
                        <li>• Cache clearing</li>
                        <li>• CSS recompilation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    @vite('resources/css/admin.css')
@stop
