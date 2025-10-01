@extends('adminlte::page')

@section('title', 'Stripe Configuration')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fab fa-stripe text-primary me-2"></i>Stripe Configuration</h1>
            <p class="text-muted mb-0">Configure Stripe payment processing settings</p>
        </div>
        <div>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Payments
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-8">
            {{-- Stripe Configuration Card --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fab fa-stripe me-2"></i>Stripe API Configuration
                    </h3>
                </div>

                <form method="POST" action="{{ route('admin.payments.update-stripe') }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        {{-- Enable/Disable Stripe --}}
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="stripe_enabled"
                                       name="enabled" value="1" {{ $config['enabled'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="stripe_enabled">
                                    <strong>Enable Stripe Payments</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Allow customers to pay using credit/debit cards via Stripe
                            </small>
                        </div>

                        <hr>

                        {{-- Environment Selection --}}
                        <div class="form-group">
                            <label for="environment"><strong>Environment</strong></label>
                            <select class="form-control" id="environment" name="environment" required>
                                <option value="test" {{ $config['environment'] === 'test' ? 'selected' : '' }}>
                                    Test Mode (Testing)
                                </option>
                                <option value="live" {{ $config['environment'] === 'live' ? 'selected' : '' }}>
                                    Live Mode (Production)
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                Use Test Mode for testing, Live Mode for production transactions
                            </small>
                        </div>

                        {{-- Test API Keys --}}
                        <div class="form-group">
                            <h5><i class="fas fa-flask text-info me-2"></i>Test API Keys</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="test_publishable_key"><strong>Test Publishable Key</strong></label>
                                        <input type="text" class="form-control" id="test_publishable_key"
                                               name="test_publishable_key" value="{{ $config['test_publishable_key'] }}"
                                               placeholder="pk_test_...">
                                        <small class="form-text text-muted">
                                            Public key for client-side usage (starts with pk_test_)
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="test_secret_key"><strong>Test Secret Key</strong></label>
                                        <input type="password" class="form-control" id="test_secret_key"
                                               name="test_secret_key" value="{{ $config['test_secret_key'] }}"
                                               placeholder="sk_test_...">
                                        <small class="form-text text-muted">
                                            Secret key for server-side usage (starts with sk_test_)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Live API Keys --}}
                        <div class="form-group">
                            <h5><i class="fas fa-globe text-success me-2"></i>Live API Keys</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="live_publishable_key"><strong>Live Publishable Key</strong></label>
                                        <input type="text" class="form-control" id="live_publishable_key"
                                               name="live_publishable_key" value="{{ $config['live_publishable_key'] }}"
                                               placeholder="pk_live_...">
                                        <small class="form-text text-muted">
                                            Public key for client-side usage (starts with pk_live_)
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="live_secret_key"><strong>Live Secret Key</strong></label>
                                        <input type="password" class="form-control" id="live_secret_key"
                                               name="live_secret_key" value="{{ $config['live_secret_key'] }}"
                                               placeholder="sk_live_...">
                                        <small class="form-text text-muted">
                                            Secret key for server-side usage (starts with sk_live_)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Webhook Settings --}}
                        <div class="form-group">
                            <h5><i class="fas fa-webhook text-warning me-2"></i>Webhook Configuration</h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="webhook_endpoint"><strong>Webhook Endpoint URL</strong></label>
                                        <input type="url" class="form-control" id="webhook_endpoint"
                                               name="webhook_endpoint" value="{{ $config['webhook_endpoint'] }}"
                                               placeholder="https://yoursite.com/webhooks/stripe">
                                        <small class="form-text text-muted">
                                            URL for Stripe to send event notifications
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="webhook_secret"><strong>Webhook Secret</strong></label>
                                        <input type="password" class="form-control" id="webhook_secret"
                                               name="webhook_secret" value="{{ $config['webhook_secret'] }}"
                                               placeholder="whsec_...">
                                        <small class="form-text text-muted">
                                            Webhook signing secret
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Connection Status --}}
                        <div class="form-group">
                            <label><strong>Connection Status</strong></label>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-{{ $config['connection_status'] === 'Connected' ? 'success' : ($config['connection_status'] === 'Failed' ? 'danger' : 'secondary') }} me-2">
                                    {{ $config['connection_status'] }}
                                </span>
                                @if($config['last_test'])
                                    <small class="text-muted">Last tested: {{ $config['last_test'] }}</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-info" id="test-stripe-connection">
                                <i class="fas fa-plug me-1"></i>Test Connection
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save Configuration
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Stripe Information Card --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>Stripe Setup Guide
                    </h3>
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-key text-info me-1"></i>Getting API Keys:</h6>
                    <ol class="small">
                        <li>Log in to <a href="https://dashboard.stripe.com" target="_blank">Stripe Dashboard</a></li>
                        <li>Go to Developers → API keys</li>
                        <li>Copy Publishable and Secret keys</li>
                        <li>Test keys start with pk_test_ and sk_test_</li>
                        <li>Live keys start with pk_live_ and sk_live_</li>
                    </ol>

                    <hr>

                    <h6><i class="fas fa-webhook text-info me-1"></i>Webhook Setup:</h6>
                    <ol class="small">
                        <li>Go to Developers → Webhooks</li>
                        <li>Add endpoint: <code>/webhooks/stripe</code></li>
                        <li>Select relevant events (payment_intent.succeeded, etc.)</li>
                        <li>Copy the webhook signing secret</li>
                    </ol>

                    <hr>

                    <h6><i class="fas fa-shield-alt text-info me-1"></i>Security Best Practices:</h6>
                    <ul class="small">
                        <li>Never expose secret keys in frontend code</li>
                        <li>Use publishable keys for client-side</li>
                        <li>Verify webhook signatures</li>
                        <li>Test in test mode first</li>
                        <li>Enable two-factor authentication</li>
                    </ul>
                </div>
            </div>

            {{-- Test Card Numbers --}}
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card me-2"></i>Test Card Numbers
                    </h3>
                </div>
                <div class="card-body">
                    <h6>Successful Payments:</h6>
                    <ul class="small">
                        <li><strong>4242 4242 4242 4242</strong> - Visa</li>
                        <li><strong>5555 5555 5555 4444</strong> - Mastercard</li>
                        <li><strong>3782 822463 10005</strong> - American Express</li>
                    </ul>

                    <h6>Failed Payments:</h6>
                    <ul class="small">
                        <li><strong>4000 0000 0000 0002</strong> - Card declined</li>
                        <li><strong>4000 0000 0000 9995</strong> - Insufficient funds</li>
                    </ul>

                    <p class="small text-muted">
                        Use any future date for expiry and any 3-digit CVC
                    </p>
                </div>
            </div>

            {{-- Test Results Card --}}
            <div class="card card-secondary" id="test-results-card" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-flask me-2"></i>Connection Test Results
                    </h3>
                </div>
                <div class="card-body" id="test-results-content">
                    {{-- Test results will be loaded here --}}
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Test Stripe Connection
    $('#test-stripe-connection').click(function() {
        const button = $(this);
        const originalText = button.html();

        // Show loading state
        button.html('<i class="fas fa-spinner fa-spin me-1"></i>Testing...').prop('disabled', true);

        // Make AJAX request
        $.ajax({
            url: '{{ route('admin.payments.test-connection') }}',
            method: 'POST',
            data: {
                method: 'stripe',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                showTestResults(response, 'success');
            },
            error: function(xhr) {
                const response = xhr.responseJSON || { success: false, message: 'Connection failed' };
                showTestResults(response, 'error');
            },
            complete: function() {
                // Restore button
                button.html(originalText).prop('disabled', false);
            }
        });
    });

    function showTestResults(response, type) {
        const card = $('#test-results-card');
        const content = $('#test-results-content');

        let html = '';
        if (response.success) {
            html = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Connection Successful!</strong><br>
                    ${response.message}
                </div>
            `;

            if (response.details) {
                html += '<h6>Account Details:</h6><ul class="small">';
                Object.entries(response.details).forEach(([key, value]) => {
                    html += `<li><strong>${key}:</strong> ${value}</li>`;
                });
                html += '</ul>';
            }
        } else {
            html = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Connection Failed!</strong><br>
                    ${response.message}
                </div>
            `;

            if (response.error_details) {
                html += '<h6>Error Details:</h6><pre class="small">' + JSON.stringify(response.error_details, null, 2) + '</pre>';
            }
        }

        content.html(html);
        card.show();
    }

    // Show/hide key fields based on environment
    $('#environment').change(function() {
        const env = $(this).val();
        if (env === 'test') {
            // Highlight test keys
            $('label[for="test_publishable_key"], label[for="test_secret_key"]').addClass('text-primary');
            $('label[for="live_publishable_key"], label[for="live_secret_key"]').removeClass('text-primary');
        } else {
            // Highlight live keys
            $('label[for="live_publishable_key"], label[for="live_secret_key"]').addClass('text-primary');
            $('label[for="test_publishable_key"], label[for="test_secret_key"]').removeClass('text-primary');
        }
    });

    // Trigger change on load
    $('#environment').trigger('change');
});
</script>
@stop
