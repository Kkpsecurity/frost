@extends('adminlte::page')

@section('title', 'Storage Settings - Admin Center')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-hdd mr-2"></i>Storage Configuration</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                <li class="breadcrumb-item active">Storage</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fab fa-aws mr-2"></i>Amazon S3 Media Storage Configuration
                    </h3>
                </div>

                <form action="{{ route('admin.settings.storage.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle mr-2"></i>Configuration Priority</h6>
                            <p class="mb-0">Environment variables (in .env file) take precedence over database settings. Use this form if you prefer database-based configuration.</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5>Media S3 Archive Storage</h5>
                                <p class="text-muted">Configuration for the dedicated media archive S3 bucket</p>

                                <div class="form-group">
                                    <label for="media_s3_key">Access Key ID</label>
                                    <input type="text" class="form-control" id="media_s3_key" name="media_s3_key"
                                           value="{{ $storageSettings['media_s3']['key'] }}"
                                           placeholder="Your S3 Access Key ID">
                                    <small class="form-text text-muted">Environment: MEDIA_S3_ACCESS_KEY</small>
                                </div>

                                <div class="form-group">
                                    <label for="media_s3_secret">Secret Access Key</label>
                                    <input type="password" class="form-control" id="media_s3_secret" name="media_s3_secret"
                                           value="{{ $storageSettings['media_s3']['secret'] }}"
                                           placeholder="Your S3 Secret Access Key">
                                    <small class="form-text text-muted">Environment: MEDIA_S3_SECRET_KEY</small>
                                </div>

                                <div class="form-group">
                                    <label for="media_s3_region">Region</label>
                                    <input type="text" class="form-control" id="media_s3_region" name="media_s3_region"
                                           value="{{ $storageSettings['media_s3']['region'] }}"
                                           placeholder="us-east-1">
                                    <small class="form-text text-muted">Environment: MEDIA_S3_REGION</small>
                                </div>

                                <div class="form-group">
                                    <label for="media_s3_bucket">Bucket Name</label>
                                    <input type="text" class="form-control" id="media_s3_bucket" name="media_s3_bucket"
                                           value="{{ $storageSettings['media_s3']['bucket'] }}"
                                           placeholder="your-media-archive-bucket">
                                    <small class="form-text text-muted">Environment: MEDIA_S3_BUCKET</small>
                                </div>

                                <div class="form-group">
                                    <label for="media_s3_endpoint">Custom Endpoint (Optional)</label>
                                    <input type="url" class="form-control" id="media_s3_endpoint" name="media_s3_endpoint"
                                           value="{{ $storageSettings['media_s3']['endpoint'] }}"
                                           placeholder="https://s3.custom-provider.com">
                                    <small class="form-text text-muted">Environment: MEDIA_S3_ENDPOINT</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5>Current Configuration Status</h5>
                                <p class="text-muted">Active configuration from environment and database</p>

                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Setting</th>
                                                <th>Status</th>
                                                <th>Source</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Access Key</td>
                                                <td>
                                                    @if(!empty($storageSettings['media_s3']['key']))
                                                        <span class="badge badge-success">Configured</span>
                                                    @else
                                                        <span class="badge badge-danger">Missing</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ !empty(env('MEDIA_S3_ACCESS_KEY')) ? 'Environment' : 'Database' }}
                                                    </small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Secret Key</td>
                                                <td>
                                                    @if(!empty($storageSettings['media_s3']['secret']))
                                                        <span class="badge badge-success">Configured</span>
                                                    @else
                                                        <span class="badge badge-danger">Missing</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ !empty(env('MEDIA_S3_SECRET_KEY')) ? 'Environment' : 'Database' }}
                                                    </small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Region</td>
                                                <td>
                                                    @if(!empty($storageSettings['media_s3']['region']))
                                                        <span class="badge badge-success">{{ $storageSettings['media_s3']['region'] }}</span>
                                                    @else
                                                        <span class="badge badge-warning">Default</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ !empty(env('MEDIA_S3_REGION')) ? 'Environment' : 'Database' }}
                                                    </small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Bucket</td>
                                                <td>
                                                    @if(!empty($storageSettings['media_s3']['bucket']))
                                                        <span class="badge badge-success">{{ $storageSettings['media_s3']['bucket'] }}</span>
                                                    @else
                                                        <span class="badge badge-danger">Missing</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ !empty(env('MEDIA_S3_BUCKET')) ? 'Environment' : 'Database' }}
                                                    </small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    <h6>Environment Configuration</h6>
                                    <p class="text-sm text-muted">Add these to your <code>.env</code> file for production use:</p>
                                    <pre class="bg-light p-2 rounded"><code>MEDIA_S3_ACCESS_KEY=your_access_key
MEDIA_S3_SECRET_KEY=your_secret_key
MEDIA_S3_REGION=us-east-1
MEDIA_S3_BUCKET=your-bucket-name
MEDIA_S3_ENDPOINT=https://s3.amazonaws.com</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Settings
                            </a>
                            <div>
                                <button type="button" class="btn btn-info mr-2" onclick="testS3Connection()">
                                    <i class="fas fa-plug mr-2"></i>Test Connection
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>Save Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
function testS3Connection() {
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
    btn.disabled = true;

    // Test S3 connection via AJAX
    $.ajax({
        url: '/admin/media-manager/disk-statuses',
        method: 'GET',
        success: function(response) {
            if (response.success && response.disk_statuses.s3) {
                const status = response.disk_statuses.s3;
                if (status.connected) {
                    toastr.success('S3 connection successful!');
                } else {
                    toastr.error('S3 connection failed: ' + status.message);
                }
            } else {
                toastr.warning('Unable to test S3 connection');
            }
        },
        error: function(xhr) {
            toastr.error('Connection test failed');
        },
        complete: function() {
            // Restore button state
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
}
</script>
@stop
