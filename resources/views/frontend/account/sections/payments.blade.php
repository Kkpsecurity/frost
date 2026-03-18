{{-- Payments Section --}}
<div class="payments-section">
    <h3 class="text-white mb-4">
        <i class="fas fa-credit-card me-2"></i>{{ __('frontend.account.payments_billing') }}
    </h3>

    {{-- Payment Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-body">
                    <h6 class="text-white-50 mb-2">
                        <i class="fas fa-dollar-sign me-2"></i>{{ __('frontend.account.total_spent') }}
                    </h6>
                    <h4 class="text-success mb-0">{{ $data['order_stats']['total_spent'] ?? '$0.00' }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-body">
                    <h6 class="text-white-50 mb-2">
                        <i class="fas fa-shopping-cart me-2"></i>{{ __('frontend.account.total_orders') }}
                    </h6>
                    <h4 class="text-info mb-0">{{ $data['order_stats']['total_orders'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark border-secondary h-100">
                <div class="card-body">
                    <h6 class="text-white-50 mb-2">
                        <i class="fas fa-wallet me-2"></i>{{ __('frontend.account.payment_methods') }}
                    </h6>
                    <h4 class="text-warning mb-0">{{ count($data['saved_methods'] ?? []) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment History --}}
    <div class="mb-4 pb-4 border-bottom border-secondary">
        <h5 class="text-white mb-3">{{ __('frontend.account.payment_history') }}</h5>
        @if (!empty($data['payment_history']))
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('frontend.account.order_id') }}</th>
                            <th>{{ __('frontend.account.date') }}</th>
                            <th>{{ __('frontend.account.description') }}</th>
                            <th>{{ __('frontend.account.amount') }}</th>
                            <th>{{ __('frontend.account.status') }}</th>
                            <th>{{ __('frontend.account.actions') }}</th>
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
                                        <i class="fas fa-download me-1"></i>{{ __('frontend.account.invoice') }}
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
                {{ __('frontend.account.no_payment_history') }}
            </div>
        @endif
    </div>

    {{-- Billing Address --}}
    @if (!empty($data['billing_address']))
        <div class="mb-4">
            <h5 class="text-white mb-3">{{ __('frontend.account.billing_address') }}</h5>
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

{{-- Add Payment Method Modal (Stripe Elements) --}}
@if (($stripeEnabled ?? false) && !($paypalEnabled ?? false))
    <div class="modal fade" id="addPaymentMethodModal" tabindex="-1" aria-labelledby="addPaymentMethodModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="addPaymentMethodModalLabel">
                        <i class="fas fa-credit-card me-2"></i>Add Credit Card
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="stripe-card-errors" class="alert alert-danger d-none" role="alert"></div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Card Details</label>
                        <div id="stripe-card-element" class="form-control bg-dark text-white border-secondary"
                            style="padding: 12px;"></div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="setDefaultCard" value="1" checked>
                        <label class="form-check-label text-white-50" for="setDefaultCard">
                            Set as default payment method
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="stripe-submit-btn" class="btn btn-primary">
                        <span id="stripe-btn-text"><i class="fas fa-lock me-2"></i>Save Card</span>
                        <span id="stripe-btn-spinner" class="d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        (function() {
            const publishableKey = {{ json_encode($stripePublishableKey ?? '') }};
            if (!publishableKey) return;

            const stripe = Stripe(publishableKey);
            const elements = stripe.elements();
            const cardElement = elements.create('card', {
                style: {
                    base: {
                        color: '#ffffff',
                        '::placeholder': {
                            color: '#6c757d'
                        },
                        iconColor: '#ffffff'
                    },
                    invalid: {
                        color: '#dc3545'
                    }
                }
            });

            const modal = document.getElementById('addPaymentMethodModal');
            let mounted = false;

            modal.addEventListener('shown.bs.modal', function() {
                if (!mounted) {
                    cardElement.mount('#stripe-card-element');
                    mounted = true;
                }
            });

            cardElement.on('change', function(event) {
                const errorEl = document.getElementById('stripe-card-errors');
                if (event.error) {
                    errorEl.textContent = event.error.message;
                    errorEl.classList.remove('d-none');
                } else {
                    errorEl.classList.add('d-none');
                }
            });

            document.getElementById('stripe-submit-btn').addEventListener('click', async function() {
                const btnText = document.getElementById('stripe-btn-text');
                const btnSpinner = document.getElementById('stripe-btn-spinner');
                const errorEl = document.getElementById('stripe-card-errors');

                btnText.classList.add('d-none');
                btnSpinner.classList.remove('d-none');
                this.disabled = true;

                try {
                    const {
                        paymentMethod,
                        error
                    } = await stripe.createPaymentMethod({
                        type: 'card',
                        card: cardElement
                    });

                    if (error) {
                        errorEl.textContent = error.message;
                        errorEl.classList.remove('d-none');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('payment_method_id', paymentMethod.id);
                    formData.append('set_default', document.getElementById('setDefaultCard').checked ? '1' :
                        '0');
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content ||
                        '');

                    const response = await fetch('{{ route('account.payments.add-stripe') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData
                    });

                    let result;
                    try {
                        result = await response.json();
                    } catch (e) {
                        result = {
                            success: false,
                            message: 'Request failed. Please refresh and try again.'
                        };
                    }
                    if (result.success) {
                        window.location.reload();
                    } else {
                        errorEl.textContent = result.message || 'Failed to save card.';
                        errorEl.classList.remove('d-none');
                    }
                } finally {
                    btnText.classList.remove('d-none');
                    btnSpinner.classList.add('d-none');
                    this.disabled = false;
                }
            });
        })();
    </script>
@endif
