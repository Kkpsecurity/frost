{{-- Order Completion Success Page --}}
<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Order Completed'">
    <x-slot:head>
        <meta name="description" content="Your course enrollment has been completed successfully">
        <style>
            .order-completed-panel {
                background-color: var(--frost-primary-color) !important;
                border: 1px solid rgba(255, 255, 255, 0.12) !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            }

            .order-detail-label {
                color: rgba(255, 255, 255, 0.6);
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .order-detail-value {
                color: #fff;
                font-weight: 500;
            }
        </style>
    </x-slot:head>

    <x-frontend.site.partials.header />

    <main class="frost-secondary-bg" style="min-height: calc(100vh - 200px); padding: 3rem 0 4rem;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">

                    {{-- Flash success --}}
                    @if (session('success'))
                        <div class="alert alert-success text-white alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Success Banner --}}
                    <div class="card order-completed-panel mb-4">
                        <div class="card-body text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-circle-check text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h1 class="text-white mb-2">Enrollment Confirmed</h1>
                            <p class="text-white-50 mb-0">
                                Your payment was processed successfully. You are all set!
                            </p>
                        </div>
                    </div>

                    {{-- Order Details --}}
                    <div class="card order-completed-panel mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-receipt me-2"></i>Order Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="order-detail-label">Order</div>
                                    <div class="order-detail-value">#{{ $order->id }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="order-detail-label">Status</div>
                                    <div class="order-detail-value">
                                        <span class="badge bg-success">Completed</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="order-detail-label">Course</div>
                                    <div class="order-detail-value">{{ $course->title ?? $course->name }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="order-detail-label">Amount Paid</div>
                                    <div class="order-detail-value text-success fs-5">
                                        ${{ number_format($order->total_price, 2) }} USD
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="order-detail-label">Date</div>
                                    <div class="order-detail-value">{{ $order->created_at->format('M j, Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- What is Next --}}
                    <div class="card order-completed-panel mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-list-check me-2"></i>What is Next?
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-start mb-3">
                                    <i class="fas fa-envelope text-primary mt-1 me-3" style="width:16px;"></i>
                                    <span class="text-white-50"><strong class="text-white">Check Your Email</strong>
                                        &mdash; A confirmation with your receipt and course details is on its
                                        way.</span>
                                </li>
                                <li class="d-flex align-items-start mb-3">
                                    <i class="fas fa-chalkboard-teacher text-primary mt-1 me-3" style="width:16px;"></i>
                                    <span class="text-white-50"><strong class="text-white">Access Your
                                            Classroom</strong> &mdash; Visit your dashboard to view course details and
                                        prepare for class.</span>
                                </li>
                                <li class="d-flex align-items-start mb-0">
                                    <i class="fas fa-bell text-primary mt-1 me-3" style="width:16px;"></i>
                                    <span class="text-white-50"><strong class="text-white">Watch for
                                            Notifications</strong> &mdash; We will alert you when your class date
                                        approaches.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-grid gap-2">
                        <a href="{{ route('classroom.dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Go to My Classroom
                        </a>
                        <a href="{{ route('account.index') }}" class="btn btn-outline-light">
                            <i class="fas fa-user-circle me-2"></i>My Account
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <x-frontend.site.partials.footer />
</x-frontend.site.site-wrapper>
