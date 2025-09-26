@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-shopping-cart"></i>
            {{ $content['title'] }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.orders.export') }}" class="btn btn-success">
                <i class="fas fa-download"></i>
                Export Orders
            </a>
            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Create Order
            </a>
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

        @if($errors->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $errors->first('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($content['stats']['total_orders']) }}</h3>
                        <p>Total Orders</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>${{ number_format($content['stats']['total_revenue'], 2) }}</h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($content['stats']['processing_orders']) }}</h3>
                        <p>Processing</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>${{ number_format($content['stats']['month_revenue'], 2) }}</h3>
                        <p>This Month</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i>
                    Filter Orders
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Statuses</option>
                                @foreach($content['order_statuses'] as $key => $label)
                                    @if($key !== 'all')
                                        <option value="{{ $key }}" {{ $content['filters']['status'] == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="customer_id">Customer</label>
                            <select name="customer_id" id="customer_id" class="form-control select2">
                                <option value="">All Customers</option>
                                @foreach($content['customers'] as $customer)
                                    <option value="{{ $customer->id }}" {{ $content['filters']['customer_id'] == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->fname }} {{ $customer->lname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="course_id">Course</label>
                            <select name="course_id" id="course_id" class="form-control select2">
                                <option value="">All Courses</option>
                                @foreach($content['courses'] as $course)
                                    <option value="{{ $course->id }}" {{ $content['filters']['course_id'] == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_range">Date Range</label>
                            <select name="date_range" id="date_range" class="form-control">
                                <option value="month" {{ $content['filters']['date_range'] == 'month' ? 'selected' : '' }}>This Month</option>
                                <option value="week" {{ $content['filters']['date_range'] == 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="year" {{ $content['filters']['date_range'] == 'year' ? 'selected' : '' }}>This Year</option>
                                <option value="all" {{ $content['filters']['date_range'] == 'all' ? 'selected' : '' }}>All Time</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                   value="{{ $content['filters']['search'] }}"
                                   placeholder="Order #, Customer name...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="btn-group-vertical d-block">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Orders ({{ $content['orders']->total() }} total)
                </h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info" onclick="selectAll()">
                            <i class="fas fa-check"></i> Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" onclick="bulkUpdateStatus()">
                            <i class="fas fa-edit"></i> Bulk Status
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                            <i class="fas fa-trash"></i> Bulk Delete
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Course</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($content['orders'] as $order)
                        <tr>
                            <td>
                                <input type="checkbox" class="order-checkbox" value="{{ $order->id }}">
                            </td>
                            <td>
                                <strong>{{ $order->order_number ?? 'ORD-' . $order->id }}</strong>
                            </td>
                            <td>
                                @if($order->User)
                                    <div class="customer-info">
                                        <strong>{{ $order->User->fname }} {{ $order->User->lname }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $order->User->email }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Unknown Customer</span>
                                @endif
                            </td>
                            <td>
                                @if($order->GetCourse())
                                    <div class="course-info">
                                        <strong>{{ $order->GetCourse()->title }}</strong>
                                    </div>
                                @else
                                    <span class="text-muted">Unknown Course</span>
                                @endif
                            </td>
                            <td>
                                <strong>${{ number_format($order->total ?? 0, 2) }}</strong>
                                @if($order->discount_amount > 0)
                                    <br>
                                    <small class="text-success">
                                        -${{ number_format($order->discount_amount, 2) }} discount
                                    </small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = match($order->status ?? 'New') {
                                        'Completed' => 'success',
                                        'Active' => 'info',
                                        'Processing' => 'warning',
                                        'Cancelled' => 'danger',
                                        'Expired' => 'secondary',
                                        default => 'primary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">
                                    {{ $order->status ?? 'New' }}
                                </span>
                            </td>
                            <td>
                                @if($order->PaymentType)
                                    {{ $order->PaymentType->name }}
                                @else
                                    <span class="text-muted">Unknown</span>
                                @endif
                            </td>
                            <td>
                                <div class="date-info">
                                    <strong>{{ $order->created_at->format('M j, Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $order->created_at->format('g:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="btn btn-info btn-sm" title="View Order">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.edit', $order) }}"
                                       class="btn btn-primary btn-sm" title="Edit Order">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-warning btn-sm"
                                            onclick="updateStatus({{ $order->id }})" title="Update Status">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                            onclick="deleteOrder({{ $order->id }})" title="Delete Order">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">No orders found</h4>
                                    <p class="text-muted">Try adjusting your filters or create a new order.</p>
                                    <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Create First Order
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($content['orders']->hasPages())
            <div class="card-footer">
                {{ $content['orders']->links() }}
            </div>
            @endif
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
                <form id="status-form" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="new_status">New Status</label>
                            <select name="status" id="new_status" class="form-control" required>
                                @foreach($content['order_statuses'] as $key => $label)
                                    @if($key !== 'all')
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status_notes">Notes (Optional)</label>
                            <textarea name="notes" id="status_notes" class="form-control" rows="3"
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm Deletion</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this order?</p>
                    <p><strong>This action cannot be undone.</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css">
<style>
.customer-info, .course-info, .date-info {
    font-size: 0.9em;
}

.empty-state {
    padding: 40px 20px;
}

.small-box .icon {
    top: -10px;
    right: 10px;
}

.table td {
    vertical-align: middle;
}

.select2-container--bootstrap {
    width: 100% !important;
}

.order-checkbox {
    cursor: pointer;
}

.btn-group-sm > .btn {
    font-size: 0.75rem;
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap',
        allowClear: true
    });

    // Select all functionality
    $('#select-all').change(function() {
        $('.order-checkbox').prop('checked', this.checked);
    });

    $('.order-checkbox').change(function() {
        if (!this.checked) {
            $('#select-all').prop('checked', false);
        }
    });
});

function selectAll() {
    $('.order-checkbox').prop('checked', true);
    $('#select-all').prop('checked', true);
}

function updateStatus(orderId) {
    $('#status-form').attr('action', `{{ url('admin/orders') }}/${orderId}/status`);
    $('#statusModal').modal('show');
}

function deleteOrder(orderId) {
    $('#delete-form').attr('action', `{{ url('admin/orders') }}/${orderId}`);
    $('#deleteModal').modal('show');
}

function bulkUpdateStatus() {
    const selectedIds = $('.order-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (selectedIds.length === 0) {
        alert('Please select orders to update.');
        return;
    }

    alert(`Bulk status update for ${selectedIds.length} orders would be implemented here.`);
}

function bulkDelete() {
    const selectedIds = $('.order-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (selectedIds.length === 0) {
        alert('Please select orders to delete.');
        return;
    }

    if (confirm(`Are you sure you want to delete ${selectedIds.length} orders? This action cannot be undone.`)) {
        alert(`Bulk delete for ${selectedIds.length} orders would be implemented here.`);
    }
}
</script>
@stop
