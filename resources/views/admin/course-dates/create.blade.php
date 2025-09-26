@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-plus"></i>
            {{ $content['title'] }}
        </h1>
        <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Course Dates
        </a>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if($errors->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $errors->first('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <form method="POST" action="{{ route('admin.course-dates.store') }}" id="create-form">
                    @csrf

                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-plus"></i>
                                Course Date Details
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="course_id">Course <span class="text-danger">*</span></label>
                                        <select name="course_id" id="course_id" class="form-control select2" required>
                                            <option value="">Select a course...</option>
                                            @foreach($content['courses'] as $course)
                                                <option value="{{ $course->id }}"
                                                    {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                    {{ $course->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('course_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="course_unit_id">Course Unit <span class="text-danger">*</span></label>
                                        <select name="course_unit_id" id="course_unit_id" class="form-control" required disabled>
                                            <option value="">Select a course first...</option>
                                        </select>
                                        @error('course_unit_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="starts_at">Start Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="starts_at" id="starts_at"
                                               class="form-control @error('starts_at') is-invalid @enderror"
                                               value="{{ old('starts_at') }}" required>
                                        @error('starts_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ends_at">End Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="ends_at" id="ends_at"
                                               class="form-control @error('ends_at') is-invalid @enderror"
                                               value="{{ old('ends_at') }}" required>
                                        @error('ends_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="instructor_id">Instructor</label>
                                        <select name="instructor_id" id="instructor_id" class="form-control select2">
                                            <option value="">No instructor assigned</option>
                                            @foreach($content['instructors'] as $instructor)
                                                <option value="{{ $instructor->id }}"
                                                    {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                                    {{ $instructor->fname }} {{ $instructor->lname }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('instructor_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-check mt-4">
                                            <input type="checkbox" name="is_active" id="is_active"
                                                   class="form-check-input" value="1"
                                                   {{ old('is_active', '1') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Course Date is Active
                                            </label>
                                        </div>
                                        @error('is_active')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3"
                                          placeholder="Optional notes about this course date...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Create Course Date
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <!-- Quick Help Card -->
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Quick Help
                        </h3>
                    </div>
                    <div class="card-body">
                        <h6><i class="fas fa-calendar"></i> Creating Course Dates</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Select the course first</li>
                            <li><i class="fas fa-check text-success"></i> Choose the specific unit/day</li>
                            <li><i class="fas fa-check text-success"></i> Set start and end times</li>
                            <li><i class="fas fa-check text-success"></i> Assign an instructor (optional)</li>
                        </ul>

                        <hr>

                        <h6><i class="fas fa-magic"></i> Need Multiple Dates?</h6>
                        <p class="text-sm">
                            Use the <strong>Auto Generator</strong> to create multiple course dates
                            with recurring schedules.
                        </p>
                        <a href="{{ route('admin.course-dates.generator') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-magic"></i>
                            Open Generator
                        </a>

                        <hr>

                        <h6><i class="fas fa-lightbulb"></i> Pro Tips</h6>
                        <ul class="list-unstyled text-sm">
                            <li><i class="fas fa-arrow-right text-primary"></i> End time must be after start time</li>
                            <li><i class="fas fa-arrow-right text-primary"></i> Inactive dates won't show in student calendar</li>
                            <li><i class="fas fa-arrow-right text-primary"></i> Notes are visible to instructors</li>
                        </ul>
                    </div>
                </div>

                <!-- Duration Preview Card -->
                <div class="card card-outline card-secondary" id="duration-preview" style="display: none;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock"></i>
                            Duration Preview
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <h4 class="text-primary" id="duration-display">-</h4>
                        <p class="text-muted mb-0">Total Duration</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Load course units when course is selected
    $('#course_id').change(function() {
        const courseId = $(this).val();
        const unitSelect = $('#course_unit_id');

        if (courseId) {
            unitSelect.prop('disabled', true).html('<option value="">Loading...</option>');

            $.get(`/admin/course-dates/api/course/${courseId}/units`)
                .done(function(units) {
                    let options = '<option value="">Select a unit...</option>';
                    units.forEach(unit => {
                        const dayText = unit.day ? ` (Day ${unit.day})` : '';
                        const selected = '{{ old("course_unit_id") }}' == unit.id ? 'selected' : '';
                        options += `<option value="${unit.id}" ${selected}>${unit.title}${dayText}</option>`;
                    });
                    unitSelect.html(options).prop('disabled', false);
                })
                .fail(function() {
                    unitSelect.html('<option value="">Error loading units</option>');
                    toastr.error('Failed to load course units');
                });
        } else {
            unitSelect.prop('disabled', true).html('<option value="">Select a course first...</option>');
        }
    });

    // Trigger change event if course is pre-selected (for validation errors)
    if ($('#course_id').val()) {
        $('#course_id').trigger('change');
    }

    // Duration calculation
    function updateDuration() {
        const startTime = $('#starts_at').val();
        const endTime = $('#ends_at').val();

        if (startTime && endTime) {
            const start = new Date(startTime);
            const end = new Date(endTime);

            if (end > start) {
                const diffMs = end - start;
                const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

                let durationText = '';
                if (diffHours > 0) {
                    durationText += `${diffHours} hour${diffHours !== 1 ? 's' : ''}`;
                }
                if (diffMinutes > 0) {
                    if (durationText) durationText += ' ';
                    durationText += `${diffMinutes} minute${diffMinutes !== 1 ? 's' : ''}`;
                }

                $('#duration-display').text(durationText || '0 minutes');
                $('#duration-preview').show();
            } else {
                $('#duration-preview').hide();
            }
        } else {
            $('#duration-preview').hide();
        }
    }

    $('#starts_at, #ends_at').change(updateDuration);

    // Set default end time when start time is set
    $('#starts_at').change(function() {
        const startTime = $(this).val();
        const endTimeInput = $('#ends_at');

        if (startTime && !endTimeInput.val()) {
            // Set default end time to 2 hours after start time
            const start = new Date(startTime);
            start.setHours(start.getHours() + 2);

            // Format back to datetime-local format
            const year = start.getFullYear();
            const month = String(start.getMonth() + 1).padStart(2, '0');
            const day = String(start.getDate()).padStart(2, '0');
            const hours = String(start.getHours()).padStart(2, '0');
            const minutes = String(start.getMinutes()).padStart(2, '0');

            const endTimeString = `${year}-${month}-${day}T${hours}:${minutes}`;
            endTimeInput.val(endTimeString);

            updateDuration();
        }
    });

    // Form validation
    $('#create-form').submit(function(e) {
        const startTime = $('#starts_at').val();
        const endTime = $('#ends_at').val();

        if (startTime && endTime) {
            const start = new Date(startTime);
            const end = new Date(endTime);

            if (end <= start) {
                e.preventDefault();
                toastr.error('End time must be after start time');
                return false;
            }

            // Check if start time is in the past
            const now = new Date();
            if (start < now) {
                if (!confirm('The start time is in the past. Are you sure you want to create this course date?')) {
                    e.preventDefault();
                    return false;
                }
            }
        }
    });

    // Auto-capitalize notes
    $('#notes').on('blur', function() {
        const text = $(this).val();
        if (text) {
            $(this).val(text.charAt(0).toUpperCase() + text.slice(1));
        }
    });
});
</script>
@stop

@section('css')
<style>
.form-group label {
    font-weight: 600;
    color: #495057;
}

.text-danger {
    font-weight: 600;
}

.card-title i {
    margin-right: 8px;
}

.invalid-feedback {
    font-size: 0.875rem;
}

.list-unstyled li {
    margin-bottom: 5px;
}

#duration-preview {
    transition: all 0.3s ease;
}

.select2-container .select2-selection--single {
    height: calc(2.25rem + 2px);
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
}

.select2-container .select2-selection--single .select2-selection__rendered {
    padding-left: 0;
    padding-right: 20px;
    line-height: 1.5;
}

.select2-container .select2-selection--single .select2-selection__arrow {
    height: calc(2.25rem + 2px);
    right: 3px;
}
</style>
@stop
