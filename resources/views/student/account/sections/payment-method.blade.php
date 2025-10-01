<div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="section-title mb-0">
            <i class="fas fa-credit-card"></i>Payment Methods
        </div>
        <div class="dropdown">
            <button class="btn-modern btn-modern-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fas fa-plus me-2"></i>Add Payment Method
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                {{-- Dynamically show available payment methods based on admin settings --}}
                @php
                    $stripeEnabled = setting('payments.stripe.enabled', false);
                    $paypalEnabled = setting('payments.paypal.enabled', false);
                @endphp

                @if ($stripeEnabled)
                    <li><a class="dropdown-item" href="#" onclick="showAddCardModal('stripe')">
                            <i class="fab fa-stripe me-2 text-primary"></i>Credit/Debit Card (Stripe)
                        </a></li>
                @endif

                @if ($paypalEnabled)
                    <li><a class="dropdown-item" href="#" onclick="showAddPayPalModal()">
                            <i class="fab fa-paypal me-2 text-primary"></i>PayPal Account
                        </a></li>
                @endif

                @if (!$stripeEnabled && !$paypalEnabled)
                    <li><span class="dropdown-item-text text-muted">
                            <i class="fas fa-exclamation-circle me-2"></i>No payment methods configured
                        </span></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><span class="dropdown-item-text small text-muted">
                            Contact administrator to enable payment methods
                        </span></li>
                @endif
            </ul>
        </div>
    </div>

    {{-- Saved Payment Methods --}}
    <div id="saved-payment-methods">
        @if (isset($paymentsData['saved_methods']) && count($paymentsData['saved_methods']) > 0)
            @foreach ($paymentsData['saved_methods'] as $method)
                <div class="payment-method-card mb-3" data-method-id="{{ $method['id'] }}">
                    <div class="card border-0"
                        style="border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    @if ($method['type'] === 'card')
                                        <div class="payment-icon me-3">
                                            <i
                                                class="fab fa-cc-{{ strtolower($method['brand']) }} fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">
                                                {{ ucfirst($method['brand']) }} •••• {{ $method['last4'] }}
                                            </div>
                                            <small class="text-muted">
                                                Expires {{ $method['exp_month'] }}/{{ $method['exp_year'] }}
                                                @if ($method['is_default'])
                                                    <span class="badge bg-success ms-2">Default</span>
                                                @endif
                                            </small>
                                        </div>
                                    @elseif($method['type'] === 'paypal')
                                        <div class="payment-icon me-3">
                                            <i class="fab fa-paypal fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">PayPal Account</div>
                                            <small class="text-muted">
                                                {{ $method['email'] }}
                                                @if ($method['is_default'])
                                                    <span class="badge bg-success ms-2">Default</span>
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if (!$method['is_default'])
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="setDefaultPaymentMethod('{{ $method['id'] }}')">
                                            <i class="fas fa-star me-1"></i>Set Default
                                        </button>
                                    @endif
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="editPaymentMethod('{{ $method['id'] }}')">
                                                    <i class="fas fa-edit me-2"></i>Edit
                                                </a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item text-danger" href="#"
                                                    onclick="deletePaymentMethod('{{ $method['id'] }}')">
                                                    <i class="fas fa-trash me-2"></i>Delete
                                                </a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-5" id="no-payment-methods">
                <div class="mb-4">
                    <i class="fas fa-credit-card fa-4x text-muted"></i>
                </div>
                <h5 class="text-muted mb-2">No saved payment methods</h5>
                <p class="text-muted">Add a payment method to make checkout faster and easier.</p>

                {{-- Dynamic buttons based on enabled payment methods --}}
                <div class="d-flex justify-content-center gap-3 mt-4">
                    @if ($stripeEnabled)
                        <button class="btn-modern btn-modern-primary" onclick="showAddCardModal('stripe')">
                            <i class="fab fa-stripe me-2"></i>Add Card
                        </button>
                    @endif

                    @if ($paypalEnabled)
                        <button class="btn btn-outline-primary" onclick="showAddPayPalModal()">
                            <i class="fab fa-paypal me-2"></i>Link PayPal
                        </button>
                    @endif

                    @if (!$stripeEnabled && !$paypalEnabled)
                        <div class="alert alert-warning border-0 mt-3" style="background: #fffbeb;">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            <small class="text-warning-emphasis">
                                Payment methods are not configured. Please contact support to enable payment processing.
                            </small>
                        </div>
                    @endif
                </div>

                {{-- Payment Method Status Information --}}
                @if ($stripeEnabled || $paypalEnabled)
                    <div class="mt-4">
                        <small class="text-muted">Available payment methods:</small>
                        <div class="d-flex justify-content-center gap-4 mt-2">
                            @if ($stripeEnabled)
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fab fa-stripe text-primary"></i>
                                    <small class="text-muted">Credit/Debit Cards</small>
                                </div>
                            @endif
                            @if ($paypalEnabled)
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fab fa-paypal text-primary"></i>
                                    <small class="text-muted">PayPal Account</small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
    </div>
    @endif
</div>

{{-- Payment Method Security Info --}}
<div class="alert alert-info border-0 mt-4" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);">
    <div class="d-flex align-items-start">
        <i class="fas fa-shield-alt text-info me-3 mt-1"></i>
        <div>
            <strong class="text-info">Your payment information is secure</strong>
            <p class="mb-0 text-info-emphasis small mt-1">
                All payment data is encrypted and processed securely through Stripe and PayPal.
                We never store your complete card information on our servers.
            </p>
        </div>
    </div>
</div>
