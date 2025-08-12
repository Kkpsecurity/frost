{{-- Student Details Modal Content --}}
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    @if($student->avatar)
                        <img src="{{ asset('storage/' . $student->avatar) }}" class="img-circle" width="80" height="80">
                    @elseif($student->use_gravatar)
                        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower($student->email)) }}?s=80&d=identicon" class="img-circle" width="80" height="80">
                    @else
                        <i class="fas fa-user-circle fa-5x text-muted"></i>
                    @endif
                    <h5 class="mt-2">{{ $student->fname }} {{ $student->lname }}</h5>
                    <p class="text-muted">{{ $student->email }}</p>
                    @if($student->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Email Verified:</strong>
                                @if($student->email_verified_at)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-warning">No</span>
                                @endif
                            </p>
                            <p><strong>Registered:</strong> {{ $student->created_at->format('M d, Y') }}</p>
                            <p><strong>Last Login:</strong> {{ $student->last_login ? $student->last_login->format('M d, Y H:i') : 'Never' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Enrollments:</strong> {{ $stats['total_enrollments'] }}</p>
                            <p><strong>Active Enrollments:</strong> {{ $stats['active_enrollments'] }}</p>
                            <p><strong>Completed Courses:</strong> {{ $stats['completed_courses'] }}</p>
                            <p><strong>Total Orders:</strong> {{ $stats['total_orders'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabbed Interface --}}
    <ul class="nav nav-tabs" id="studentDetailTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="account-tab" data-toggle="tab" href="#account" role="tab">
                <i class="fas fa-user"></i> Account Details
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="educational-tab" data-toggle="tab" href="#educational" role="tab">
                <i class="fas fa-graduation-cap"></i> Educational Data
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="enrollments-tab" data-toggle="tab" href="#enrollments" role="tab">
                <i class="fas fa-book-open"></i> Enrollments
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="financial-tab" data-toggle="tab" href="#financial" role="tab">
                <i class="fas fa-dollar-sign"></i> Financial
            </a>
        </li>
    </ul>

    <div class="tab-content mt-3" id="studentDetailTabContent">
        {{-- Account Details Tab --}}
        <div class="tab-pane fade show active" id="account" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Account Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Personal Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>First Name:</strong></td>
                                    <td>{{ $student->fname }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Name:</strong></td>
                                    <td>{{ $student->lname }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $student->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $student->phone ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $student->address ?? 'Not provided' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Account Status</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($student->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Email Verified:</strong></td>
                                    <td>
                                        @if($student->email_verified_at)
                                            <span class="badge badge-success">Verified on {{ $student->email_verified_at->format('M d, Y') }}</span>
                                        @else
                                            <span class="badge badge-warning">Unverified</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Registered:</strong></td>
                                    <td>{{ $student->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Login:</strong></td>
                                    <td>{{ $student->last_login ? $student->last_login->format('M d, Y H:i') : 'Never logged in' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $student->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Educational Data Tab --}}
        <div class="tab-pane fade" id="educational" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Educational Progress</h3>
                </div>
                <div class="card-body">
                    @if($enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Enrolled Date</th>
                                        <th>Completed Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        <tr>
                                            <td>{{ $enrollment->course->name ?? 'Unknown Course' }}</td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $enrollment->progress ?? 0 }}%" aria-valuenow="{{ $enrollment->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                                        {{ $enrollment->progress ?? 0 }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($enrollment->completed_at)
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($enrollment->expired_at && $enrollment->expired_at < now())
                                                    <span class="badge badge-danger">Expired</span>
                                                @else
                                                    <span class="badge badge-info">Active</span>
                                                @endif
                                            </td>
                                            <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                                            <td>{{ $enrollment->completed_at ? $enrollment->completed_at->format('M d, Y') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-graduation-cap fa-3x text-muted"></i>
                            <p class="mt-3 text-muted">No course enrollments found for this student.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Enrollments Tab --}}
        <div class="tab-pane fade" id="enrollments" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Course Enrollments</h3>
                </div>
                <div class="card-body">
                    {{-- Same content as educational for now, but could be expanded --}}
                    @if($enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Enrollment Date</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        <tr>
                                            <td>{{ $enrollment->course->name ?? 'Unknown Course' }}</td>
                                            <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                                            <td>{{ $enrollment->expired_at ? $enrollment->expired_at->format('M d, Y') : 'No expiry' }}</td>
                                            <td>
                                                @if($enrollment->completed_at)
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($enrollment->expired_at && $enrollment->expired_at < now())
                                                    <span class="badge badge-danger">Expired</span>
                                                @else
                                                    <span class="badge badge-info">Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $enrollment->progress ?? 0 }}%">
                                                        {{ $enrollment->progress ?? 0 }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="viewEnrollmentDetails({{ $enrollment->id }})">
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
                            <i class="fas fa-book-open fa-3x text-muted"></i>
                            <p class="mt-3 text-muted">No enrollments found for this student.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Financial Tab --}}
        <div class="tab-pane fade" id="financial" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Financial Records</h3>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Product</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment Method</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->product_name ?? 'Unknown Product' }}</td>
                                            <td>${{ number_format($order->amount, 2) }}</td>
                                            <td>
                                                @switch($order->status)
                                                    @case('completed')
                                                        <span class="badge badge-success">Completed</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge badge-danger">Failed</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ ucfirst($order->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $order->payment_method ?? 'N/A' }}</td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-dollar-sign fa-3x text-muted"></i>
                            <p class="mt-3 text-muted">No financial records found for this student.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewEnrollmentDetails(enrollmentId) {
    // Implement enrollment details view
    alert('Enrollment details for ID: ' + enrollmentId);
}
</script>
