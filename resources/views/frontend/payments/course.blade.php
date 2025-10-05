{{-- Course Payment Selection Page --}}
<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Select Payment Method'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Choose your payment method for course enrollment' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'payment, course enrollment, stripe, paypal' }}">
        <link rel="stylesheet" href="{{ asset('css/components/payment.css') }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="frost-secondary-bg py-5" style="min-height: 70vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    {{-- Course Info Header --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-1">{{ $course->title }}</h4>
                                    <p class="text-muted mb-0">{{ $course->description ?? 'Professional Security Training Course' }}</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h3 class="text-primary mb-0">${{ number_format((float)$course->price, 2) }}</h3>
                                    <small class="text-muted">USD</small>
                                </div>
                            </div>
                            {{-- Debug Info --}}
                            <div class="mt-3 p-2" style="background: #f8f9fa; border-radius: 5px; font-size: 12px;">
                                <strong>Debug:</strong>
                                Stripe: {{ $paymentConfig['stripe_enabled'] ? 'ENABLED' : 'DISABLED' }} |
                                PayPal: {{ $paymentConfig['paypal_enabled'] ? 'ENABLED' : 'DISABLED' }} |
                                Guest: {{ ($isGuest ?? true) ? 'YES' : 'NO' }}
                            </div>
                        </div>
                    </div>

                    @if($isGuest ?? true)
                        {{-- Quick Registration Panel for Guests --}}
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-plus me-2"></i>Complete Your Registration
                                </h5>
                                <small>Enter your details to proceed with payment</small>
                            </div>
                            <div class="card-body">
                                <form id="guestRegistrationForm" class="row g-3">
                                    @csrf
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="firstName" name="first_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="lastName" name="last_name" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                            <label class="form-check-label" for="terms">
                                                I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a> <span class="text-danger">*</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                            <label class="form-check-label" for="newsletter">
                                                Subscribe to course updates and security training newsletters
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- Payment Method Selection --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>Select Payment Method
                            </h5>
                            <small class="text-muted">Choose your preferred payment method below</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($paymentConfig['stripe_enabled'])
                                    <div class="col-md-6 mb-3">
                                        <form action="{{ route('payments.course.stripe', $course->id) }}" method="POST" class="payment-form">
                                            @csrf
                                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                                            <div class="payment-option stripe-option">
                                                <div class="payment-header">
                                                    <i class="fab fa-cc-stripe fa-2x text-primary"></i>
                                                    <h6>Credit/Debit Card</h6>
                                                </div>
                                                <div class="payment-body">
                                                    <p class="small text-muted">Pay securely with your credit or debit card via Stripe</p>
                                                    <div class="accepted-cards">
                                                        <i class="fab fa-cc-visa"></i>
                                                        <i class="fab fa-cc-mastercard"></i>
                                                        <i class="fab fa-cc-amex"></i>
                                                        <i class="fab fa-cc-discover"></i>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100 mt-3" id="stripePaymentBtn">
                                                    <i class="fas fa-lock me-2"></i>Pay with Card - ${{ number_format((float)$course->price, 2) }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if($paymentConfig['paypal_enabled'])
                                    <div class="col-md-6 mb-3">
                                        <form action="{{ route('payments.course.paypal', $course->id) }}" method="POST" class="payment-form">
                                            @csrf
                                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                                            <div class="payment-option paypal-option">
                                                <div class="payment-header">
                                                    <i class="fab fa-paypal fa-2x" style="color: #0070ba;"></i>
                                                    <h6>PayPal</h6>
                                                </div>
                                                <div class="payment-body">
                                                    <p class="small text-muted">Pay with your PayPal account or PayPal Credit</p>
                                                    <div class="paypal-benefits">
                                                        <small class="text-success">
                                                            <i class="fas fa-check me-1"></i>Buyer Protection
                                                        </small>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn w-100 mt-3" style="background: #0070ba; color: white; border: none;" id="paypalPaymentBtn">
                                                    <i class="fab fa-paypal me-2"></i>Pay with PayPal - ${{ number_format((float)$course->price, 2) }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if(!$paymentConfig['stripe_enabled'] && !$paymentConfig['paypal_enabled'])
                                    <div class="col-12">
                                        <div class="alert alert-warning text-center">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Payment methods are currently being configured.</strong>
                                            <br>Please contact support to complete your enrollment.
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Security Info --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="security-info text-center">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt text-success me-1"></i>
                                            Your payment information is secure and encrypted
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Back to Course Button --}}
                    <div class="text-center mt-4">
                        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Course Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-frontend.site.partials.footer />

    <style>
        .payment-option {
            background: linear-gradient(145deg, #1e3a8a, #1e40af);
            border: 2px solid rgba(79, 70, 229, 0.3);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.4s ease;
            height: 100%;
            color: white;
        }

        .payment-option:hover {
            transform: translateY(-5px);
            border-color: #8b5cf6;
            box-shadow: 0 15px 35px rgba(79, 70, 229, 0.4);
            background: linear-gradient(145deg, #3730a3, #4f46e5);
        }

        .payment-header h6 {
            margin-top: 10px;
            font-weight: 600;
            color: white;
            font-size: 1.1rem;
        }

        .payment-body p {
            color: rgba(255, 255, 255, 0.8);
        }

        .accepted-cards i {
            font-size: 1.5rem;
            margin: 0 5px;
            color: #6c757d;
        }

        .paypal-benefits {
            margin-top: 10px;
        }

        .security-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .registration-complete {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>

    @if($isGuest ?? true)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add payment method click tracking for debugging
            const stripeBtn = document.getElementById('stripePaymentBtn');
            const paypalBtn = document.getElementById('paypalPaymentBtn');

            if (stripeBtn) {
                stripeBtn.addEventListener('click', function() {
                    console.log('Stripe payment method selected');
                });
            }

            if (paypalBtn) {
                paypalBtn.addEventListener('click', function() {
                    console.log('PayPal payment method selected');
                });
            }
        });
    </script>
    @endif
</x-frontend.site.site-wrapper>
