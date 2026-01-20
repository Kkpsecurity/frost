@extends('adminlte::page')

@section('title', 'Edit Course Date')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Edit Course Date</h1>
        <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.course-dates.update', $content['course_date']->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Left Column - Schedule Information -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Schedule Information</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>Please correct the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Course Selection -->
                        <div class="form-group">
                            <label for="course_id">Course <span class="text-danger">*</span></label>
                            <select class="form-control @error('course_id') is-invalid @enderror"
                                    id="course_id"
                                    name="course_id"
                                    required>
                                <option value="">Select Course</option>
                                @foreach($content['courses'] as $course)
                                    <option value="{{ $course->id }}"
                                            {{ old('course_id', $content['course_date']->CourseUnit->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Course Unit Selection -->
                        <div class="form-group">
                            <label for="course_unit_id">Course Unit <span class="text-danger">*</span></label>
                            <select class="form-control @error('course_unit_id') is-invalid @enderror"
                                    id="course_unit_id"
                                    name="course_unit_id"
                                    required>
                                <option value="">Select Course Unit</option>
                                @foreach($content['courses'] as $course)
                                    @foreach($course->CourseUnits as $unit)
                                        <option value="{{ $unit->id }}"
                                                data-course="{{ $course->id }}"
                                                {{ old('course_unit_id', $content['course_date']->course_unit_id) == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->title }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('course_unit_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Start Date & Time -->
                        <div class="form-group">
                            <label for="starts_at">Start Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local"
                                   class="form-control @error('starts_at') is-invalid @enderror"
                                   id="starts_at"
                                   name="starts_at"
                                   value="{{ old('starts_at', $content['course_date']->starts_at ? $content['course_date']->starts_at->format('Y-m-d\TH:i') : '') }}"
                                   required>
                            @error('starts_at')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- End Date & Time -->
                        <div class="form-group">
                            <label for="ends_at">End Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local"
                                   class="form-control @error('ends_at') is-invalid @enderror"
                                   id="ends_at"
                                   name="ends_at"
                                   value="{{ old('ends_at', $content['course_date']->ends_at ? $content['course_date']->ends_at->format('Y-m-d\TH:i') : '') }}"
                                   required>
                            @error('ends_at')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $content['course_date']->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Active (visible to students and instructors)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Instructor & Additional Info -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Instructor Assignment</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="instructor_id">Assigned Instructor</label>
                            <select class="form-control @error('instructor_id') is-invalid @enderror"
                                    id="instructor_id"
                                    name="instructor_id">
                                <option value="">No Instructor Assigned</option>
                                @foreach($content['instructors'] as $instructor)
                                    <option value="{{ $instructor->id }}"
                                            {{ old('instructor_id', $content['course_date']->InstUnit->user_id ?? '') == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->fname }} {{ $instructor->lname }}
                                    </option>
                                @endforeach
                            </select>
                            @error('instructor_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Assign an instructor to this class session
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i>
                            <strong>Note:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Start time must be before end time</li>
                                <li>Students will be notified of schedule changes</li>
                                <li>Inactive classes are hidden from the schedule</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Current Enrollment Info -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Current Enrollment</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-6">Enrolled Students:</dt>
                            <dd class="col-sm-6">
                                <span class="badge badge-info">{{ $content['course_date']->StudentUnits->count() }}</span>
                            </dd>

                            <dt class="col-sm-6">Completed:</dt>
                            <dd class="col-sm-6">
                                <span class="badge badge-success">
                                    {{ $content['course_date']->StudentUnits->whereNotNull('completed_at')->count() }}
                                </span>
                            </dd>

                            <dt class="col-sm-6">In Progress:</dt>
                            <dd class="col-sm-6">
                                <span class="badge badge-warning">
                                    {{ $content['course_date']->StudentUnits->whereNotNull('started_at')->whereNull('completed_at')->count() }}
                                </span>
                            </dd>
                        </dl>
                        <a href="{{ route('admin.course-dates.show', $content['course_date']->id) }}"
                           class="btn btn-sm btn-outline-primary btn-block mt-2">
                            <i class="fas fa-eye"></i> View Full Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('admin.course-dates.show', $content['course_date']->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="button" class="btn btn-danger float-right" data-toggle="modal" data-target="#deleteModal">
                    <i class="fas fa-trash"></i> Delete Course Date
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Confirm Deletion</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Are you sure you want to delete this course date?</strong></p>
                <p>This action cannot be undone. All associated data including:</p>
                <ul>
                    <li>Student enrollments ({{ $content['course_date']->StudentUnits->count() }} students)</li>
                    <li>Instructor assignments</li>
                    <li>Attendance records</li>
                </ul>
                <p>will be permanently removed.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.course-dates.destroy', $content['course_date']->id) }}"
                      method="POST"
                      style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Filter course units based on selected course
    $('#course_id').change(function() {
        var courseId = $(this).val();
        $('#course_unit_id option').hide();
        $('#course_unit_id option[value=""]').show();
        if (courseId) {
            $('#course_unit_id option[data-course="' + courseId + '"]').show();
        }
        $('#course_unit_id').val('');
    });

    // Trigger on page load to filter units
    $('#course_id').trigger('change');
});
</script>
@stop
