@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Page Header with Create Button -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <h3><i class="fas fa-calendar-alt"></i> Course Dates Schedule</h3>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('admin.course-dates.create') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i> Create Course Date
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-3">
                <div class="col-lg-2-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $content['stats']['total'] }}</h3>
                            <p>Total Dates</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $content['stats']['active'] }}</h3>
                            <p>Active</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $content['stats']['upcoming'] }}</h3>
                            <p>Upcoming</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2-4 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ $content['stats']['this_week'] }}</h3>
                            <p>This Week</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2-4 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ $content['stats']['this_month'] }}</h3>
                            <p>This Month</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter and Search Card -->
            <div class="card card-secondary collapsed-card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filters & Search
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.course-dates.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course_id">Course</label>
                                    <select name="course_id" id="course_id" class="form-control">
                                        <option value="">All Courses</option>
                                        @foreach($content['courses'] as $course)
                                            <option value="{{ $course->id }}"
                                                    @if($content['filters']['course_id'] == $course->id) selected @endif>
                                                {{ $course->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="all" @if($content['filters']['status'] == 'all') selected @endif>All</option>
                                        <option value="active" @if($content['filters']['status'] == 'active') selected @endif>Active</option>
                                        <option value="inactive" @if($content['filters']['status'] == 'inactive') selected @endif>Inactive</option>
                                        <option value="upcoming" @if($content['filters']['status'] == 'upcoming') selected @endif>Upcoming</option>
                                        <option value="past" @if($content['filters']['status'] == 'past') selected @endif>Past</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_range">Date Range</label>
                                    <select name="date_range" id="date_range" class="form-control">
                                        <option value="month" @if($content['filters']['date_range'] == 'month') selected @endif>This Month</option>
                                        <option value="week" @if($content['filters']['date_range'] == 'week') selected @endif>This Week</option>
                                        <option value="year" @if($content['filters']['date_range'] == 'year') selected @endif>This Year</option>
                                        <option value="all" @if($content['filters']['date_range'] == 'all') selected @endif>All Time</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="instructor_id">Instructor</label>
                                    <select name="instructor_id" id="instructor_id" class="form-control">
                                        <option value="">All Instructors</option>
                                        @foreach($content['instructors'] as $instructor)
                                            <option value="{{ $instructor->id }}"
                                                    @if($content['filters']['instructor_id'] == $instructor->id) selected @endif>
                                                {{ $instructor->fname }} {{ $instructor->lname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Course Dates Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Course Dates Schedule
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-secondary">{{ $content['course_dates']->total() }} total</span>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    @if($content['course_dates']->count() > 0)
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Unit</th>
                                    <th>Start Date/Time</th>
                                    <th>End Date/Time</th>
                                    <th>Duration</th>
                                    <th>Instructor</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($content['course_dates'] as $courseDate)
                                    <tr>
                                        <td>
                                            <strong>{{ $courseDate->CourseUnit->Course->title }}</strong>
                                            @if($courseDate->CourseUnit->Course->needs_range)
                                                <span class="badge badge-warning badge-sm ml-1">Range</span>
                                            @endif
                                        </td>
                                        <td>{{ $courseDate->CourseUnit->title }}</td>
                                        <td>
                                            <div>{{ \Carbon\Carbon::parse($courseDate->starts_at)->format('M j, Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($courseDate->starts_at)->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            <div>{{ \Carbon\Carbon::parse($courseDate->ends_at)->format('M j, Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($courseDate->ends_at)->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $duration = \Carbon\Carbon::parse($courseDate->starts_at)->diffInMinutes(\Carbon\Carbon::parse($courseDate->ends_at));
                                                $hours = intval($duration / 60);
                                                $minutes = $duration % 60;
                                            @endphp
                                            {{ $hours }}h {{ $minutes }}m
                                        </td>
                                        <td>
                                            @if($courseDate->InstUnit && $courseDate->InstUnit->User)
                                                <div>{{ $courseDate->InstUnit->User->fname }} {{ $courseDate->InstUnit->User->lname }}</div>
                                                <small class="text-muted">{{ $courseDate->InstUnit->User->email }}</small>
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $courseDate->StudentUnits->count() }}</span>
                                        </td>
                                        <td>
                                            @if($courseDate->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif

                                            @php
                                                $now = now();
                                                $start = \Carbon\Carbon::parse($courseDate->starts_at);
                                                $end = \Carbon\Carbon::parse($courseDate->ends_at);
                                            @endphp

                                            @if($now < $start)
                                                <span class="badge badge-warning badge-sm ml-1">Upcoming</span>
                                            @elseif($now >= $start && $now <= $end)
                                                <span class="badge badge-primary badge-sm ml-1">In Progress</span>
                                            @else
                                                <span class="badge badge-secondary badge-sm ml-1">Past</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.course-dates.show', $courseDate) }}"
                                                   class="btn btn-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.course-dates.edit', $courseDate) }}"
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-{{ $courseDate->is_active ? 'secondary' : 'success' }} btn-sm toggle-active-btn"
                                                        data-id="{{ $courseDate->id }}"
                                                        data-active="{{ $courseDate->is_active ? 1 : 0 }}"
                                                        title="{{ $courseDate->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas fa-{{ $courseDate->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                                @if($courseDate->StudentUnits->count() == 0)
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm delete-btn"
                                                            data-id="{{ $courseDate->id }}"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No course dates found</h5>
                            <p class="text-muted">Try adjusting your filters or create a new course date.</p>
                            <a href="{{ route('admin.course-dates.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Course Date
                            </a>
                        </div>
                    @endif
                </div>
                @if($content['course_dates']->hasPages())
                    <div class="card-footer">
                        {{ $content['course_dates']->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this course date? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box .inner h3 {
            font-size: 2.2rem;
        }
        .table td {
            vertical-align: middle;
        }
        .btn-group-sm > .btn {
            margin-right: 2px;
        }
        .badge-sm {
            font-size: 0.7em;
        }
        /* Custom grid for 5-column layout */
        @media (min-width: 992px) {
            .col-lg-2-4 {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Toggle active status
            $('.toggle-active-btn').on('click', function() {
                const btn = $(this);
                const courseId = btn.data('id');
                const isActive = btn.data('active');

                $.ajax({
                    url: `/admin/course-dates/${courseId}/toggle-active`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload(); // Refresh to update status
                            toastr.success(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Error updating status');
                    }
                });
            });

            // Delete confirmation
            $('.delete-btn').on('click', function() {
                const courseId = $(this).data('id');
                $('#deleteForm').attr('action', `/admin/course-dates/${courseId}`);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@stop
