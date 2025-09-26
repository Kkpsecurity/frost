@extends('adminlte::page')

@section('title', 'Edit Course: ' . $course->title)

@section('content_header')
    <x-admin.partials.titlebar
        title="Edit Course"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Courses', 'url' => route('admin.courses.dashboard')],
            ['title' => $course->title, 'url' => route('admin.courses.manage.view', $course)],
            ['title' => 'Edit']
        ]"
    />
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Course Information</h3>
                </div>

                <form method="POST" action="{{ route('admin.courses.manage.update', $course) }}">
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
                        <h5 class="text-primary mb-3"><i class="fas fa-info-circle"></i> Basic Information</h5>

                        <div class="form-group">
                            <label for="title">Course Title <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control @error('title') is-invalid @enderror"
                                id="title"
                                name="title"
                                value="{{ old('title', $course->title) }}"
                                required
                                maxlength="64"
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Short course title (max 64 characters)
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="title_long">Long Course Title</label>
                            <input
                                type="text"
                                class="form-control @error('title_long') is-invalid @enderror"
                                id="title_long"
                                name="title_long"
                                value="{{ old('title_long', $course->title_long) }}"
                                maxlength="500"
                            >
                            @error('title_long')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Course Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="999.99"
                                            class="form-control @error('price') is-invalid @enderror"
                                            id="price"
                                            name="price"
                                            value="{{ old('price', $course->price) }}"
                                            required
                                        >
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_minutes">Total Duration (minutes) <span class="text-danger">*</span></label>
                                    <input
                                        type="number"
                                        min="1"
                                        class="form-control @error('total_minutes') is-invalid @enderror"
                                        id="total_minutes"
                                        name="total_minutes"
                                        value="{{ old('total_minutes', $course->total_minutes) }}"
                                        required
                                    >
                                    @error('total_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="policy_expire_days">Policy Expiration (days) <span class="text-danger">*</span></label>
                            <input
                                type="number"
                                min="1"
                                max="1000"
                                class="form-control @error('policy_expire_days') is-invalid @enderror"
                                id="policy_expire_days"
                                name="policy_expire_days"
                                value="{{ old('policy_expire_days', $course->policy_expire_days) }}"
                                required
                            >
                            @error('policy_expire_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- Course Configuration --}}
                        <h5 class="text-primary mb-3"><i class="fas fa-cogs"></i> Course Configuration</h5>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="exam_id">Associated Exam <span class="text-danger">*</span></label>
                                    <select
                                        class="form-control @error('exam_id') is-invalid @enderror"
                                        id="exam_id"
                                        name="exam_id"
                                        required
                                    >
                                        <option value="">Select Exam</option>
                                        @foreach($exams as $exam)
                                            <option value="{{ $exam->id }}"
                                                {{ old('exam_id', $course->exam_id) == $exam->id ? 'selected' : '' }}>
                                                {{ $exam->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('exam_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="eq_spec_id">Question Specification <span class="text-danger">*</span></label>
                                    <select
                                        class="form-control @error('eq_spec_id') is-invalid @enderror"
                                        id="eq_spec_id"
                                        name="eq_spec_id"
                                        required
                                    >
                                        <option value="">Select Spec</option>
                                        @foreach($examQuestionSpecs as $spec)
                                            <option value="{{ $spec->id }}"
                                                {{ old('eq_spec_id', $course->eq_spec_id) == $spec->id ? 'selected' : '' }}>
                                                {{ $spec->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('eq_spec_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="zoom_creds_id">Zoom Credentials <span class="text-danger">*</span></label>
                                    <select
                                        class="form-control @error('zoom_creds_id') is-invalid @enderror"
                                        id="zoom_creds_id"
                                        name="zoom_creds_id"
                                        required
                                    >
                                        <option value="">Select Zoom Account</option>
                                        @foreach($zoomCreds as $cred)
                                            <option value="{{ $cred->id }}"
                                                {{ old('zoom_creds_id', $course->zoom_creds_id) == $cred->id ? 'selected' : '' }}>
                                                {{ $cred->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('zoom_creds_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="needs_range"
                                            name="needs_range"
                                            value="1"
                                            {{ old('needs_range', $course->needs_range) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="needs_range">
                                            Requires Range Date
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="is_active"
                                            name="is_active"
                                            value="1"
                                            {{ old('is_active', $course->is_active) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="is_active">
                                            Course Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="dates_template">Dates Template (JSON)</label>
                            <textarea
                                class="form-control @error('dates_template') is-invalid @enderror"
                                id="dates_template"
                                name="dates_template"
                                rows="8"
                            >{{ old('dates_template', $course->dates_template ? json_encode($course->dates_template, JSON_PRETTY_PRINT) : '') }}</textarea>
                            @error('dates_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Optional JSON configuration for course date scheduling
                            </small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update Course
                        </button>
                        <a href="{{ route('admin.courses.manage.view', $course) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Current Course Info --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Course Info</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Course ID:</dt>
                        <dd class="col-sm-7">{{ $course->id }}</dd>

                        <dt class="col-sm-5">Type:</dt>
                        <dd class="col-sm-7">
                            <span class="badge badge-{{ $course->getCourseTypeBadgeColor() }}">
                                {{ $course->getCourseType() }}
                            </span>
                        </dd>

                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            @if($course->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Archived</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Enrollments:</dt>
                        <dd class="col-sm-7">{{ $course->CourseAuths()->count() }}</dd>

                        <dt class="col-sm-5">Created:</dt>
                        <dd class="col-sm-7">
                            @if($course->created_at)
                                {{ $course->created_at->format('M d, Y') }}
                            @else
                                N/A
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            {{-- Duration Calculator (same as create form) --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calculator"></i> Duration Calculator
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Calculate duration in minutes:</p>

                    <div class="form-group">
                        <label for="calc-days">Days:</label>
                        <input type="number" id="calc-days" class="form-control" value="{{ $course->getDurationDays() }}" min="1">
                    </div>

                    <div class="form-group">
                        <label for="calc-hours">Hours per day:</label>
                        <input type="number" id="calc-hours" class="form-control" value="8" min="1">
                    </div>

                    <div class="form-group">
                        <label>Total Minutes:</label>
                        <input type="text" id="calc-result" class="form-control" readonly>
                    </div>

                    <button type="button" class="btn btn-primary btn-sm" id="calc-apply">
                        Apply to Course
                    </button>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Stats</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Students</span>
                                    <span class="info-box-number">{{ $course->CourseAuths()->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-graduation-cap"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed</span>
                                    <span class="info-box-number">{{ $course->CourseAuths()->whereNotNull('completed_at')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
$(function () {
    // Duration calculator (same as create form)
    function calculateMinutes() {
        const days = parseInt($('#calc-days').val()) || 0;
        const hours = parseInt($('#calc-hours').val()) || 0;
        const minutes = days * hours * 60;
        $('#calc-result').val(minutes);
    }

    $('#calc-days, #calc-hours').on('input', calculateMinutes);
    calculateMinutes(); // Initial calculation

    // Apply calculated duration to form
    $('#calc-apply').click(function() {
        $('#total_minutes').val($('#calc-result').val());
        $(this).removeClass('btn-primary').addClass('btn-success').text('Applied!');
        setTimeout(() => {
            $(this).removeClass('btn-success').addClass('btn-primary').text('Apply to Course');
        }, 2000);
    });

    // Form validation
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

        // Validate JSON if provided
        const jsonTemplate = $('#dates_template').val();
        if (jsonTemplate.trim()) {
            try {
                JSON.parse(jsonTemplate);
                $('#dates_template').removeClass('is-invalid');
            } catch (e) {
                $('#dates_template').addClass('is-invalid');
                isValid = false;
            }
        }

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid:first').offset().top - 100
            }, 500);
        }
    });

    // Remove validation classes when user starts typing
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@endsection
