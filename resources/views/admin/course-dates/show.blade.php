@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-calendar-day"></i>
            Course Date Details
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
            <a href="{{ route('admin.course-dates.edit', $content['course_date']) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Edit
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @php
            $courseDate = $content['course_date'];
            $course = $courseDate->CourseUnit->Course;
            $courseUnit = $courseDate->CourseUnit;
        @endphp

        <div class="row">
            <!-- Course Information -->
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Course Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Course:</dt>
                                    <dd class="col-sm-8">
                                        <strong>{{ $course->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $course->description ?? 'No description' }}</small>
                                    </dd>

                                    <dt class="col-sm-4">Course Unit:</dt>
                                    <dd class="col-sm-8">{{ $courseUnit->title }}</dd>

                                    @if($courseUnit->day)
                                    <dt class="col-sm-4">Day:</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge badge-info">Day {{ $courseUnit->day }}</span>
                                    </dd>
                                    @endif

                                    @if($courseUnit->ordering)
                                    <dt class="col-sm-4">Unit Order:</dt>
                                    <dd class="col-sm-8">{{ $courseUnit->ordering }}</dd>
                                    @endif
                                </dl>
                            </div>

                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Date:</dt>
                                    <dd class="col-sm-8">
                                        @if($courseDate->starts_at instanceof \Carbon\Carbon)
                                            {{ $courseDate->starts_at->format('l, F j, Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($courseDate->starts_at)->format('l, F j, Y') }}
                                        @endif
                                    </dd>

                                    <dt class="col-sm-4">Time:</dt>
                                    <dd class="col-sm-8">
                                        @if($courseDate->starts_at instanceof \Carbon\Carbon && $courseDate->ends_at instanceof \Carbon\Carbon)
                                            {{ $courseDate->starts_at->format('g:i A') }} - {{ $courseDate->ends_at->format('g:i A') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($courseDate->starts_at)->format('g:i A') }} - {{ \Carbon\Carbon::parse($courseDate->ends_at)->format('g:i A') }}
                                        @endif
                                    </dd>

                                    <dt class="col-sm-4">Duration:</dt>
                                    <dd class="col-sm-8">
                                        @php
                                            $start = $courseDate->starts_at instanceof \Carbon\Carbon
                                                ? $courseDate->starts_at
                                                : \Carbon\Carbon::parse($courseDate->starts_at);
                                            $end = $courseDate->ends_at instanceof \Carbon\Carbon
                                                ? $courseDate->ends_at
                                                : \Carbon\Carbon::parse($courseDate->ends_at);
                                            $duration = $start->diffInHours($end);
                                        @endphp
                                        {{ $duration }} {{ $duration == 1 ? 'hour' : 'hours' }}
                                    </dd>

                                    <dt class="col-sm-4">Status:</dt>
                                    <dd class="col-sm-8">
                                        @if($courseDate->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructor Information -->
                @if($courseDate->InstUnit)
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chalkboard-teacher"></i>
                            Instructor Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    @if($courseDate->InstUnit->CreatedBy)
                                    <dt class="col-sm-4">Instructor:</dt>
                                    <dd class="col-sm-8">
                                        {{ $courseDate->InstUnit->CreatedBy->fname }} {{ $courseDate->InstUnit->CreatedBy->lname }}
                                    </dd>
                                    @endif

                                    @if($courseDate->InstUnit->Assistant)
                                    <dt class="col-sm-4">Assistant:</dt>
                                    <dd class="col-sm-8">
                                        {{ $courseDate->InstUnit->Assistant->fname }} {{ $courseDate->InstUnit->Assistant->lname }}
                                    </dd>
                                    @endif
                                </dl>
                            </div>

                            <div class="col-md-6">
                                <dl class="row">
                                    @if($courseDate->InstUnit->created_at)
                                    <dt class="col-sm-4">Created:</dt>
                                    <dd class="col-sm-8">
                                        {{ \Carbon\Carbon::parse($courseDate->InstUnit->created_at)->format('M j, Y g:i A') }}
                                    </dd>
                                    @endif

                                    @if($courseDate->InstUnit->completed_at)
                                    <dt class="col-sm-4">Completed:</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge badge-success">
                                            {{ \Carbon\Carbon::parse($courseDate->InstUnit->completed_at)->format('M j, Y g:i A') }}
                                        </span>
                                    </dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Students Enrolled -->
                @if($courseDate->StudentUnits && $courseDate->StudentUnits->isNotEmpty())
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users"></i>
                            Enrolled Students ({{ $courseDate->StudentUnits->count() }})
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Enrollment Status</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courseDate->StudentUnits as $studentUnit)
                                        @if($studentUnit->CourseAuth && $studentUnit->CourseAuth->User)
                                        <tr>
                                            <td>
                                                <strong>{{ $studentUnit->CourseAuth->User->fname }} {{ $studentUnit->CourseAuth->User->lname }}</strong>
                                            </td>
                                            <td>{{ $studentUnit->CourseAuth->User->email }}</td>
                                            <td>
                                                @if($studentUnit->CourseAuth->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($studentUnit->completed_at)
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($studentUnit->started_at)
                                                    <span class="badge badge-warning">In Progress</span>
                                                @else
                                                    <span class="badge badge-light">Not Started</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.course-dates.edit', $courseDate) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                                Edit Course Date
                            </a>

                            <form method="POST" action="{{ route('admin.course-dates.toggle-active', $courseDate) }}" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-{{ $courseDate->is_active ? 'warning' : 'success' }} btn-sm w-100">
                                    <i class="fas fa-{{ $courseDate->is_active ? 'pause' : 'play' }}"></i>
                                    {{ $courseDate->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <button type="button" class="btn btn-info btn-sm" onclick="duplicateCourseDate()">
                                <i class="fas fa-copy"></i>
                                Duplicate Course Date
                            </button>

                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteCourseDate()">
                                <i class="fas fa-trash"></i>
                                Delete Course Date
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar"></i>
                            Statistics
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Students Enrolled</span>
                                <span class="info-box-number">{{ $courseDate->StudentUnits ? $courseDate->StudentUnits->count() : 0 }}</span>
                            </div>
                        </div>

                        @php
                            $completedStudents = $courseDate->StudentUnits ? $courseDate->StudentUnits->whereNotNull('completed_at')->count() : 0;
                            $inProgressStudents = $courseDate->StudentUnits ? $courseDate->StudentUnits->whereNotNull('started_at')->whereNull('completed_at')->count() : 0;
                        @endphp

                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Completed</span>
                                <span class="info-box-number">{{ $completedStudents }}</span>
                            </div>
                        </div>

                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">In Progress</span>
                                <span class="info-box-number">{{ $inProgressStudents }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Navigation -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-sitemap"></i>
                            Course Navigation
                        </h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Course:</strong> {{ $course->title }}</p>
                        <p><strong>Current Unit:</strong> {{ $courseUnit->title }}</p>

                        @php
                            // Get other course dates for this course unit
                            $otherCourseDates = \App\Models\CourseDate::where('course_unit_id', $courseUnit->id)
                                ->where('id', '!=', $courseDate->id)
                                ->orderBy('starts_at')
                                ->limit(5)
                                ->get();
                        @endphp

                        @if($otherCourseDates->isNotEmpty())
                            <hr>
                            <p><strong>Other dates for this unit:</strong></p>
                            <ul class="list-unstyled">
                                @foreach($otherCourseDates as $otherDate)
                                <li class="mb-1">
                                    <a href="{{ route('admin.course-dates.show', $otherDate) }}" class="text-sm">
                                        @if($otherDate->starts_at instanceof \Carbon\Carbon)
                                            {{ $otherDate->starts_at->format('M j, Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($otherDate->starts_at)->format('M j, Y') }}
                                        @endif
                                        @if(!$otherDate->is_active)
                                            <span class="badge badge-secondary badge-sm">Inactive</span>
                                        @endif
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm Deletion</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this course date?</p>
                    <p><strong>This action cannot be undone.</strong></p>
                    <p>Course: <strong>{{ $course->title }} - {{ $courseUnit->title }}</strong></p>
                    <p>Date: <strong>
                        @if($courseDate->starts_at instanceof \Carbon\Carbon)
                            {{ $courseDate->starts_at->format('M j, Y g:i A') }}
                        @else
                            {{ \Carbon\Carbon::parse($courseDate->starts_at)->format('M j, Y g:i A') }}
                        @endif
                    </strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('admin.course-dates.destroy', $courseDate) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Course Date</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
function deleteCourseDate() {
    $('#deleteModal').modal('show');
}

function duplicateCourseDate() {
    if (confirm('Create a duplicate of this course date?')) {
        // You could implement duplication logic here
        alert('Duplication feature would be implemented here');
    }
}
</script>
@stop

@section('css')
<style>
.info-box {
    margin-bottom: 15px;
}

.course-item {
    border-left: 3px solid #007bff;
    padding-left: 10px;
    margin-bottom: 10px;
}

.course-item:hover {
    background-color: #f8f9fa;
}

dl.row dt {
    font-weight: 600;
}

.badge-sm {
    font-size: 0.7em;
}
</style>
@stop
