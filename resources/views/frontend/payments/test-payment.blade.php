{{--
    ⚠️ TEMPORARY TEST VIEW - TO BE REMOVED ⚠️
    See: docs/tasks/TEMPORARY_TEST_FILES.md
--}}

<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Test Payment - DO NOT USE IN PRODUCTION'">
    <x-slot:head>
        <meta name="robots" content="noindex, nofollow">
        <style>
            .test-warning {
                background: #ff0000;
                color: white;
                padding: 20px;
                margin: 20px 0;
                border: 3px solid #cc0000;
                border-radius: 8px;
                font-size: 18px;
                font-weight: bold;
                text-align: center;
            }

            .payment-summary {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                margin: 20px 0;
            }
        </style>
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container my-5">
        <div class="test-warning">
            ⚠️ TEMPORARY TEST PAGE - NOT FOR PRODUCTION USE ⚠️
            <br><small>This is a stub payment page for testing enrollment flow</small>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Test Payment Page
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="payment-summary">
                            <h4>Order Summary</h4>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Course:</strong></div>
                                <div class="col-sm-8">{{ $course->title }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Order ID:</strong></div>
                                <div class="col-sm-8">#{{ $order->id }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Payment ID:</strong></div>
                                <div class="col-sm-8">#{{ $payment->id }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Amount:</strong></div>
                                <div class="col-sm-8"><strong
                                        class="text-primary">${{ number_format($payment->amount, 2) }} USD</strong>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Status:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge bg-warning">{{ $payment->status }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Test Mode:</strong> No actual payment processing is configured. Click "Complete Test
                            Payment" to simulate a successful payment.
                        </div>

                        <form method="POST" action="{{ route('payments.payflowpro.payment_return', $payment) }}">
                            @csrf
                            <input type="hidden" name="RESULT" value="0">
                            <input type="hidden" name="PNREF" value="TEST{{ rand(100000, 999999) }}">
                            <input type="hidden" name="PPREF" value="TEST{{ rand(100000, 999999) }}">

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check-circle me-2"></i>Complete Test Payment
                                </button>
                                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>

                        <div class="mt-4">
                            <h5>Debug Information</h5>
                            <small class="text-muted">
                                <ul>
                                    <li>Payment Method: {{ $payment->payment_method }}</li>
                                    <li>Gateway: {{ $payment->gateway }}</li>
                                    <li>Currency: {{ $payment->currency }}</li>
                                    <li>Created: {{ $payment->created_at->format('Y-m-d H:i:s') }}</li>
                                </ul>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-4">
                    <strong>⚠️ Important:</strong> This test payment page must be replaced with proper PayFlowPro/Stripe
                    integration before production use. See <code>docs/tasks/TEMPORARY_TEST_FILES.md</code> for details.
                </div>
            </div>
        </div>
    </div>

    <x-frontend.site.partials.footer />
</x-frontend.site.site-wrapper>
