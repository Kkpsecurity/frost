@extends('adminlte::page')

@section('title', 'PayPal Configuration')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fab fa-paypal text-primary me-2"></i>PayPal Configuration</h1>
            <p class="text-muted mb-0">Configure PayPal payment processing settings</p>
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
            {{-- PayPal Configuration Card --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fab fa-paypal me-2"></i>PayPal API Configuration
                    </h3>
                </div>

                <form method="POST" action="{{ route('admin.payments.update-paypal') }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        {{-- Enable/Disable PayPal --}}
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="paypal_enabled"
                                       name="enabled" value="1" {{ $config['enabled'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="paypal_enabled">
                                    <strong>Enable PayPal Payments</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Allow customers to pay using PayPal
                            </small>
                        </div>

                        <hr>

                        {{-- Environment Selection --}}
                        <div class="form-group">
                            <label for="environment"><strong>Environment</strong></label>
                            <select class="form-control" id="environment" name="environment" required>
                                <option value="sandbox" {{ $config['environment'] === 'sandbox' ? 'selected' : '' }}>
                                    Sandbox (Testing)
                                </option>
                                <option value="live" {{ $config['environment'] === 'live' ? 'selected' : '' }}>
                                    Live (Production)
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                Use Sandbox for testing, Live for production transactions
                            </small>
                        </div>

                        {{-- API Credentials --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id"><strong>Client ID</strong></label>
                                    <input type="text" class="form-control" id="client_id" name="client_id"
                                           value="{{ $config['client_id'] }}" required>
                                    <small class="form-text text-muted">
                                        PayPal application Client ID
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_secret"><strong>Client Secret</strong></label>
                                    <input type="password" class="form-control" id="client_secret" name="client_secret"
                                           value="{{ $config['client_secret'] }}" required>
                                    <small class="form-text text-muted">
                                        PayPal application Client Secret
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Webhook URL --}}
                        <div class="form-group">
                            <label for="webhook_url"><strong>Webhook URL</strong></label>
                            <input type="url" class="form-control" id="webhook_url" name="webhook_url"
                                   value="{{ $config['webhook_url'] }}" placeholder="https://yoursite.com/webhooks/paypal">
                            <small class="form-text text-muted">
                                URL for PayPal to send payment notifications (optional)
                            </small>
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
                            <button type="button" class="btn btn-info" id="test-paypal-connection">
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
            {{-- PayPal Information Card --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>PayPal Setup Guide
                    </h3>
                </div>
                <div class="card-body">
                    <h6><i class="fas fa-key text-info me-1"></i>Getting API Credentials:</h6>
                    <ol class="small">
                        <li>Log in to <a href="https://developer.paypal.com" target="_blank">PayPal Developer Dashboard</a></li>
                        <li>Create a new application or select existing one</li>
                        <li>Copy the Client ID and Client Secret</li>
                        <li>Configure sandbox/live environment settings</li>
                    </ol>

                    <hr>

                    <h6><i class="fas fa-cog text-info me-1"></i>Environment Settings:</h6>
                    <ul class="small">
                        <li><strong>Sandbox:</strong> For testing payments</li>
                        <li><strong>Live:</strong> For real transactions</li>
                        <li>Always test in sandbox first</li>
                    </ul>

                    <hr>

                    <h6><i class="fas fa-shield-alt text-info me-1"></i>Security Notes:</h6>
                    <ul class="small">
                        <li>Keep Client Secret confidential</li>
                        <li>Use HTTPS for webhook URLs</li>
                        <li>Regularly test connections</li>
                        <li>Monitor transaction logs</li>
                    </ul>
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
    // Test PayPal Connection
    $('#test-paypal-connection').click(function() {
        const button = $(this);
        const originalText = button.html();

        // Show loading state
        button.html('<i class="fas fa-spinner fa-spin me-1"></i>Testing...').prop('disabled', true);

        // Make AJAX request
        $.ajax({
            url: '{{ route('admin.payments.test-connection') }}',
            method: 'POST',
            data: {
                method: 'paypal',
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
                html += '<h6>Connection Details:</h6><ul class="small">';
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
});
</script>
@stop
