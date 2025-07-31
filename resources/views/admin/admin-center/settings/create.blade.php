@extends('adminlte::page')

@section('title', 'Create Setting')

@section('content_header')
    <x-admin.widgets.admin-header />
@stopnlte::page')

@section('title', 'Create Setting')

@section('content_header')
    <h1>Create Setting</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New Setting</h3>
                </div>

                <form action="{{ route('admin.settings.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="key">Setting Key</label>
                            <input type="text" class="form-control @error('key') is-invalid @enderror"
                                   id="key" name="key" value="{{ old('key') }}"
                                   placeholder="e.g., site.title, app.debug">
                            @error('key')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Use dot notation for grouping (e.g., 'site.title', 'mail.from')
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="value">Setting Value</label>
                            <textarea class="form-control @error('value') is-invalid @enderror"
                                      id="value" name="value" rows="3"
                                      placeholder="Enter the setting value">{{ old('value') }}</textarea>
                            @error('value')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Setting
                        </button>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Setting Guidelines
                    </h3>
                </div>
                <div class="card-body">
                    <h6><strong>Key Naming:</strong></h6>
                    <ul class="list-unstyled">
                        <li>• Use lowercase with dots</li>
                        <li>• Group related settings</li>
                        <li>• Examples: site.title, mail.driver</li>
                    </ul>

                    <h6><strong>Value Types:</strong></h6>
                    <ul class="list-unstyled">
                        <li>• Text strings</li>
                        <li>• Numbers</li>
                        <li>• JSON for complex data</li>
                        <li>• Boolean: true/false</li>
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
