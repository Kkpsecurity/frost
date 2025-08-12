@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-graduation-cap"></i> {{ $content['course']->title }}
                    </h3>
                    <div class="card-tools">
                        @if($content['course']->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Archived</span>
                        @endif
                        <span class="badge badge-{{ $content['course']->getCourseTypeBadgeColor() }} ml-2">
                            {{ $content['course']->getCourseType() }} Course
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Course Information Section -->
                        <div class="col-md-8">
                            <h4>Course Information</h4>
                            <div class="row">
                                <!-- Course Details Column -->
                                <div class="col-md-6">
                                    <h5>Course Details</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="120">Course ID</th>
                                            <td>{{ $content['course']->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Title</th>
                                            <td>{{ $content['course']->title }}</td>
                                        </tr>
                                        @if($content['course']->title_long)
                                        <tr>
                                            <th>Full Title</th>
                                            <td>{{ $content['course']->title_long }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Course Type</th>
                                            <td>
                                                <span class="badge badge-{{ $content['course']->getCourseTypeBadgeColor() }}">
                                                    {{ $content['course']->getCourseTypeDisplayName() }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Duration</th>
                                            <td>{{ $content['course']->getDurationDays() }} days</td>
                                        </tr>
                                        <tr>
                                            <th>Frequency</th>
                                            <td>{{ ucfirst($content['course']->getFrequencyType()) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Price</th>
                                            <td>${{ number_format($content['course']->price, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Minutes</th>
                                            <td>{{ number_format($content['course']->total_minutes) }} minutes</td>
                                        </tr>
                                        <tr>
                                            <th>Expiration</th>
                                            <td>{{ $content['course']->policy_expire_days }} days</td>
                                        </tr>
                                        <tr>
                                            <th>Needs Range</th>
                                            <td>
                                                @if($content['course']->needs_range)
                                                    <span class="badge badge-warning">Yes</span>
                                                @else
                                                    <span class="badge badge-secondary">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @if($content['course']->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Archived</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Course Lessons Column -->
                                <div class="col-md-6 course-lessons">
                                    <h5>Course Lessons</h5>
                                    @if($content['course_units']->count() > 0)
                                        <div class="card">
                                            <div class="card-body p-0">
                                                @php $totalLessons = 0; @endphp
                                                @foreach($content['course_units'] as $unit)
                                                    @php
                                                        $lessons = $unit->GetLessons();
                                                        $totalLessons += $lessons->count();
                                                    @endphp
                                                    <div class="border-bottom p-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="mb-1">{{ $unit->title }}</h6>
                                                                @if($unit->admin_title)
                                                                    <small class="text-muted">{{ $unit->admin_title }}</small>
                                                                @endif
                                                            </div>
                                                            <span class="badge badge-info">
                                                                {{ $lessons->count() }} lesson{{ $lessons->count() != 1 ? 's' : '' }}
                                                            </span>
                                                        </div>
                                                        @if($lessons->count() > 0)
                                                            <div class="mt-2">
                                                                <small class="text-muted">Lessons:</small>
                                                                <ul class="list-unstyled mb-0 mt-1">
                                                                    @foreach($lessons as $lesson)
                                                                        <li class="text-sm">
                                                                            <i class="fas fa-play-circle text-primary mr-1"></i>
                                                                            {{ $lesson->title }}
                                                                            @if($lesson->duration_minutes)
                                                                                <span class="text-muted">({{ $lesson->duration_minutes }} min)</span>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @else
                                                            <small class="text-muted">No lessons assigned to this unit</small>
                                                        @endif
                                                    </div>
                                                @endforeach

                                                @if($totalLessons > 0)
                                                    <div class="p-3 bg-light">
                                                        <strong>Total: {{ $totalLessons }} lesson{{ $totalLessons != 1 ? 's' : '' }} across {{ $content['course_units']->count() }} unit{{ $content['course_units']->count() != 1 ? 's' : '' }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> No course units or lessons have been created yet.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Column -->
                        <div class="col-md-4">
                            <h4>Course Statistics</h4>

                            <!-- Total Enrollments -->
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-users"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Enrollments</span>
                                    <span class="info-box-number">{{ $content['course_auths_count'] ?? 0 }}</span>
                                </div>
                            </div>

                            <!-- Active Enrollments -->
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-user-check"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Enrollments</span>
                                    <span class="info-box-number">{{ $content['active_enrollments'] ?? 0 }}</span>
                                </div>
                            </div>

                            <!-- Course Units -->
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-list"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Course Units</span>
                                    <span class="info-box-number">{{ $content['course_units']->count() ?? 0 }}</span>
                                </div>
                            </div>

                            <!-- Total Lessons -->
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-play-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Lessons</span>
                                    <span class="info-box-number">
                                        @php
                                            $totalLessons = 0;
                                            foreach($content['course_units'] as $unit) {
                                                $totalLessons += $unit->GetLessons()->count();
                                            }
                                        @endphp
                                        {{ $totalLessons }}
                                    </span>
                                </div>
                            </div>

                            <!-- Max Participants -->
                            <div class="info-box">
                                <span class="info-box-icon bg-secondary">
                                    <i class="fas fa-user-friends"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Max Participants</span>
                                    <span class="info-box-number">{{ $content['course']->getMaxParticipants() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <a href="{{ route('admin.courses.management.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Courses
                    </a>
                    @if(auth('admin')->user()->role_id <= 2)
                        <a href="{{ route('admin.courses.management.edit', $content['course']) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Course
                        </a>
                        @if($content['course']->is_active)
                            <button onclick="archiveCourse({{ $content['course']->id }})" class="btn btn-secondary">
                                <i class="fas fa-archive"></i> Archive Course
                            </button>
                        @else
                            <button onclick="restoreCourse({{ $content['course']->id }})" class="btn btn-success">
                                <i class="fas fa-undo"></i> Restore Course
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .info-box {
            margin-bottom: 15px;
        }
        .lesson-item {
            padding: 0.25rem 0;
            border-bottom: 1px solid #f4f4f4;
        }
        .lesson-item:last-child {
            border-bottom: none;
        }
        .text-sm {
            font-size: 0.875rem;
        }
        .course-lessons .card {
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }
        .course-lessons .border-bottom:last-child {
            border-bottom: none !important;
        }
    </style>
@stop

@section('js')
    <script>
        function archiveCourse(id) {
            Swal.fire({
                title: 'Archive Course?',
                text: "This course will be archived but not deleted. You can restore it later.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.courses.management.archive', $content['course']) }}',
                        type: 'PATCH',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Archived!', 'Course has been archived.', 'success');
                            location.reload();
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        }

        function restoreCourse(id) {
            Swal.fire({
                title: 'Restore Course?',
                text: "This course will be restored and made active again.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.courses.management.restore', $content['course']) }}',
                        type: 'PATCH',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Restored!', 'Course has been restored.', 'success');
                            location.reload();
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        }
    </script>
@stop
