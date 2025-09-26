@extends('adminlte::page')

@section('title', $content['title'] . ' - Frost Admin')

@section('content_header')
    <x-admin.partials.titlebar
        :title="$content['title']"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Lesson Management', 'url' => route('admin.lessons.index')],
            ['title' => $content['lesson']->title, 'url' => route('admin.lessons.show', $content['lesson'])],
            ['title' => 'Edit']
        ]"
    />
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.lessons.update', $content['lesson']) }}" id="lesson-form">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Main Lesson Information --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-book"></i> Edit Lesson Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title" class="required">Lesson Title</label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $content['lesson']->title) }}"
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
                                               value="{{ old('credit_minutes', $content['lesson']->credit_minutes) }}"
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
                                        Duration for credit calculation. Will be converted to <span id="credit-hours">{{ round($content['lesson']->credit_minutes / 60, 1) }}</span> hours.
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
                                               value="{{ old('video_seconds', $content['lesson']->video_seconds) }}"
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
                                        Video duration in seconds. Shows as <span id="video-time">{{ gmdate('H:i:s', $content['lesson']->video_seconds) }}</span>.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Current Course Unit Assignments --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-link"></i> Current Assignments
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-success" id="add-assignment">
                                <i class="fas fa-plus"></i> Add Assignment
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="assignments-container">
                            @if($content['lesson']->CourseUnitLessons->count() > 0)
                                @foreach($content['lesson']->CourseUnitLessons as $index => $cul)
                                    <div class="assignment-item border rounded p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Course</label>
                                                    <select class="form-control course-select" data-selected-course="{{ $cul->CourseUnit->Course->id }}">
                                                        <option value="">Select a course...</option>
                                                        @foreach($content['courses'] as $course)
                                                            <option value="{{ $course->id }}" {{ $course->id == $cul->CourseUnit->Course->id ? 'selected' : '' }}>
                                                                {{ $course->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Course Unit</label>
                                                    <select class="form-control unit-select" name="course_units[]" data-selected-unit="{{ $cul->course_unit_id }}">
                                                        <option value="{{ $cul->course_unit_id }}" selected>{{ $cul->CourseUnit->title }}</option>
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
                                                           value="{{ $cul->progress_minutes }}"
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
                                                           value="{{ $cul->instr_seconds }}"
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
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>No assignments:</strong> This lesson is not currently assigned to any course units.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar with Actions and Info --}}
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
                                <i class="fas fa-save"></i> Update Lesson
                            </button>
                            <a href="{{ route('admin.lessons.show', $content['lesson']) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>

                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('admin.lessons.units', $content['lesson']) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-cogs"></i> Manage Units
                            </a>
                            <a href="{{ route('admin.lessons.show', $content['lesson']) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Current Information --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Current Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <dl class="row small">
                            <dt class="col-sm-6">Lesson ID:</dt>
                            <dd class="col-sm-6">#{{ $content['lesson']->id }}</dd>

                            <dt class="col-sm-6">Current Title:</dt>
                            <dd class="col-sm-6">{{ $content['lesson']->title }}</dd>

                            <dt class="col-sm-6">Credit Minutes:</dt>
                            <dd class="col-sm-6">{{ $content['lesson']->credit_minutes }}</dd>

                            <dt class="col-sm-6">Video Seconds:</dt>
                            <dd class="col-sm-6">{{ $content['lesson']->video_seconds }}</dd>

                            <dt class="col-sm-6">Course Units:</dt>
                            <dd class="col-sm-6">{{ $content['lesson']->CourseUnitLessons->count() }}</dd>

                            <dt class="col-sm-6">Exam Questions:</dt>
                            <dd class="col-sm-6">{{ $content['lesson']->ExamQuestions->count() }}</dd>
                        </dl>
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
                        <h6>Editing Tips</h6>
                        <ul class="list-unstyled small">
                            <li>• Changes to credit minutes will affect all course unit assignments</li>
                            <li>• Removing assignments will unlink the lesson from those units</li>
                            <li>• Progress minutes can be different for each course unit</li>
                        </ul>

                        <h6 class="mt-3">Warning</h6>
                        <ul class="list-unstyled small text-warning">
                            <li>• Changing the title affects all course displays</li>
                            <li>• Credit minute changes impact student progress tracking</li>
                        </ul>
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
            let assignmentCounter = {{ $content['lesson']->CourseUnitLessons->count() }};

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

            // Update calculations on input
            $('#credit_minutes').on('input', updateCreditHours);
            $('#video_seconds').on('input', updateVideoTime);

            // Add assignment functionality
            $('#add-assignment').on('click', function() {
                const template = document.getElementById('assignment-template');
                const clone = template.content.cloneNode(true);

                assignmentCounter++;
                $('#assignments-container .alert').hide();
                $('#assignments-container').append(clone);
            });

            // Remove assignment functionality (delegated event)
            $(document).on('click', '.remove-assignment', function() {
                $(this).closest('.assignment-item').remove();
                assignmentCounter--;

                // Show info alert if no assignments remain
                if (assignmentCounter === 0) {
                    $('#assignments-container').append(`
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>No assignments:</strong> This lesson will not be assigned to any course units.
                        </div>
                    `);
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
