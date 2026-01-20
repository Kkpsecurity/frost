@extends('adminlte::page')

@section('title', 'Transaction Logs')

@section('content_header')
    <h1>Transaction Logs</h1>
@stop

@section('content')
    <!-- Stats Overview -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format(\App\Models\Order::whereNotNull('completed_at')->count()) }}</h3>
                    <p>Completed</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format(\App\Models\Order::whereNull('completed_at')->whereNull('refunded_at')->count()) }}</h3>
                    <p>Pending</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format(\App\Models\Order::whereNotNull('refunded_at')->count()) }}</h3>
                    <p>Refunded</p>
                </div>
                <div class="icon">
                    <i class="fas fa-undo"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>${{ number_format(\App\Models\Order::whereNotNull('completed_at')->sum('total_price'), 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history"></i> All Transactions
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#filterModal">
                    <i class="fas fa-filter"></i> Filters
                </button>
                <button type="button" class="btn btn-sm btn-success" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="transactionsTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Course</th>
                            <th>Course Price</th>
                            <th>Discount</th>
                            <th>Total Price</th>
                            <th>Payment Method</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        @php
                            $order = \App\Models\Order::with(['User', 'Course', 'PaymentType', 'DiscountCode'])->find($transaction->id);
                        @endphp
                        <tr>
                            <td>
                                <strong>#{{ $transaction->id }}</strong>
                            </td>
                            <td>
                                {{ $transaction->user_name }}
                                <br>
                                <small class="text-muted">ID: {{ $transaction->user_id }}</small>
                            </td>
                            <td>
                                @if($order && $order->Course)
                                    {{ $order->Course->name ?? 'N/A' }}
                                    <br>
                                    <small class="text-muted">ID: {{ $transaction->course_id }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>${{ number_format($transaction->course_price, 2) }}</td>
                            <td>
                                @if($transaction->discount_code_id)
                                    @if($order && $order->DiscountCode)
                                        <span class="badge badge-success">
                                            {{ $order->DiscountCode->code }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            -${{ number_format($transaction->course_price - $transaction->total_price, 2) }}
                                        </small>
                                    @else
                                        <span class="badge badge-secondary">Applied</span>
                                    @endif
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                <strong>${{ number_format($transaction->total_price, 2) }}</strong>
                            </td>
                            <td>
                                @if($order && $order->PaymentType)
                                    <span class="badge badge-info">
                                        {{ $order->PaymentType->name }}
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <small>
                                    Created: {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y H:i') }}
                                    @if($transaction->completed_at)
                                        <br>Completed: {{ \Carbon\Carbon::parse($transaction->completed_at)->format('M d, Y H:i') }}
                                    @endif
                                    @if($transaction->refunded_at)
                                        <br>Refunded: {{ \Carbon\Carbon::parse($transaction->refunded_at)->format('M d, Y H:i') }}
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($transaction->refunded_at)
                                    <span class="badge badge-danger">Refunded</span>
                                @elseif($transaction->completed_at)
                                    <span class="badge badge-success">Completed</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.orders.show', $transaction->id) }}"
                                       class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!$transaction->refunded_at && $transaction->completed_at)
                                        <button class="btn btn-sm btn-warning" title="Refund"
                                                onclick="confirmRefund({{ $transaction->id }})">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No transactions found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="3" class="text-right">Page Totals:</td>
                            <td>${{ number_format($transactions->sum('course_price'), 2) }}</td>
                            <td>-${{ number_format($transactions->sum('course_price') - $transactions->sum('total_price'), 2) }}</td>
                            <td>${{ number_format($transactions->sum('total_price'), 2) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer clearfix">
            <div class="float-left">
                Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions
            </div>
            <div class="float-right">
                {{ $transactions->links() }}
            </div>
        </div>
        @endif
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Transactions</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.admin-center.transaction-logs') }}" method="GET">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_type" class="form-control">
                                <option value="">All Methods</option>
                                @foreach(\App\Models\PaymentType::all() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" name="date_from" class="form-control" placeholder="From">
                                </div>
                                <div class="col-6">
                                    <input type="date" name="date_to" class="form-control" placeholder="To">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Amount Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="amount_min" class="form-control" placeholder="Min $" step="0.01">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="amount_max" class="form-control" placeholder="Max $" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Order ID, User Name, Course...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <a href="{{ route('admin.admin-center.transaction-logs') }}" class="btn btn-warning">Clear Filters</a>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function confirmRefund(orderId) {
        if (confirm('Are you sure you want to refund this order? This action cannot be undone.')) {
            // TODO: Implement refund functionality
            alert('Refund functionality will be implemented here. Order ID: ' + orderId);
        }
    }

    // DataTable initialization (optional, requires DataTables library)
    $(document).ready(function() {
        // Add search functionality
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#transactionsTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@stop

@section('css')
<style>
    @media print {
        .card-header .card-tools,
        .btn-group,
        .pagination,
        .card-footer {
            display: none !important;
        }
    }
</style>
@stop
