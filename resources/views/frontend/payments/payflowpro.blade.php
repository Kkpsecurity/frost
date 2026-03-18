{{-- PayFlowPro Payment Form --}}
<x-frontend.site.site-wrapper :title="'PayFlowPro Payment - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Complete your payment with PayFlowPro">
        <style>
            .payment-input {
                height: 45px;
                font-size: 16px;
            }

            .card-icon {
                font-size: 2rem;
                margin-right: 0.5rem;
            }

            .form-label {
                font-weight: 500;
            }
        </style>
    </x-slot:head>

    <x-frontend.site.partials.header />

    <main class="main-page-content frost-secondary-bg">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Page Header -->
                    <div class="text-center mb-4">
                        <h1 class="h2 mb-2">
                            <i class="fas fa-credit-card text-success"></i>
                            PayFlow Pro Payment
                        </h1>
                        <p class="text-white-50">Enter your card details to complete payment</p>
                    </div>

                    <!-- Order Summary Card -->
                    <div class="card mb-4 bg-dark border-secondary text-white">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Order Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-1">{{ $course->name }}</h6>
                                    <p class="text-white-50 small mb-0">Order #{{ $order->id }}</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h4 class="mb-0">${{ number_format($order->total, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form Card -->
                    <div class="card bg-dark border-secondary text-white">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>
                                Payment Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('payments.payflowpro.process', $payment) }}" method="POST"
                                id="payment-form">
                                @csrf

                                <!-- Card Number -->
                                <div class="mb-3">
                                    <label for="card-number" class="form-label">
                                        Card Number
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-dark border-secondary text-white">
                                            <i class="fas fa-credit-card"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control payment-input bg-dark border-secondary text-white @error('card_number') is-invalid @enderror"
                                            id="card-number" name="card_number" required
                                            placeholder="1234 5678 9012 3456" maxlength="19" pattern="[0-9\s]+"
                                            inputmode="numeric" value="{{ old('card_number') }}">
                                        @error('card_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-white-50">Accepted cards: Visa, Mastercard, American
                                        Express</small>
                                </div>

                                <!-- Cardholder Name -->
                                <div class="mb-3">
                                    <label for="cardholder-name" class="form-label">
                                        Cardholder Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control payment-input bg-dark border-secondary text-white"
                                        id="cardholder-name" name="cardholder_name" required placeholder="John Doe">
                                </div>

                                <div class="row">
                                    <!-- Expiration Date -->
                                    <div class="col-md-6 mb-3">
                                        <label for="expiry-date" class="form-label">
                                            Expiration Date
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control payment-input bg-dark border-secondary text-white @error('expiry_date') is-invalid @enderror"
                                            id="expiry-date" name="expiry_date" required placeholder="MM/YY"
                                            maxlength="5" pattern="[0-9/]+" inputmode="numeric"
                                            value="{{ old('expiry_date') }}">
                                        @error('expiry_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- CVV -->
                                    <div class="col-md-6 mb-3">
                                        <label for="cvv" class="form-label">
                                            CVV
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control payment-input bg-dark border-secondary text-white @error('cvv') is-invalid @enderror"
                                            id="cvv" name="cvv" required placeholder="123" maxlength="4"
                                            pattern="[0-9]+" inputmode="numeric">
                                        @error('cvv')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-white-50">3-4 digits on back of card</small>
                                    </div>
                                </div>

                                <!-- Billing Address -->
                                <div class="mb-3">
                                    <label for="billing-address" class="form-label">
                                        Billing Address
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control payment-input bg-dark border-secondary text-white"
                                        id="billing-address" name="billing_address" required placeholder="123 Main St">
                                </div>

                                <!-- ZIP Code -->
                                <div class="mb-3">
                                    <label for="zip" class="form-label">
                                        ZIP Code
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control payment-input bg-dark border-secondary text-white"
                                        id="zip" name="zip" required placeholder="10001" maxlength="10"
                                        pattern="[0-9-]+" inputmode="numeric">
                                </div>

                                <div class="alert alert-info bg-dark border border-info text-white">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Test Mode:</strong> This is a test payment form. No actual charges will be
                                    made.
                                    <br>
                                    <small class="text-white-50">Use any valid card format for testing purposes</small>
                                </div>

                                <!-- Form Actions -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="{{ route('checkout.show', $order) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Back to Checkout
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg" id="submit-button">
                                        <i class="fas fa-lock me-1"></i>
                                        Pay ${{ number_format($order->total, 2) }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Security Notice -->
                    <div class="text-center mt-4">
                        <small class="text-white-50">
                            <i class="fas fa-lock me-1"></i>
                            Your payment information is secure and encrypted with SSL
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-frontend.site.partials.footer />

    <x-slot:scripts>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('payment-form');
                const cardNumberEl = document.getElementById('card-number');
                const expiryDateEl = document.getElementById('expiry-date');
                const cvvEl = document.getElementById('cvv');

                // ── Luhn (Mod-10) check ──────────────────────────────────────
                function luhn(value) {
                    const digits = value.replace(/\D/g, '');
                    if (digits.length < 13) return false;
                    let sum = 0;
                    let alt = false;
                    for (let i = digits.length - 1; i >= 0; i--) {
                        let n = parseInt(digits[i], 10);
                        if (alt) {
                            n *= 2;
                            if (n > 9) n -= 9;
                        }
                        sum += n;
                        alt = !alt;
                    }
                    return sum % 10 === 0;
                }

                // ── Expiry not in the past ───────────────────────────────────
                function expiryValid(value) {
                    const parts = value.split('/');
                    if (parts.length !== 2) return false;
                    const month = parseInt(parts[0], 10);
                    const year = parseInt('20' + parts[1], 10);
                    if (month < 1 || month > 12) return false;
                    const now = new Date();
                    const exp = new Date(year, month); // first day of month AFTER expiry
                    return exp > now;
                }

                // ── Inline error helper ──────────────────────────────────────
                function setError(el, msg) {
                    el.classList.add('is-invalid');
                    el.classList.remove('is-valid');
                    let fb = el.parentElement.querySelector('.invalid-feedback');
                    if (!fb) {
                        fb = document.createElement('div');
                        fb.className = 'invalid-feedback';
                        el.parentElement.appendChild(fb);
                    }
                    fb.textContent = msg;
                }

                function clearError(el) {
                    el.classList.remove('is-invalid');
                    el.classList.add('is-valid');
                    const fb = el.parentElement.querySelector('.invalid-feedback');
                    if (fb) fb.textContent = '';
                }

                // ── Card number: format + live Luhn feedback ─────────────────
                cardNumberEl.addEventListener('input', function(e) {
                    const raw = e.target.value.replace(/\D/g, '').substring(0, 16);
                    e.target.value = raw.match(/.{1,4}/g)?.join(' ') || raw;
                    // Only show Luhn error once a plausible length is entered
                    if (raw.length >= 13) {
                        luhn(raw) ?
                            clearError(cardNumberEl) :
                            setError(cardNumberEl, 'Invalid card number.');
                    } else {
                        cardNumberEl.classList.remove('is-invalid', 'is-valid');
                    }
                });

                // ── Expiry: auto-slash + live past-date feedback ─────────────
                expiryDateEl.addEventListener('input', function(e) {
                    let raw = e.target.value.replace(/\D/g, '');
                    if (raw.length >= 3) {
                        raw = raw.substring(0, 2) + '/' + raw.substring(2, 4);
                    }
                    e.target.value = raw;
                    if (raw.length === 5) {
                        expiryValid(raw) ?
                            clearError(expiryDateEl) :
                            setError(expiryDateEl, 'Card has expired or date is invalid.');
                    } else {
                        expiryDateEl.classList.remove('is-invalid', 'is-valid');
                    }
                });

                // ── CVV: digits only ─────────────────────────────────────────
                cvvEl.addEventListener('input', function(e) {
                    e.target.value = e.target.value.replace(/\D/g, '');
                });

                // ── Form submit: full validation gate ───────────────────────
                form.addEventListener('submit', function(e) {
                    let valid = true;

                    const rawCard = cardNumberEl.value.replace(/\D/g, '');
                    if (!luhn(rawCard)) {
                        e.preventDefault();
                        setError(cardNumberEl, 'Invalid card number.');
                        valid = false;
                    }

                    if (!expiryValid(expiryDateEl.value)) {
                        e.preventDefault();
                        setError(expiryDateEl, 'Card has expired or date is invalid.');
                        valid = false;
                    }

                    const cvvLen = cvvEl.value.length;
                    if (cvvLen < 3 || cvvLen > 4) {
                        e.preventDefault();
                        setError(cvvEl, 'CVV must be 3 or 4 digits.');
                        valid = false;
                    }

                    if (valid) {
                        const submitButton = document.getElementById('submit-button');
                        submitButton.disabled = true;
                        submitButton.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                    }
                });
            });
        </script>
    </x-slot:scripts>

</x-frontend.site.site-wrapper>
