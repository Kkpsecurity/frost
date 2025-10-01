@extends('adminlte::page')

@section('title', 'Storage Settings')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Storage Configuration</h1>
        <div>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Settings
            </a>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">S3 Storage Configuration</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <form action="{{ route('admin.settings.update-storage') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Configuration Priority</h5>
                            Environment variables (.env file) take precedence over database settings.
                            Database settings are used as fallbacks when environment variables are not set.
                        </div>

                        <h5>Media S3 Configuration</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="media_s3_key">Access Key</label>
                                    <input type="text" class="form-control" id="media_s3_key" name="media_s3_key"
                                           value="{{ old('media_s3_key', $storageSettings['media_s3']['key']) }}"
                                           placeholder="Enter S3 Access Key">
                                    <small class="form-text text-muted">
                                        Environment: <code>MEDIA_S3_ACCESS_KEY</code>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="media_s3_secret">Secret Key</label>
                                    <input type="password" class="form-control" id="media_s3_secret" name="media_s3_secret"
                                           value="{{ old('media_s3_secret', $storageSettings['media_s3']['secret']) }}"
                                           placeholder="Enter S3 Secret Key">
                                    <small class="form-text text-muted">
                                        Environment: <code>MEDIA_S3_SECRET_KEY</code>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="media_s3_region">Region</label>
                                    <select class="form-control" id="media_s3_region" name="media_s3_region">
                                        <option value="">Select Region</option>
                                        <option value="us-east-1" {{ old('media_s3_region', $storageSettings['media_s3']['region']) == 'us-east-1' ? 'selected' : '' }}>US East (N. Virginia)</option>
                                        <option value="us-east-2" {{ old('media_s3_region', $storageSettings['media_s3']['region']) == 'us-east-2' ? 'selected' : '' }}>US East (Ohio)</option>
                                        <option value="us-west-1" {{ old('media_s3_region', $storageSettings['media_s3']['region']) == 'us-west-1' ? 'selected' : '' }}>US West (N. California)</option>
                                        <option value="us-west-2" {{ old('media_s3_region', $storageSettings['media_s3']['region']) == 'us-west-2' ? 'selected' : '' }}>US West (Oregon)</option>
                                        <option value="eu-west-1" {{ old('media_s3_region', $storageSettings['media_s3']['region']) == 'eu-west-1' ? 'selected' : '' }}>Europe (Ireland)</option>
                                        <option value="eu-central-1" {{ old('media_s3_region', $storageSettings['media_s3']['region']) == 'eu-central-1' ? 'selected' : '' }}>Europe (Frankfurt)</option>
                                        <option value="ap-southeast-1" {{ old('media_s3_region', $storageSettings['media_s3']['region']) == 'ap-southeast-1' ? 'selected' : '' }}>Asia Pacific (Singapore)</option>
                                        <option value="ap-northeast-1" {{ old('media_s3_region', $storageSettings['media_s3']['region']) == 'ap-northeast-1' ? 'selected' : '' }}>Asia Pacific (Tokyo)</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Environment: <code>MEDIA_S3_REGION</code>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="media_s3_bucket">Bucket Name</label>
                                    <input type="text" class="form-control" id="media_s3_bucket" name="media_s3_bucket"
                                           value="{{ old('media_s3_bucket', $storageSettings['media_s3']['bucket']) }}"
                                           placeholder="Enter S3 Bucket Name">
                                    <small class="form-text text-muted">
                                        Environment: <code>MEDIA_S3_BUCKET</code>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="media_s3_endpoint">Custom Endpoint (Optional)</label>
                                    <input type="url" class="form-control" id="media_s3_endpoint" name="media_s3_endpoint"
                                           value="{{ old('media_s3_endpoint', $storageSettings['media_s3']['endpoint']) }}"
                                           placeholder="https://s3.example.com">
                                    <small class="form-text text-muted">
                                        Environment: <code>MEDIA_S3_ENDPOINT</code><br>
                                        Leave empty for standard AWS S3
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Test Connection</label>
                                    <div>
                                        <button type="button" class="btn btn-info btn-sm" onclick="testS3Connection('media')">
                                            <i class="fas fa-plug"></i> Test Media S3 Connection
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">
                                        Test the connection with current settings
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5>AWS S3 Configuration (Legacy)</h5>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            These are legacy AWS settings. Use Media S3 settings above for new configurations.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>AWS Access Key</label>
                                    <input type="text" class="form-control" readonly
                                           value="{{ $storageSettings['aws']['key'] ? '••••••••••••' . substr($storageSettings['aws']['key'], -4) : 'Not Set' }}">
                                    <small class="form-text text-muted">
                                        Environment: <code>AWS_ACCESS_KEY_ID</code>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>AWS Secret Key</label>
                                    <input type="text" class="form-control" readonly
                                           value="{{ $storageSettings['aws']['secret'] ? '••••••••••••' . substr($storageSettings['aws']['secret'], -4) : 'Not Set' }}">
                                    <small class="form-text text-muted">
                                        Environment: <code>AWS_SECRET_ACCESS_KEY</code>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>AWS Region</label>
                                    <input type="text" class="form-control" readonly value="{{ $storageSettings['aws']['region'] ?: 'Not Set' }}">
                                    <small class="form-text text-muted">
                                        Environment: <code>AWS_DEFAULT_REGION</code>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>AWS Bucket</label>
                                    <input type="text" class="form-control" readonly value="{{ $storageSettings['aws']['bucket'] ?: 'Not Set' }}">
                                    <small class="form-text text-muted">
                                        Environment: <code>AWS_BUCKET</code>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Storage Settings
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Storage Information</h3>
                </div>
                <div class="card-body">
                    <h6>Current Storage Driver</h6>
                    <p class="text-muted">
                        <code>{{ config('filesystems.default', 'local') }}</code>
                    </p>

                    <h6>Available Disks</h6>
                    <ul class="list-unstyled">
                        @foreach(config('filesystems.disks', []) as $disk => $config)
                            <li>
                                <i class="fas fa-hdd text-muted"></i>
                                <strong>{{ $disk }}</strong>
                                <small class="text-muted">({{ $config['driver'] ?? 'unknown' }})</small>
                            </li>
                        @endforeach
                    </ul>

                    <h6>Storage Path</h6>
                    <p class="text-muted">
                        <small><code>{{ storage_path() }}</code></small>
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Configuration Guide</h3>
                </div>
                <div class="card-body">
                    <h6>Environment Setup</h6>
                    <p class="text-muted">
                        Add these variables to your <code>.env</code> file:
                    </p>
                    <pre class="bg-light p-2 rounded" style="font-size: 11px;">MEDIA_S3_ACCESS_KEY=your_key
MEDIA_S3_SECRET_KEY=your_secret
MEDIA_S3_REGION=us-east-1
MEDIA_S3_BUCKET=your-bucket
MEDIA_S3_ENDPOINT=</pre>

                    <h6 class="mt-3">IAM Permissions</h6>
                    <p class="text-muted">
                        Required S3 permissions for your IAM user:
                    </p>
                    <ul class="text-muted" style="font-size: 12px;">
                        <li>s3:GetObject</li>
                        <li>s3:PutObject</li>
                        <li>s3:DeleteObject</li>
                        <li>s3:ListBucket</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function resetForm() {
            if (confirm('Are you sure you want to reset the storage settings?')) {
                document.querySelector('form').reset();
            }
        }

        function testS3Connection(type) {
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
            button.disabled = true;

            // Simulate connection test (you would implement actual test endpoint)
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;

                // This would be replaced with actual AJAX call to test endpoint
                alert('Connection test would be implemented here. Create a test endpoint in your controller.');
            }, 2000);
        }
    </script>
@stop
