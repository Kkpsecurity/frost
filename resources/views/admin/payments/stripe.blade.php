@extends('adminlte::page')

@section('title', 'Stripe Configuration')

@section('content_header')
    <h1>
        <i class="fab fa-stripe"></i> Stripe Configuration
    </h1>
@stop

@section('content')
    <div class="container-fluid">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">

            <div class="col-lg-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Stripe API Keys</h3>
                        <div class="card-tools">
                            <button type="button" id="test-connection-btn" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-plug mr-1"></i> Test Connection
                            </button>
                        </div>
                    </div>
                    <form action="{{ route('admin.payments.update-stripe') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">

                            <div class="form-group">
                                <label>Mode</label>
                                <div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input class="custom-control-input" type="radio" id="mode_test" name="mode"
                                            value="test" {{ ($mode ?? 'test') === 'test' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="mode_test">Test Mode</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input class="custom-control-input" type="radio" id="mode_live" name="mode"
                                            value="live" {{ ($mode ?? 'test') === 'live' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="mode_live">Live Mode</label>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5>Test Keys <small class="text-muted">(pk_test_ / sk_test_)</small></h5>

                            <div class="form-group">
                                <label for="test_publishable_key">Test Publishable Key</label>
                                <input type="text" id="test_publishable_key" name="test_publishable_key"
                                    class="form-control @error('test_publishable_key') is-invalid @enderror"
                                    value="{{ old('test_publishable_key', $testPublishableKey) }}"
                                    placeholder="pk_test_...">
                                @error('test_publishable_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="test_secret_key">Test Secret Key</label>
                                <input type="password" id="test_secret_key" name="test_secret_key"
                                    class="form-control @error('test_secret_key') is-invalid @enderror"
                                    placeholder="{{ $hasTestSecretKey ? '••••••••••••••••••' : 'sk_test_...' }}">
                                @if ($hasTestSecretKey)
                                    <small class="form-text text-success">
                                        <i class="fas fa-check-circle mr-1"></i> Key configured — leave blank to keep
                                        existing.
                                    </small>
                                @endif
                                @error('test_secret_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>
                            <h5>Live Keys <small class="text-muted">(pk_live_ / sk_live_)</small></h5>

                            <div class="form-group">
                                <label for="live_publishable_key">Live Publishable Key</label>
                                <input type="text" id="live_publishable_key" name="live_publishable_key"
                                    class="form-control @error('live_publishable_key') is-invalid @enderror"
                                    value="{{ old('live_publishable_key', $livePublishableKey) }}"
                                    placeholder="pk_live_...">
                                @error('live_publishable_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="live_secret_key">Live Secret Key</label>
                                <input type="password" id="live_secret_key" name="live_secret_key"
                                    class="form-control @error('live_secret_key') is-invalid @enderror"
                                    placeholder="{{ $hasLiveSecretKey ? '••••••••••••••••••' : 'sk_live_...' }}">
                                @if ($hasLiveSecretKey)
                                    <small class="form-text text-success">
                                        <i class="fas fa-check-circle mr-1"></i> Key configured — leave blank to keep
                                        existing.
                                    </small>
                                @endif
                                @error('live_secret_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Save Stripe Settings
                            </button>
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Instructions</h3>
                    </div>
                    <div class="card-body">
                        <p>Get your API keys from the
                            <a href="https://dashboard.stripe.com/apikeys" target="_blank" rel="noopener noreferrer">
                                Stripe Dashboard
                            </a>.
                        </p>
                        <ul>
                            <li>Use <strong>Test</strong> keys during development</li>
                            <li>Switch to <strong>Live</strong> keys for production</li>
                            <li>The publishable key is used in frontend JS</li>
                            <li>The secret key is never exposed to the browser</li>
                        </ul>
                        <div id="test-result" class="mt-3" style="display:none;"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('js')
    <script>
        document.getElementById('test-connection-btn').addEventListener('click', function() {
            const btn = this;
            const resultEl = document.getElementById('test-result');

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span> Testing...';

            fetch('{{ route('admin.payments.test-connection') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        gateway: 'stripe'
                    })
                })
                .then(r => r.json())
                .then(data => {
                    resultEl.style.display = 'block';
                    resultEl.innerHTML = `<div class="alert alert-${data.success ? 'success' : 'danger'} mb-0">
            <i class="fas fa-${data.success ? 'check' : 'times'}-circle mr-1"></i> ${data.message}
        </div>`;
                })
                .catch(() => {
                    resultEl.style.display = 'block';
                    resultEl.innerHTML = '<div class="alert alert-danger mb-0">Request failed.</div>';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-plug mr-1"></i> Test Connection';
                });
        });
    </script>
@stop
