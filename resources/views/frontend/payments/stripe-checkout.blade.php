{{-- Stripe Checkout Simulation Page --}}
<x-frontend.site.site-wrapper title="Stripe Checkout - {{ $course->title }}">
    <x-slot:head>
        <meta name="description" content="Complete your payment securely with Stripe">
        <style>
            body { background: #f6f9fc; }
            .stripe-checkout {
                min-height: 100vh;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .checkout-card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
                padding: 40px;
                max-width: 450px;
                width: 100%;
                margin: 20px;
            }
            .stripe-logo {
                color: #635bff;
                font-size: 24px;
                font-weight: 600;
                text-align: center;
                margin-bottom: 30px;
            }
            .payment-form .form-control {
                border: 1px solid #e6ebf1;
                border-radius: 6px;
                padding: 12px 16px;
                font-size: 16px;
                transition: border-color 0.15s ease;
            }
            .payment-form .form-control:focus {
                border-color: #635bff;
                box-shadow: 0 0 0 3px rgba(99, 91, 255, 0.1);
            }
            .btn-stripe {
                background: #635bff;
                border: none;
                border-radius: 6px;
                color: white;
                font-weight: 500;
                padding: 12px 16px;
                font-size: 16px;
                width: 100%;
                transition: background 0.15s ease;
            }
            .btn-stripe:hover {
                background: #5a67d8;
                color: white;
            }
            .order-summary {
                background: #f8fafc;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 30px;
            }
            .security-badges {
                display: flex;
                justify-content: center;
                gap: 20px;
                margin-top: 20px;
                opacity: 0.6;
            }
        </style>
    </x-slot:head>

    <div class="stripe-checkout">
        <div class="checkout-card">
            <div class="stripe-logo">
                <i class="fab fa-stripe-s"></i> Stripe Checkout
            </div>

            {{-- Order Summary --}}
            <div class="order-summary">
                <h6 class="mb-3">Order Summary</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ $course->title }}</span>
                    <span>${{ number_format((float)$amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Processing Fee</span>
                    <span>$0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span>${{ number_format((float)$amount, 2) }} {{ $currency }}</span>
                </div>
            </div>

            {{-- Payment Form --}}
            <form class="payment-form" action="{{ route('payments.success-simulation') }}" method="POST">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">
                <input type="hidden" name="amount" value="{{ $amount }}">
                <input type="hidden" name="payment_method" value="stripe">
                <input type="hidden" name="return_url" value="{{ $return_url }}">

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ auth()->user()->email ?? '' }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Card Number</label>
                    <input type="text" class="form-control" placeholder="1234 1234 1234 1234" value="4242 4242 4242 4242" required>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Expiry</label>
                        <input type="text" class="form-control" placeholder="MM/YY" value="12/27" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">CVC</label>
                        <input type="text" class="form-control" placeholder="123" value="123" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Cardholder Name</label>
                    <input type="text" class="form-control" name="cardholder_name" value="{{ auth()->user()->name ?? '' }}" required>
                </div>

                <button type="submit" class="btn btn-stripe">
                    <i class="fas fa-lock me-2"></i>Pay ${{ number_format((float)$amount, 2) }}
                </button>
            </form>

            <div class="security-badges">
                <small><i class="fas fa-shield-alt"></i> Secured by Stripe</small>
                <small><i class="fas fa-lock"></i> SSL Encrypted</small>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('payments.course', $course->id) }}" class="text-muted small">
                    <i class="fas fa-arrow-left me-1"></i>Back to payment options
                </a>
            </div>
        </div>
    </div>
</x-frontend.site.site-wrapper>
