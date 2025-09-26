@extends('adminlte::page')

@section('title', 'Create Order')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-plus-circle"></i>
            Create New Order
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Orders
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

        <form method="POST" action="{{ route('admin.orders.store') }}" id="order-form">
            @csrf

            <div class="row">
                <!-- Order Information -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-shopping-cart"></i>
                                Order Information
                            </h3>
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
                                                        {{ old('customer_id') == $user->id ? 'selected' : '' }}>
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
                                                        {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                    {{ $course->title }} - ${{ number_format($course->price ?? 0, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Course selection will set the base price</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            @foreach($orderStatuses as $key => $label)
                                                @if($key !== 'all')
                                                    <option value="{{ $key }}" {{ old('status', 'New') == $key ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_type_id">Payment Method</label>
                                        <select name="payment_type_id" id="payment_type_id" class="form-control">
                                            <option value="">Select payment method...</option>
                                            @foreach($paymentTypes as $paymentType)
                                                <option value="{{ $paymentType->id }}" {{ old('payment_type_id') == $paymentType->id ? 'selected' : '' }}>
                                                    {{ $paymentType->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes">Order Notes</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3"
                                                placeholder="Add any special notes or instructions for this order...">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discount Section -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-percentage"></i>
                                Discount & Pricing
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
                                                        {{ old('discount_code_id') == $discount->id ? 'selected' : '' }}>
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
                                                   value="{{ old('custom_discount') }}"
                                                   placeholder="0.00">
                                        </div>
                                        <small class="form-text text-muted">Override discount code with custom amount</small>
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
                                <i class="fas fa-calculator"></i>
                                Order Summary
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="order-summary">
                                <div class="summary-row">
                                    <span>Course Price:</span>
                                    <span id="course-price">$0.00</span>
                                </div>
                                <div class="summary-row" id="discount-row" style="display: none;">
                                    <span>Discount:</span>
                                    <span id="discount-amount" class="text-success">-$0.00</span>
                                </div>
                                <div class="summary-row" id="tax-row">
                                    <span>Tax:</span>
                                    <span id="tax-amount">$0.00</span>
                                </div>
                                <hr>
                                <div class="summary-row total-row">
                                    <strong>
                                        <span>Total:</span>
                                        <span id="order-total">$0.00</span>
                                    </strong>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="form-group">
                                    <label for="subtotal">Subtotal</label>
                                    <input type="hidden" name="subtotal" id="subtotal-input" value="{{ old('subtotal', 0) }}">
                                    <input type="text" class="form-control" id="subtotal-display" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="discount_amount">Discount Amount</label>
                                    <input type="hidden" name="discount_amount" id="discount-input" value="{{ old('discount_amount', 0) }}">
                                    <input type="text" class="form-control" id="discount-display" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="tax_amount">Tax Amount</label>
                                    <input type="hidden" name="tax_amount" id="tax-input" value="{{ old('tax_amount', 0) }}">
                                    <input type="text" class="form-control" id="tax-display" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="total">Total Amount</label>
                                    <input type="hidden" name="total" id="total-input" value="{{ old('total', 0) }}">
                                    <input type="text" class="form-control font-weight-bold" id="total-display" readonly>
                                </div>
                            </div>

                            <!-- Customer Info -->
                            <div id="customer-info" class="mt-4" style="display: none;">
                                <h5><i class="fas fa-user"></i> Customer Details</h5>
                                <div class="customer-details">
                                    <p><strong>Name:</strong> <span id="customer-name"></span></p>
                                    <p><strong>Email:</strong> <span id="customer-email"></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-6">
                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-block">
                                        <i class="fas fa-times"></i>
                                        Cancel
                                    </a>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fas fa-save"></i>
                                        Create Order
                                    </button>
                                </div>
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

    // Initial calculation
    updateOrderCalculations();
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

    // Calculate tax
    const taxAmount = subtotal * TAX_RATE;

    // Calculate total
    const total = subtotal + taxAmount;

    // Update display
    $('#course-price').text('$' + coursePrice.toFixed(2));

    if (discountAmount > 0) {
        $('#discount-amount').text('-$' + discountAmount.toFixed(2));
        $('#discount-row').show();
    } else {
        $('#discount-row').hide();
    }

    $('#tax-amount').text('$' + taxAmount.toFixed(2));
    $('#order-total').text('$' + total.toFixed(2));

    // Update hidden inputs
    $('#subtotal-input').val(subtotal.toFixed(2));
    $('#discount-input').val(discountAmount.toFixed(2));
    $('#tax-input').val(taxAmount.toFixed(2));
    $('#total-input').val(total.toFixed(2));

    // Update display inputs
    $('#subtotal-display').val('$' + subtotal.toFixed(2));
    $('#discount-display').val('-$' + discountAmount.toFixed(2));
    $('#tax-display').val('$' + taxAmount.toFixed(2));
    $('#total-display').val('$' + total.toFixed(2));
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
    const total = parseFloat($('#total-input').val()) || 0;
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
