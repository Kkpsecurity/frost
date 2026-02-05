@extends('adminlte::page')

@section('title', 'Edit Student')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-edit"></i> Edit Student</h1>
        <div>
            <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Details
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
                @csrf
                @method('PUT')

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Basic Information -->
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title"><i class="fas fa-user"></i> Basic Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">First Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('fname') is-invalid @enderror"
                                           id="fname"
                                           name="fname"
                                           value="{{ old('fname', $student->fname) }}"
                                           required>
                                    @error('fname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Last Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('lname') is-invalid @enderror"
                                           id="lname"
                                           name="lname"
                                           value="{{ old('lname', $student->lname) }}"
                                           required>
                                    @error('lname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $student->email) }}"
                                           required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    @if($student->email_verified_at)
                                        <small class="form-text text-success">
                                            <i class="fas fa-check-circle"></i> Email verified on {{ \Carbon\Carbon::parse($student->email_verified_at)->format('M d, Y') }}
                                        </small>
                                    @else
                                        <small class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Email not verified
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="is_active">Account Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('is_active') is-invalid @enderror"
                                            id="is_active"
                                            name="is_active">
                                        <option value="1" {{ old('is_active', $student->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active', $student->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="email_opt_in">Email Notifications</label>
                                    <select class="form-control @error('email_opt_in') is-invalid @enderror"
                                            id="email_opt_in"
                                            name="email_opt_in">
                                        <option value="1" {{ old('email_opt_in', $student->email_opt_in) == 1 ? 'selected' : '' }}>Enabled</option>
                                        <option value="0" {{ old('email_opt_in', $student->email_opt_in) == 0 ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                    @error('email_opt_in')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title"><i class="fas fa-phone"></i> Contact Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone', $student->student_info['phone'] ?? '') }}"
                                           placeholder="(555) 123-4567">
                                    @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date"
                                           class="form-control @error('dob') is-invalid @enderror"
                                           id="dob"
                                           name="dob"
                                           value="{{ old('dob', $student->student_info['dob'] ?? '') }}">
                                    @error('dob')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Street Address</label>
                            <input type="text"
                                   class="form-control @error('address') is-invalid @enderror"
                                   id="address"
                                   name="address"
                                   value="{{ old('address', $student->student_info['address'] ?? '') }}"
                                   placeholder="123 Main St">
                            @error('address')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address2">Address Line 2</label>
                            <input type="text"
                                   class="form-control @error('address2') is-invalid @enderror"
                                   id="address2"
                                   name="address2"
                                   value="{{ old('address2', $student->student_info['address2'] ?? '') }}"
                                   placeholder="Apt, Suite, Unit, etc.">
                            @error('address2')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text"
                                           class="form-control @error('city') is-invalid @enderror"
                                           id="city"
                                           name="city"
                                           value="{{ old('city', $student->student_info['city'] ?? '') }}"
                                           placeholder="City">
                                    @error('city')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <input type="text"
                                           class="form-control @error('state') is-invalid @enderror"
                                           id="state"
                                           name="state"
                                           value="{{ old('state', $student->student_info['state'] ?? '') }}"
                                           placeholder="FL"
                                           maxlength="2"
                                           style="text-transform: uppercase;">
                                    @error('state')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="zip">ZIP Code</label>
                                    <input type="text"
                                           class="form-control @error('zip') is-invalid @enderror"
                                           id="zip"
                                           name="zip"
                                           value="{{ old('zip', $student->student_info['zip'] ?? '') }}"
                                           placeholder="12345">
                                    @error('zip')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text"
                                   class="form-control @error('country') is-invalid @enderror"
                                   id="country"
                                   name="country"
                                   value="{{ old('country', $student->student_info['country'] ?? 'US') }}"
                                   placeholder="US">
                            @error('country')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card">
                    <div class="card-header bg-secondary">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Additional Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="initials">Initials</label>
                                    <input type="text"
                                           class="form-control @error('initials') is-invalid @enderror"
                                           id="initials"
                                           name="initials"
                                           value="{{ old('initials', $student->student_info['initials'] ?? '') }}"
                                           maxlength="3"
                                           placeholder="J"
                                           style="text-transform: uppercase;">
                                    @error('initials')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Middle initial or suffix</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="suffix">Suffix</label>
                                    <input type="text"
                                           class="form-control @error('suffix') is-invalid @enderror"
                                           id="suffix"
                                           name="suffix"
                                           value="{{ old('suffix', $student->student_info['suffix'] ?? '') }}"
                                           maxlength="10"
                                           placeholder="Jr, Sr, III, etc.">
                                    @error('suffix')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Admin Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes"
                                      name="notes"
                                      rows="4"
                                      placeholder="Internal notes about this student (not visible to student)">{{ old('notes', $student->student_info['notes'] ?? '') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Password Reset Section -->
                <div class="card">
                    <div class="card-header bg-warning">
                        <h3 class="card-title"><i class="fas fa-key"></i> Password Management</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Leave password fields blank to keep the current password.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           autocomplete="new-password">
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 8 characters</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm New Password</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           autocomplete="new-password">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> Course enrollment and unit assignments cannot be changed here. Please use the respective management sections.
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-header.bg-primary,
        .card-header.bg-info,
        .card-header.bg-secondary,
        .card-header.bg-warning {
            color: white;
        }
        .form-group label {
            font-weight: 600;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-uppercase state field
        $('#state').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });

        // Auto-uppercase initials field
        $('#initials').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });

        // Form validation
        $('form').on('submit', function(e) {
            const password = $('#password').val();
            const passwordConfirm = $('#password_confirmation').val();

            if (password && password !== passwordConfirm) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }

            if (password && password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return false;
            }
        });
    </script>
@stop
