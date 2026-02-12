{{-- Stripe Payment Form --}}
<x-frontend.site.site-wrapper :title="'Stripe Payment - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Complete your payment with Stripe">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="https://js.stripe.com/v3/"></script>
        <style>
            #card-element {
                border: 1px solid #ced4da;
                border-radius: 0.375rem;
                padding: 12px;
                background: white;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }

            #card-element.StripeElement--focus {
                border-color: #86b7fe;
                outline: 0;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }

            #card-element.StripeElement--invalid {
                border-color: #dc3545;
            }

            #card-element.StripeElement--complete {
                border-color: #198754;
            }

            .payment-processing {
                pointer-events: none;
                opacity: 0.6;
            }

            #card-errors {
                font-size: 0.875rem;
                margin-top: 0.5rem;
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
                            <i class="fab fa-stripe text-info"></i>
                            Stripe Payment
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
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>
                                Card Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="payment-form">
                                @csrf

                                <!-- Email (for Stripe receipt) -->
                                <div class="mb-4">
                                    <label for="email" class="form-label">
                                        Email Address
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control form-control-lg" id="email"
                                        name="email" value="{{ auth()->user()->email }}" required>
                                    <small class="text-muted">Receipt will be sent to this email</small>
                                </div>

                                <!-- Cardholder Name -->
                                <div class="mb-4">
                                    <label for="cardholder-name" class="form-label">
                                        Cardholder Name
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-lg" id="cardholder-name"
                                        name="cardholder_name" value="{{ auth()->user()->name }}" required
                                        placeholder="Name on card">
                                </div>

                                <!-- Card Element -->
                                <div class="mb-4">
                                    <label for="card-element" class="form-label">
                                        Card Information
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div id="card-element"></div>
                                    <div id="card-errors" class="text-danger" role="alert"></div>
                                </div>

                                <!-- Accepted Cards -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <small class="text-muted">We accept:</small>
                                        <i class="fab fa-cc-visa fa-2x text-primary"></i>
                                        <i class="fab fa-cc-mastercard fa-2x text-warning"></i>
                                        <i class="fab fa-cc-amex fa-2x text-info"></i>
                                        <i class="fab fa-cc-discover fa-2x text-danger"></i>
                                    </div>
                                </div>

                                <!-- Test Mode Alert -->
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Test Mode:</strong> This is a test environment. Use test card:
                                    <code>4242 4242 4242 4242</code>
                                    <br>
                                    <small class="text-muted">Use any future expiry date and any 3-digit CVC</small>
                                </div>

                                <!-- Payment Error -->
                                <div id="payment-error" class="alert alert-danger d-none" role="alert"></div>

                                <!-- Form Actions -->
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <a href="{{ route('checkout.show', $order) }}"
                                        class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Back
                                    </a>
                                    <button type="submit" class="btn btn-info btn-lg px-5" id="submit-button">
                                        <span id="button-text">
                                            <i class="fas fa-lock me-2"></i>
                                            Pay ${{ number_format($order->total, 2) }}
                                        </span>
                                        <span id="spinner" class="spinner-border spinner-border-sm d-none"
                                            role="status">
                                            <span class="visually-hidden">Processing...</span>
                                        </span>
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
            // Initialize Stripe with publishable key
            // TODO: Replace with your actual Stripe publishable key from .env
            const stripeKey = '{{ config('services.stripe.key', 'pk_test_51234567890') }}';
            const stripe = Stripe(stripeKey);

            // Create Stripe Elements instance
            const elements = stripe.elements();

            // Create and mount card element with custom styling
            const cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#212529',
                        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial',
                        '::placeholder': {
                            color: '#6c757d'
                        }
                    },
                    invalid: {
                        color: '#dc3545',
                        iconColor: '#dc3545'
                    }
                },
                hidePostalCode: true
            });

            cardElement.mount('#card-element');

            // Handle real-time validation errors from card element
            cardElement.on('change', function(event) {
                const displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            // Handle form submission
            const form = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit-button');
            const buttonText = document.getElementById('button-text');
            const spinner = document.getElementById('spinner');
            const paymentError = document.getElementById('payment-error');

            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                // Disable submit button and show spinner
                submitButton.disabled = true;
                buttonText.classList.add('d-none');
                spinner.classList.remove('d-none');
                form.classList.add('payment-processing');
                paymentError.classList.add('d-none');

                const cardholderName = document.getElementById('cardholder-name').value;
                const email = document.getElementById('email').value;

                try {
                    // Create PaymentIntent on server
                    const response = await fetch('{{ route('payments.stripe.intent', $payment) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            payment_id: {{ $payment->id }},
                            email: email
                        })
                    });

                    const data = await response.json();

                    if (!response.ok || !data.clientSecret) {
                        throw new Error(data.error || 'Failed to initialize payment');
                    }

                    // Confirm payment with Stripe
                    const {
                        error: stripeError,
                        paymentIntent
                    } = await stripe.confirmCardPayment(
                        data.clientSecret, {
                            payment_method: {
                                card: cardElement,
                                billing_details: {
                                    name: cardholderName,
                                    email: email
                                }
                            }
                        }
                    );

                    if (stripeError) {
                        // Show error to customer
                        paymentError.textContent = stripeError.message;
                        paymentError.classList.remove('d-none');

                        // Re-enable button
                        submitButton.disabled = false;
                        buttonText.classList.remove('d-none');
                        spinner.classList.add('d-none');
                        form.classList.remove('payment-processing');
                    } else {
                        // Payment succeeded!
                        if (paymentIntent.status === 'succeeded') {
                            // Update payment on server
                            const confirmResponse = await fetch(
                            '{{ route('payments.stripe.confirm', $payment) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content
                                },
                                body: JSON.stringify({
                                    payment_intent_id: paymentIntent.id,
                                    payment_method: paymentIntent.payment_method
                                })
                            });

                            if (confirmResponse.ok) {
                                // Redirect to success page
                                window.location.href = '{{ route('order.completed', $order) }}';
                            } else {
                                throw new Error('Failed to confirm payment on server');
                            }
                        }
                    }
                } catch (error) {
                    console.error('Payment error:', error);
                    paymentError.textContent = error.message || 'An unexpected error occurred. Please try again.';
                    paymentError.classList.remove('d-none');

                    // Re-enable button
                    submitButton.disabled = false;
                    buttonText.classList.remove('d-none');
                    spinner.classList.add('d-none');
                    form.classList.remove('payment-processing');
                }
            });
        </script>
    </x-slot:scripts>

</x-frontend.site.site-wrapper>
