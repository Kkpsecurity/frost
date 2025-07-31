@extends('adminlte::page')

@section('title', 'Setting Details')

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> Setting: {{ $key }}
                    </h3>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label for="key"><strong>Setting Key:</strong></label>
                        <div class="input-group">
                            <input type="text" class="form-control-plaintext"
                                   id="key" value="{{ $key }}" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="copyToClipboard('{{ $key }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><strong>Setting Value:</strong></label>
                        <div class="border p-3 bg-light rounded">
                            @if(is_null($value))
                                <em class="text-muted">null</em>
                            @elseif(is_bool($value))
                                <span class="badge badge-{{ $value ? 'success' : 'danger' }}">
                                    {{ $value ? 'true' : 'false' }}
                                </span>
                            @elseif(is_array($value) || is_object($value))
                                <pre><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                            @else
                                <code>{{ $value }}</code>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label><strong>Value Type:</strong></label>
                        <span class="badge badge-primary">{{ gettype($value) }}</span>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('admin.settings.edit', $key) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Setting
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button type="button" class="btn btn-danger"
                            onclick="deleteSetting('{{ $key }}')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Setting Information
                    </h3>
                </div>
                <div class="card-body">
                    <h6><strong>Usage in Code:</strong></h6>
                    <div class="border p-2 bg-light mb-3">
                        <code>setting('{{ $key }}')</code>
                    </div>

                    <h6><strong>Helper Usage:</strong></h6>
                    <div class="border p-2 bg-light mb-3">
                        <code>SettingHelper::get('{{ $key }}')</code>
                    </div>

                    @if(strpos($key, '.') !== false)
                    <h6><strong>Group:</strong></h6>
                    <span class="badge badge-info">{{ explode('.', $key)[0] }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this setting?</p>
                <p><strong>Key:</strong> <code id="deleteKey">{{ $key }}</code></p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    This action cannot be undone!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Setting
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    @vite('resources/css/admin.css')
<style>
    .form-control-plaintext {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
    }
    pre code {
        background: none;
        padding: 0;
        font-size: 0.9em;
    }
</style>
@stop

@section('js')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        toastr.success('Setting key copied to clipboard!');
    });
}

function deleteSetting(key) {
    $('#deleteKey').text(key);
    $('#deleteForm').attr('action', '{{ route("admin.settings.destroy", ":key") }}'.replace(':key', key));
    $('#deleteModal').modal('show');
}
</script>
@stop
