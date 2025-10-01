@extends('adminlte::page')

@section('title', 'View Setting')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>View Setting</h1>
        <div>
            <a href="{{ route('admin.settings.edit', $key) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Setting
            </a>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Settings
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Setting Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Full Key:</strong>
                            <p class="text-muted">{{ $key }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Group:</strong>
                            <p class="text-muted">
                                <span class="badge badge-info">{{ explode('.', $key)[0] ?? 'N/A' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Key Name:</strong>
                            <p class="text-muted">{{ explode('.', $key, 2)[1] ?? $key }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Value Type:</strong>
                            <p class="text-muted">
                                @if($valueType === 'boolean')
                                    <span class="badge badge-success">Boolean</span>
                                @elseif($valueType === 'number')
                                    <span class="badge badge-warning">Numeric</span>
                                @elseif($valueType === 'json')
                                    <span class="badge badge-info">JSON/Array</span>
                                @else
                                    <span class="badge badge-secondary">String</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <strong>Current Value:</strong>
                            <div class="mt-2">
                                @if($valueType === 'boolean')
                                    <span class="badge badge-{{ $value ? 'success' : 'danger' }}">
                                        {{ $value ? 'TRUE' : 'FALSE' }}
                                    </span>
                                @elseif($valueType === 'json')
                                    <pre class="bg-light p-3 border rounded"><code>{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : (json_decode($value) ? json_encode(json_decode($value), JSON_PRETTY_PRINT) : $value) }}</code></pre>
                                @else
                                    <div class="bg-light p-3 border rounded">
                                        {{ strlen($value) > 200 ? substr($value, 0, 200) . '...' : $value }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(strlen($value) > 200)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Value truncated for display. Full length: {{ strlen($value) }} characters.
                            </small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.settings.edit', $key) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Setting
                        </a>
                        <button class="btn btn-info btn-sm" onclick="copyToClipboard('{{ $key }}')">
                            <i class="fas fa-copy"></i> Copy Key
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="copyToClipboard('{{ addslashes($value) }}')">
                            <i class="fas fa-copy"></i> Copy Value
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteSetting('{{ $key }}')">
                            <i class="fas fa-trash"></i> Delete Setting
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Usage Information</h3>
                </div>
                <div class="card-body">
                    <h6>Access in Code:</h6>
                    <code class="d-block bg-light p-2 rounded">
                        Setting::get('{{ $key }}')
                    </code>

                    <h6 class="mt-3">Laravel Config:</h6>
                    <code class="d-block bg-light p-2 rounded">
                        config('{{ str_replace('.', '.', $key) }}')
                    </code>

                    <h6 class="mt-3">Helper Function:</h6>
                    <code class="d-block bg-light p-2 rounded">
                        getSetting('{{ explode('.', $key)[0] }}', '{{ explode('.', $key, 2)[1] ?? $key }}')
                    </code>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy to clipboard');
            });
        }

        function deleteSetting(key) {
            if (confirm('Are you sure you want to delete this setting? This action cannot be undone.')) {
                // Create a form to submit DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/settings/${encodeURIComponent(key)}`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@stop
