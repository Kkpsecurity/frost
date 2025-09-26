@extends('adminlte::page')

@php
    // Determine status badge class
    $status = $order->status ?? 'New';
    switch($status) {
        case 'Completed':
            $statusBadgeClass = 'success';
            break;
        case 'Cancelled':
            $statusBadgeClass = 'danger';
            break;
        case 'Processing':
            $statusBadgeClass = 'warning';
            break;
        case 'Active':
            $statusBadgeClass = 'info';
            break;
        default:
            $statusBadgeClass = 'secondary';
            break;
    }
@endphp

@section('title', 'Order #' . ($order->order_number ?? 'ORD-' . $order->id))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-shopping-cart"></i>
            Order #{{ $order->order_number ?? 'ORD-' . $order->id }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Orders
            </a>
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Edit Order
            </a>
            <button type="button" class="btn btn-warning" onclick="updateStatus()">
                <i class="fas fa-sync"></i>
                Update Status
            </button>
            <div class="btn-group">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-download"></i>
                    Export
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.orders.invoice', $order) }}">
                        <i class="fas fa-file-invoice"></i>
                        Download Invoice
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.orders.receipt', $order) }}">
                        <i class="fas fa-receipt"></i>
                        Download Receipt
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <!-- Order Details -->
            <div class="col-md-8">
                <!-- Order Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Order Information
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-{{ $statusBadgeClass }} badge-lg">
                                {{ $order->status ?? 'New' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Order Number:</strong></td>
                                        <td>{{ $order->order_number ?? 'ORD-' . $order->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Order Date:</strong></td>
                                        <td>{{ $order->created_at->format('F j, Y g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $statusBadgeClass }}">
                                                {{ $order->status ?? 'New' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Method:</strong></td>
                                        <td>
                                            @if($order->PaymentType)
                                                {{ $order->PaymentType->name }}
                                            @else
                                                <span class="text-muted">Not specified</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Last Updated:</strong></td>
                                        <td>{{ $order->updated_at->format('F j, Y g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created By:</strong></td>
                                        <td>
                                            @if($order->CreatedBy)
                                                {{ $order->CreatedBy->fname }} {{ $order->CreatedBy->lname }}
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Updated By:</strong></td>
                                        <td>
                                            @if($order->UpdatedBy)
                                                {{ $order->UpdatedBy->fname }} {{ $order->UpdatedBy->lname }}
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reference:</strong></td>
                                        <td>{{ $order->reference ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if($order->notes)
                        <div class="mt-3">
                            <h5>Order Notes:</h5>
                            <div class="alert alert-info">
                                <i class="fas fa-sticky-note"></i>
                                {{ $order->notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user"></i>
                            Customer Information
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($order->User)
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $order->User->fname }} {{ $order->User->lname }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>
                                            <a href="mailto:{{ $order->User->email }}">
                                                {{ $order->User->email }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $order->User->phone ?? 'Not provided' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer Since:</strong></td>
                                        <td>{{ $order->User->created_at->format('F j, Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="customer-actions">
                                    <h6>Quick Actions:</h6>
                                    <div class="btn-group-vertical btn-group-sm">
                                        <a href="mailto:{{ $order->User->email }}" class="btn btn-outline-primary">
                                            <i class="fas fa-envelope"></i>
                                            Send Email
                                        </a>
                                        <a href="{{ route('admin.students.manage.view', $order->User) }}" class="btn btn-outline-info">
                                            <i class="fas fa-user-edit"></i>
                                            View Customer Profile
                                        </a>
                                        <a href="{{ route('admin.orders.index', ['customer_id' => $order->User->id]) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-history"></i>
                                            View Order History
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Customer information not available
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Course Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-graduation-cap"></i>
                            Course Information
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($order->GetCourse())
                        <div class="course-details">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4>{{ $order->GetCourse()->title }}</h4>
                                    @if($order->GetCourse()->description)
                                        <p class="text-muted">{{ Str::limit($order->GetCourse()->description, 200) }}</p>
                                    @endif

                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Course ID:</strong></td>
                                            <td>{{ $order->GetCourse()->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Category:</strong></td>
                                            <td>{{ $order->GetCourse()->category ?? 'Not categorized' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Duration:</strong></td>
                                            <td>{{ $order->GetCourse()->duration ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Difficulty:</strong></td>
                                            <td>{{ $order->GetCourse()->difficulty ?? 'Not specified' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    <div class="course-actions">
                                        <h6>Course Actions:</h6>
                                        <div class="btn-group-vertical btn-group-sm">
                                            <a href="{{ route('admin.courses.manage.view', $order->GetCourse()) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                                View Course Details
                                            </a>
                                            <a href="{{ route('admin.courses.manage.edit', $order->GetCourse()) }}" class="btn btn-outline-info">
                                                <i class="fas fa-edit"></i>
                                                Edit Course
                                            </a>
                                            @if($order->status === 'Completed' && $order->User)
                                                {{-- Enrollment functionality not yet implemented --}}
                                                <button class="btn btn-outline-success" disabled>
                                                    <i class="fas fa-user-plus"></i>
                                                    Enroll Student (Coming Soon)
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Course information not available
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Order History / Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history"></i>
                            Order History
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="time-label">
                                <span class="bg-primary">Order Timeline</span>
                            </div>

                            <div>
                                <i class="fas fa-plus bg-success"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $order->created_at->format('M j, Y g:i A') }}
                                    </span>
                                    <h3 class="timeline-header">Order Created</h3>
                                    <div class="timeline-body">
                                        Order was created with status: <strong>{{ $order->status ?? 'New' }}</strong>
                                        @if($order->CreatedBy)
                                            by {{ $order->CreatedBy->fname }} {{ $order->CreatedBy->lname }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($order->updated_at != $order->created_at)
                            <div>
                                <i class="fas fa-edit bg-warning"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $order->updated_at->format('M j, Y g:i A') }}
                                    </span>
                                    <h3 class="timeline-header">Order Updated</h3>
                                    <div class="timeline-body">
                                        Order was last modified
                                        @if($order->UpdatedBy)
                                            by {{ $order->UpdatedBy->fname }} {{ $order->UpdatedBy->lname }}
                                        @endif
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

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card card-primary card-outline sticky-top">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calculator"></i>
                            Order Summary
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>${{ number_format($order->subtotal ?? 0, 2) }}</span>
                            </div>

                            @if($order->discount_amount > 0)
                            <div class="summary-row discount-row">
                                <span>
                                    Discount:
                                    @if($order->DiscountCode)
                                        <small class="text-muted">({{ $order->DiscountCode->code }})</small>
                                    @endif
                                </span>
                                <span class="text-success">-${{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            @endif

                            @if($order->tax_amount > 0)
                            <div class="summary-row">
                                <span>Tax:</span>
                                <span>${{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            @endif

                            <hr>
                            <div class="summary-row total-row">
                                <strong>
                                    <span>Total:</span>
                                    <span class="text-primary">${{ number_format($order->total ?? 0, 2) }}</span>
                                </strong>
                            </div>
                        </div>

                        @if($order->DiscountCode)
                        <div class="discount-info mt-3">
                            <h6><i class="fas fa-percentage"></i> Discount Applied</h6>
                            <div class="alert alert-success">
                                <strong>{{ $order->DiscountCode->code }}</strong><br>
                                @if($order->DiscountCode->discount_type === 'percentage')
                                    {{ $order->DiscountCode->discount_value }}% off
                                @else
                                    ${{ number_format($order->DiscountCode->discount_value, 2) }} off
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Quick Actions -->
                        <div class="mt-4">
                            <h6><i class="fas fa-bolt"></i> Quick Actions</h6>
                            <div class="btn-group-vertical btn-block">
                                @if($order->status !== 'Completed')
                                <button type="button" class="btn btn-success btn-sm" onclick="markComplete()">
                                    <i class="fas fa-check"></i>
                                    Mark as Complete
                                </button>
                                @endif

                                @if($order->status !== 'Cancelled')
                                <button type="button" class="btn btn-danger btn-sm" onclick="cancelOrder()">
                                    <i class="fas fa-times"></i>
                                    Cancel Order
                                </button>
                                @endif

                                <button type="button" class="btn btn-info btn-sm" onclick="sendEmail()">
                                    <i class="fas fa-envelope"></i>
                                    Send Email to Customer
                                </button>

                                <a href="{{ route('admin.orders.duplicate', $order) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-copy"></i>
                                    Duplicate Order
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Order Status</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="status-form" method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="status">New Status</label>
                            <select name="status" id="status" class="form-control" required>
                                @foreach($orderStatuses as $key => $label)
                                    @if($key !== 'all')
                                        <option value="{{ $key }}" {{ ($order->status ?? 'New') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                    placeholder="Add notes about this status change..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
.order-summary {
    font-size: 1rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.total-row {
    font-size: 1.2rem;
    padding-top: 10px;
}

.discount-row span.text-success {
    font-weight: 600;
}

.sticky-top {
    position: sticky;
    top: 20px;
}

.customer-actions, .course-actions {
    border-left: 3px solid #007bff;
    padding-left: 15px;
}

.table-borderless td {
    border: none;
    padding: 0.25rem 0.5rem;
}

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
    width: 4px;
    background: #dee2e6;
    left: 31px;
    margin: 0;
    border-radius: 2px;
}

.timeline > div {
    position: relative;
}

.timeline > div > .timeline-item {
    box-shadow: 0 0 1px rgba(0,0,0,0.125), 0 1px 3px rgba(0,0,0,0.2);
    border-radius: 3px;
    margin-top: 0;
    background: #fff;
    color: #495057;
    margin-left: 60px;
    margin-right: 15px;
    margin-bottom: 15px;
    padding: 0;
}

.timeline > div > .fas {
    width: 30px;
    height: 30px;
    font-size: 15px;
    line-height: 30px;
    position: absolute;
    color: #666;
    background: #fafafa;
    border-radius: 50%;
    text-align: center;
    left: 18px;
    top: 0;
}

.timeline > .time-label > span {
    font-weight: 600;
    color: #fff;
    border-radius: 4px;
    display: inline-block;
    padding: 5px 10px;
}

.timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding: 10px 15px;
    font-size: 16px;
    line-height: 1.1;
}

.timeline-body, .timeline-footer {
    padding: 10px 15px;
}

.time {
    color: #999;
    float: right;
    padding: 10px;
    font-size: 12px;
}

@media (max-width: 768px) {
    .sticky-top {
        position: relative;
    }
}
</style>
@stop

@section('js')
<script>
function updateStatus() {
    $('#statusModal').modal('show');
}

function markComplete() {
    if (confirm('Mark this order as complete?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.orders.complete", $order) }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }
}

function cancelOrder() {
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.orders.cancel", $order) }}';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }
}

function sendEmail() {
    @if($order->User)
        const subject = encodeURIComponent('Regarding your order #{{ $order->order_number ?? "ORD-" . $order->id }}');
        const body = encodeURIComponent('Hi {{ $order->User->fname }},\n\nI hope this email finds you well. I wanted to follow up regarding your recent order...\n\nBest regards,\nThe Team');
        window.location.href = `mailto:{{ $order->User->email }}?subject=${subject}&body=${body}`;
    @else
        alert('No customer email available for this order.');
    @endif
}
</script>
@stop
