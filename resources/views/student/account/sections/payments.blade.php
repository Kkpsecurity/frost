{{-- Modern Payments Section Content --}}
<div class="row">
    <div class="col-lg-12">
        {{-- Payment Methods Management --}}
        <div class="modern-card mb-4">
            @include('student.account.sections.payment-method')
        </div>

        {{-- Order Statistics --}}
        @if(isset($paymentsData['order_stats']) && $paymentsData['order_stats']['total_orders'] > 0)
            <div class="modern-card mb-4">
                @include('student.account.components.order-statistics', ['stats' => $paymentsData['order_stats']])
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
                        <table class="table table-hover" style="background: transparent;">
                            <thead style="background: rgba(255,255,255,0.1);">
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
                                    <tr style="background: rgba(255,255,255,0.5);">
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


</div>

{{-- Add Payment Method Modals --}}
{{-- Stripe Card Modal --}}
<div class="modal fade" id="addStripeCardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, var(--frost-primary-color, #212a3e) 0%, var(--frost-secondary-color, #394867) 100%);">
                <h5 class="modal-title text-white">
                    <i class="fab fa-stripe me-2"></i>Add Credit/Debit Card
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="stripe-card-form">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Card Information</label>
                            <div id="stripe-card-element" class="form-control" style="height: 50px; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px;">
                                <!-- Stripe Elements will create form elements here -->
                            </div>
                            <div id="stripe-card-errors" class="text-danger mt-2" role="alert"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cardholder Name</label>
                            <input type="text" class="form-control" id="stripe-cardholder-name" placeholder="John Doe" required style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 12px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Billing ZIP Code</label>
                            <input type="text" class="form-control" id="stripe-billing-zip" placeholder="12345" required style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 12px;">
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="stripe-set-default" checked>
                        <label class="form-check-label fw-bold" for="stripe-set-default">
                            Set as default payment method
                        </label>
                    </div>

                    <div class="alert alert-info border-0" style="background: #f0f9ff;">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        <small class="text-info-emphasis">Your card information is securely processed by Stripe and encrypted at rest.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-modern btn-modern-primary" id="stripe-submit-button">
                    <i class="fas fa-plus me-2"></i>Add Card
                </button>
            </div>
        </div>
    </div>
</div>

{{-- PayPal Account Modal --}}
<div class="modal fade" id="addPayPalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px; border: none; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0070ba 0%, #003087 100%);">
                <h5 class="modal-title text-white">
                    <i class="fab fa-paypal me-2"></i>Link PayPal Account
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-4">
                    <i class="fab fa-paypal fa-4x text-primary mb-3"></i>
                    <h6>Connect your PayPal account for secure payments</h6>
                    <p class="text-muted small">You'll be redirected to PayPal to authorize this connection.</p>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="paypal-set-default" checked>
                    <label class="form-check-label fw-bold" for="paypal-set-default">
                        Set as default payment method
                    </label>
                </div>

                <div class="alert alert-warning border-0" style="background: #fffbeb;">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    <small class="text-warning-emphasis">You'll need an active PayPal account to complete this setup.</small>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" id="paypal-connect-button" style="background: #0070ba;">
                    <i class="fab fa-paypal me-2"></i>Connect PayPal
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deletePaymentMethodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px; border: none; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete Payment Method
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p>Are you sure you want to delete this payment method? This action cannot be undone.</p>
                <div class="alert alert-warning border-0" style="background: #fffbeb;">
                    <small class="text-warning-emphasis">
                        <i class="fas fa-info-circle me-1"></i>
                        If this is your default payment method, please set another method as default first.
                    </small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-payment-method">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Payment Method Cards */
.payment-method-card {
    transition: all 0.3s ease;
}

.payment-method-card:hover {
    transform: translateY(-2px);
}

.payment-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
}

/* Stripe Elements Styling */
.StripeElement {
    box-sizing: border-box;
    height: 40px;
    padding: 10px 12px;
    border: 1px solid transparent;
    border-radius: 4px;
    background-color: white;
    box-shadow: 0 1px 3px 0 #e6ebf1;
    -webkit-transition: box-shadow 150ms ease;
    transition: box-shadow 150ms ease;
}

