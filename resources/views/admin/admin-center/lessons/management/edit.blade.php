@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> {{ $content['title'] }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.lessons.management.show', $content['lesson']) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <a href="{{ route('admin.lessons.management.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Lessons
                        </a>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.lessons.management.update', $content['lesson']) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Lesson Information -->
                            <div class="col-md-6">
                                <h5>Lesson Details</h5>

                                <div class="form-group">
                                    <label for="title">Lesson Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title"
                                           class="form-control @error('title') is-invalid @enderror"
                                           value="{{ old('title', $content['lesson']->title) }}"
                                           placeholder="Enter lesson title" required>
                                    @error('title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 64 characters</small>
                                </div>

                                <div class="form-group">
                                    <label for="credit_minutes">Credit Minutes <span class="text-danger">*</span></label>
                                    <input type="number" name="credit_minutes" id="credit_minutes"
                                           class="form-control @error('credit_minutes') is-invalid @enderror"
                                           value="{{ old('credit_minutes', $content['lesson']->credit_minutes) }}"
                                           min="1" max="9999"
                                           placeholder="e.g., 60" required>
                                    @error('credit_minutes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Duration in minutes for course credit</small>
                                </div>

                                <div class="form-group">
                                    <label for="video_seconds">Video Duration (seconds)</label>
                                    <input type="number" name="video_seconds" id="video_seconds"
                                           class="form-control @error('video_seconds') is-invalid @enderror"
                                           value="{{ old('video_seconds', $content['lesson']->video_seconds) }}"
                                           min="0" max="999999"
                                           placeholder="e.g., 3600">
                                    @error('video_seconds')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Total video duration in seconds (optional)</small>
                                </div>

                                <!-- Duration Display -->
                                <div class="alert alert-info" id="duration-display">
                                    <h6><i class="fas fa-clock"></i> Duration Summary</h6>
                                    <div id="duration-text"></div>
                                </div>
                            </div>

                            <!-- Course Unit Assignment -->
                            <div class="col-md-6">
                                <h5>Course Unit Assignment</h5>
                                <p class="text-muted">Assign this lesson to specific course units (optional)</p>

                                <div id="course-unit-assignments">
                                    @if($content['lesson']->CourseUnitLessons->count() > 0)
                                        @foreach($content['lesson']->CourseUnitLessons as $index => $assignment)
                                            <div class="course-unit-assignment" data-index="{{ $index }}">
                                                <div class="card card-outline card-secondary mb-3">
                                                    <div class="card-header">
                                                        <h6>Assignment #{{ $index + 1 }}</h6>
                                                        <button type="button" class="btn btn-sm btn-danger float-right remove-assignment" style="{{ $loop->first && $content['lesson']->CourseUnitLessons->count() == 1 ? 'display: none;' : '' }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <label>Course</label>
                                                            <select name="courses[]" class="form-control course-select">
                                                                <option value="">Select a Course</option>
                                                                @foreach($content['courses'] as $course)
                                                                    <option value="{{ $course->id }}"
                                                                            {{ $assignment->CourseUnit->course_id == $course->id ? 'selected' : '' }}>
                                                                        {{ $course->title }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Course Unit</label>
                                                            <select name="course_units[]" class="form-control course-unit-select">
                                                                <option value="">Select Course First</option>
                                                                @foreach($assignment->CourseUnit->Course->CourseUnits as $unit)
                                                                    <option value="{{ $unit->id }}"
                                                                            {{ $assignment->course_unit_id == $unit->id ? 'selected' : '' }}>
                                                                        {{ $unit->title }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Progress Minutes</label>
                                                            <input type="number" name="progress_minutes[]" class="form-control progress-minutes"
                                                                   value="{{ $assignment->progress_minutes }}"
                                                                   min="0" max="9999" placeholder="Will use credit minutes if empty">
                                                            <small class="form-text text-muted">Override the default credit minutes for this assignment</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="course-unit-assignment" data-index="0">
                                            <div class="card card-outline card-secondary mb-3">
                                                <div class="card-header">
                                                    <h6>Assignment #1</h6>
                                                    <button type="button" class="btn btn-sm btn-danger float-right remove-assignment" style="display: none;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label>Course</label>
                                                        <select name="courses[]" class="form-control course-select">
                                                            <option value="">Select a Course</option>
                                                            @foreach($content['courses'] as $course)
                                                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Course Unit</label>
                                                        <select name="course_units[]" class="form-control course-unit-select">
                                                            <option value="">Select Course First</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Progress Minutes</label>
                                                        <input type="number" name="progress_minutes[]" class="form-control progress-minutes"
                                                               min="0" max="9999" placeholder="Will use credit minutes if empty">
                                                        <small class="form-text text-muted">Override the default credit minutes for this assignment</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <button type="button" class="btn btn-secondary btn-sm" id="add-assignment">
                                    <i class="fas fa-plus"></i> Add Another Assignment
                                </button>

                                @if($content['lesson']->CourseUnitLessons->count() > 0)
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Note:</strong> This lesson is currently assigned to {{ $content['lesson']->CourseUnitLessons->count() }} course unit(s).
                                        Changes will affect existing course structures.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Lesson ID:</strong> {{ $content['lesson']->id }}
                                </small>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Update Lesson
                                </button>
                                <a href="{{ route('admin.lessons.management.show', $content['lesson']) }}" class="btn btn-info ml-2">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <a href="{{ route('admin.lessons.management.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .form-group label {
            font-weight: 600;
        }
        .text-danger {
            color: #dc3545 !important;
        }
        .is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            display: block;
        }
        .course-unit-assignment .card-header h6 {
            margin: 0;
            display: inline-block;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let assignmentIndex = {{ $content['lesson']->CourseUnitLessons->count() > 0 ? $content['lesson']->CourseUnitLessons->count() - 1 : 0 }};
            const coursesData = @json($content['courses']->mapWithKeys(function($course) {
                return [$course->id => $course->CourseUnits->map(function($unit) {
                    return ['id' => $unit->id, 'title' => $unit->title];
                })];
            }));

            // Update duration display
            function updateDurationDisplay() {
                const creditMinutes = parseInt($('#credit_minutes').val()) || 0;
                const videoSeconds = parseInt($('#video_seconds').val()) || 0;

                if (creditMinutes > 0 || videoSeconds > 0) {
                    let text = '';
                    if (creditMinutes > 0) {
                        const hours = Math.floor(creditMinutes / 60);
                        const minutes = creditMinutes % 60;
                        text += `<strong>Credit:</strong> ${creditMinutes} minutes`;
                        if (hours > 0) {
                            text += ` (${hours}h ${minutes}m)`;
                        }
                    }
                    if (videoSeconds > 0) {
                        const hours = Math.floor(videoSeconds / 3600);
                        const minutes = Math.floor((videoSeconds % 3600) / 60);
                        const seconds = videoSeconds % 60;
                        if (text) text += '<br>';
                        text += `<strong>Video:</strong> ${videoSeconds} seconds (${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')})`;
                    }
                    $('#duration-text').html(text);
                    $('#duration-display').show();
                } else {
                    $('#duration-display').hide();
                }
            }

            // Load course units when course is selected
            function setupCourseUnitLoader(assignmentDiv) {
                const courseSelect = assignmentDiv.find('.course-select');
                const courseUnitSelect = assignmentDiv.find('.course-unit-select');
                const currentCourseUnitId = courseUnitSelect.val();

                courseSelect.on('change', function() {
                    const courseId = $(this).val();
                    courseUnitSelect.html('<option value="">Loading...</option>');

                    if (courseId && coursesData[courseId]) {
                        courseUnitSelect.html('<option value="">Select a Unit</option>');
                        coursesData[courseId].forEach(function(unit) {
                            const selected = unit.id == currentCourseUnitId ? 'selected' : '';
                            courseUnitSelect.append(`<option value="${unit.id}" ${selected}>${unit.title}</option>`);
                        });
                    } else {
                        courseUnitSelect.html('<option value="">Select Course First</option>');
                    }
                });
            }

            // Add new assignment
            $('#add-assignment').on('click', function() {
                assignmentIndex++;
                const assignmentHtml = `
                    <div class="course-unit-assignment" data-index="${assignmentIndex}">
                        <div class="card card-outline card-secondary mb-3">
                            <div class="card-header">
                                <h6>Assignment #${assignmentIndex + 1}</h6>
                                <button type="button" class="btn btn-sm btn-danger float-right remove-assignment">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Course</label>
                                    <select name="courses[]" class="form-control course-select">
                                        <option value="">Select a Course</option>
                                        @foreach($content['courses'] as $course)
                                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Course Unit</label>
                                    <select name="course_units[]" class="form-control course-unit-select">
                                        <option value="">Select Course First</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Progress Minutes</label>
                                    <input type="number" name="progress_minutes[]" class="form-control progress-minutes"
                                           min="0" max="9999" placeholder="Will use credit minutes if empty">
                                    <small class="form-text text-muted">Override the default credit minutes for this assignment</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                const newAssignment = $(assignmentHtml);
                $('#course-unit-assignments').append(newAssignment);
                setupCourseUnitLoader(newAssignment);
                updateRemoveButtons();
            });

            // Remove assignment
            $(document).on('click', '.remove-assignment', function() {
                $(this).closest('.course-unit-assignment').remove();
                updateRemoveButtons();
                renumberAssignments();
            });

            // Update remove button visibility
            function updateRemoveButtons() {
                const assignments = $('.course-unit-assignment');
                if (assignments.length > 1) {
                    $('.remove-assignment').show();
                } else {
                    $('.remove-assignment').hide();
                }
            }

            // Renumber assignments
            function renumberAssignments() {
                $('.course-unit-assignment').each(function(index) {
                    $(this).find('.card-header h6').text(`Assignment #${index + 1}`);
                });
            }

            // Event listeners
            $('#credit_minutes, #video_seconds').on('input', updateDurationDisplay);

            // Initialize
            $('.course-unit-assignment').each(function() {
                setupCourseUnitLoader($(this));
            });
            updateDurationDisplay();
            updateRemoveButtons();
        });
    </script>
@stop
