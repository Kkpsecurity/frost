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
                                    <h3 class="text-primary mb-0">${{ number_format($course->price, 2) }}</h3>
                                    <small class="text-muted">USD</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Method Selection --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>Select Payment Method
                            </h5>
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
                                                <button type="submit" class="btn btn-primary w-100 mt-3">
                                                    <i class="fas fa-lock me-2"></i>Pay with Card
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
                                                <button type="submit" class="btn w-100 mt-3" style="background: #0070ba; color: white; border: none;">
                                                    <i class="fab fa-paypal me-2"></i>Pay with PayPal
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
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }

        .payment-option:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
        }

        .payment-header h6 {
            margin-top: 10px;
            font-weight: 600;
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
    </style>
</x-frontend.site.site-wrapper>