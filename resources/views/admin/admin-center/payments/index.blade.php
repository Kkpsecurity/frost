@extends('adminlte::page')

@section('title', 'Payment Methods')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Payment Methods</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
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

    <!-- Payment Methods Overview -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card"></i> Payment Methods Configuration
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Configure payment methods for your site. Currently supporting PayPal and Stripe integration.
                    </p>

                    <div class="row">
                        @foreach($paymentMethods as $key => $method)
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 {{ $method['enabled'] ? 'border-success' : 'border-secondary' }}">
                                    <div class="card-header bg-{{ $method['enabled'] ? 'success' : 'secondary' }} text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="{{ $method['icon'] }} fa-lg"></i>
                                                <strong>{{ $method['name'] }}</strong>
                                            </div>
                                            <span class="badge badge-{{ $method['enabled'] ? 'light' : 'dark' }}">
                                                {{ $method['enabled'] ? 'ENABLED' : 'DISABLED' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted">Status:</small>
                                                <p class="mb-2">
                                                    <span class="badge badge-{{ $method['enabled'] ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($method['status']) }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Currency:</small>
                                                <p class="mb-2">{{ $method['currency'] }}</p>
                                            </div>
                                        </div>

                                        @if($key === 'paypal')
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <small class="text-muted">Mode:</small>
                                                    <p class="mb-2">
                                                        <span class="badge badge-{{ $method['mode'] === 'live' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($method['mode']) }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mt-3">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.payments.' . $key) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-cog"></i> Configure
                                                </a>
                                                @if($method['enabled'])
                                                    <button type="button" class="btn btn-info btn-sm" onclick="testConnection('{{ $key }}')">
                                                        <i class="fas fa-plug"></i> Test
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Statistics -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Payment Statistics
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-exchange-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Transactions</span>
                                    <span class="info-box-number">{{ number_format($stats['total_transactions']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Successful</span>
                                    <span class="info-box-number">{{ number_format($stats['successful_payments']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-times-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Failed</span>
                                    <span class="info-box-number">{{ number_format($stats['failed_payments']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Revenue</span>
                                    <span class="info-box-number">${{ number_format($stats['total_revenue'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fab fa-paypal"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">PayPal Transactions</span>
                                    <span class="info-box-number">{{ number_format($stats['paypal_transactions']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fab fa-stripe"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Stripe Transactions</span>
                                    <span class="info-box-number">{{ number_format($stats['stripe_transactions']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.payments.paypal') }}" class="btn btn-primary">
                            <i class="fab fa-paypal"></i> Configure PayPal
                        </a>
                        <a href="{{ route('admin.payments.stripe') }}" class="btn btn-info">
                            <i class="fab fa-stripe"></i> Configure Stripe
                        </a>
                        <button type="button" class="btn btn-success" onclick="testAllConnections()">
                            <i class="fas fa-plug"></i> Test All Connections
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .info-box {
            transition: transform 0.2s;
        }
        .info-box:hover {
            transform: translateY(-2px);
        }
        .card {
            transition: box-shadow 0.2s;
        }
        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,.125);
        }
    </style>
@stop

@section('js')
    <script>
        function testConnection(method) {
            const button = event.target;
            const originalText = button.innerHTML;

            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testing...';
            button.disabled = true;

            fetch('{{ route("admin.payments.test-connection") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ method: method })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Connection test failed: ' + error.message);
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }

        function testAllConnections() {
            const methods = ['paypal', 'stripe'];
            let results = [];

            methods.forEach(method => {
                fetch('{{ route("admin.payments.test-connection") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ method: method })
                })
                .then(response => response.json())
                .then(data => {
                    results.push(`${method.toUpperCase()}: ${data.success ? '✅' : '❌'} ${data.message}`);

                    if (results.length === methods.length) {
                        alert('Test Results:\n\n' + results.join('\n'));
                    }
                })
                .catch(error => {
                    results.push(`${method.toUpperCase()}: ❌ Error - ${error.message}`);

                    if (results.length === methods.length) {
                        alert('Test Results:\n\n' + results.join('\n'));
                    }
                });
            });
        }

        console.log('Payments management page loaded');
    </script>
@stop
