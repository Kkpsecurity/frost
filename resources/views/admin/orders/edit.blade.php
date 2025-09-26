@extends('adminlte::page')

@section('title', 'Edit Order #' . ($order->order_number ?? 'ORD-' . $order->id))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-edit"></i>
            Edit Order #{{ $order->order_number ?? 'ORD-' . $order->id }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Order
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i>
                All Orders
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h4><i class="fas fa-exclamation-triangle"></i> Please correct the following errors:</h4>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.orders.update', $order) }}" id="order-form">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Order Information -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-shopping-cart"></i>
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
                                    <div class="form-group">
                                        <label for="customer_id">Customer *</label>
                                        <select name="customer_id" id="customer_id" class="form-control select2" required>
                                            <option value="">Select a customer...</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}"
                                                        data-email="{{ $user->email }}"
                                                        {{ (old('customer_id', $order->user_id) == $user->id) ? 'selected' : '' }}>
                                                    {{ $user->fname }} {{ $user->lname }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Type to search for a customer</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="course_id">Course *</label>
                                        <select name="course_id" id="course_id" class="form-control select2" required>
                                            <option value="">Select a course...</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}"
                                                        data-price="{{ $course->price ?? 0 }}"
                                                        {{ (old('course_id', $order->course_id) == $course->id) ? 'selected' : '' }}>
                                                    {{ $course->title }} - ${{ number_format($course->price ?? 0, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Course selection will update the base price</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="order_number">Order Number</label>
                                        <input type="text" name="order_number" id="order_number" class="form-control"
                                               value="{{ old('order_number', $order->order_number) }}"
                                               placeholder="Auto-generated if left blank">
                                        <small class="form-text text-muted">Leave blank for auto-generation</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            @foreach($orderStatuses as $key => $label)
                                                @if($key !== 'all')
                                                    <option value="{{ $key }}" {{ (old('status', $order->status ?? 'New') == $key) ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="payment_type_id">Payment Method</label>
                                        <select name="payment_type_id" id="payment_type_id" class="form-control">
                                            <option value="">Select payment method...</option>
                                            @foreach($paymentTypes as $paymentType)
                                                <option value="{{ $paymentType->id }}" {{ (old('payment_type_id', $order->payment_type_id) == $paymentType->id) ? 'selected' : '' }}>
                                                    {{ $paymentType->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference">Reference</label>
                                        <input type="text" name="reference" id="reference" class="form-control"
                                               value="{{ old('reference', $order->reference) }}"
                                               placeholder="External reference or transaction ID">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="order_date">Order Date</label>
                                        <input type="datetime-local" name="order_date" id="order_date" class="form-control"
                                               value="{{ old('order_date', $order->created_at->format('Y-m-d\TH:i')) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes">Order Notes</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3"
                                                placeholder="Add any special notes or instructions for this order...">{{ old('notes', $order->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Section -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calculator"></i>
                                Pricing & Discount
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="discount_code_id">Discount Code</label>
                                        <select name="discount_code_id" id="discount_code_id" class="form-control">
                                            <option value="">No discount code</option>
                                            @foreach($discountCodes as $discount)
                                                <option value="{{ $discount->id }}"
                                                        data-type="{{ $discount->discount_type }}"
                                                        data-value="{{ $discount->discount_value }}"
                                                        {{ (old('discount_code_id', $order->discount_code_id) == $discount->id) ? 'selected' : '' }}>
                                                    {{ $discount->code }} -
                                                    @if($discount->discount_type === 'percentage')
                                                        {{ $discount->discount_value }}% off
                                                    @else
                                                        ${{ number_format($discount->discount_value, 2) }} off
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="custom_discount">Custom Discount Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" name="custom_discount" id="custom_discount"
                                                   class="form-control" step="0.01" min="0"
                                                   value="{{ old('custom_discount', ($order->discount_amount > 0 && !$order->discount_code_id) ? $order->discount_amount : '') }}"
                                                   placeholder="0.00">
                                        </div>
                                        <small class="form-text text-muted">Override discount code with custom amount</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="subtotal">Subtotal</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" name="subtotal" id="subtotal" class="form-control"
                                                   step="0.01" min="0" value="{{ old('subtotal', $order->subtotal ?? 0) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tax_amount">Tax Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" name="tax_amount" id="tax_amount" class="form-control"
                                                   step="0.01" min="0" value="{{ old('tax_amount', $order->tax_amount ?? 0) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="total">Total Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" name="total" id="total" class="form-control font-weight-bold"
                                                   step="0.01" min="0" value="{{ old('total', $order->total ?? 0) }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Additional Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Created:</label>
                                        <p class="form-control-static">
                                            {{ $order->created_at->format('F j, Y g:i A') }}
                                            @if($order->CreatedBy)
                                                by {{ $order->CreatedBy->fname }} {{ $order->CreatedBy->lname }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Last Updated:</label>
                                        <p class="form-control-static">
                                            {{ $order->updated_at->format('F j, Y g:i A') }}
                                            @if($order->UpdatedBy)
                                                by {{ $order->UpdatedBy->fname }} {{ $order->UpdatedBy->lname }}
                                            @endif
                                        </p>
                                    </div>
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
                                <i class="fas fa-receipt"></i>
                                Current Order Summary
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="order-summary">
                                <div class="summary-row">
                                    <span>Course Price:</span>
                                    <span id="course-price">${{ number_format($order->GetCourse()->price ?? 0, 2) }}</span>
                                </div>
                                <div class="summary-row" id="discount-row" style="{{ ($order->discount_amount ?? 0) > 0 ? '' : 'display: none;' }}">
                                    <span>Discount:</span>
                                    <span id="discount-amount" class="text-success">-${{ number_format($order->discount_amount ?? 0, 2) }}</span>
                                </div>
                                <div class="summary-row">
                                    <span>Subtotal:</span>
                                    <span id="subtotal-display">${{ number_format($order->subtotal ?? 0, 2) }}</span>
                                </div>
                                <div class="summary-row" id="tax-row">
                                    <span>Tax:</span>
                                    <span id="tax-display">${{ number_format($order->tax_amount ?? 0, 2) }}</span>
                                </div>
                                <hr>
                                <div class="summary-row total-row">
                                    <strong>
                                        <span>Total:</span>
                                        <span id="order-total" class="text-primary">${{ number_format($order->total ?? 0, 2) }}</span>
                                    </strong>
                                </div>
                            </div>

                            <!-- Customer Info -->
                            <div id="customer-info" class="mt-4" style="{{ $order->User ? '' : 'display: none;' }}">
                                <h5><i class="fas fa-user"></i> Customer Details</h5>
                                <div class="customer-details">
                                    <p><strong>Name:</strong> <span id="customer-name">{{ $order->User ? $order->User->fname . ' ' . $order->User->lname : '' }}</span></p>
                                    <p><strong>Email:</strong> <span id="customer-email">{{ $order->User->email ?? '' }}</span></p>
                                </div>
                            </div>

                            @if($order->DiscountCode)
                            <div class="discount-info mt-3">
                                <h6><i class="fas fa-percentage"></i> Applied Discount</h6>
                                <div class="alert alert-success alert-sm">
                                    <strong>{{ $order->DiscountCode->code }}</strong><br>
                                    @if($order->DiscountCode->discount_type === 'percentage')
                                        {{ $order->DiscountCode->discount_value }}% off
                                    @else
                                        ${{ number_format($order->DiscountCode->discount_value, 2) }} off
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-6">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-times"></i>
                                        Cancel
                                    </a>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fas fa-save"></i>
                                        Update Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
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

                                <a href="{{ route('admin.orders.duplicate', $order) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-copy"></i>
                                    Duplicate Order
                                </a>

                                <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-file-invoice"></i>
                                    Download Invoice
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css">
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

.sticky-top {
    position: sticky;
    top: 20px;
}

.select2-container--bootstrap {
    width: 100% !important;
}

.customer-details p {
    margin-bottom: 0.5rem;
}

.form-group label {
    font-weight: 600;
}

.card-primary.card-outline {
    border-top: 3px solid #007bff;
}

.alert-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.form-control-static {
    padding-top: 7px;
    padding-bottom: 7px;
    margin-bottom: 0;
    min-height: 34px;
}

@media (max-width: 768px) {
    .sticky-top {
        position: relative;
    }
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

    // Tax rate (you can make this configurable)
    const TAX_RATE = 0.08; // 8% tax

    // Update calculations when course changes
    $('#course_id').change(function() {
        updateOrderCalculations();
    });

    // Update calculations when discount changes
    $('#discount_code_id, #custom_discount').change(function() {
        updateOrderCalculations();
    });

    // Update calculations when tax is manually changed
    $('#tax_amount').change(function() {
        updateTotals();
    });

    // Show customer info when customer selected
    $('#customer_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            const customerName = selectedOption.text().split(' (')[0];
            const customerEmail = selectedOption.data('email');

            $('#customer-name').text(customerName);
            $('#customer-email').text(customerEmail);
            $('#customer-info').show();
        } else {
            $('#customer-info').hide();
        }
    });
});

function updateOrderCalculations() {
    const courseSelect = $('#course_id');
    const discountSelect = $('#discount_code_id');
    const customDiscountInput = $('#custom_discount');

    // Get course price
    let coursePrice = 0;
    const selectedCourse = courseSelect.find('option:selected');
    if (selectedCourse.val()) {
        coursePrice = parseFloat(selectedCourse.data('price')) || 0;
    }

    // Calculate discount
    let discountAmount = 0;

    // Custom discount takes priority
    const customDiscount = parseFloat(customDiscountInput.val()) || 0;
    if (customDiscount > 0) {
        discountAmount = customDiscount;
    } else {
        // Check discount code
        const selectedDiscount = discountSelect.find('option:selected');
        if (selectedDiscount.val()) {
            const discountType = selectedDiscount.data('type');
            const discountValue = parseFloat(selectedDiscount.data('value')) || 0;

            if (discountType === 'percentage') {
                discountAmount = coursePrice * (discountValue / 100);
            } else {
                discountAmount = discountValue;
            }
        }
    }

    // Ensure discount doesn't exceed course price
    discountAmount = Math.min(discountAmount, coursePrice);

    // Calculate subtotal
    const subtotal = coursePrice - discountAmount;

    // Calculate tax (use manual tax if set, otherwise calculate)
    let taxAmount = parseFloat($('#tax_amount').val()) || 0;
    if (taxAmount === 0) {
        taxAmount = subtotal * TAX_RATE;
    }

    // Update display
    $('#course-price').text('$' + coursePrice.toFixed(2));

    if (discountAmount > 0) {
        $('#discount-amount').text('-$' + discountAmount.toFixed(2));
        $('#discount-row').show();
    } else {
        $('#discount-row').hide();
    }

    // Update form inputs
    $('#subtotal').val(subtotal.toFixed(2));
    $('#tax_amount').val(taxAmount.toFixed(2));

    updateDisplays(subtotal, discountAmount, taxAmount);
    updateTotals();
}

function updateTotals() {
    const subtotal = parseFloat($('#subtotal').val()) || 0;
    const taxAmount = parseFloat($('#tax_amount').val()) || 0;
    const total = subtotal + taxAmount;

    $('#total').val(total.toFixed(2));

    // Update displays
    $('#order-total').text('$' + total.toFixed(2));
}

function updateDisplays(subtotal, discountAmount, taxAmount) {
    $('#subtotal-display').text('$' + subtotal.toFixed(2));
    $('#tax-display').text('$' + taxAmount.toFixed(2));
}

function markComplete() {
    if (confirm('Mark this order as complete? This will save any changes first.')) {
        $('#status').val('Completed');
        $('#order-form').submit();
    }
}

function cancelOrder() {
    if (confirm('Are you sure you want to cancel this order? This will save any changes first.')) {
        $('#status').val('Cancelled');
        $('#order-form').submit();
    }
}

// Form validation
$('#order-form').submit(function(e) {
    let isValid = true;
    let errorMessages = [];

    // Check required fields
    if (!$('#customer_id').val()) {
        errorMessages.push('Please select a customer');
        isValid = false;
    }

    if (!$('#course_id').val()) {
        errorMessages.push('Please select a course');
        isValid = false;
    }

    // Check if total is greater than 0
    const total = parseFloat($('#total').val()) || 0;
    if (total <= 0) {
        errorMessages.push('Order total must be greater than $0.00');
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
        alert('Please correct the following errors:\n\n' + errorMessages.join('\n'));
    }
});
</script>
@stop
