@extends('adminlte::page')

@section('title', 'Create New Order')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Create New Order</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                <li class="breadcrumb-item active">Create</li>
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
                        <i class="fas fa-plus"></i> Order Information
                    </h3>
                </div>
                
                <form action="{{ route('admin.orders.store') }}" method="POST" id="create-order-form">
                    @csrf
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Student <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-control" required>
                                        <option value="">Select Student</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->fname }} {{ $user->lname }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Select the student for this order</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course_id">Course <span class="text-danger">*</span></label>
                                    <select name="course_id" id="course_id" class="form-control" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" 
                                                data-price="{{ $course->price ?? 0 }}"
                                                {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->title }} - ${{ number_format($course->price ?? 0, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Select the course to purchase</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_type_id">Payment Method <span class="text-danger">*</span></label>
                                    <select name="payment_type_id" id="payment_type_id" class="form-control" required>
                                        <option value="">Select Payment Method</option>
                                        @foreach($paymentTypes as $paymentType)
                                            <option value="{{ $paymentType->id }}" {{ old('payment_type_id') == $paymentType->id ? 'selected' : '' }}>
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
                                                {{ old('discount_code_id') == $discountCode->id ? 'selected' : '' }}>
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
                                               class="form-control" value="{{ old('course_price', '0.00') }}" required>
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
                                               class="form-control" value="{{ old('total_price', '0.00') }}" required readonly>
                                    </div>
                                    <small class="text-muted">Final price after discounts</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> This order will be created in pending status. You can mark it as paid after creation to generate the CourseAuth automatically.
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Order
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Help Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-question-circle"></i> Order Creation Guide
                    </h3>
                </div>
                <div class="card-body">
                    <h6>Steps to Create an Order:</h6>
                    <ol>
                        <li><strong>Select Student:</strong> Choose the student who will own this order</li>
                        <li><strong>Select Course:</strong> Pick the course to be purchased</li>
                        <li><strong>Payment Method:</strong> Choose how the payment will be processed</li>
                        <li><strong>Apply Discount:</strong> Optionally apply a discount code</li>
                        <li><strong>Review Pricing:</strong> Verify the final total price</li>
                        <li><strong>Create Order:</strong> Submit to create the order</li>
                    </ol>

                    <hr>

                    <h6>After Creation:</h6>
                    <ul>
                        <li>Order will be in <span class="badge badge-warning">Pending</span> status</li>
                        <li>Mark as paid to trigger CourseAuth creation</li>
                        <li>Student will receive course access automatically</li>
                    </ul>

                    <hr>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Important:</strong> Ensure the student doesn't already have active access to the selected course to avoid conflicts.
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100">
                        <button type="button" class="btn btn-info" onclick="checkStudentCourses()">
                            <i class="fas fa-search"></i> Check Student's Existing Courses
                        </button>
                        
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#manualCourseAuthModal">
                            <i class="fas fa-user-plus"></i> Grant Manual Access Instead
                        </button>
                        
                        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                            <i class="fas fa-users"></i> Browse Students
                        </a>
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
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Use this if you want to grant course access without creating a commercial order record.
                        </div>
                        
                        <div class="form-group">
                            <label for="manual_user_id">Student</label>
                            <select name="user_id" id="manual_user_id" class="form-control" required>
                                <option value="">Select Student</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->fname }} {{ $user->lname }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="manual_course_id">Course</label>
                            <select name="course_id" id="manual_course_id" class="form-control" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="manual_expire_date">Expiration Date (Optional)</label>
                            <input type="date" name="expire_date" id="manual_expire_date" class="form-control">
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
        .form-group label span.text-danger {
            font-size: 0.8em;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto-fill course price when course is selected
            $('#course_id').change(function() {
                const selectedOption = $(this).find('option:selected');
                const price = selectedOption.data('price') || 0;
                $('#course_price').val(price.toFixed(2));
                calculateTotal();
            });

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
            $('#create-order-form').submit(function(e) {
                const userId = $('#user_id').val();
                const courseId = $('#course_id').val();
                const totalPrice = parseFloat($('#total_price').val());

                if (!userId || !courseId) {
                    e.preventDefault();
                    toastr.error('Please select both student and course.');
                    return false;
                }

                if (totalPrice < 0) {
                    e.preventDefault();
                    toastr.error('Total price cannot be negative.');
                    return false;
                }

                return true;
            });
        });

        // Check student's existing courses
        function checkStudentCourses() {
            const userId = $('#user_id').val();
            if (!userId) {
                toastr.warning('Please select a student first.');
                return;
            }

            // This would typically make an AJAX call to check existing courses
            // For now, just redirect to student profile
            window.open('{{ route("admin.students.show", ":id") }}'.replace(':id', userId), '_blank');
        }
    </script>
@stop
