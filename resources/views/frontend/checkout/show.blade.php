{{-- Checkout Page - Payment Method Selection --}}
<x-frontend.site.site-wrapper :title="'Checkout - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Complete your course enrollment">
        <style>
            .payment-option {
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .payment-option:hover {
                background-color: #f8f9fa;
                border-color: #0d6efd !important;
            }

            .payment-option .form-check-input:checked~.form-check-label {
                font-weight: 500;
            }

            .cursor-pointer {
                cursor: pointer;
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
                        <h1 class="h2 mb-2">Checkout</h1>
                        <h3 class="text-light">Complete your enrollment</h3>
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
                                    <p class="text-muted small mb-0">
                                        Course Enrollment
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h5 class="mb-0">${{ number_format($order->total, 2) }}</h5>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-8">
                                    <strong>Order #{{ $order->id }}</strong>
                                </div>
                                <div class="col-md-4 text-end">
                                    <strong>Total: ${{ number_format($order->total, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection Card -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>
                                Select Payment Method
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('checkout.process', $order) }}" method="POST" id="checkout-form">
                                @csrf

                                <div class="payment-methods">
                                    <!-- PayPal Option -->
                                    @if ($paymentTypes->contains('name', 'PayPal'))
                                        <div class="form-check payment-option mb-3 p-3 ms-1">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                id="paypal" value="paypal">
                                            <label class="form-check-label w-100 cursor-pointer" for="paypal">
                                                <div class="d-flex align-items-center">
                                                    <i class="fab fa-paypal fa-2x text-primary me-3"></i>
                                                    <div>
                                                        <strong>PayPal</strong>
                                                        <p class="mb-0 small text-muted">Pay securely with your PayPal
                                                            account
                                                        </p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endif

                                    <!-- Stripe Option -->
                                    @if ($paymentTypes->contains('name', 'Stripe'))
                                        <div class="form-check payment-option mb-3 p-3 ms-1">
                                            <input class="form-check-input ms-1 me-2" type="radio"
                                                name="payment_method" id="stripe" value="stripe">
                                            <label class="form-check-label w-100 cursor-pointer" for="stripe">
                                                <div class="d-flex align-items-center">
                                                    <i class="fab fa-stripe fa-2x text-info me-3"></i>
                                                    <div>
                                                        <strong>Credit/Debit Card</strong>
                                                        <p class="mb-0 small text-muted">Pay with Visa, Mastercard,
                                                            American
                                                            Express</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endif

                                    <!-- PayFlowPro Option (fallback) -->
                                    <div class="form-check payment-option mb-3 p-3 ">
                                        <input class="form-check-input ms-1 me-2" type="radio" name="payment_method"
                                            id="payflowpro" value="payflowpro" checked>
                                        <label class="form-check-label w-100 cursor-pointer me-2" for="payflowpro">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-credit-card fa-2x text-success me-3"></i>
                                                <div>
                                                    <strong>PayFlow Pro</strong>
                                                    <p class="mb-0 small text-muted">Secure payment processing</p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                @error('payment_method')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Back to Courses
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        Continue to Payment
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Security Notice -->
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="fas fa-lock me-1"></i>
                            Your payment information is secure and encrypted
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
                // Make entire payment option clickable
                document.querySelectorAll('.payment-option').forEach(function(option) {
                    option.addEventListener('click', function(e) {
                        if (e.target.type !== 'radio') {
                            const radio = this.querySelector('input[type="radio"]');
                            radio.checked = true;
                        }
                    });
                });
            });
        </script>
    </x-slot:scripts>

</x-frontend.site.site-wrapper>
