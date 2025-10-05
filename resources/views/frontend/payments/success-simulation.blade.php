{{-- Payment Success Simulation Page --}}
<x-frontend.site.site-wrapper title="Payment Successful">
    <x-slot:head>
        <meta name="description" content="Payment completed successfully">
        <style>
            body { background: #f8f9fa; }
            .success-container {
                min-height: 80vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 40px 20px;
            }
            .success-card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                padding: 60px 40px;
                max-width: 500px;
                width: 100%;
                text-align: center;
            }
            .success-icon {
                color: #28a745;
                font-size: 64px;
                margin-bottom: 30px;
                animation: checkmark 0.6s ease-in-out;
            }
            @keyframes checkmark {
                0% { transform: scale(0); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }
            .success-title {
                color: #2c3e50;
                font-size: 28px;
                font-weight: 600;
                margin-bottom: 20px;
            }
            .payment-details {
                background: #f8f9fa;
                border-radius: 8px;
                padding: 20px;
                margin: 30px 0;
                text-align: left;
            }
            .payment-method-badge {
                display: inline-block;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
            }
            .stripe-badge {
                background: #635bff;
                color: white;
            }
            .paypal-badge {
                background: #0070ba;
                color: white;
            }
            .btn-continue {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                padding: 15px 30px;
                font-size: 16px;
                text-decoration: none;
                display: inline-block;
                transition: transform 0.2s ease;
            }
            .btn-continue:hover {
                transform: translateY(-2px);
                color: white;
                text-decoration: none;
            }
        </style>
    </x-slot:head>

    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <h1 class="success-title">Payment Successful!</h1>
            <p class="text-muted mb-4">
                Your payment has been processed successfully. You will receive a confirmation email shortly.
            </p>

            {{-- Payment Details --}}
            <div class="payment-details">
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Transaction ID:</div>
                    <div class="col-sm-8">{{ $transaction_id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Payment Method:</div>
                    <div class="col-sm-8">
                        <span class="payment-method-badge {{ $payment_method == 'stripe' ? 'stripe-badge' : 'paypal-badge' }}">
                            {{ $payment_method == 'stripe' ? 'Stripe' : 'PayPal' }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Amount:</div>
                    <div class="col-sm-8">${{ number_format((float)$amount, 2) }}</div>
                </div>
                @if($email)
                <div class="row mb-3">
                    <div class="col-sm-4 fw-bold">Email:</div>
                    <div class="col-sm-8">{{ $email }}</div>
                </div>
                @endif
                <div class="row">
                    <div class="col-sm-4 fw-bold">Status:</div>
                    <div class="col-sm-8">
                        <span class="badge bg-success">Completed</span>
                    </div>
                </div>
            </div>

            {{-- Simulation Notice --}}
            <div class="alert alert-info small mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Demo Mode:</strong> This is a payment simulation for testing purposes.
                No actual charge has been made.
            </div>

            {{-- Action Buttons --}}
            <div class="d-grid gap-2 d-md-block">
                @if($return_url)
                <a href="{{ $return_url }}" class="btn-continue me-2">
                    <i class="fas fa-arrow-left me-2"></i>Return to Course
                </a>
                @endif
                <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                    Browse More Courses
                </a>
            </div>

            {{-- Auto-redirect notice --}}
            <div class="mt-4">
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    You will be redirected automatically in <span id="countdown">10</span> seconds
                </small>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            // Auto-redirect countdown
            let countdown = 10;
            const countdownElement = document.getElementById('countdown');
            const redirectUrl = '{{ $return_url ?: route("courses.index") }}';

            const timer = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;

                if (countdown <= 0) {
                    clearInterval(timer);
                    window.location.href = redirectUrl;
                }
            }, 1000);

            // Stop countdown if user interacts with the page
            document.addEventListener('click', () => {
                clearInterval(timer);
                countdownElement.parentElement.style.display = 'none';
            });
        </script>
    </x-slot:scripts>
</x-frontend.site.site-wrapper>
