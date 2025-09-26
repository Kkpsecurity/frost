{{-- Modern Payments Section Content --}}
<div class="row">
    <div class="col-lg-8">
        {{-- Payment Methods --}}
        <div class="modern-card mb-4">
            <div class="card-body">
                <div class="section-title">
                    <i class="fas fa-credit-card"></i>Payment Methods
                </div>

                @if(count($paymentsData['payment_methods']) > 0)
                    <div class="row">
                        @foreach($paymentsData['payment_methods'] as $method)
                            <div class="col-md-12 mb-3">
                                <div class="card border" style="border-radius: 12px;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-credit-card fa-2x text-secondary me-3"></i>
                                            <div>
                                                <div class="fw-bold">{{ $method['note'] ?? 'Payment Methods' }}</div>
                                                <small class="text-muted">Historical payment methods from completed orders</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-credit-card fa-3x text-muted"></i>
                        </div>
                        <h6 class="text-muted">No payment methods added</h6>
                        <p class="text-muted small">Add a payment method to make purchases easier.</p>
                    </div>
                @endif

                <div class="d-flex justify-content-center mt-4">
                    <button class="btn-modern btn-modern-primary">
                        <i class="fas fa-plus"></i>Add Payment Method
                    </button>
                </div>
            </div>
        </div>

        {{-- Order Statistics --}}
        @if(isset($paymentsData['order_stats']) && $paymentsData['order_stats']['total_orders'] > 0)
        <div class="modern-card mb-4">
            <div class="card-body">
                <div class="section-title">
                    <i class="fas fa-chart-bar"></i>Order Statistics
                </div>

                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="stat-item">
                            <div class="stat-number">{{ $paymentsData['order_stats']['total_orders'] }}</div>
                            <div class="stat-label">Total Orders</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-item">
                            <div class="stat-number text-success">{{ $paymentsData['order_stats']['completed_orders'] }}</div>
                            <div class="stat-label">Completed</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-item">
                            <div class="stat-number text-warning">{{ $paymentsData['order_stats']['pending_orders'] }}</div>
                            <div class="stat-label">Pending</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-item">
                            <div class="stat-number text-primary">{{ $paymentsData['order_stats']['total_spent'] }}</div>
                            <div class="stat-label">Total Spent</div>
                        </div>
                    </div>
                </div>

                @if($paymentsData['order_stats']['refunded_orders'] > 0)
                <div class="row justify-content-center">
                    <div class="col-md-6 text-center">
                        <div class="stat-item">
                            <div class="stat-number text-danger">{{ $paymentsData['order_stats']['total_refunded'] }}</div>
                            <div class="stat-label">Total Refunded ({{ $paymentsData['order_stats']['refunded_orders'] }} orders)</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Payment History --}}
        <div class="modern-card">
            <div class="card-body">
                <div class="section-title">
                    <i class="fas fa-history"></i>Payment History
                </div>

                @if(count($paymentsData['payment_history']) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentsData['payment_history'] as $payment)
                                    <tr>
                                        <td>
                                            <code>{{ $payment['id'] }}</code>
                                        </td>
                                        <td>{{ $payment['date'] }}</td>
                                        <td>
                                            <div>
                                                {{ $payment['description'] }}
                                            </div>
                                        </td>
                                        <td><strong>{{ $payment['amount'] }}</strong></td>
                                        <td>
                                            @if($payment['status'] === 'paid')
                                                <span class="summary-badge" style="background: #dcfce7; color: #166534;">
                                                    <i class="fas fa-check-circle me-1"></i>Paid
                                                </span>
                                            @elseif($payment['refunded'])
                                                <span class="summary-badge" style="background: #fee2e2; color: #991b1b;">
                                                    <i class="fas fa-undo me-1"></i>Refunded
                                                </span>
                                            @else
                                                <span class="summary-badge" style="background: #fef3c7; color: #92400e;">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ $payment['download_url'] }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-receipt fa-3x text-muted"></i>
                        </div>
                        <h6 class="text-muted">No payment history</h6>
                        <p class="text-muted small">Your payment history will appear here after making purchases.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Billing Address --}}
        <div class="modern-card mb-4">
            <div class="card-body">
                <div class="section-title">
                    <i class="fas fa-map-marker-alt"></i>Billing Address
                </div>

                <div class="billing-info">
                    <div class="summary-item">
                        <span class="summary-label">Address</span>
                        <span class="summary-value">{{ $paymentsData['billing_address']['line1'] }}</span>
                    </div>
                    @if($paymentsData['billing_address']['line2'])
                        <div class="summary-item">
                            <span class="summary-label">Line 2</span>
                            <span class="summary-value">{{ $paymentsData['billing_address']['line2'] }}</span>
                        </div>
                    @endif
                    <div class="summary-item">
                        <span class="summary-label">City</span>
                        <span class="summary-value">{{ $paymentsData['billing_address']['city'] }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">State</span>
                        <span class="summary-value">{{ $paymentsData['billing_address']['state'] }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Postal Code</span>
                        <span class="summary-value">{{ $paymentsData['billing_address']['postal_code'] }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Phone</span>
                        <span class="summary-value">{{ $paymentsData['billing_address']['phone'] }}</span>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-edit me-1"></i>Update Billing Address
                    </button>
                </div>
            </div>
        </div>

        {{-- Subscription Status --}}
        <div class="modern-card">
            <div class="card-body">
                <div class="section-title">
                    <i class="fas fa-crown"></i>Subscription
                </div>

                @if($paymentsData['subscription_status']['active'])
                    <div class="text-center mb-3">
                        <div class="summary-badge active mb-2" style="font-size: 1.125rem; padding: 0.75rem 1.5rem;">
                            {{ $paymentsData['subscription_status']['plan'] }}
                        </div>
                        <div class="h4 mb-0">{{ $paymentsData['subscription_status']['amount'] }}</div>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Status</span>
                        <span class="summary-badge active">Active</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Next Billing</span>
                        <span class="summary-value">{{ $paymentsData['subscription_status']['next_billing_date']->format('M j, Y') }}</span>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-edit me-1"></i>Change Plan
                        </button>
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-times me-1"></i>Cancel Subscription
                        </button>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-crown fa-3x text-muted"></i>
                        </div>
                        <h6 class="text-muted">No Active Subscription</h6>
                        <p class="text-muted small">Upgrade to access premium features.</p>
                        <button class="btn-modern btn-modern-primary btn-sm">
                            <i class="fas fa-arrow-up me-1"></i>Upgrade Now
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