.StripeElement--focus {
    box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
    border-color: #fa755a;
}

.StripeElement--webkit-autofill {
    background-color: #fefde5 !important;
}

/* Loading States */
.btn-loading {
    position: relative;
    color: transparent !important;
}

.btn-loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s ease infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Fix remaining white backgrounds */
.table-responsive .table {
    background: transparent !important;
}

.table-responsive .table thead th {
    background: rgba(255,255,255,0.2) !important;
    border-color: rgba(255,255,255,0.1) !important;
    color: #1e293b;
    font-weight: 600;
}

.table-responsive .table tbody tr {
    background: rgba(255,255,255,0.5) !important;
    backdrop-filter: blur(5px);
}

.table-responsive .table tbody tr:hover {
    background: rgba(255,255,255,0.7) !important;
}

.table-responsive .table tbody td {
    border-color: rgba(255,255,255,0.2) !important;
    color: #1e293b;
}

.form-control {
    background: rgba(255,255,255,0.9) !important;
    backdrop-filter: blur(5px);
    border: 2px solid rgba(226,232,240,0.5) !important;
}

.form-control:focus {
    background: rgba(255,255,255,0.95) !important;
    border-color: var(--frost-primary-color, #212a3e) !important;
}

.modal-body {
    background: transparent !important;
}

.dropdown-menu {
    background: rgba(255,255,255,0.95) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(226,232,240,0.3) !important;
}

.dropdown-item {
    background: transparent !important;
}

.dropdown-item:hover {
    background: rgba(33, 42, 62, 0.1) !important;
}
</style>

<script>
// Global variables for Stripe
let stripe, elements, cardElement;

// Payment gateway configuration
const paymentConfig = {
    stripe: {
        enabled: {{ isset($stripeEnabled) && $stripeEnabled ? 'true' : 'false' }},
        @if(isset($stripeEnabled) && $stripeEnabled)
        publishableKey: '{{ setting("payments.stripe.environment", "test") === "test" ? setting("payments.stripe.test_publishable_key", "") : setting("payments.stripe.live_publishable_key", "") }}'
        @endif
    },
    paypal: {
        enabled: {{ isset($paypalEnabled) && $paypalEnabled ? 'true' : 'false' }}
    }
};

// Initialize Stripe dynamically based on configuration
function initializeStripe() {
    if (!paymentConfig.stripe.enabled) {
        console.log('Stripe is not enabled');
        return;
    }

    const stripePublishableKey = paymentConfig.stripe.publishableKey;

    if (typeof Stripe !== 'undefined' && stripePublishableKey && stripePublishableKey.startsWith('pk_')) {
        stripe = Stripe(stripePublishableKey);
        elements = stripe.elements();

        // Create card element with enhanced styling
        cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#424770',
                    fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                    iconColor: '#666EE8',
                },
                invalid: {
                    color: '#e74c3c',
                    iconColor: '#e74c3c',
                },
                complete: {
                    color: '#27ae60',
                    iconColor: '#27ae60',
                }
            },
        });
        console.log('Stripe initialized successfully');
    } else {
        console.error('Stripe could not be initialized:', {
            stripeLoaded: typeof Stripe !== 'undefined',
            keyProvided: !!stripePublishableKey,
            keyFormat: stripePublishableKey ? stripePublishableKey.substring(0, 8) + '...' : 'none'
        });
    }
}

