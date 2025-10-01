@extends('adminlte::page')

@section('title', 'Create Setting')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Create Setting</h1>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Settings
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Setting</h3>
                </div>
                <form action="{{ route('admin.settings.store') }}" method="POST">
                    @csrf
                    
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="group">Group *</label>
                            <input type="text" class="form-control @error('group') is-invalid @enderror" 
                                   id="group" name="group" value="{{ old('group') }}" required
                                   placeholder="e.g., app, site, auth">
                            @error('group')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Group name for organizing settings</small>
                        </div>

                        <div class="form-group">
                            <label for="key">Key *</label>
                            <input type="text" class="form-control @error('key') is-invalid @enderror" 
                                   id="key" name="key" value="{{ old('key') }}" required
                                   placeholder="e.g., name, version, debug">
                            @error('key')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Unique key within the group</small>
                        </div>

                        <div class="form-group">
                            <label for="value">Value *</label>
                            <textarea class="form-control @error('value') is-invalid @enderror" 
                                      id="value" name="value" rows="4" required>{{ old('value') }}</textarea>
                            @error('value')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Setting value (can be text, JSON, etc.)</small>
                        </div>

                        <div class="form-group">
                            <label for="type">Type</label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                                <option value="string" {{ old('type', 'string') == 'string' ? 'selected' : '' }}>String</option>
                                <option value="integer" {{ old('type') == 'integer' ? 'selected' : '' }}>Integer</option>
                                <option value="boolean" {{ old('type') == 'boolean' ? 'selected' : '' }}>Boolean</option>
                                <option value="json" {{ old('type') == 'json' ? 'selected' : '' }}>JSON</option>
                                <option value="array" {{ old('type') == 'array' ? 'selected' : '' }}>Array</option>
                            </select>
                            @error('type')
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Setting Guidelines</h3>
                </div>
                <div class="card-body">
                    <h6>Group Examples:</h6>
                    <ul class="list-unstyled">
                        <li><code>app</code> - Application settings</li>
                        <li><code>site</code> - Site configuration</li>
                        <li><code>auth</code> - Authentication settings</li>
                        <li><code>mail</code> - Email configuration</li>
                        <li><code>adminlte</code> - AdminLTE theme settings</li>
                    </ul>

                    <h6 class="mt-3">Key Naming:</h6>
                    <ul class="list-unstyled">
                        <li>Use lowercase</li>
                        <li>Use underscores for spaces</li>
                        <li>Be descriptive but concise</li>
                        <li>Example: <code>max_upload_size</code></li>
                    </ul>

                    <h6 class="mt-3">Value Types:</h6>
                    <ul class="list-unstyled">
                        <li><strong>String:</strong> Regular text</li>
                        <li><strong>Integer:</strong> Numbers only</li>
                        <li><strong>Boolean:</strong> true/false</li>
                        <li><strong>JSON:</strong> JSON formatted data</li>
                        <li><strong>Array:</strong> Comma-separated values</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-format key on input
            $('#key').on('input', function() {
                let value = $(this).val();
                value = value.toLowerCase().replace(/[^a-z0-9_]/g, '_').replace(/_+/g, '_');
                $(this).val(value);
            });

            // Auto-format group on input
            $('#group').on('input', function() {
                let value = $(this).val();
                value = value.toLowerCase().replace(/[^a-z0-9_]/g, '_').replace(/_+/g, '_');
                $(this).val(value);
            });

            // Format value based on type
            $('#type').on('change', function() {
                const type = $(this).val();
                const valueField = $('#value');
                
                if (type === 'boolean') {
                    valueField.attr('placeholder', 'true or false');
                } else if (type === 'integer') {
                    valueField.attr('placeholder', 'e.g., 100, 0, -1');
                } else if (type === 'json') {
                    valueField.attr('placeholder', '{"key": "value"}');
                } else if (type === 'array') {
                    valueField.attr('placeholder', 'value1,value2,value3');
                } else {
                    valueField.attr('placeholder', 'Enter value...');
                }
            });
        });
    </script>
@stop