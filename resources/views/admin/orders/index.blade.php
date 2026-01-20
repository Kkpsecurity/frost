@extends('adminlte::page')

@section('title', 'Orders Management')

@section('content_header')
    <h1>Orders Management</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Statistics Row -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $content['stats']['total'] }}</h3>
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
                    <h3>{{ $content['stats']['completed'] }}</h3>
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
                    <h3>{{ $content['stats']['processing'] }}</h3>
                    <p>Processing</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>${{ number_format($content['stats']['total_revenue'], 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Orders</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Search User</label>
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Name or email..."
                                   value="{{ $content['filters']['search'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="completed" {{ ($content['filters']['status'] ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="processing" {{ ($content['filters']['status'] ?? '') == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="cancelled" {{ ($content['filters']['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Course</label>
                            <select name="course_id" class="form-control">
                                <option value="">All Courses</option>
                                @foreach($content['courses'] as $course)
                                    <option value="{{ $course->id }}" {{ ($content['filters']['course_id'] ?? '') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date"
                                   name="date_from"
                                   class="form-control"
                                   value="{{ $content['filters']['date_from'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date"
                                   name="date_to"
                                   class="form-control"
                                   value="{{ $content['filters']['date_to'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
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
                Orders
                <span class="badge badge-info">{{ $content['orders']->total() }}</span>
            </h3>
        </div>
        <div class="card-body table-responsive p-0">
            @if($content['orders']->count() > 0)
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Course</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($content['orders'] as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                </td>
                                <td>
                                    {{ $order->User->fname }} {{ $order->User->lname }}
                                    <br>
                                    <small class="text-muted">{{ $order->User->email }}</small>
                                </td>
                                <td>{{ $order->Course->title ?? 'N/A' }}</td>
                                <td>
                                    <strong>${{ number_format($order->total_price, 2) }}</strong>
                                    @if($order->discount_code_id)
                                        <br><small class="text-success">
                                            <i class="fas fa-tag"></i> Discount Applied
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $order->PaymentType->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    @if($order->refunded_at)
                                        <span class="badge badge-danger">
                                            <i class="fas fa-ban"></i> Cancelled
                                        </span>
                                    @elseif($order->completed_at)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Completed
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> Processing
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $order->created_at->format('M d, Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $order->created_at->format('g:i A') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                           class="btn btn-info"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No orders found matching your criteria.</p>
                </div>
            @endif
        </div>
        @if($content['orders']->hasPages())
            <div class="card-footer">
                {{ $content['orders']->links() }}
            </div>
        @endif
    </div>
</div>
@stop
