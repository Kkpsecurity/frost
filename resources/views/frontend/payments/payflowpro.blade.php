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
                        <p class="text-muted">Enter your card details to complete payment</p>
                    </div>

                    <!-- Order Summary Card -->
                    <div class="card mb-4">
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
                                    <p class="text-muted small mb-0">Order #{{ $order->id }}</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h4 class="mb-0">${{ number_format($order->total, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form Card -->
                    <div class="card">
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
                                        <span class="input-group-text">
                                            <i class="fas fa-credit-card"></i>
                                        </span>
                                        <input type="text" class="form-control payment-input" id="card-number"
                                            name="card_number" required placeholder="1234 5678 9012 3456" maxlength="19"
                                            pattern="[0-9\s]+" inputmode="numeric">
                                    </div>
                                    <small class="text-muted">Accepted cards: Visa, Mastercard, American Express</small>
                                </div>

                                <!-- Cardholder Name -->
                                <div class="mb-3">
                                    <label for="cardholder-name" class="form-label">
                                        Cardholder Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control payment-input" id="cardholder-name"
                                        name="cardholder_name" required placeholder="John Doe">
                                </div>

                                <div class="row">
                                    <!-- Expiration Date -->
                                    <div class="col-md-6 mb-3">
                                        <label for="expiry-date" class="form-label">
                                            Expiration Date
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control payment-input" id="expiry-date"
                                            name="expiry_date" required placeholder="MM/YY" maxlength="5"
                                            pattern="[0-9/]+" inputmode="numeric">
                                    </div>

                                    <!-- CVV -->
                                    <div class="col-md-6 mb-3">
                                        <label for="cvv" class="form-label">
                                            CVV
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control payment-input" id="cvv"
                                            name="cvv" required placeholder="123" maxlength="4" pattern="[0-9]+"
                                            inputmode="numeric">
                                        <small class="text-muted">3-4 digits on back of card</small>
                                    </div>
                                </div>

                                <!-- Billing Address -->
                                <div class="mb-3">
                                    <label for="billing-address" class="form-label">
                                        Billing Address
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control payment-input" id="billing-address"
                                        name="billing_address" required placeholder="123 Main St">
                                </div>

                                <div class="row">
                                    <!-- City -->
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">
                                            City
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control payment-input" id="city"
                                            name="city" required placeholder="New York">
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-3 mb-3">
                                        <label for="state" class="form-label">
                                            State
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control payment-input" id="state"
                                            name="state" required placeholder="NY" maxlength="2">
                                    </div>

                                    <!-- ZIP Code -->
                                    <div class="col-md-3 mb-3">
                                        <label for="zip" class="form-label">
                                            ZIP Code
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control payment-input" id="zip"
                                            name="zip" required placeholder="10001" maxlength="10"
                                            pattern="[0-9-]+" inputmode="numeric">
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Test Mode:</strong> This is a test payment form. No actual charges will be
                                    made.
                                    <br>
                                    <small>Use any valid card format for testing purposes</small>
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
                        <small class="text-muted">
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
                const cardNumber = document.getElementById('card-number');
                const expiryDate = document.getElementById('expiry-date');
                const cvv = document.getElementById('cvv');

                // Format card number with spaces
                cardNumber.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\s/g, '');
                    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                    e.target.value = formattedValue;
                });

                // Format expiry date with slash
                expiryDate.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 2) {
                        value = value.substring(0, 2) + '/' + value.substring(2, 4);
                    }
                    e.target.value = value;
                });

                // Limit CVV to numbers only
                cvv.addEventListener('input', function(e) {
                    e.target.value = e.target.value.replace(/\D/g, '');
                });

                // Form submission
                form.addEventListener('submit', function(e) {
                    const submitButton = document.getElementById('submit-button');
                    submitButton.disabled = true;
                    submitButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                });
            });
        </script>
    </x-slot:scripts>

</x-frontend.site.site-wrapper>
