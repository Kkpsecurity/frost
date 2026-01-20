@extends('adminlte::page')

@section('title', 'Payment Gateway Settings')

@section('content_header')
    <h1>Payment Gateway Settings</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Payment Statistics -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format(\App\Models\Order::whereNotNull('completed_at')->count()) }}</h3>
                    <p>Completed Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format(\App\Models\Order::whereNull('completed_at')->whereNull('refunded_at')->count()) }}</h3>
                    <p>Pending Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format(\App\Models\Order::whereNotNull('refunded_at')->count()) }}</h3>
                    <p>Refunded Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-undo"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>${{ number_format(\App\Models\Order::whereNotNull('completed_at')->sum('total_price'), 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Stripe Settings -->
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fab fa-stripe"></i> Stripe Configuration
                    </h3>
                    <div class="card-tools">
                        @if($settings['stripe_enabled'])
                            <span class="badge badge-success">Enabled</span>
                        @else
                            <span class="badge badge-secondary">Disabled</span>
                        @endif
                    </div>
                </div>
                <form action="{{ route('admin.admin-center.payment-gateway.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="gateway" value="stripe">

                    <div class="card-body">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="stripe_enabled"
                                       name="stripe_enabled" value="1" {{ $settings['stripe_enabled'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="stripe_enabled">
                                    Enable Stripe Payments
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="stripe_key">Publishable Key</label>
                            <input type="text" class="form-control" id="stripe_key"
                                   name="stripe_key" value="{{ $settings['stripe_key'] ?? '' }}"
                                   placeholder="pk_test_...">
                            <small class="form-text text-muted">Your Stripe publishable API key</small>
                        </div>

                        <div class="form-group">
                            <label for="stripe_secret">Secret Key</label>
                            <input type="password" class="form-control" id="stripe_secret"
                                   name="stripe_secret" placeholder="sk_test_...">
                            <small class="form-text text-muted">Your Stripe secret API key (stored securely)</small>
                        </div>

                        <div class="form-group">
                            <label for="stripe_webhook">Webhook URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control"
                                       value="{{ url('/webhooks/stripe') }}" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button"
                                            onclick="navigator.clipboard.writeText('{{ url('/webhooks/stripe') }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Configure this URL in your Stripe dashboard</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Test Mode:</strong> Use test keys (pk_test_* and sk_test_*) for development.
                            Switch to live keys (pk_live_* and sk_live_*) for production.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Stripe Settings
                        </button>
                        <a href="https://dashboard.stripe.com/apikeys" target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i> Get API Keys
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- PayPal Settings -->
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fab fa-paypal"></i> PayPal Configuration
                    </h3>
                    <div class="card-tools">
                        @if($settings['paypal_enabled'])
                            <span class="badge badge-success">Enabled</span>
                        @else
                            <span class="badge badge-secondary">Disabled</span>
                        @endif
                    </div>
                </div>
                <form action="{{ route('admin.admin-center.payment-gateway.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="gateway" value="paypal">

                    <div class="card-body">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="paypal_enabled"
                                       name="paypal_enabled" value="1" {{ $settings['paypal_enabled'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="paypal_enabled">
                                    Enable PayPal Payments
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="paypal_client_id">Client ID</label>
                            <input type="text" class="form-control" id="paypal_client_id"
                                   name="paypal_client_id" value="{{ $settings['paypal_client_id'] ?? '' }}"
                                   placeholder="AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS">
                            <small class="form-text text-muted">Your PayPal REST API Client ID</small>
                        </div>

                        <div class="form-group">
                            <label for="paypal_secret">Secret Key</label>
                            <input type="password" class="form-control" id="paypal_secret"
                                   name="paypal_secret" placeholder="Enter PayPal secret key">
                            <small class="form-text text-muted">Your PayPal REST API secret key (stored securely)</small>
                        </div>

                        <div class="form-group">
                            <label for="paypal_mode">Mode</label>
                            <select class="form-control" id="paypal_mode" name="paypal_mode">
                                <option value="sandbox">Sandbox (Test)</option>
                                <option value="live">Live (Production)</option>
                            </select>
                            <small class="form-text text-muted">Use Sandbox for testing, Live for production</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Sandbox Mode:</strong> Use sandbox credentials for testing.
                            Create a sandbox account at <a href="https://developer.paypal.com/developer/accounts" target="_blank">PayPal Developer</a>.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save"></i> Save PayPal Settings
                        </button>
                        <a href="https://developer.paypal.com/developer/applications" target="_blank" class="btn btn-outline-info">
                            <i class="fas fa-external-link-alt"></i> Get API Credentials
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Methods Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card"></i> Active Payment Methods
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.admin-center.payment-methods') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-cog"></i> Manage Methods
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $paymentTypes = \App\Models\PaymentType::where('is_active', true)->get();
                        @endphp

                        @forelse($paymentTypes as $type)
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-{{ $type->id == 1 ? 'money-bill-wave' : ($type->id == 2 ? 'credit-card' : ($type->id == 3 ? 'university' : 'wallet')) }}"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ $type->name }}</span>
                                        <span class="info-box-number">
                                            {{ \App\Models\Order::where('payment_type_id', $type->id)->whereNotNull('completed_at')->count() }} orders
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">No active payment methods found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Recent Transactions
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.admin-center.transaction-logs') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-history"></i> View All Transactions
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $recentOrders = \App\Models\Order::with(['User', 'Course', 'PaymentType'])
                            ->whereNotNull('completed_at')
                            ->latest('completed_at')
                            ->limit(10)
                            ->get();
                    @endphp

                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>User</th>
                                    <th>Course</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}">
                                            #{{ $order->id }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($order->User)
                                            {{ $order->User->fullname() }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->Course)
                                            {{ $order->Course->name ?? 'N/A' }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($order->total_price, 2) }}</td>
                                    <td>
                                        @if($order->PaymentType)
                                            <span class="badge badge-info">{{ $order->PaymentType->name }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->completed_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($order->refunded_at)
                                            <span class="badge badge-danger">Refunded</span>
                                        @else
                                            <span class="badge badge-success">Completed</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No recent transactions</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Auto-hide success messages after 5 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut();
    }, 5000);
</script>
@stop
