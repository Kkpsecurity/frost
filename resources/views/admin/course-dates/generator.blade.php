@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-magic"></i>
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
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($errors->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $errors->first('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <!-- Generator Form -->
            <div class="col-lg-8">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs"></i>
                            Course Date Generator
                        </h3>
                    </div>
                    <form id="generator-form">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="course_id">Course <span class="text-danger">*</span></label>
                                        <select name="course_id" id="course_id" class="form-control select2" required>
                                            <option value="">Select a course...</option>
                                            @foreach($content['courses'] as $course)
                                                <option value="{{ $course->id }}">
                                                    {{ $course->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="instructor_id">Default Instructor</label>
                                        <select name="instructor_id" id="instructor_id" class="form-control select2">
                                            <option value="">No default instructor</option>
                                            @foreach($content['instructors'] as $instructor)
                                                <option value="{{ $instructor->id }}">
                                                    {{ $instructor->fname }} {{ $instructor->lname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="start_date" id="start_date"
                                               class="form-control" required min="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date">End Date <span class="text-danger">*</span></label>
                                        <input type="date" name="end_date" id="end_date"
                                               class="form-control" required min="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="time_start">Start Time <span class="text-danger">*</span></label>
                                        <input type="time" name="time_start" id="time_start"
                                               class="form-control" required value="09:00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="time_end">End Time <span class="text-danger">*</span></label>
                                        <input type="time" name="time_end" id="time_end"
                                               class="form-control" required value="17:00">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="schedule_pattern">Schedule Pattern <span class="text-danger">*</span></label>
                                <select name="schedule_pattern" id="schedule_pattern" class="form-control" required>
                                    <option value="">Select a schedule pattern...</option>
                                    <option value="daily">Daily (Monday - Friday)</option>
                                    <option value="weekly">Weekly (Same day each week)</option>
                                    <option value="bi-weekly">Bi-weekly (Every other week)</option>
                                    <option value="monthly">Monthly (Same date each month)</option>
                                    <option value="custom">Custom Days</option>
                                </select>
                            </div>

                            <div class="form-group" id="custom-days-group" style="display: none;">
                                <label>Custom Days</label>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check-inline">
                                            <input type="checkbox" name="custom_days[]" value="1" id="monday" class="form-check-input">
                                            <label class="form-check-label" for="monday">Monday</label>
                                        </div>
                                        <div class="form-check-inline">
                                            <input type="checkbox" name="custom_days[]" value="2" id="tuesday" class="form-check-input">
                                            <label class="form-check-label" for="tuesday">Tuesday</label>
                                        </div>
                                        <div class="form-check-inline">
                                            <input type="checkbox" name="custom_days[]" value="3" id="wednesday" class="form-check-input">
                                            <label class="form-check-label" for="wednesday">Wednesday</label>
                                        </div>
                                        <div class="form-check-inline">
                                            <input type="checkbox" name="custom_days[]" value="4" id="thursday" class="form-check-input">
                                            <label class="form-check-label" for="thursday">Thursday</label>
                                        </div>
                                        <div class="form-check-inline">
                                            <input type="checkbox" name="custom_days[]" value="5" id="friday" class="form-check-input">
                                            <label class="form-check-label" for="friday">Friday</label>
                                        </div>
                                        <div class="form-check-inline">
                                            <input type="checkbox" name="custom_days[]" value="6" id="saturday" class="form-check-input">
                                            <label class="form-check-label" for="saturday">Saturday</label>
                                        </div>
                                        <div class="form-check-inline">
                                            <input type="checkbox" name="custom_days[]" value="0" id="sunday" class="form-check-input">
                                            <label class="form-check-label" for="sunday">Sunday</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="overwrite_existing" id="overwrite_existing"
                                               class="form-check-input" value="1">
                                        <label class="form-check-label" for="overwrite_existing">
                                            Overwrite existing course dates for selected course
                                            <small class="text-muted d-block">
                                                Warning: This will delete all existing dates for the selected course and create new ones
                                            </small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-info" id="preview-btn">
                                    <i class="fas fa-eye"></i>
                                    Preview Dates
                                </button>
                                <button type="button" class="btn btn-success" id="generate-btn" disabled>
                                    <i class="fas fa-magic"></i>
                                    Generate Course Dates
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Preview Results -->
                <div class="card card-outline card-info" id="preview-card" style="display: none;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i>
                            Preview Results
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="preview-content">
                            <!-- Preview will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar with Info and Cleanup -->
            <div class="col-lg-4">
                <!-- Generator Status -->
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Generator Info
                        </h3>
                    </div>
                    <div class="card-body">
                        <h6><i class="fas fa-magic"></i> How It Works</h6>
                        <ol class="text-sm">
                            <li>Select a course and date range</li>
                            <li>Choose your schedule pattern</li>
                            <li>Preview the generated dates</li>
                            <li>Generate all dates at once</li>
                        </ol>

                        <hr>

                        <h6><i class="fas fa-calendar-check"></i> Schedule Patterns</h6>
                        <ul class="list-unstyled text-sm">
                            <li><strong>Daily:</strong> Monday through Friday</li>
                            <li><strong>Weekly:</strong> Same weekday each week</li>
                            <li><strong>Bi-weekly:</strong> Every other week</li>
                            <li><strong>Monthly:</strong> Same date each month</li>
                            <li><strong>Custom:</strong> Select specific days</li>
                        </ul>

                        <hr>

                        <h6><i class="fas fa-lightbulb"></i> Pro Tips</h6>
                        <ul class="list-unstyled text-sm">
                            <li><i class="fas fa-check text-success"></i> Always preview before generating</li>
                            <li><i class="fas fa-check text-success"></i> Use cleanup to remove old dates</li>
                            <li><i class="fas fa-check text-success"></i> Consider holidays when setting dates</li>
                        </ul>
                    </div>
                </div>

                <!-- Cleanup Tool -->
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-broom"></i>
                            Cleanup Tool
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-sm">Clean up existing course dates before generating new ones.</p>

                        <form id="cleanup-form">
                            @csrf
                            <div class="form-group">
                                <label for="cleanup_course_id">Course</label>
                                <select name="course_id" id="cleanup_course_id" class="form-control">
                                    <option value="">Select course to cleanup...</option>
                                    @foreach($content['courses'] as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="cleanup_type">Cleanup Type</label>
                                <select name="cleanup_type" id="cleanup_type" class="form-control">
                                    <option value="future">Future dates only</option>
                                    <option value="inactive">Inactive dates only</option>
                                    <option value="all">All dates (dangerous!)</option>
                                </select>
                            </div>

                            <button type="button" class="btn btn-warning btn-sm btn-block" id="cleanup-btn">
                                <i class="fas fa-broom"></i>
                                Clean Up Dates
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Current Stats -->
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i>
                            Quick Stats
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header text-primary">{{ $content['generator_status']['total_courses'] ?? 0 }}</h5>
                                    <span class="description-text">Active Courses</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header text-success">{{ $content['generator_status']['total_dates'] ?? 0 }}</h5>
                                    <span class="description-text">Course Dates</span>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center mt-3">
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header text-warning">{{ $content['generator_status']['upcoming_dates'] ?? 0 }}</h5>
                                    <span class="description-text">Upcoming</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <h5 class="description-header text-info">{{ $content['generator_status']['this_week'] ?? 0 }}</h5>
                                    <span class="description-text">This Week</span>
                                </div>
                            </div>
                        </div>
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

    // Show/hide custom days based on schedule pattern
    $('#schedule_pattern').change(function() {
        if ($(this).val() === 'custom') {
            $('#custom-days-group').show();
        } else {
            $('#custom-days-group').hide();
            $('input[name="custom_days[]"]').prop('checked', false);
        }
    });

    // Validate end date is after start date
    $('#start_date, #end_date').change(function() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        if (startDate && endDate) {
            if (new Date(endDate) <= new Date(startDate)) {
                $('#end_date').val('');
                toastr.error('End date must be after start date');
            }
        }
    });

    // Validate end time is after start time
    $('#time_start, #time_end').change(function() {
        const startTime = $('#time_start').val();
        const endTime = $('#time_end').val();

        if (startTime && endTime) {
            if (endTime <= startTime) {
                $('#time_end').val('');
                toastr.error('End time must be after start time');
            }
        }
    });

    // Preview functionality
    $('#preview-btn').click(function() {
        const formData = new FormData($('#generator-form')[0]);
        const btn = $(this);

        // Validation
        if (!validateForm()) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading Preview...');

        $.ajax({
            url: '{{ route("admin.course-dates.generator.preview") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    displayPreview(response.preview);
                    $('#preview-card').show();
                    $('#generate-btn').prop('disabled', false);

                    // Scroll to preview
                    $('html, body').animate({
                        scrollTop: $('#preview-card').offset().top - 100
                    }, 500);
                } else {
                    toastr.error(response.message || 'Preview failed');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Error generating preview';
                toastr.error(errorMsg);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-eye"></i> Preview Dates');
            }
        });
    });

    // Generate functionality
    $('#generate-btn').click(function() {
        const formData = new FormData($('#generator-form')[0]);
        const btn = $(this);

        if (!validateForm()) {
            return;
        }

        const overwrite = $('#overwrite_existing').is(':checked');
        const confirmMsg = overwrite ?
            'This will overwrite existing course dates. Are you sure?' :
            'This will generate new course dates. Continue?';

        if (!confirm(confirmMsg)) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');

        $.ajax({
            url: '{{ route("admin.course-dates.generator.generate") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);

                    // Reset form and hide preview
                    $('#generator-form')[0].reset();
                    $('#preview-card').hide();
                    $('.select2').trigger('change');

                    // Redirect to course dates list after short delay
                    setTimeout(() => {
                        window.location.href = '{{ route("admin.course-dates.index") }}';
                    }, 2000);
                } else {
                    toastr.error(response.message || 'Generation failed');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Error generating course dates';
                toastr.error(errorMsg);
            },
            complete: function() {
                btn.prop('disabled', true).html('<i class="fas fa-magic"></i> Generate Course Dates');
            }
        });
    });

    // Cleanup functionality
    $('#cleanup-btn').click(function() {
        const courseId = $('#cleanup_course_id').val();
        const cleanupType = $('#cleanup_type').val();

        if (!courseId) {
            toastr.error('Please select a course to cleanup');
            return;
        }

        const courseName = $('#cleanup_course_id option:selected').text();
        const typeText = cleanupType.replace('_', ' ');

        if (!confirm(`Are you sure you want to cleanup ${typeText} for "${courseName}"? This cannot be undone.`)) {
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cleaning...');

        $.ajax({
            url: '{{ route("admin.course-dates.generator.cleanup") }}',
            type: 'POST',
            data: {
                course_id: courseId,
                cleanup_type: cleanupType,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#cleanup-form')[0].reset();
                } else {
                    toastr.error(response.message || 'Cleanup failed');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Error during cleanup';
                toastr.error(errorMsg);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-broom"></i> Clean Up Dates');
            }
        });
    });

    function validateForm() {
        const required = ['course_id', 'start_date', 'end_date', 'time_start', 'time_end', 'schedule_pattern'];

        for (let field of required) {
            if (!$(`#${field}`).val()) {
                toastr.error(`Please fill in the ${field.replace('_', ' ')} field`);
                $(`#${field}`).focus();
                return false;
            }
        }

        // Check custom days if pattern is custom
        if ($('#schedule_pattern').val() === 'custom') {
            if ($('input[name="custom_days[]"]:checked').length === 0) {
                toastr.error('Please select at least one custom day');
                return false;
            }
        }

        return true;
    }

    function displayPreview(preview) {
        let html = '';

        if (preview.dates && preview.dates.length > 0) {
            html += `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>${preview.dates.length} course dates</strong> will be generated
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Course Unit</th>
                                <th>Day of Week</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            preview.dates.forEach(date => {
                html += `
                    <tr>
                        <td>${new Date(date.starts_at).toLocaleDateString()}</td>
                        <td>${new Date(date.starts_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} -
                            ${new Date(date.ends_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</td>
                        <td>${date.course_unit_title}</td>
                        <td>${new Date(date.starts_at).toLocaleDateString('en-US', {weekday: 'long'})}</td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';
        } else {
            html = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No course dates will be generated with the current settings
                </div>
            `;
        }

        $('#preview-content').html(html);
    }
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

.description-block {
    padding: 10px 0;
}

.description-header {
    margin: 0;
    font-weight: 600;
}

.description-text {
    font-size: 0.875rem;
    color: #6c757d;
}

.form-check-inline {
    margin-right: 1rem;
    margin-bottom: 0.5rem;
}

.alert {
    border-left: 4px solid;
}

.alert-info {
    border-left-color: #17a2b8;
}

.alert-warning {
    border-left-color: #ffc107;
}

#preview-card {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
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
