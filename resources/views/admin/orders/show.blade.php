@extends('adminlte::page')

@section('title', 'Order Details - #' . $order->id)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Order Details - #{{ $order->id }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                <li class="breadcrumb-item active">Order #{{ $order->id }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Order Status Timeline -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-timeline"></i> Order Timeline
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-red">{{ $order->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <!-- Order Created -->
                        <div>
                            <i class="fas fa-plus bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $order->created_at->format('H:i') }}</span>
                                <h3 class="timeline-header">Order Created</h3>
                                <div class="timeline-body">
                                    Order #{{ $order->id }} was created for {{ $order->User->fname }} {{ $order->User->lname }}
                                </div>
                            </div>
                        </div>

                        @if($order->completed_at)
                        <!-- Payment Completed -->
                        <div>
                            <i class="fas fa-check bg-green"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $order->completed_at->format('H:i') }}</span>
                                <h3 class="timeline-header">Payment Completed</h3>
                                <div class="timeline-body">
                                    Payment of ${{ number_format($order->total_price, 2) }} was processed successfully
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($order->CourseAuth)
                        <!-- CourseAuth Created -->
                        <div>
                            <i class="fas fa-key bg-purple"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $order->CourseAuth->created_at->format('H:i') }}</span>
                                <h3 class="timeline-header">Course Access Granted</h3>
                                <div class="timeline-body">
                                    CourseAuth #{{ $order->CourseAuth->id }} was created - User can now access the course
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($order->refunded_at)
                        <!-- Order Refunded -->
                        <div>
                            <i class="fas fa-undo bg-red"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($order->refunded_at)->format('H:i') }}</span>
                                <h3 class="timeline-header">Order Refunded</h3>
                                <div class="timeline-body">
                                    Order was refunded by {{ $order->RefundedBy->fname ?? 'System' }} {{ $order->RefundedBy->lname ?? '' }}
                                </div>
                            </div>
                        </div>
                        @endif

                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Information -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Order Information
                    </h3>
                    <div class="card-tools">
                        @if(!$order->completed_at)
                            <button type="button" class="btn btn-success btn-sm" onclick="markAsPaid({{ $order->id }})">
                                <i class="fas fa-check"></i> Mark as Paid
                            </button>
                        @endif
                        
                        @if($order->CanRefund())
                            <button type="button" class="btn btn-warning btn-sm" onclick="processRefund({{ $order->id }})">
                                <i class="fas fa-undo"></i> Process Refund
                            </button>
                        @endif

                        @if(!$order->completed_at)
                            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Order
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer Information</h6>
                            <p><strong>Name:</strong> {{ $order->User->fname }} {{ $order->User->lname }}</p>
                            <p><strong>Email:</strong> {{ $order->User->email }}</p>
                            <p><strong>Student ID:</strong> {{ $order->User->id }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Information</h6>
                            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                            <p><strong>Created:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>Status:</strong> 
                                @if($order->refunded_at)
                                    <span class="badge badge-danger">Refunded</span>
                                @elseif($order->completed_at)
                                    <span class="badge badge-success">Paid</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-muted">Course Details</h6>
                    <div class="row">
                        <div class="col-md-8">
                            <p><strong>Course:</strong> {{ $order->Course->title ?? 'N/A' }}</p>
                            <p><strong>Course ID:</strong> {{ $order->course_id }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Course Price:</strong> ${{ number_format($order->course_price, 2) }}</p>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-muted">Payment Details</h6>
                    <table class="table table-sm">
                        <tr>
                            <td>Course Price:</td>
                            <td class="text-right">${{ number_format($order->course_price, 2) }}</td>
                        </tr>
                        @if($order->DiscountCode)
                        <tr>
                            <td>Discount ({{ $order->DiscountCode->code }}):</td>
                            <td class="text-right">-${{ number_format($order->course_price - $order->total_price, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="font-weight-bold">
                            <td>Total:</td>
                            <td class="text-right">${{ number_format($order->total_price, 2) }}</td>
                        </tr>
                    </table>

                    <p><strong>Payment Method:</strong> {{ $order->PaymentType->name ?? 'N/A' }}</p>
                    
                    @if($order->completed_at)
                        <p><strong>Payment Completed:</strong> {{ $order->completed_at->format('M d, Y H:i') }}</p>
                    @endif

                    @if($order->refunded_at)
                        <p><strong>Refunded:</strong> {{ \Carbon\Carbon::parse($order->refunded_at)->format('M d, Y H:i') }}</p>
                        <p><strong>Refunded By:</strong> {{ $order->RefundedBy->fname ?? 'System' }} {{ $order->RefundedBy->lname ?? '' }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- CourseAuth Information -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key"></i> Course Access
                    </h3>
                </div>
                <div class="card-body">
                    @if($order->CourseAuth)
                        <p><strong>CourseAuth ID:</strong> #{{ $order->CourseAuth->id }}</p>
                        <p><strong>Status:</strong> 
                            @if($order->CourseAuth->IsActive())
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </p>
                        <p><strong>Start Date:</strong> {{ $order->CourseAuth->start_date ?? 'N/A' }}</p>
                        <p><strong>Expiry Date:</strong> {{ $order->CourseAuth->expire_date ?? 'No Expiry' }}</p>
                        
                        @if($order->CourseAuth->completed_at)
                            <p><strong>Completed:</strong> {{ $order->CourseAuth->completed_at->format('M d, Y') }}</p>
                            <p><strong>Passed:</strong> 
                                @if($order->CourseAuth->is_passed)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-danger">No</span>
                                @endif
                            </p>
                        @endif

                        @if($order->CourseAuth->disabled_at)
                            <p><strong>Disabled:</strong> {{ $order->CourseAuth->disabled_at->format('M d, Y') }}</p>
                            <p><strong>Reason:</strong> {{ $order->CourseAuth->disabled_reason ?? 'N/A' }}</p>
                        @endif

                        <div class="mt-3">
                            <a href="{{ route('admin.students.show', $order->User->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-user"></i> View Student Profile
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No CourseAuth has been created for this order yet.
                        </div>
                        
                        @if($order->completed_at)
                            <button type="button" class="btn btn-success btn-sm" onclick="createCourseAuth({{ $order->id }})">
                                <i class="fas fa-key"></i> Create CourseAuth
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                        
                        <a href="{{ route('admin.students.show', $order->User->id) }}" class="btn btn-info">
                            <i class="fas fa-user"></i> View Student
                        </a>

                        @if($order->Course)
                            <a href="{{ route('admin.courses.management.show', $order->Course->id) }}" class="btn btn-primary">
                                <i class="fas fa-book"></i> View Course
                            </a>
                        @endif

                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#manualCourseAuthModal">
                            <i class="fas fa-user-plus"></i> Grant Manual Access
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual CourseAuth Modal -->
    <div class="modal fade" id="manualCourseAuthModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Grant Manual CourseAuth</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.orders.grant-manual-course-auth') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="user_id">Student</label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="{{ $order->User->id }}" selected>{{ $order->User->fname }} {{ $order->User->lname }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="course_id">Course</label>
                            <select name="course_id" id="course_id" class="form-control" required>
                                <option value="">Select Course</option>
                                @foreach(\App\Services\RCache::Courses() as $course)
                                    <option value="{{ $course->id }}" {{ $course->id == $order->course_id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="expire_date">Expiration Date (Optional)</label>
                            <input type="date" name="expire_date" id="expire_date" class="form-control">
                            <small class="text-muted">Leave blank for no expiration</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Grant Access</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .timeline {
            position: relative;
            margin: 0 0 30px 0;
            padding: 0;
            list-style: none;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 30px;
            width: 4px;
            background: #ddd;
        }
        
        .timeline > div {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline > div > i {
            position: absolute;
            left: 18px;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            text-align: center;
            line-height: 25px;
            color: #fff;
            font-size: 12px;
        }
        
        .timeline-item {
            background: #f9f9f9;
            border-radius: 3px;
            margin-left: 60px;
            padding: 10px;
            border-left: 3px solid #ddd;
        }
        
        .time-label > span {
            font-weight: 600;
            padding: 5px 10px;
            display: inline-block;
            border-radius: 4px;
            color: #fff;
        }
    </style>
@stop

@section('js')
    <script>
        // Mark as Paid function
        function markAsPaid(orderId) {
            if (confirm('Are you sure you want to mark this order as paid?')) {
                $.post('{{ route("admin.orders.mark-as-paid", ":id") }}'.replace(':id', orderId), {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                });
            }
        }

        // Process Refund function
        function processRefund(orderId) {
            if (confirm('Are you sure you want to refund this order? This will revoke the associated CourseAuth.')) {
                $.post('{{ route("admin.orders.process-refund", ":id") }}'.replace(':id', orderId), {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                });
            }
        }

        // Create CourseAuth function
        function createCourseAuth(orderId) {
            if (confirm('Are you sure you want to create CourseAuth for this order?')) {
                $.post('{{ route("admin.orders.mark-as-paid", ":id") }}'.replace(':id', orderId), {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                });
            }
        }
    </script>
@stop
