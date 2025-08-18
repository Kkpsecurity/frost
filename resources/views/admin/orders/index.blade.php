@extends('adminlte::page')

@section('title', 'Orders Management')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Orders Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Orders</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Statistics Cards -->
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="total-orders">{{ App\Models\Order::count() }}</h3>
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
                            <h3 id="paid-orders">{{ App\Models\Order::whereNotNull('completed_at')->count() }}</h3>
                            <p>Paid Orders</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="pending-orders">{{ App\Models\Order::whereNull('completed_at')->count() }}</h3>
                            <p>Pending Orders</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="refunded-orders">{{ App\Models\Order::whereNotNull('refunded_at')->count() }}</h3>
                            <p>Refunded Orders</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-undo"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Orders List
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create Order
                        </a>
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#manualCourseAuthModal">
                            <i class="fas fa-user-plus"></i> Grant Manual CourseAuth
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <select id="status-filter" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Paid</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="payment-type-filter" class="form-control">
                                <option value="">All Payment Types</option>
                                @foreach(\App\Services\RCache::PaymentTypes() as $paymentType)
                                    <option value="{{ $paymentType->id }}">{{ $paymentType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" id="date-from" class="form-control" placeholder="From Date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" id="date-to" class="form-control" placeholder="To Date">
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="clear-filters" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="orders-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Course</th>
                                    <th>Total</th>
                                    <th>Payment Method</th>
                                    <th>Payment Status</th>
                                    <th>CourseAuth Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
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
                                <option value="">Select Student</option>
                                @foreach(\App\Models\User::where('role_id', '>', 4)->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->fname }} {{ $user->lname }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="course_id">Course</label>
                            <select name="course_id" id="course_id" class="form-control" required>
                                <option value="">Select Course</option>
                                @foreach(\App\Services\RCache::Courses() as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route("admin.orders.data") }}',
                    data: function (d) {
                        d.status_filter = $('#status-filter').val();
                        d.payment_type_filter = $('#payment-type-filter').val();
                        d.date_from = $('#date-from').val();
                        d.date_to = $('#date-to').val();
                    }
                },
                columns: [
                    {data: 'order_id', name: 'id'},
                    {data: 'customer_name', name: 'customer_name', orderable: false},
                    {data: 'course_name', name: 'course_name', orderable: false},
                    {data: 'total_display', name: 'total_price'},
                    {data: 'payment_method', name: 'payment_method', orderable: false},
                    {data: 'payment_status', name: 'payment_status', orderable: false},
                    {data: 'course_auth_status', name: 'course_auth_status', orderable: false},
                    {data: 'created_date', name: 'created_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[7, 'desc']]
            });

            // Filter event handlers
            $('#status-filter, #payment-type-filter, #date-from, #date-to').change(function() {
                table.draw();
            });

            $('#clear-filters').click(function() {
                $('#status-filter, #payment-type-filter, #date-from, #date-to').val('');
                table.draw();
            });
        });

        // Mark as Paid function
        function markAsPaid(orderId) {
            if (confirm('Are you sure you want to mark this order as paid?')) {
                $.post('{{ route("admin.orders.mark-as-paid", ":id") }}'.replace(':id', orderId), {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#orders-table').DataTable().ajax.reload();
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
                        $('#orders-table').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                });
            }
        }
    </script>
@stop
