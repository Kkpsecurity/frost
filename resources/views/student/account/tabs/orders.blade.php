{{-- Orders Tab Content --}}
<div class="row ">
    <div class="col-lg-8">
        {{-- Course Enrollments --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Course Enrollments</h6>
                <span class="badge bg-primary">{{ $ordersData['total_courses'] }} Total</span>
            </div>
            <div class="card-body">
                @if($ordersData['course_enrollments']->count() > 0)
                    <div class="row">
                        @foreach($ordersData['course_enrollments'] as $enrollment)
                            <div class="col-md-6 mb-4">
                                <div class="course-card p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">{{ $enrollment['course_name'] }}</h6>
                                        @switch($enrollment['status'])
                                            @case('Active')
                                                <span class="badge bg-success">{{ $enrollment['status'] }}</span>
                                                @break
                                            @case('Completed')
                                                <span class="badge bg-info">{{ $enrollment['status'] }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $enrollment['status'] }}</span>
                                        @endswitch
                                    </div>
                                    <p class="text-muted small mb-2">{{ $enrollment['course_code'] }}</p>

                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Enrolled</small>
                                            <strong>{{ $enrollment['enrolled_date'] }}</strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Progress</small>
                                            <strong>{{ $enrollment['completion_status'] }}</strong>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-success">{{ $enrollment['price'] }}</span>
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Course Enrollments</h5>
                        <p class="text-muted mb-3">You haven't enrolled in any courses yet.</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Browse Courses
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Order History --}}
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-receipt me-2"></i>Order History</h6>
            </div>
            <div class="card-body">
                @if(!empty($ordersData['order_history']))
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ordersData['order_history'] as $order)
                                    <tr>
                                        <td><strong>#{{ $order['id'] }}</strong></td>
                                        <td>{{ $order['date'] }}</td>
                                        <td>{{ $order['items'] }}</td>
                                        <td><strong>${{ $order['total'] }}</strong></td>
                                        <td><span class="badge bg-success">{{ $order['status'] }}</span></td>
                                        <td>
                                            <button class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No order history available</p>
                        <small class="text-muted">Order tracking coming soon</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Course Statistics --}}
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Course Statistics</h6>
            </div>
            <div class="card-body">
                <div class="stat-card mb-3">
                    <div class="display-6 fw-bold">{{ $ordersData['active_courses'] }}</div>
                    <div class="small">Active Courses</div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Total Enrolled</span>
                    <span class="fw-bold">{{ $ordersData['total_courses'] }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Completed</span>
                    <span class="fw-bold text-success">{{ $ordersData['completed_courses'] }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">In Progress</span>
                    <span class="fw-bold text-primary">{{ $ordersData['active_courses'] }}</span>
                </div>

                @if($ordersData['total_courses'] > 0)
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: {{ ($ordersData['completed_courses'] / $ordersData['total_courses']) * 100 }}%">
                        </div>
                    </div>
                    <small class="text-muted">
                        {{ round(($ordersData['completed_courses'] / $ordersData['total_courses']) * 100) }}% Completion Rate
                    </small>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Enroll in Course
                    </a>
                    <button class="btn btn-outline-secondary" disabled>
                        <i class="fas fa-download me-1"></i>Download Certificates
                    </button>
                    <button class="btn btn-outline-info" disabled>
                        <i class="fas fa-file-invoice me-1"></i>View Invoices
                    </button>
                </div>
                <small class="text-muted d-block mt-2">Some features coming soon</small>
            </div>
        </div>
    </div>
</div>
