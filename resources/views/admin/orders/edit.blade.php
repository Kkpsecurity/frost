@extends('adminlte::page')

@section('title', 'Edit Order #' . $order->id)

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Edit Order #{{ $order->id }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.orders.show', $order->id) }}">Order #{{ $order->id }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Order Information
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $order->completed_at ? 'success' : 'warning' }}">
                            {{ $order->completed_at ? 'Completed' : 'Pending' }}
                        </span>
                    </div>
                </div>
                
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="edit-order-form">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($order->completed_at)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Warning:</strong> This order has been completed. Modifying it may affect the associated CourseAuth and student access.
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Student <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-control" required 
                                            {{ $order->completed_at ? 'disabled' : '' }}>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $order->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->fname }} {{ $user->lname }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($order->completed_at)
                                        <input type="hidden" name="user_id" value="{{ $order->user_id }}">
                                        <small class="text-muted">Student cannot be changed for completed orders</small>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course_id">Course <span class="text-danger">*</span></label>
                                    <select name="course_id" id="course_id" class="form-control" required
                                            {{ $order->completed_at ? 'disabled' : '' }}>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" 
                                                data-price="{{ $course->price ?? 0 }}"
                                                {{ $order->course_id == $course->id ? 'selected' : '' }}>
                                                {{ $course->title }} - ${{ number_format($course->price ?? 0, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($order->completed_at)
                                        <input type="hidden" name="course_id" value="{{ $order->course_id }}">
                                        <small class="text-muted">Course cannot be changed for completed orders</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_type_id">Payment Method <span class="text-danger">*</span></label>
                                    <select name="payment_type_id" id="payment_type_id" class="form-control" required>
                                        @foreach($paymentTypes as $paymentType)
                                            <option value="{{ $paymentType->id }}" {{ $order->payment_type_id == $paymentType->id ? 'selected' : '' }}>
                                                {{ $paymentType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="discount_code_id">Discount Code (Optional)</label>
                                    <select name="discount_code_id" id="discount_code_id" class="form-control">
                                        <option value="">No Discount</option>
                                        @foreach($discountCodes as $discountCode)
                                            <option value="{{ $discountCode->id }}" 
                                                data-discount="{{ $discountCode->discount_amount ?? 0 }}"
                                                data-type="{{ $discountCode->discount_type ?? 'fixed' }}"
                                                {{ $order->discount_code_id == $discountCode->id ? 'selected' : '' }}>
                                                {{ $discountCode->code }} - 
                                                @if($discountCode->discount_type == 'percentage')
                                                    {{ $discountCode->discount_amount }}% off
                                                @else
                                                    ${{ $discountCode->discount_amount }} off
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5>Pricing Details</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course_price">Course Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" name="course_price" id="course_price" 
                                               class="form-control" value="{{ $order->course_price }}" required>
                                    </div>
                                    <small class="text-muted">Base price for the course</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_price">Total Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" name="total_price" id="total_price" 
                                               class="form-control" value="{{ $order->total_price }}" required readonly>
                                    </div>
                                    <small class="text-muted">Final price after discounts</small>
                                </div>
                            </div>
                        </div>

                        @if($order->completed_at)
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Order Completed:</strong> {{ $order->completed_at->format('M j, Y \a\t g:i A') }}
                                @if($order->courseAuth)
                                    <br><small>CourseAuth ID: #{{ $order->courseAuth->id }}</small>
                                @endif
                            </div>
                        @endif

                        @if($order->refunded_at)
                            <div class="alert alert-danger">
                                <i class="fas fa-undo"></i>
                                <strong>Order Refunded:</strong> {{ $order->refunded_at->format('M j, Y \a\t g:i A') }}
                                @if($order->refundedBy)
                                    <br><small>Refunded by: {{ $order->refundedBy->fname }} {{ $order->refundedBy->lname }}</small>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Order
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                @if(!$order->refunded_at)
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Order
                                    </button>
                                @else
                                    <span class="text-muted">Cannot edit refunded orders</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Order Status Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Order Status
                    </h3>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>Created:</dt>
                        <dd>{{ $order->created_at->format('M j, Y \a\t g:i A') }}</dd>
                        
                        <dt>Last Updated:</dt>
                        <dd>{{ $order->updated_at->format('M j, Y \a\t g:i A') }}</dd>
                        
                        <dt>Status:</dt>
                        <dd>
                            @if($order->refunded_at)
                                <span class="badge badge-danger">Refunded</span>
                            @elseif($order->completed_at)
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </dd>
                        
                        @if($order->courseAuth)
                            <dt>CourseAuth:</dt>
                            <dd>
                                <a href="#" class="btn btn-sm btn-outline-info">
                                    View Access #{{ $order->courseAuth->id }}
                                </a>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Actions Card -->
            @if(!$order->refunded_at)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs"></i> Quick Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical w-100">
                            @if(!$order->completed_at)
                                <form action="{{ route('admin.orders.mark-as-paid', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100" 
                                            onclick="return confirm('Mark this order as paid? This will create CourseAuth automatically.')">
                                        <i class="fas fa-check"></i> Mark as Paid
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.orders.process-refund', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100" 
                                            onclick="return confirm('Process refund for this order? This will revoke CourseAuth access.')">
                                        <i class="fas fa-undo"></i> Process Refund
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            
                            <a href="{{ route('admin.students.show', $order->user_id) }}" class="btn btn-secondary">
                                <i class="fas fa-user"></i> View Student
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Edit History -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Edit Guidelines
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6>What can be edited:</h6>
                        <ul class="mb-0">
                            <li>Payment method</li>
                            <li>Discount codes</li>
                            <li>Pricing (for pending orders)</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6>Restrictions:</h6>
                        <ul class="mb-0">
                            <li>Student/Course locked after completion</li>
                            <li>Refunded orders cannot be edited</li>
                            <li>Changes may affect CourseAuth</li>
                        </ul>
                    </div>

                    @if($order->completed_at && $order->courseAuth)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This order is linked to CourseAuth #{{ $order->courseAuth->id }}. Changes may affect student access.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .form-group label span.text-danger {
            font-size: 0.8em;
        }
        
        .form-control:disabled {
            background-color: #f8f9fa;
            opacity: 0.65;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-fill course price when course is selected (only if not completed)
            @if(!$order->completed_at)
                $('#course_id').change(function() {
                    const selectedOption = $(this).find('option:selected');
                    const price = selectedOption.data('price') || 0;
                    $('#course_price').val(price.toFixed(2));
                    calculateTotal();
                });
            @endif

            // Recalculate total when course price or discount changes
            $('#course_price, #discount_code_id').change(function() {
                calculateTotal();
            });

            function calculateTotal() {
                const coursePrice = parseFloat($('#course_price').val()) || 0;
                const discountOption = $('#discount_code_id').find('option:selected');
                const discountAmount = parseFloat(discountOption.data('discount')) || 0;
                const discountType = discountOption.data('type') || 'fixed';
                
                let total = coursePrice;
                
                if (discountAmount > 0) {
                    if (discountType === 'percentage') {
                        total = coursePrice - (coursePrice * discountAmount / 100);
                    } else {
                        total = coursePrice - discountAmount;
                    }
                }
                
                // Ensure total is not negative
                total = Math.max(0, total);
                
                $('#total_price').val(total.toFixed(2));
            }

            // Form validation
            $('#edit-order-form').submit(function(e) {
                const totalPrice = parseFloat($('#total_price').val());

                if (totalPrice < 0) {
                    e.preventDefault();
                    toastr.error('Total price cannot be negative.');
                    return false;
                }

                @if($order->completed_at)
                    if (!confirm('This order is completed and has CourseAuth. Are you sure you want to modify it?')) {
                        e.preventDefault();
                        return false;
                    }
                @endif

                return true;
            });

            // Calculate total on page load
            calculateTotal();
        });
    </script>
@stop
