@extends('adminlte::page')

@section('title', 'Create New Course')

@section('content_header')
    <x-admin.partials.titlebar
        title="Create New Course"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Courses', 'url' => route('admin.courses.dashboard')],
            ['title' => 'Create']
        ]"
    />
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Course Information</h3>
                </div>

                <form method="POST" action="{{ route('admin.courses.manage.store') }}">
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

                        {{-- Basic Information --}}
                        <h5 class="text-primary mb-3"><i class="fas fa-info-circle"></i> Basic Information</h5>

                        <div class="form-group">
                            <label for="title">Course Title <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                class="form-control @error('title') is-invalid @enderror"
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                required
                                maxlength="64"
                                placeholder="e.g., D Course - Basic Security Training"
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Short course title (max 64 characters). Include "D Course" or "G Course" for type detection.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="title_long">Long Course Title</label>
                            <input
                                type="text"
                                class="form-control @error('title_long') is-invalid @enderror"
                                id="title_long"
                                name="title_long"
                                value="{{ old('title_long') }}"
                                maxlength="500"
                                placeholder="Full descriptive title for marketing and certificates"
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
                                            value="{{ old('price') }}"
                                            required
                                            placeholder="0.00"
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
                                        value="{{ old('total_minutes') }}"
                                        required
                                        placeholder="e.g., 2400 (5 days × 8 hours × 60 min)"
                                    >
                                    @error('total_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Total course duration in minutes
                                    </small>
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
                                value="{{ old('policy_expire_days', 180) }}"
                                required
                            >
                            @error('policy_expire_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Number of days after which the course authorization expires
                            </small>
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
                                            <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>
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
                                            <option value="{{ $spec->id }}" {{ old('eq_spec_id') == $spec->id ? 'selected' : '' }}>
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
                                            <option value="{{ $cred->id }}" {{ old('zoom_creds_id', 2) == $cred->id ? 'selected' : '' }}>
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

                        <div class="form-group">
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="needs_range"
                                    name="needs_range"
                                    value="1"
                                    {{ old('needs_range') ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="needs_range">
                                    Requires Range Date
                                </label>
                                <small class="form-text text-muted">
                                    Check if this course requires range scheduling
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="dates_template">Dates Template (JSON)</label>
                            <textarea
                                class="form-control @error('dates_template') is-invalid @enderror"
                                id="dates_template"
                                name="dates_template"
                                rows="8"
                                placeholder='{"days": 5, "hours_per_day": 8, "frequency": "weekly"}'
                            >{{ old('dates_template') }}</textarea>
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
                            <i class="fas fa-save"></i> Create Course
                        </button>
                        <a href="{{ route('admin.courses.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Course Type Helper --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info"></i> Course Type Detection
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb"></i> Course Type Auto-Detection</h6>
                        <p class="mb-2">The system automatically detects course type based on the title:</p>
                        <ul class="mb-0">
                            <li><strong>D Course:</strong> Include "D Course" or "D-Course" in title</li>
                            <li><strong>G Course:</strong> Include "G Course" or "G-Course" in title</li>
                        </ul>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-calendar-week"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">D Course</span>
                                    <span class="info-box-number">5 Days</span>
                                    <span class="progress-description">Weekly intensive</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">G Course</span>
                                    <span class="info-box-number">3 Days</span>
                                    <span class="progress-description">Biweekly compact</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Duration Calculator --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calculator"></i> Duration Calculator
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Common duration calculations:</p>

                    <div class="form-group">
                        <label for="calc-days">Days:</label>
                        <input type="number" id="calc-days" class="form-control" value="5" min="1">
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

            {{-- JSON Template Examples --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-code"></i> Template Examples
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Common date templates:</p>

                    <button type="button" class="btn btn-outline-success btn-sm mb-2" data-template="d-course">
                        D Course Template
                    </button>
                    <br>
                    <button type="button" class="btn btn-outline-info btn-sm mb-2" data-template="g-course">
                        G Course Template
                    </button>
                    <br>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-template">
                        Clear Template
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
$(function () {
    // Duration calculator
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

    // JSON template buttons
    $('[data-template]').click(function() {
        const template = $(this).data('template');
        let jsonTemplate = '';

        if (template === 'd-course') {
            jsonTemplate = JSON.stringify({
                "type": "D",
                "days": 5,
                "hours_per_day": 8,
                "frequency": "weekly",
                "max_participants": 20
            }, null, 2);
        } else if (template === 'g-course') {
            jsonTemplate = JSON.stringify({
                "type": "G",
                "days": 3,
                "hours_per_day": 8,
                "frequency": "biweekly",
                "max_participants": 15
            }, null, 2);
        }

        $('#dates_template').val(jsonTemplate);
    });

    $('#clear-template').click(function() {
        $('#dates_template').val('');
    });

    // Course type detection from title
    $('#title').on('input', function() {
        const title = $(this).val().toLowerCase();

        if (title.includes('d course') || title.includes('d-course')) {
            $('#calc-days').val(5);
            $('#calc-hours').val(8);
            calculateMinutes();
        } else if (title.includes('g course') || title.includes('g-course')) {
            $('#calc-days').val(3);
            $('#calc-hours').val(8);
            calculateMinutes();
        }
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
        if (jsonTemplate) {
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
