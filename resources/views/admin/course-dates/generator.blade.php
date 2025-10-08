@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>
                <i class="fas fa-plus-circle"></i>
                {{ $content['title'] }}
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-info-circle"></i>
                Create a test course for today - you'll return to the Instructor Dashboard when done
            </p>
        </div>
        <a href="/dashboards/instructor" class="btn btn-primary">
            <i class="fas fa-chalkboard-teacher"></i>
            Back to Instructor Dashboard
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
            <!-- Simple Test Course Generator -->
            <div class="col-lg-8">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-plus-circle"></i>
                            Create Test Course for Today
                        </h3>
                    </div>
                    <form id="generator-form">
                        @csrf
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Quick Test Course Creation</strong><br>
                                This tool creates a simple test course for today using the course's template times.
                                Perfect for testing when no courses are scheduled.
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="course_id">Select Course <span class="text-danger">*</span></label>
                                        <select name="course_id" id="course_id" class="form-control select2" required>
                                            <option value="">Choose a course to create test session...</option>
                                            @foreach($content['courses'] as $course)
                                                <option value="{{ $course->id }}">
                                                    {{ $course->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">The course's template times will be used automatically</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="instructor_id">Assign Instructor</label>
                                        <select name="instructor_id" id="instructor_id" class="form-control select2">
                                            <option value="">No instructor assigned</option>
                                            @foreach($content['instructors'] as $instructor)
                                                <option value="{{ $instructor->id }}">
                                                    {{ $instructor->fname }} {{ $instructor->lname }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Optional: Assign an instructor to this test course</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="bg-light p-3 rounded">
                                        <h6><i class="fas fa-calendar-day"></i> Course Date Details</h6>
                                        <p class="mb-1"><strong>Date:</strong> {{ date('l, F j, Y') }} (Today)</p>
                                        <p class="mb-1"><strong>Times:</strong> Will use the selected course's template start/end times</p>
                                        <p class="mb-0"><strong>Purpose:</strong> Testing and demonstration</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-success btn-lg" id="generate-btn">
                                <i class="fas fa-plus-circle"></i>
                                Create Test Course for Today
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Generation Results -->
                <div class="card card-outline card-success" id="results-card" style="display: none;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-check-circle"></i>
                            Test Course Created
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="results-content">
                            <!-- Results will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar with Info -->
            <div class="col-lg-4">
                <!-- Test Course Info -->
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Test Course Generator
                        </h3>
                    </div>
                    <div class="card-body">
                        <h6><i class="fas fa-rocket"></i> Quick Setup</h6>
                        <ol class="text-sm">
                            <li>Select a course from the dropdown</li>
                            <li>Optionally assign an instructor</li>
                            <li>Click "Create Test Course for Today"</li>
                            <li>Course is ready for testing!</li>
                        </ol>

                        <hr>

                        <h6><i class="fas fa-clock"></i> Template Times</h6>
                        <p class="text-sm text-muted">
                            Each course has template start and end times configured.
                            The test course will automatically use these times for today's session.
                        </p>

                        <hr>

                        <h6><i class="fas fa-arrow-left"></i> After Creation</h6>
                        <p class="text-sm text-muted">
                            Once your test course is created, you'll be automatically redirected back to the
                            <strong>Instructor Dashboard</strong> where you can start your class.
                        </p>

                        <div class="text-center mt-3">
                            <a href="/dashboards/instructor" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-chalkboard-teacher"></i> Back to Dashboard
                            </a>
                        </div>
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
    // Initialize Select2 for dropdowns
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Select an option...'
    });

    // Simple test course generation
    $('#generate-btn').click(function() {
        const courseId = $('#course_id').val();
        const instructorId = $('#instructor_id').val();
        const btn = $(this);

        // Simple validation
        if (!courseId) {
            toastr.error('Please select a course');
            return;
        }

        if (!confirm('Create a test course for today using this course\'s template times?')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating Test Course...');

        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('course_id', courseId);
        if (instructorId) {
            formData.append('instructor_id', instructorId);
        }

        $.ajax({
            url: '{{ route("admin.course-dates.generator.generate") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);

                    // Show results with countdown
                    $('#results-content').html(`
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> Test Course Created Successfully!</h5>
                            <p><strong>Course:</strong> ${response.course_title}</p>
                            <p><strong>Date:</strong> ${response.date}</p>
                            <p><strong>Time:</strong> ${response.start_time} - ${response.end_time}</p>
                            ${response.instructor ? `<p><strong>Instructor:</strong> ${response.instructor}</p>` : ''}
                        </div>
                        <div class="text-center">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i>
                                Redirecting to Instructor Dashboard in <span id="countdown">3</span> seconds...
                            </div>
                            <a href="/dashboards/instructor" class="btn btn-primary mr-2">
                                <i class="fas fa-chalkboard-teacher"></i> Go to Instructor Dashboard Now
                            </a>
                            <a href="{{ route('admin.course-dates.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list"></i> View All Course Dates
                            </a>
                        </div>
                    `);
                    $('#results-card').show();

                    // Reset form
                    $('#generator-form')[0].reset();
                    $('.select2').trigger('change');

                    // Auto-redirect countdown
                    let countdown = 3;
                    const countdownInterval = setInterval(() => {
                        countdown--;
                        $('#countdown').text(countdown);
                        if (countdown <= 0) {
                            clearInterval(countdownInterval);
                            window.location.href = '/dashboards/instructor';
                        }
                    }, 1000);
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

    // Simple validation for test course creation
    function validateCourseSelection() {
        if (!$('#course_id').val()) {
            toastr.error('Please select a course to create a test session');
            $('#course_id').focus();
            return false;
        }
        return true;
    }

    // Optional: Show course details when selected
    $('#course_id').change(function() {
        const courseId = $(this).val();
        if (courseId) {
            // Could add AJAX call here to show course template times
            toastr.info('Course selected - ready to create test session');
        }
    });

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
