{{-- Order Completion Success Page --}}
<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Order Completed'">
    <x-slot:head>
        <meta name="description" content="Your course enrollment has been completed successfully">
        <style>
            .success-checkmark {
                width: 80px;
                height: 80px;
                margin: 0 auto;
            }

            .success-checkmark .check-icon {
                width: 80px;
                height: 80px;
                position: relative;
                border-radius: 50%;
                box-sizing: content-box;
                border: 4px solid #28a745;
            }

            .success-checkmark .check-icon::before {
                top: 3px;
                left: -2px;
                width: 30px;
                transform-origin: 100% 50%;
                border-radius: 100px 0 0 100px;
            }

            .success-checkmark .check-icon::after {
                top: 0;
                left: 30px;
                width: 60px;
                transform-origin: 0 50%;
                border-radius: 0 100px 100px 0;
                animation: rotate-circle 4.25s ease-in;
            }

            .success-checkmark .check-icon::before,
            .success-checkmark .check-icon::after {
                content: '';
                height: 100px;
                position: absolute;
                background: #fff;
                transform: rotate(-45deg);
            }

            .success-checkmark .icon-line {
                height: 5px;
                background-color: #28a745;
                display: block;
                border-radius: 2px;
                position: absolute;
                z-index: 10;
            }

            .success-checkmark .icon-line.line-tip {
                top: 46px;
                left: 14px;
                width: 25px;
                transform: rotate(45deg);
                animation: icon-line-tip 0.75s;
            }

            .success-checkmark .icon-line.line-long {
                top: 38px;
                right: 8px;
                width: 47px;
                transform: rotate(-45deg);
                animation: icon-line-long 0.75s;
            }

            @keyframes icon-line-tip {
                0% {
                    width: 0;
                    left: 1px;
                    top: 19px;
                }

                54% {
                    width: 0;
                    left: 1px;
                    top: 19px;
                }

                70% {
                    width: 50px;
                    left: -8px;
                    top: 37px;
                }

                84% {
                    width: 17px;
                    left: 21px;
                    top: 48px;
                }

                100% {
                    width: 25px;
                    left: 14px;
                    top: 45px;
                }
            }

            @keyframes icon-line-long {
                0% {
                    width: 0;
                    right: 46px;
                    top: 54px;
                }

                65% {
                    width: 0;
                    right: 46px;
                    top: 54px;
                }

                84% {
                    width: 55px;
                    right: 0px;
                    top: 35px;
                }

                100% {
                    width: 47px;
                    right: 8px;
                    top: 38px;
                }
            }
        </style>
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Success Message --}}
                <div class="card border-0 shadow-lg">
                    <div class="card-body text-center py-5">
                        <div class="success-checkmark">
                            <div class="check-icon">
                                <span class="icon-line line-tip"></span>
                                <span class="icon-line line-long"></span>
                                <div class="icon-circle"></div>
                                <div class="icon-fix"></div>
                            </div>
                        </div>

                        <h1 class="text-success mt-4 mb-3">
                            <i class="fas fa-check-circle"></i> Order Completed!
                        </h1>
                        <p class="lead text-muted">
                            Thank you for your enrollment. Your order has been processed successfully.
                        </p>
                    </div>
                </div>

                {{-- Order Details --}}
                <div class="card mt-4">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Order Details
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Order Number:</strong></div>
                            <div class="col-sm-8">#{{ $order->id }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Course:</strong></div>
                            <div class="col-sm-8">{{ $course->title }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Amount Paid:</strong></div>
                            <div class="col-sm-8"><strong
                                    class="text-success">${{ number_format($order->total_price, 2) }} USD</strong></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Order Date:</strong></div>
                            <div class="col-sm-8">{{ $order->created_at->format('F j, Y \a\t g:i A') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Status:</strong></div>
                            <div class="col-sm-8">
                                <span class="badge bg-success">Completed</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Next Steps --}}
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-list-check me-2"></i>What's Next?
                        </h4>
                    </div>
                    <div class="card-body">
                        <ol class="mb-0">
                            <li class="mb-3">
                                <strong>Check Your Email</strong> - You'll receive a confirmation email with your
                                receipt and course details
                            </li>
                            <li class="mb-3">
                                <strong>Access Your Course</strong> - Visit your dashboard to start learning
                            </li>
                            <li class="mb-3">
                                <strong>Complete Your Profile</strong> - Ensure your profile information is up to date
                            </li>
                            <li class="mb-0">
                                <strong>Join Your Classroom</strong> - Watch for notifications about your course start
                                date
                            </li>
                        </ol>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('account.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-circle me-2"></i>Go to My Account
                    </a>
                    <a href="{{ route('courses.list') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-graduation-cap me-2"></i>Browse More Courses
                    </a>
                </div>

                {{-- Support Info --}}
                <div class="alert alert-info mt-4">
                    <i class="fas fa-headset me-2"></i>
                    <strong>Need Help?</strong> If you have any questions about your enrollment, please contact our
                    support team.
                </div>
            </div>
        </div>
    </div>

    <x-frontend.site.partials.footer />
</x-frontend.site.site-wrapper>
