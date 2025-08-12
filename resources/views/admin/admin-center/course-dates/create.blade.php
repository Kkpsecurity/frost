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
                        <i class="fas fa-plus"></i> {{ $content['title'] }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.course-dates.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Course Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course_id">Course <span class="text-danger">*</span></label>
                                    <select name="course_id" id="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
                                        <option value="">Select a Course</option>
                                        @foreach($content['courses'] as $course)
                                            <option value="{{ $course->id }}" @if(old('course_id') == $course->id) selected @endif>
                                                {{ $course->title }}
                                                @if($course->needs_range) (Range Required) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Course Unit Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course_unit_id">Course Unit <span class="text-danger">*</span></label>
                                    <select name="course_unit_id" id="course_unit_id" class="form-control @error('course_unit_id') is-invalid @enderror" required>
                                        <option value="">Select Course First</option>
                                    </select>
                                    @error('course_unit_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Start Date & Time -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date"
                                           class="form-control @error('start_date') is-invalid @enderror"
                                           value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                    <input type="time" name="start_time" id="start_time"
                                           class="form-control @error('start_time') is-invalid @enderror"
                                           value="{{ old('start_time', '09:00') }}" required>
                                    @error('start_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- End Date & Time -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date"
                                           class="form-control @error('end_date') is-invalid @enderror"
                                           value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_time">End Time <span class="text-danger">*</span></label>
                                    <input type="time" name="end_time" id="end_time"
                                           class="form-control @error('end_time') is-invalid @enderror"
                                           value="{{ old('end_time', '17:00') }}" required>
                                    @error('end_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Instructor Assignment -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="inst_unit_id">Instructor</label>
                                    <select name="inst_unit_id" id="inst_unit_id" class="form-control @error('inst_unit_id') is-invalid @enderror">
                                        <option value="">Select an Instructor (Optional)</option>
                                        @foreach($content['instructors'] as $instructor)
                                            <option value="{{ $instructor->id }}" @if(old('inst_unit_id') == $instructor->id) selected @endif>
                                                {{ $instructor->fname }} {{ $instructor->lname }}
                                                ({{ $instructor->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('inst_unit_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty to assign later</small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" id="is_active"
                                               class="form-check-input" value="1"
                                               @if(old('is_active', true)) checked @endif>
                                        <label class="form-check-label" for="is_active">
                                            Active (Course date will be available for scheduling)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes/Description -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="form-control @error('notes') is-invalid @enderror"
                                              placeholder="Optional notes about this course date">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Duration Display -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" id="duration-display" style="display: none;">
                                    <i class="fas fa-clock"></i> <strong>Duration:</strong> <span id="duration-text"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Create Course Date
                                </button>
                                <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary ml-2">
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
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Load course units when course is selected
            $('#course_id').on('change', function() {
                const courseId = $(this).val();
                const courseUnitSelect = $('#course_unit_id');

                courseUnitSelect.html('<option value="">Loading...</option>');

                if (courseId) {
                    $.ajax({
                        url: `/admin/course-dates/course-units/${courseId}`,
                        method: 'GET',
                        success: function(response) {
                            courseUnitSelect.html('<option value="">Select a Unit</option>');
                            response.forEach(function(unit) {
                                courseUnitSelect.append(`<option value="${unit.id}">${unit.title}</option>`);
                            });

                            // Restore old value if exists
                            const oldValue = '{{ old("course_unit_id") }}';
                            if (oldValue) {
                                courseUnitSelect.val(oldValue);
                            }
                        },
                        error: function() {
                            courseUnitSelect.html('<option value="">Error loading units</option>');
                        }
                    });
                } else {
                    courseUnitSelect.html('<option value="">Select Course First</option>');
                }
            });

            // Calculate duration when dates/times change
            function calculateDuration() {
                const startDate = $('#start_date').val();
                const startTime = $('#start_time').val();
                const endDate = $('#end_date').val();
                const endTime = $('#end_time').val();

                if (startDate && startTime && endDate && endTime) {
                    const start = new Date(`${startDate}T${startTime}`);
                    const end = new Date(`${endDate}T${endTime}`);

                    if (end > start) {
                        const diffMs = end - start;
                        const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                        const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

                        $('#duration-text').text(`${diffHours} hours and ${diffMinutes} minutes`);
                        $('#duration-display').show();
                    } else {
                        $('#duration-display').hide();
                    }
                } else {
                    $('#duration-display').hide();
                }
            }

            $('#start_date, #start_time, #end_date, #end_time').on('change', calculateDuration);

            // Auto-fill end date when start date is selected
            $('#start_date').on('change', function() {
                const startDate = $(this).val();
                if (startDate && !$('#end_date').val()) {
                    $('#end_date').val(startDate);
                    calculateDuration();
                }
            });

            // Trigger course change if old value exists
            const oldCourse = '{{ old("course_id") }}';
            if (oldCourse) {
                $('#course_id').trigger('change');
            }

            // Initial duration calculation
            calculateDuration();
        });
    </script>
@stop