// Show add card modal
function showAddCardModal(provider) {
    if (provider === 'stripe') {
        if (!paymentConfig.stripe.enabled) {
            alert('Stripe payments are not currently enabled. Please contact support.');
            return;
        }

        if (!stripe || !cardElement) {
            alert('Stripe is not properly configured. Please contact support.');
            return;
        }

        const modal = new bootstrap.Modal(document.getElementById('addStripeCardModal'));
        modal.show();

        // Mount Stripe card element when modal is shown
        setTimeout(() => {
            if (cardElement && !cardElement._mounted) {
                cardElement.mount('#stripe-card-element');

                // Handle real-time validation errors from the card Element
                cardElement.on('change', (event) => {
                    const displayError = document.getElementById('stripe-card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
            }
        }, 300);
    }
}

// Show PayPal modal
function showAddPayPalModal() {
    if (!paymentConfig.paypal.enabled) {
        alert('PayPal payments are not currently enabled. Please contact support.');
        return;
    }

    const modal = new bootstrap.Modal(document.getElementById('addPayPalModal'));
    modal.show();
}

// Set default payment method
function setDefaultPaymentMethod(methodId) {
    // Show loading state
    const button = event.target;
    button.classList.add('btn-loading');

    // TODO: Make AJAX request to set default payment method
    fetch('/account/payments/set-default', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ method_id: methodId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to show updated default status
            window.location.reload();
        } else {
            alert('Failed to set default payment method: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while setting the default payment method.');
    })
    .finally(() => {
        button.classList.remove('btn-loading');
    });
}

// Delete payment method
function deletePaymentMethod(methodId) {
    // Store method ID for confirmation
    document.getElementById('confirm-delete-payment-method').setAttribute('data-method-id', methodId);

    // Show confirmation modal
    const modal = new bootstrap.Modal(document.getElementById('deletePaymentMethodModal'));
    modal.show();
}

// Handle Stripe card submission
document.getElementById('stripe-submit-button').addEventListener('click', async (event) => {
    event.preventDefault();

    const button = event.target;
    button.classList.add('btn-loading');

    if (!stripe || !cardElement) {
        alert('Stripe is not properly initialized. Please refresh the page and try again.');
        button.classList.remove('btn-loading');
        return;
    }

    const cardholderName = document.getElementById('stripe-cardholder-name').value;
    const billingZip = document.getElementById('stripe-billing-zip').value;
    const setDefault = document.getElementById('stripe-set-default').checked;

    if (!cardholderName || !billingZip) {
        alert('Please fill in all required fields.');
        button.classList.remove('btn-loading');
        return;
    }

    // Create payment method
    const {error, paymentMethod} = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
        billing_details: {
            name: cardholderName,
            address: {
                postal_code: billingZip,
            },
        },
    });

    if (error) {
        console.error('Stripe error:', error);
        document.getElementById('stripe-card-errors').textContent = error.message;
        button.classList.remove('btn-loading');
    } else {
        // Send payment method to your server
        fetch('/account/payments/add-stripe-method', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                payment_method_id: paymentMethod.id,
                set_default: setDefault
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal and reload page
                bootstrap.Modal.getInstance(document.getElementById('addStripeCardModal')).hide();
                window.location.reload();
            } else {
                alert('Failed to save payment method: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the payment method.');
        })
        .finally(() => {
            button.classList.remove('btn-loading');
        });
    }
});

// Handle PayPal connection
document.getElementById('paypal-connect-button').addEventListener('click', () => {
    const button = event.target;
    button.classList.add('btn-loading');

    const setDefault = document.getElementById('paypal-set-default').checked;

    // Redirect to PayPal OAuth
    window.location.href = `/account/payments/connect-paypal?set_default=${setDefault}`;
});

// Handle delete confirmation
document.getElementById('confirm-delete-payment-method').addEventListener('click', () => {
    const methodId = event.target.getAttribute('data-method-id');
    const button = event.target;
    button.classList.add('btn-loading');

    fetch('/account/payments/delete-method', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ method_id: methodId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and reload page
            bootstrap.Modal.getInstance(document.getElementById('deletePaymentMethodModal')).hide();
            window.location.reload();
        } else {
            alert('Failed to delete payment method: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the payment method.');
    })
    .finally(() => {
        button.classList.remove('btn-loading');
    });
});

// Initialize everything when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Stripe
    initializeStripe();

    // Clean up Stripe elements when modals are hidden
    document.getElementById('addStripeCardModal').addEventListener('hidden.bs.modal', () => {
        if (cardElement && cardElement._mounted) {
            cardElement.unmount();
        }
        // Clear form
        document.getElementById('stripe-card-form').reset();
        document.getElementById('stripe-card-errors').textContent = '';
    });
});
</script>
