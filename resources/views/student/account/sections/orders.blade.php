{{-- Modern Orders Section Content --}}
<div class="row">
    <div class="col-12">
        {{-- Order Statistics --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="modern-card">
                    <div class="card-body text-center">
                        <div class="summary-badge count mb-2" style="font-size: 1.5rem; padding: 1rem;">
                            {{ $ordersData['total_courses'] }}
                        </div>
                        <h6 class="mb-0">Total Courses</h6>
                        <small class="text-muted">All time</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="modern-card">
                    <div class="card-body text-center">
                        <div class="summary-badge active mb-2" style="font-size: 1.5rem; padding: 1rem;">
                            {{ $ordersData['active_courses'] }}
                        </div>
                        <h6 class="mb-0">Active Courses</h6>
                        <small class="text-muted">Currently enrolled</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="modern-card">
                    <div class="card-body text-center">
                        <div class="summary-badge" style="background: #dcfce7; color: #166534; font-size: 1.5rem; padding: 1rem;">
                            {{ $ordersData['completed_courses'] }}
                        </div>
                        <h6 class="mb-0">Completed</h6>
                        <small class="text-muted">Finished courses</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="modern-card">
                    <div class="card-body text-center">
                        <div class="summary-badge" style="background: #f3e8ff; color: #7c3aed; font-size: 1.5rem; padding: 1rem;">
                            ${{ number_format(($ordersData['total_courses'] * 249), 2) }}
                        </div>
                        <h6 class="mb-0">Total Spent</h6>
                        <small class="text-muted">Lifetime value</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Course Enrollments --}}
        <div class="modern-card mb-4">
            <div class="card-body">
                <div class="section-title">
                    <i class="fas fa-graduation-cap"></i>Course Enrollments
                </div>

                @if(count($ordersData['course_enrollments']) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Enrolled Date</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ordersData['course_enrollments'] as $order)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $order['course_name'] }}</strong>
                                                <br><small class="text-muted">Code: {{ $order['course_code'] }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $order['enrolled_date'] }}</td>
                                        <td>
                                            @if($order['completion_status'] === 'Completed')
                                                <span class="summary-badge" style="background: #dcfce7; color: #166534;">
                                                    <i class="fas fa-check-circle me-1"></i>Completed
                                                </span>
                                            @else
                                                <span class="summary-badge active">
                                                    <i class="fas fa-play-circle me-1"></i>In Progress
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                @php
                                                    $progress = $order['completion_status'] === 'Completed' ? 100 : rand(25, 85);
                                                @endphp
                                                <div class="progress-bar" style="width: {{ $progress }}%; background: linear-gradient(135deg, var(--frost-primary-color, #212a3e) 0%, var(--frost-secondary-color, #394867) 100%);"></div>
                                            </div>
                                            <small class="text-muted">{{ $progress }}%</small>
                                        </td>
                                        <td><strong>{{ $order['price'] }}</strong></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>View
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-shopping-bag fa-3x text-muted"></i>
                        </div>
                        <h6 class="text-muted">No course enrollments found</h6>
                        <p class="text-muted small">When you enroll in courses, they will appear here.</p>
                        <a href="/courses" class="btn-modern btn-modern-primary">
                            <i class="fas fa-search me-1"></i>Browse Courses
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Order History (Future Feature) --}}
        <div class="modern-card">
            <div class="card-body">
                <div class="section-title">
                    <i class="fas fa-receipt"></i>Order History
                </div>

                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-clock fa-3x text-muted"></i>
                    </div>
                    <h6 class="text-muted">Order History Coming Soon</h6>
                    <p class="text-muted small">Detailed purchase history and invoices will be available here.</p>
                    <button class="btn btn-outline-secondary" disabled>
                        <i class="fas fa-history me-1"></i>View Order History
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
