{{-- Payment Processing Panel --}}
@props(['payment' => null, 'course' => null, 'paymentConfig' => []])

@push('component-styles')
    <link rel="stylesheet" href="{{ asset('css/components/payment.css') }}">
@endpush

<div class="frost-secondary-bg py-5" style="min-height: calc(100vh - 200px);">
    <div class="container">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb" style="background: rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 12px 20px;">
                <li class="breadcrumb-item">
                    <a href="{{ route('courses.index') }}" class="text-info text-decoration-none">
                        <i class="fas fa-home me-1"></i>Courses
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('courses.show', $course->id) }}" class="text-info text-decoration-none">{{ $course->title }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('courses.enroll', $course->id) }}" class="text-info text-decoration-none">Enrollment</a>
                </li>
                <li class="breadcrumb-item active text-white" aria-current="page">Payment</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                {{-- Payment Processing Card --}}
                <div class="card border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.95); border-radius: 15px;">
                    <div class="card-header text-center py-4" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); border-radius: 15px 15px 0 0;">
                        <h2 class="text-white mb-2">
                            <i class="fas fa-credit-card me-2"></i>
                            Secure Payment
                        </h2>
                        <p class="text-white-50 mb-0">Complete your course enrollment</p>
                    </div>

                    <div class="card-body p-5">
                        {{-- Order Summary --}}
                        <div class="order-summary mb-5">
                            <h4 class="text-dark mb-4">
                                <i class="fas fa-receipt me-2 text-info"></i>Order Summary
                            </h4>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="course-info">
                                        <h5 class="text-dark mb-2">{{ $course->title }}</h5>
                                        <p class="text-muted mb-2">{{ $course->description ?? 'Professional security training course' }}</p>

                                        <div class="course-details">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>Student: {{ auth()->user()->fname }} {{ auth()->user()->lname }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="price-summary">
                                        <div class="price-line mb-2">
                                            <span class="text-muted">Course Fee:</span>
                                            <span class="text-dark fw-bold">${{ number_format($payment->amount, 2) }}</span>
                                        </div>
                                        <div class="price-line mb-2">
                                            <span class="text-muted">Processing Fee:</span>
                                            <span class="text-dark">$0.00</span>
                                        </div>
                                        <hr>
                                        <div class="price-total">
                                            <span class="h5 text-dark">Total:</span>
                                            <span class="h4 text-primary fw-bold">${{ number_format($payment->amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Methods --}}
                        <div class="payment-methods">
                            <h4 class="text-dark mb-4">
                                <i class="fas fa-credit-card me-2 text-info"></i>Choose Payment Method
                            </h4>

                            <div class="row">
                                {{-- Stripe Payment --}}
                                @if($paymentConfig['stripe_enabled'])
                                    <div class="col-lg-6 mb-4">
                                        <div class="payment-option h-100">
                                            <div class="payment-card p-4 border rounded h-100" style="border-color: #6772e5 !important; background: rgba(103, 114, 229, 0.05);">
                                                <div class="text-center mb-3">
                                                    <i class="fab fa-stripe fa-3x" style="color: #6772e5;"></i>
                                                    <h5 class="text-dark mt-2">Credit/Debit Card</h5>
                                                    <p class="text-muted small">Powered by Stripe</p>
                                                </div>

                                                <div class="accepted-cards text-center mb-3">
                                                    <i class="fab fa-cc-visa fa-2x me-2 text-primary"></i>
                                                    <i class="fab fa-cc-mastercard fa-2x me-2 text-warning"></i>
                                                    <i class="fab fa-cc-amex fa-2x me-2 text-info"></i>
                                                    <i class="fab fa-cc-discover fa-2x text-success"></i>
                                                </div>

                                                <form method="POST" action="{{ route('payments.stripe.process', $payment->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                                        <i class="fas fa-credit-card me-2"></i>Pay with Card
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- PayPal Payment --}}
                                @if($paymentConfig['paypal_enabled'])
                                    <div class="col-lg-6 mb-4">
                                        <div class="payment-option h-100">
                                            <div class="payment-card p-4 border rounded h-100" style="border-color: #0070ba !important; background: rgba(0, 112, 186, 0.05);">
                                                <div class="text-center mb-3">
                                                    <i class="fab fa-paypal fa-3x" style="color: #0070ba;"></i>
                                                    <h5 class="text-dark mt-2">PayPal</h5>
                                                    <p class="text-muted small">Pay with your PayPal account</p>
                                                </div>

                                                <div class="paypal-info text-center mb-3">
                                                    <p class="text-muted small">
                                                        <i class="fas fa-shield-alt me-1"></i>
                                                        100% Secure & Protected
                                                    </p>
                                                </div>

                                                <form method="POST" action="{{ route('payments.paypal.process', $payment->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-lg w-100" style="background: #0070ba; color: white; border: none;">
                                                        <i class="fab fa-paypal me-2"></i>Pay with PayPal
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- No Payment Methods Available --}}
                                @if(!$paymentConfig['stripe_enabled'] && !$paymentConfig['paypal_enabled'])
                                    <div class="col-12">
                                        <div class="alert alert-warning text-center">
                                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                            <h5>Payment Methods Unavailable</h5>
                                            <p>Payment processing is currently being configured. Please contact support for assistance with your enrollment.</p>

                                            <div class="mt-3">
                                                <a href="mailto:support@kkpsecurity.com" class="btn btn-outline-warning me-2">
                                                    <i class="fas fa-envelope me-2"></i>Contact Support
                                                </a>
                                                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-secondary">
                                                    <i class="fas fa-arrow-left me-2"></i>Back to Course
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Security Notice --}}
                        <div class="security-notice mt-5 p-4 rounded" style="background: rgba(40, 167, 69, 0.1); border: 1px solid #28a745;">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <i class="fas fa-lock fa-3x text-success"></i>
                                </div>
                                <div class="col-md-10">
                                    <h6 class="text-dark mb-2">
                                        <i class="fas fa-shield-alt me-2 text-success"></i>Your Payment is 100% Secure
                                    </h6>
                                    <p class="text-muted mb-0 small">
                                        We use industry-standard encryption and security measures to protect your payment information.
                                        Your card details are never stored on our servers and are processed securely by our payment partners.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Help Section --}}
                        <div class="help-section mt-4 text-center">
                            <p class="text-muted">
                                Need help? Contact our support team at
                                <a href="mailto:support@kkpsecurity.com" class="text-info">support@kkpsecurity.com</a>
                                or call <a href="tel:+1234567890" class="text-info">+1 (234) 567-8900</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
