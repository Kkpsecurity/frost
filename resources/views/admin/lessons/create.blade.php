@extends('adminlte::page')

@section('title', 'Create New Lesson - Frost Admin')

@section('content_header')
    <x-admin.partials.titlebar
        title="Create New Lesson"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Lesson Management', 'url' => route('admin.lessons.index')],
            ['title' => 'Create New Lesson']
        ]"
    />
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.lessons.store') }}" id="lesson-form">
        @csrf

        <div class="row">
            {{-- Main Lesson Information --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-book"></i> Lesson Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title" class="required">Lesson Title</label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}"
                                   required
                                   maxlength="64"
                                   placeholder="Enter lesson title...">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Maximum 64 characters. This will be displayed to students.
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="credit_minutes" class="required">Credit Minutes</label>
                                    <div class="input-group">
                                        <input type="number"
                                               id="credit_minutes"
                                               name="credit_minutes"
                                               class="form-control @error('credit_minutes') is-invalid @enderror"
                                               value="{{ old('credit_minutes') }}"
                                               required
                                               min="1"
                                               max="9999"
                                               placeholder="60">
                                        <div class="input-group-append">
                                            <span class="input-group-text">minutes</span>
                                        </div>
                                        @error('credit_minutes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        Duration for credit calculation. Will be converted to <span id="credit-hours">0.0</span> hours.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="video_seconds">Video Length (seconds)</label>
                                    <div class="input-group">
                                        <input type="number"
                                               id="video_seconds"
                                               name="video_seconds"
                                               class="form-control @error('video_seconds') is-invalid @enderror"
                                               value="{{ old('video_seconds', 0) }}"
                                               min="0"
                                               max="999999"
                                               placeholder="3600">
                                        <div class="input-group-append">
                                            <span class="input-group-text">seconds</span>
                                        </div>
                                        @error('video_seconds')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        Video duration in seconds. Shows as <span id="video-time">0:00:00</span>.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Course Unit Assignments --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-link"></i> Course Unit Assignments
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-success" id="add-assignment">
                                <i class="fas fa-plus"></i> Add Assignment
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="assignments-container">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Optional:</strong> You can assign this lesson to course units now, or do it later from the lesson management page.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar with Actions and Help --}}
            <div class="col-md-4">
                {{-- Action Buttons --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cogs"></i> Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="btn-group w-100 mb-3" role="group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Create Lesson
                            </button>
                            <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="create-another">
                            <label class="form-check-label" for="create-another">
                                Create another lesson after saving
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Help Information --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-question-circle"></i> Help
                        </h3>
                    </div>
                    <div class="card-body">
                        <h6>Lesson Information</h6>
                        <ul class="list-unstyled small">
                            <li><strong>Title:</strong> Should be clear and descriptive</li>
                            <li><strong>Credit Minutes:</strong> Used for progress tracking</li>
                            <li><strong>Video Length:</strong> Actual video duration (optional)</li>
                        </ul>

                        <h6 class="mt-3">Course Unit Assignments</h6>
                        <ul class="list-unstyled small">
                            <li>• Lessons can be assigned to multiple course units</li>
                            <li>• Each assignment can have different progress minutes</li>
                            <li>• Ordering determines sequence within the unit</li>
                        </ul>

                        <h6 class="mt-3">Best Practices</h6>
                        <ul class="list-unstyled small">
                            <li>• Use consistent naming conventions</li>
                            <li>• Set appropriate credit minutes</li>
                            <li>• Assign to relevant course units</li>
                        </ul>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i> Available Courses
                        </h3>
                    </div>
                    <div class="card-body p-2">
                        @if($content['courses']->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($content['courses']->take(5) as $course)
                                    <div class="list-group-item d-flex justify-content-between align-items-center p-2">
                                        <small>{{ Str::limit($course->title, 25) }}</small>
                                        <span class="badge badge-primary badge-pill">
                                            {{ $course->CourseUnits->count() }} units
                                        </span>
                                    </div>
                                @endforeach
                                @if($content['courses']->count() > 5)
                                    <div class="list-group-item text-center p-2">
                                        <small class="text-muted">
                                            ...and {{ $content['courses']->count() - 5 }} more courses
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-exclamation-circle text-warning fa-2x mb-2"></i>
                                <p class="small text-muted mb-0">No active courses found</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Assignment Template (Hidden) --}}
    <template id="assignment-template">
        <div class="assignment-item border rounded p-3 mb-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Course</label>
                        <select class="form-control course-select" required>
                            <option value="">Select a course...</option>
                            @foreach($content['courses'] as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Course Unit</label>
                        <select class="form-control unit-select" name="course_units[]" required disabled>
                            <option value="">First select a course...</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Progress Minutes</label>
                        <input type="number"
                               class="form-control progress-minutes"
                               name="progress_minutes[]"
                               min="0"
                               max="9999"
                               placeholder="60">
                        <small class="form-text text-muted">
                            Leave empty to use credit minutes
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Instructor Seconds</label>
                        <input type="number"
                               class="form-control"
                               name="instr_seconds[]"
                               min="0"
                               max="999999"
                               placeholder="0">
                        <small class="form-text text-muted">
                            Instructor preparation time
                        </small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="d-block">
                            <button type="button" class="btn btn-danger btn-sm remove-assignment" title="Remove Assignment">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            let assignmentCounter = 0;

            // Update credit hours display
            function updateCreditHours() {
                const minutes = parseInt($('#credit_minutes').val()) || 0;
                const hours = (minutes / 60).toFixed(1);
                $('#credit-hours').text(hours);
            }

            // Update video time display
            function updateVideoTime() {
                const seconds = parseInt($('#video_seconds').val()) || 0;
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;
                const timeStr = `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                $('#video-time').text(timeStr);
            }

            // Initial calculations
            updateCreditHours();
            updateVideoTime();

            // Update calculations on input
            $('#credit_minutes').on('input', updateCreditHours);
            $('#video_seconds').on('input', updateVideoTime);

            // Add assignment functionality
            $('#add-assignment').on('click', function() {
                const template = document.getElementById('assignment-template');
                const clone = template.content.cloneNode(true);

                // Update the clone with unique identifiers if needed
                assignmentCounter++;

                $('#assignments-container').append(clone);

                // Hide the info alert if this is the first assignment
                if (assignmentCounter === 1) {
                    $('#assignments-container .alert').hide();
                }
            });

            // Remove assignment functionality (delegated event)
            $(document).on('click', '.remove-assignment', function() {
                $(this).closest('.assignment-item').remove();
                assignmentCounter--;

                // Show the info alert if no assignments remain
                if (assignmentCounter === 0) {
                    $('#assignments-container .alert').show();
                }
            });

            // Course selection for units (delegated event)
            $(document).on('change', '.course-select', function() {
                const courseId = $(this).val();
                const unitSelect = $(this).closest('.assignment-item').find('.unit-select');

                if (courseId) {
                    // Load course units via AJAX
                    $.get(`/admin/lessons/api/course/${courseId}/units`)
                        .done(function(units) {
                            unitSelect.empty().append('<option value="">Select a course unit...</option>');
                            units.forEach(function(unit) {
                                unitSelect.append(`<option value="${unit.id}">${unit.title}</option>`);
                            });
                            unitSelect.prop('disabled', false);
                        })
                        .fail(function() {
                            alert('Failed to load course units. Please try again.');
                            unitSelect.empty().append('<option value="">Failed to load units</option>').prop('disabled', true);
                        });
                } else {
                    unitSelect.empty().append('<option value="">First select a course...</option>').prop('disabled', true);
                }
            });

            // Auto-populate progress minutes with credit minutes (delegated event)
            $(document).on('focus', '.progress-minutes', function() {
                if (!$(this).val()) {
                    const creditMinutes = $('#credit_minutes').val();
                    if (creditMinutes) {
                        $(this).val(creditMinutes);
                    }
                }
            });

            // Form submission handling
            $('#lesson-form').on('submit', function(e) {
                const createAnother = $('#create-another').is(':checked');
                if (createAnother) {
                    // Add a hidden input to redirect back to create form
                    $(this).append('<input type="hidden" name="create_another" value="1">');
                }
            });

            // Form validation
            $('form').on('submit', function(e) {
                let valid = true;

                // Validate title
                if (!$('#title').val().trim()) {
                    alert('Please enter a lesson title.');
                    $('#title').focus();
                    return false;
                }

                // Validate credit minutes
                const creditMinutes = parseInt($('#credit_minutes').val());
                if (!creditMinutes || creditMinutes < 1) {
                    alert('Please enter valid credit minutes (minimum 1).');
                    $('#credit_minutes').focus();
                    return false;
                }

                // Validate assignments (if any)
                $('.assignment-item').each(function() {
                    const courseUnit = $(this).find('.unit-select').val();
                    if (!courseUnit) {
                        alert('Please select course units for all assignments or remove incomplete assignments.');
                        $(this).find('.unit-select').focus();
                        valid = false;
                        return false;
                    }
                });

                return valid;
            });
        });
    </script>
@endsection

@section('css')
    <style>
        .required::after {
            content: " *";
            color: #e74c3c;
        }

        .assignment-item {
            background-color: #f8f9fa;
            position: relative;
        }

        .assignment-item:hover {
            background-color: #e9ecef;
        }

        .card-tools .btn {
            padding: 0.375rem 0.75rem;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        .input-group-text {
            background-color: #e9ecef;
            border-color: #ced4da;
        }

        @media (max-width: 768px) {
            .btn-group {
                width: 100%;
            }

            .btn-group .btn {
                flex: 1;
            }

            .assignment-item .row .col-md-4,
            .assignment-item .row .col-md-6 {
                margin-bottom: 1rem;
            }
        }
    </style>
@endsection
