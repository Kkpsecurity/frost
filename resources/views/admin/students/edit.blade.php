@extends('adminlte::page')

@section('title', 'Edit Student: ' . (trim(($student->fname ?? '') . ' ' . ($student->lname ?? '')) ?: 'Student'))

@section('content_header')
    <x-admin.partials.titlebar
        title="Edit Student"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Students', 'url' => route('admin.students.dashboard')],
            ['title' => trim(($student->fname ?? '') . ' ' . ($student->lname ?? '')), 'url' => route('admin.students.manage.view', $student)],
            ['title' => 'Edit']
        ]"
    />
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Student Information</h3>
                </div>

                <form method="POST" action="{{ route('admin.students.manage.update', $student) }}">
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

                        {{-- Basic Information --}}
                        <h5 class="text-primary mb-3"><i class="fas fa-user"></i> Basic Information</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">First Name <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        class="form-control @error('fname') is-invalid @enderror"
                                        id="fname"
                                        name="fname"
                                        value="{{ old('fname', $student->fname) }}"
                                        required
                                    >
                                    @error('fname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="initials">Middle Initial</label>
                                    @php
                                        $initialsValue = $student->student_info['initials'] ?? '';
                                    @endphp
                                    <input
                                        type="text"
                                        class="form-control @error('initials') is-invalid @enderror"
                                        id="initials"
                                        name="initials"
                                        value="{{ old('initials', $initialsValue) }}"
                                        maxlength="5"
                                    >
                                    @error('initials')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="suffix">Suffix</label>
                                    @php
                                        $suffixValue = $student->student_info['suffix'] ?? '';
                                        $suffixes = config('define.suffixes', ['Jr.', 'Sr.', 'I', 'II', 'III']);
                                    @endphp
                                    <select
                                        class="form-control @error('suffix') is-invalid @enderror"
                                        id="suffix"
                                        name="suffix"
                                    >
                                        <option value="">None</option>
                                        @foreach($suffixes as $suffix)
                                            <option value="{{ $suffix }}" {{ old('suffix', $suffixValue) === $suffix ? 'selected' : '' }}>
                                                {{ $suffix }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('suffix')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="lname">Last Name <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control @error('lname') is-invalid @enderror"
                                id="lname"
                                name="lname"
                                value="{{ old('lname', $student->lname) }}"
                                required
                            >
                            @error('lname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Contact Information --}}
                        <h5 class="text-primary mb-3"><i class="fas fa-address-book"></i> Contact Information</h5>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input
                                        type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        value="{{ old('email', $student->email) }}"
                                        required
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    @php
                                        $phoneValue = $student->student_info['phone'] ?? '';
                                    @endphp
                                    <input
                                        type="text"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        id="phone"
                                        name="phone"
                                        value="{{ old('phone', $phoneValue) }}"
                                    >
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            @php
                                $dobValue = $student->student_info['dob'] ?? '';
                                // Format date for display (YYYY-MM-DD format for HTML date input)
                                if ($dobValue && !empty($dobValue)) {
                                    try {
                                        $dobFormatted = \Carbon\Carbon::parse($dobValue)->format('Y-m-d');
                                    } catch (\Exception $e) {
                                        $dobFormatted = '';
                                    }
                                } else {
                                    $dobFormatted = '';
                                }
                            @endphp
                            <input
                                type="date"
                                class="form-control @error('dob') is-invalid @enderror"
                                id="dob"
                                name="dob"
                                value="{{ old('dob', $dobFormatted) }}"
                            >
                            @error('dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Account Settings --}}
                        <h5 class="text-primary mb-3"><i class="fas fa-cogs"></i> Account Settings</h5>

                        <div class="form-group">
                            <label for="status">Account Status <span class="text-danger">*</span></label>
                            @php
                                $statusOldOrCurrent = old('status', $student->is_active ? 'active' : 'inactive');
                            @endphp
                            <select
                                class="form-control @error('status') is-invalid @enderror"
                                id="status"
                                name="status"
                                required
                            >
                                <option value="active" {{ $statusOldOrCurrent === 'active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="inactive" {{ $statusOldOrCurrent === 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="email_opt_in"
                                            name="email_opt_in"
                                            value="1"
                                            {{ old('email_opt_in', $student->email_opt_in) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="email_opt_in">
                                            Email Marketing Opt-In
                                        </label>
                                        <small class="form-text text-muted">
                                            Allow promotional emails and updates
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="use_gravatar"
                                            name="use_gravatar"
                                            value="1"
                                            {{ old('use_gravatar', $student->use_gravatar) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="use_gravatar">
                                            Use Gravatar for Avatar
                                        </label>
                                        <small class="form-text text-muted">
                                            Use Gravatar service for profile picture
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="avatar">Custom Avatar URL</label>
                            <input
                                type="url"
                                class="form-control @error('avatar') is-invalid @enderror"
                                id="avatar"
                                name="avatar"
                                value="{{ old('avatar', $student->avatar) }}"
                                placeholder="https://example.com/avatar.jpg"
                            >
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Custom avatar URL (will be ignored if 'Use Gravatar' is checked)
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Account Information</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="form-text text-muted">
                                        <strong>Member Since:</strong> {{ $student->created_at?->format('M d, Y') }}
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small class="form-text text-muted">
                                        <strong>Last Updated:</strong> {{ $student->updated_at?->format('M d, Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Student
                        </button>
                        <a href="{{ route('admin.students.manage.view', $student) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Current Student Info --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8">{{ trim(($student->fname ?? '') . ' ' . ($student->lname ?? '')) }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $student->email }}</dd>

                        <dt class="col-sm-4">Phone:</dt>
                        <dd class="col-sm-8">{{ $student->student_info['phone'] ?? 'Not provided' }}</dd>

                        <dt class="col-sm-4">Initial:</dt>
                        <dd class="col-sm-8">{{ $student->student_info['initials'] ?? 'Not provided' }}</dd>

                        <dt class="col-sm-4">Suffix:</dt>
                        <dd class="col-sm-8">{{ $student->student_info['suffix'] ?? 'Not provided' }}</dd>

                        <dt class="col-sm-4">Date of Birth:</dt>
                        <dd class="col-sm-8">
                            @if(!empty($student->student_info['dob']))
                                {{ \Carbon\Carbon::parse($student->student_info['dob'])->format('M d, Y') }}
                            @else
                                Not provided
                            @endif
                        </dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($student->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Joined:</dt>
                        <dd class="col-sm-8">{{ $student->created_at?->format('M d, Y') }}</dd>

                        <dt class="col-sm-4">Email Opt-In:</dt>
                        <dd class="col-sm-8">
                            @if($student->email_opt_in)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Avatar:</dt>
                        <dd class="col-sm-8">
                            @if($student->use_gravatar)
                                <span class="badge badge-info">Gravatar</span>
                            @elseif($student->avatar)
                                <span class="badge badge-primary">Custom</span>
                            @else
                                <span class="badge badge-secondary">Default</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            {{-- Student Stats --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Student Stats</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Orders</span>
                                    <span class="info-box-number">{{ \App\Models\Order::where('user_id', $student->id)->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-book"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Courses</span>
                                    <span class="info-box-number">{{ $student->activeCourseAuths->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-graduation-cap"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed</span>
                                    <span class="info-box-number">{{ $student->inactiveCourseAuths->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Danger Zone</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">These actions cannot be undone. Please be careful.</p>

                    @if($student->is_active)
                        <form method="POST" action="{{ route('admin.students.manage.deactivate', $student) }}" class="mb-2">
                            @csrf
                            <button
                                type="submit"
                                class="btn btn-warning btn-block"
                                onclick="return confirm('Are you sure you want to deactivate this student? This will prevent them from accessing their courses.')"
                            >
                                <i class="fas fa-ban"></i> Deactivate Student
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.students.manage.activate', $student) }}" class="mb-2">
                            @csrf
                            <button
                                type="submit"
                                class="btn btn-success btn-block"
                                onclick="return confirm('Are you sure you want to activate this student? This will restore their access to courses.')"
                            >
                                <i class="fas fa-check"></i> Activate Student
                            </button>
                        </form>
                    @endif

                    <small class="text-muted d-block mt-2">
                        Deactivating a student will prevent them from accessing their courses but will not delete their data.
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
$(function () {
    // Phone number formatting
    $('#phone').on('input', function () {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 10) {
            value = value.replace(/(\d{3})(\d{3})(\d{0,4})/, '($1) $2-$3');
        } else if (value.length >= 6) {
            value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
        }
        $(this).val(value.trim());
    });

    // Avatar field interaction with gravatar checkbox
    $('#use_gravatar').change(function() {
        const avatarField = $('#avatar');
        if ($(this).is(':checked')) {
            avatarField.attr('disabled', true).addClass('text-muted');
            avatarField.siblings('.form-text').text('Custom avatar URL (disabled when using Gravatar)');
        } else {
            avatarField.attr('disabled', false).removeClass('text-muted');
            avatarField.siblings('.form-text').text('Custom avatar URL (will be ignored if "Use Gravatar" is checked)');
        }
    });

    // Initial state for avatar field
    $('#use_gravatar').trigger('change');

    // Form validation feedback
    $('form').on('submit', function(e) {
        let isValid = true;

        // Check required fields
        $('input[required], select[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Email validation
        const email = $('#email').val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            $('#email').addClass('is-invalid');
            isValid = false;
        }

        // Phone validation (if provided)
        const phone = $('#phone').val();
        if (phone && phone.length > 0 && phone.replace(/\D/g, '').length < 10) {
            $('#phone').addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid:first').offset().top - 100
            }, 500);
        }
    });

    // Remove validation classes when user starts typing
    $('input, select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@endsection
