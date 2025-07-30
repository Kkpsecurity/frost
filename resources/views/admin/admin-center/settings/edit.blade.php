@extends('adminlte::page')

@section('title', 'Edit Setting')

@section('content_header')
    <h1>Edit Setting</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit: {{ $key ?? 'Setting' }}</li>
        </ol>
    </nav>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Edit Setting: {{ $settingName }}
                    </h3>
                </div>

                <form action="{{ route('admin.settings.update', $key) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="key">Setting Key</label>
                            <input type="text" class="form-control-plaintext"
                                   id="key" value="{{ $key }}" readonly>
                            <small class="form-text text-muted">
                                Setting key cannot be modified
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="value">Setting Value</label>
                            <textarea class="form-control @error('value') is-invalid @enderror"
                                      id="value" name="value" rows="5"
                                      placeholder="Enter the setting value">{{ old('value', $value) }}</textarea>
                            @error('value')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        @if($prefix)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Prefix:</strong> {{ $prefix }}
                        </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Setting
                        </button>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <a href="{{ route('admin.settings.show', $key) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Current Value
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Original Value:</label>
                        <div class="border p-2 bg-light">
                            <code>{{ $value ?? 'null' }}</code>
                        </div>
                    </div>

                    <small class="text-muted">
                        Be careful when modifying system settings as it may affect application functionality.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .breadcrumb {
        background-color: transparent;
        margin-bottom: 1rem;
    }
    .form-control-plaintext {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
    }
</style>
@stop
