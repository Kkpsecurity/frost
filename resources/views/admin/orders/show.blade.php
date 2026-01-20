@extends('adminlte::page')

@section('title', 'Order Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Order #{{ $content['order']->order_number }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif

    <div class="row">
        <!-- Left Column - Order Details -->
        <div class="col-md-8">
            <!-- Order Status Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Status</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="mb-0">
                                @if($content['order']->refunded_at)
                                    <span class="badge badge-danger badge-lg">
                                        <i class="fas fa-ban"></i> Cancelled
                                    </span>
                                @elseif($content['order']->completed_at)
                                    <span class="badge badge-success badge-lg">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </span>
                                @else
                                    <span class="badge badge-warning badge-lg">
                                        <i class="fas fa-clock"></i> Processing
                                    </span>
                                @endif
                            </h4>
                        </div>
                        <div class="text-right">
                            <p class="text-muted mb-0">Order Date</p>
                            <strong>{{ $content['order']->created_at->format('M d, Y g:i A') }}</strong>
                        </div>
                    </div>

                    @if($content['order']->completed_at)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            Completed on {{ $content['order']->completed_at->format('M d, Y g:i A') }}
                        </div>
                    @endif

                    @if($content['order']->refunded_at)
                        <div class="alert alert-danger">
                            <i class="fas fa-ban"></i>
                            Cancelled on {{ $content['order']->refunded_at->format('M d, Y g:i A') }}
                            @if($content['order']->RefundedBy)
                                by {{ $content['order']->RefundedBy->fname }} {{ $content['order']->RefundedBy->lname }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Items</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th width="150" class="text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>{{ $content['order']->Course->title ?? 'N/A' }}</strong>
                                    @if($content['order']->Course->description)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($content['order']->Course->description, 100) }}</small>
                                    @endif
                                </td>
                                <td class="text-right">
                                    ${{ number_format($content['order']->course_price, 2) }}
                                </td>
                            </tr>
                            @if($content['order']->discount_code_id && $content['order']->DiscountCode)
                                <tr>
                                    <td>
                                        <span class="text-success">
                                            <i class="fas fa-tag"></i> Discount Code: {{ $content['order']->DiscountCode->code }}
                                        </span>
                                    </td>
                                    <td class="text-right text-success">
                                        -${{ number_format($content['order']->discount_amount, 2) }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th>Total</th>
                                <th class="text-right">
                                    <h4 class="mb-0">${{ number_format($content['order']->total_price, 2) }}</h4>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Payment Method:</dt>
                        <dd class="col-sm-8">{{ $content['order']->PaymentType->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Subtotal:</dt>
                        <dd class="col-sm-8">${{ number_format($content['order']->course_price, 2) }}</dd>

                        @if($content['order']->discount_code_id)
                            <dt class="col-sm-4">Discount:</dt>
                            <dd class="col-sm-8 text-success">
                                -${{ number_format($content['order']->discount_amount, 2) }}
                            </dd>
                        @endif

                        <dt class="col-sm-4"><strong>Total Paid:</strong></dt>
                        <dd class="col-sm-8">
                            <strong class="text-primary">${{ number_format($content['order']->total_price, 2) }}</strong>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Right Column - Customer & Actions -->
        <div class="col-md-4">
            <!-- Customer Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Information</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($content['order']->User->avatar)
                            <img src="{{ $content['order']->User->avatar }}"
                                 alt="Customer"
                                 class="img-circle"
                                 style="width: 80px; height: 80px;">
                        @else
                            <div class="img-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                 style="width: 80px; height: 80px;">
                                <span style="font-size: 2rem;">
                                    {{ strtoupper(substr($content['order']->User->fname, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <h5 class="text-center">
                        {{ $content['order']->User->fname }} {{ $content['order']->User->lname }}
                    </h5>

                    <hr>

                    <dl class="row mb-0">
                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">
                            <a href="mailto:{{ $content['order']->User->email }}">
                                {{ $content['order']->User->email }}
                            </a>
                        </dd>

                        @if($content['order']->User->student_info && isset($content['order']->User->student_info['phone']))
                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8">
                                <a href="tel:{{ $content['order']->User->student_info['phone'] }}">
                                    {{ $content['order']->User->student_info['phone'] }}
                                </a>
                            </dd>
                        @endif

                        <dt class="col-sm-4">User ID:</dt>
                        <dd class="col-sm-8">{{ $content['order']->User->id }}</dd>
                    </dl>

                    <a href="{{ route('admin.students.show', $content['order']->User->id) }}"
                       class="btn btn-outline-primary btn-block mt-3">
                        <i class="fas fa-user"></i> View Student Profile
                    </a>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Timeline</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-info">{{ $content['order']->created_at->format('M d, Y') }}</span>
                        </div>

                        <div>
                            <i class="fas fa-shopping-cart bg-primary"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock"></i> {{ $content['order']->created_at->format('g:i A') }}
                                </span>
                                <h3 class="timeline-header">Order Created</h3>
                                <div class="timeline-body">
                                    Order placed for {{ $content['order']->Course->title ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        @if($content['order']->completed_at)
                            <div>
                                <i class="fas fa-check-circle bg-success"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $content['order']->completed_at->format('g:i A') }}
                                    </span>
                                    <h3 class="timeline-header">Order Completed</h3>
                                    <div class="timeline-body">
                                        Payment processed successfully
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($content['order']->refunded_at)
                            <div>
                                <i class="fas fa-ban bg-danger"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $content['order']->refunded_at->format('g:i A') }}
                                    </span>
                                    <h3 class="timeline-header">Order Cancelled</h3>
                                    <div class="timeline-body">
                                        Refund processed
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

            <!-- Quick Actions -->
            @if(!$content['order']->refunded_at)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Actions</h3>
                    </div>
                    <div class="card-body">
                        @if(!$content['order']->completed_at)
                            <form action="{{ route('admin.orders.complete', $content['order']) }}"
                                  method="POST"
                                  class="mb-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check"></i> Mark as Completed
                                </button>
                            </form>
                        @endif

                        @if($content['order']->CanRefund())
                            <button type="button"
                                    class="btn btn-danger btn-block"
                                    data-toggle="modal"
                                    data-target="#refundModal">
                                <i class="fas fa-undo"></i> Process Refund
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Confirm Refund</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.orders.refund', $content['order']) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p><strong>Are you sure you want to process a refund for this order?</strong></p>
                    <p>Order: <strong>{{ $content['order']->order_number }}</strong></p>
                    <p>Amount: <strong>${{ number_format($content['order']->total_price, 2) }}</strong></p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-undo"></i> Process Refund
                    </button>
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
    width: 4px;
    background: #ddd;
    left: 31px;
    margin: 0;
    border-radius: 2px;
}
.timeline > div {
    margin-bottom: 15px;
    position: relative;
}
.timeline > div > .timeline-item {
    margin-top: -25px;
    background: #fff;
    color: #444;
    margin-left: 60px;
    margin-right: 15px;
    padding: 0;
    position: relative;
}
.timeline > div > .fas,
.timeline > div > .far,
.timeline > div > .fab,
.timeline > div > .fal,
.timeline > div > .ion {
    width: 30px;
    height: 30px;
    font-size: 15px;
    line-height: 30px;
    position: absolute;
    color: #666;
    background: #d2d6de;
    border-radius: 50%;
    text-align: center;
    left: 18px;
    top: 0;
}
.timeline > .time-label > span {
    font-weight: 600;
    padding: 5px;
    display: inline-block;
    background-color: #fff;
    border-radius: 4px;
}
.timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding: 10px;
    font-size: 16px;
    line-height: 1.1;
}
.timeline-body,
.timeline-footer {
    padding: 10px;
}
.timeline > div > .timeline-item > .time {
    color: #999;
    float: right;
    padding: 10px;
    font-size: 12px;
}
</style>
@stop
