{{-- PayPal Checkout Simulation Page --}}
<x-frontend.site.site-wrapper title="PayPal Checkout - {{ $course->title }}">
    <x-slot:head>
        <meta name="description" content="Complete your payment securely with PayPal">
        <style>
            body { background: #f5f5f5; }
            .paypal-checkout {
                min-height: 100vh;
                background: linear-gradient(135deg, #0070ba 0%, #003087 100%);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .checkout-card {
                background: white;
                border-radius: 8px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
                padding: 40px;
                max-width: 450px;
                width: 100%;
                margin: 20px;
            }
            .paypal-logo {
                text-align: center;
                margin-bottom: 30px;
            }
            .paypal-logo img {
                max-width: 150px;
                height: auto;
            }
            .paypal-logo-text {
                color: #0070ba;
                font-size: 32px;
                font-weight: 600;
                font-family: 'PayPal Sans', sans-serif;
            }
            .login-section {
                border: 1px solid #cbd2d9;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
            }
            .payment-form .form-control {
                border: 1px solid #cbd2d9;
                border-radius: 4px;
                padding: 12px 16px;
                font-size: 16px;
            }
            .payment-form .form-control:focus {
                border-color: #0070ba;
                box-shadow: 0 0 0 2px rgba(0, 112, 186, 0.2);
            }
            .btn-paypal {
                background: #ffc439;
                border: 1px solid #ffc439;
                border-radius: 25px;
                color: #003087;
                font-weight: 600;
                padding: 12px 24px;
                font-size: 16px;
                width: 100%;
                transition: all 0.15s ease;
            }
            .btn-paypal:hover {
                background: #ffb700;
                border-color: #ffb700;
                color: #003087;
            }
            .btn-paypal-blue {
                background: #0070ba;
                border: 1px solid #0070ba;
                border-radius: 25px;
                color: white;
                font-weight: 600;
                padding: 12px 24px;
                font-size: 16px;
                width: 100%;
                margin-top: 10px;
            }
            .btn-paypal-blue:hover {
                background: #005ea6;
                border-color: #005ea6;
                color: white;
            }
            .order-summary {
                background: #f7f7f7;
                border-radius: 4px;
                padding: 20px;
                margin-bottom: 20px;
            }
            .divider {
                text-align: center;
                margin: 20px 0;
                position: relative;
            }
            .divider::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 0;
                right: 0;
                height: 1px;
                background: #cbd2d9;
            }
            .divider span {
                background: white;
                padding: 0 15px;
                color: #6c757d;
                font-size: 14px;
            }
        </style>
    </x-slot:head>

    <div class="paypal-checkout">
        <div class="checkout-card">
            <div class="paypal-logo">
                <div class="paypal-logo-text">PayPal</div>
            </div>

            {{-- Order Summary --}}
            <div class="order-summary">
                <h6 class="mb-3">You're paying {{ config('app.name') }}</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ $course->title }}</span>
                    <span>${{ number_format((float)$amount, 2) }} {{ $currency }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span>${{ number_format((float)$amount, 2) }} {{ $currency }}</span>
                </div>
            </div>

            {{-- PayPal Login Section --}}
            <div class="login-section">
                <h6 class="mb-3">Log in to your PayPal account</h6>
                <form class="payment-form" action="{{ route('payments.success-simulation') }}" method="POST">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <input type="hidden" name="amount" value="{{ $amount }}">
                    <input type="hidden" name="payment_method" value="paypal">
                    <input type="hidden" name="return_url" value="{{ $return_url }}">

                    <div class="mb-3">
                        <input type="email" class="form-control" name="paypal_email" placeholder="Email or mobile number" value="demo@paypal.com" required>
                    </div>

                    <button type="submit" class="btn btn-paypal">
                        Continue
                    </button>
                </form>
            </div>

            <div class="divider">
                <span>or</span>
            </div>

            {{-- Guest Checkout --}}
            <form action="{{ route('payments.success-simulation') }}" method="POST">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">
                <input type="hidden" name="amount" value="{{ $amount }}">
                <input type="hidden" name="payment_method" value="paypal_guest">
                <input type="hidden" name="return_url" value="{{ $return_url }}">

                <button type="submit" class="btn btn-paypal-blue">
                    Pay with Debit or Credit Card
                </button>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Your payment information is secure and encrypted
                </small>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('payments.course', $course->id) }}" class="text-muted small">
                    <i class="fas fa-arrow-left me-1"></i>Back to payment options
                </a>
            </div>
        </div>
    </div>
</x-frontend.site.site-wrapper>
