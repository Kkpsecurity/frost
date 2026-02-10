{{-- Payments Section --}}
<div class="payments-section">
    <h3 class="text-white mb-4">
        <i class="fas fa-credit-card me-2"></i>Payments & Billing
    </h3>

    {{-- Payment Services Status --}}
    <div class="alert alert-info mb-4">
        <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Payment Services</h6>
        <div class="d-flex gap-4">
            <div>
                <i class="fab fa-stripe fa-lg me-2"></i>
                <strong>Stripe:</strong>
                @if ($stripeEnabled ?? false)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Not Configured</span>
                @endif
            </div>
            <div>
                <i class="fab fa-paypal fa-lg me-2"></i>
                <strong>PayPal:</strong>
                @if ($paypalEnabled ?? false)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Not Configured</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Payment Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-body">
                    <h6 class="text-white-50 mb-2">
                        <i class="fas fa-dollar-sign me-2"></i>Total Spent
                    </h6>
                    <h4 class="text-success mb-0">{{ $data['order_stats']['total_spent'] ?? '$0.00' }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-body">
                    <h6 class="text-white-50 mb-2">
                        <i class="fas fa-shopping-cart me-2"></i>Total Orders
                    </h6>
                    <h4 class="text-info mb-0">{{ $data['order_stats']['total_orders'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-body">
                    <h6 class="text-white-50 mb-2">
                        <i class="fas fa-wallet me-2"></i>Payment Methods
                    </h6>
                    <h4 class="text-warning mb-0">{{ count($data['saved_methods'] ?? []) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-body">
                    <h6 class="text-white-50 mb-2">
                        <i class="fas fa-undo me-2"></i>Refunded
                    </h6>
                    <h4 class="text-danger mb-0">{{ $data['order_stats']['total_refunded'] ?? '$0.00' }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment History --}}
    <div class="mb-4 pb-4 border-bottom border-secondary">
        <h5 class="text-white mb-3">Payment History</h5>
        @if (!empty($data['payment_history']))
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['payment_history'] as $payment)
                            <tr>
                                <td class="text-white-50">{{ $payment['id'] }}</td>
                                <td class="text-white-50">{{ $payment['date'] }}</td>
                                <td class="text-white">{{ $payment['description'] }}</td>
                                <td class="text-success">{{ $payment['amount'] }}</td>
                                <td>
                                    @if ($payment['refunded'])
                                        <span class="badge bg-danger">Refunded</span>
                                    @elseif($payment['status'] === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ $payment['download_url'] }}" class="btn btn-sm btn-outline-primary"
                                        target="_blank">
                                        <i class="fas fa-download me-1"></i>Invoice
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-secondary">
                <i class="fas fa-info-circle me-2"></i>
                No payment history available.
            </div>
        @endif
    </div>

    {{-- Saved Payment Methods --}}
    <div class="mb-4 pb-4 border-bottom border-secondary">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="text-white mb-0">
                <i class="fas fa-wallet me-2"></i>Saved Payment Methods
            </h5>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#addPaymentMethodModal">
                <i class="fas fa-plus me-1"></i>Add Payment Method
            </button>
        </div>

        @if (!empty($data['saved_methods']))
            <div class="row g-3 mb-3">
                @foreach ($data['saved_methods'] as $method)
                    <div class="col-md-6">
                        <div class="card bg-dark border-secondary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        @if ($method['type'] === 'card')
                                            <i
                                                class="fab fa-cc-{{ strtolower($method['brand']) }} fa-2x text-primary mb-2"></i>
                                            <p class="text-white mb-1">•••• •••• •••• {{ $method['last4'] }}</p>
                                            <small class="text-white-50">Expires
                                                {{ $method['exp_month'] }}/{{ $method['exp_year'] }}</small>
                                        @elseif($method['type'] === 'paypal')
                                            <i class="fab fa-paypal fa-2x text-primary mb-2"></i>
                                            <p class="text-white mb-1">{{ $method['email'] }}</p>
                                        @endif
                                        @if ($method['is_default'])
                                            <span class="badge bg-success mt-2">Default</span>
                                        @endif
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="if(confirm('Remove this payment method?')) { /* TODO: Implement removal */ }">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-secondary">
                <i class="fas fa-info-circle me-2"></i>
                No saved payment methods.
                @if (!($stripeEnabled ?? false) && !($paypalEnabled ?? false))
                    <strong>Note:</strong> Payment services (Stripe and PayPal) are not currently configured.
                @else
                    Add a payment method to make future purchases easier.
                @endif
            </div>
        @endif

        {{-- Add Payment Method Buttons --}}
        <div class="d-flex gap-2">
            @if ($stripeEnabled)
                <button class="btn btn-outline-primary">
                    <i class="fas fa-credit-card me-2"></i>Add Credit Card
                </button>
            @endif
            @if ($paypalEnabled)
                <button class="btn btn-outline-info">
                    <i class="fab fa-paypal me-2"></i>Connect PayPal
                </button>
            @endif
        </div>
    </div>

    {{-- Billing Address --}}
    @if (!empty($data['billing_address']))
        <div class="mb-4">
            <h5 class="text-white mb-3">Billing Address</h5>
            <div class="card bg-dark border-secondary">
                <div class="card-body">
                    <p class="text-white mb-1">{{ $data['billing_address']['line1'] }}</p>
                    @if (!empty($data['billing_address']['line2']))
                        <p class="text-white mb-1">{{ $data['billing_address']['line2'] }}</p>
                    @endif
                    <p class="text-white mb-1">
                        {{ $data['billing_address']['city'] }}, {{ $data['billing_address']['state'] }}
                        {{ $data['billing_address']['postal_code'] }}
                    </p>
                    <p class="text-white mb-0">{{ $data['billing_address']['country'] }}</p>
                    @if (!empty($data['billing_address']['phone']))
                        <p class="text-white-50 mt-2 mb-0">
                            <i class="fas fa-phone me-2"></i>{{ $data['billing_address']['phone'] }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
