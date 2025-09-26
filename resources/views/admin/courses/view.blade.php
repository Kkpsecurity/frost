@extends('adminlte::page')

@section('title', 'Course: ' . $course->title)

@section('content_header')
    <x-admin.partials.titlebar
        title="Course Details"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Courses', 'url' => route('admin.courses.dashboard')],
            ['title' => $course->title]
        ]"
    />
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            {{-- Course Information --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book"></i> Course Information
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.courses.manage.edit', $course) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Course
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Course ID:</dt>
                        <dd class="col-sm-9">{{ $course->id }}</dd>

                        <dt class="col-sm-3">Title:</dt>
                        <dd class="col-sm-9">{{ $course->title }}</dd>

                        <dt class="col-sm-3">Long Title:</dt>
                        <dd class="col-sm-9">{{ $course->title_long ?: 'Not set' }}</dd>

                        <dt class="col-sm-3">Price:</dt>
                        <dd class="col-sm-9">${{ number_format($course->price, 2) }}</dd>

                        <dt class="col-sm-3">Course Type:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-{{ $course->getCourseTypeBadgeColor() }}">
                                {{ $course->getCourseTypeDisplayName() }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
                            @if($course->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Archived</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Duration:</dt>
                        <dd class="col-sm-9">
                            {{ number_format($course->total_minutes) }} minutes
                            <small class="text-muted">({{ $course->getDurationDays() }} days)</small>
                        </dd>

                        <dt class="col-sm-3">Policy Expiry:</dt>
                        <dd class="col-sm-9">{{ $course->policy_expire_days }} days</dd>

                        <dt class="col-sm-3">Needs Range:</dt>
                        <dd class="col-sm-9">
                            @if($course->needs_range)
                                <span class="badge badge-warning">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Exam ID:</dt>
                        <dd class="col-sm-9">{{ $course->exam_id }}</dd>

                        <dt class="col-sm-3">Question Spec ID:</dt>
                        <dd class="col-sm-9">{{ $course->eq_spec_id }}</dd>

                        <dt class="col-sm-3">Zoom Credentials:</dt>
                        <dd class="col-sm-9">ID: {{ $course->zoom_creds_id }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Course Documents --}}
            @php $docs = $course->GetDocs(); @endphp
            @if(!empty($docs))
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-pdf"></i> Course Documents
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($docs as $filename => $url)
                        <div class="col-md-6 mb-2">
                            <a href="{{ $url }}" target="_blank" class="btn btn-outline-danger btn-block">
                                <i class="fas fa-file-pdf"></i> {{ $filename }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Recent Enrollments --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Recent Enrollments
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.courses.manage.enrollments', $course) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($recentEnrollments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Enrolled Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentEnrollments as $enrollment)
                                <tr>
                                    <td>
                                        @if($enrollment->User)
                                            {{ $enrollment->User->fullname() }}
                                            <small class="text-muted d-block">{{ $enrollment->User->email }}</small>
                                        @else
                                            <span class="text-muted">User not found</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($enrollment->created_at)->format('M d, Y') }}</td>
                                    <td>
                                        @if($enrollment->completed_at)
                                            <span class="badge badge-success">Completed</span>
                                        @else
                                            <span class="badge badge-warning">In Progress</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($enrollment->User)
                                        <a href="{{ route('admin.students.manage.view', $enrollment->User) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-muted">No enrollments yet</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Course Statistics --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Course Statistics
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Enrollments</span>
                                    <span class="info-box-number">{{ number_format($stats['total_enrollments']) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Students</span>
                                    <span class="info-box-number">{{ number_format($stats['active_enrollments']) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-graduation-cap"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed</span>
                                    <span class="info-box-number">{{ number_format($stats['completed_enrollments']) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Revenue</span>
                                    <span class="info-box-number">${{ number_format($stats['total_revenue'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <a href="{{ route('admin.courses.manage.enrollments', $course) }}" class="btn btn-info btn-block">
                                <i class="fas fa-users"></i> Manage Enrollments
                            </a>
                        </div>

                        <div class="col-12 mb-2">
                            <a href="{{ route('admin.courses.manage.units', $course) }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-list-ol"></i> Manage Units ({{ $stats['total_units'] }})
                            </a>
                        </div>

                        <div class="col-12 mb-2">
                            <a href="{{ route('admin.courses.manage.edit', $course) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-edit"></i> Edit Course
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course Actions --}}
            <div class="card card-{{ $course->is_active ? 'warning' : 'success' }}">
                <div class="card-header">
                    <h3 class="card-title">Course Actions</h3>
                </div>
                <div class="card-body">
                    @if($course->is_active)
                        <p class="text-muted">Archive this course to make it inactive and hidden from students.</p>
                        <button class="btn btn-warning btn-block archive-course" data-id="{{ $course->id }}">
                            <i class="fas fa-archive"></i> Archive Course
                        </button>
                    @else
                        <p class="text-muted">Restore this course to make it active and visible to students.</p>
                        <button class="btn btn-success btn-block restore-course" data-id="{{ $course->id }}">
                            <i class="fas fa-undo"></i> Restore Course
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
$(function () {
    // Archive course
    $('.archive-course').click(function() {
        var courseId = $(this).data('id');

        Swal.fire({
            title: 'Archive Course?',
            text: 'This will make the course inactive and hide it from students.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, archive it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/courses/manage/' + courseId + '/archive',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Archived!', response.message, 'success')
                                .then(() => location.reload());
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to archive course.', 'error');
                    }
                });
            }
        });
    });

    // Restore course
    $('.restore-course').click(function() {
        var courseId = $(this).data('id');

        Swal.fire({
            title: 'Restore Course?',
            text: 'This will make the course active and visible to students.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, restore it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/courses/manage/' + courseId + '/restore',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Restored!', response.message, 'success')
                                .then(() => location.reload());
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to restore course.', 'error');
                    }
                });
            }
        });
    });
});
</script>

@if(session('success'))
<script>
$(document).ready(function() {
    Swal.fire({
        title: 'Success!',
        text: '{{ session('success') }}',
        icon: 'success',
        timer: 3000,
        showConfirmButton: false
    });
});
</script>
@endif
@endsection
