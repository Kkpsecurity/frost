@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-calendar-alt"></i>
            {{ $content['title'] }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.course-dates.calendar') }}" class="btn btn-success">
                <i class="fas fa-calendar"></i>
                Calendar View
            </a>
            <a href="{{ route('admin.course-dates.generator') }}" class="btn btn-info">
                <i class="fas fa-magic"></i>
                Auto Generator
            </a>
            <a href="{{ route('admin.course-dates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Create Course Date
            </a>
        </div>
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

        <!-- Statistics Cards -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $content['stats']['total'] }}</h3>
                        <p>Total Course Dates</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
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
            <div class="col-lg-3 col-6">
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
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ $content['stats']['today'] }}</h3>
                        <p>Today</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i>
                    Filters & Search
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.course-dates.index') }}" id="filters-form">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="course_id">Course</label>
                                <select name="course_id" id="course_id" class="form-control select2">
                                    <option value="">All Courses</option>
                                    @foreach($content['courses'] as $course)
                                        <option value="{{ $course->id }}"
                                            {{ $content['filters']['course_id'] == $course->id ? 'selected' : '' }}>
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
                                    <option value="all" {{ $content['filters']['status'] == 'all' ? 'selected' : '' }}>All Status</option>
                                    <option value="active" {{ $content['filters']['status'] == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $content['filters']['status'] == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="upcoming" {{ $content['filters']['status'] == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    <option value="current" {{ $content['filters']['status'] == 'current' ? 'selected' : '' }}>Current</option>
                                    <option value="past" {{ $content['filters']['status'] == 'past' ? 'selected' : '' }}>Past</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_range">Date Range</label>
                                <select name="date_range" id="date_range" class="form-control">
                                    <option value="all" {{ $content['filters']['date_range'] == 'all' ? 'selected' : '' }}>All Dates</option>
                                    <option value="today" {{ $content['filters']['date_range'] == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="week" {{ $content['filters']['date_range'] == 'week' ? 'selected' : '' }}>This Week</option>
                                    <option value="month" {{ $content['filters']['date_range'] == 'month' ? 'selected' : '' }}>This Month</option>
                                    <option value="year" {{ $content['filters']['date_range'] == 'year' ? 'selected' : '' }}>This Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="instructor_id">Instructor</label>
                                <select name="instructor_id" id="instructor_id" class="form-control select2">
                                    <option value="">All Instructors</option>
                                    @foreach($content['instructors'] as $instructor)
                                        <option value="{{ $instructor->id }}"
                                            {{ $content['filters']['instructor_id'] == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->fname }} {{ $instructor->lname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" name="search" id="search" class="form-control"
                                       placeholder="Course title..."
                                       value="{{ $content['filters']['search'] }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                Apply Filters
                            </button>
                            <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Clear Filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Course Dates Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Course Dates ({{ $content['course_dates']->total() }} total)
                </h3>
                <div class="card-tools">
                    @if($content['course_dates']->count() > 0)
                        <button type="button" class="btn btn-sm btn-danger" id="bulk-delete-btn" disabled>
                            <i class="fas fa-trash"></i>
                            Delete Selected
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle"
                                    data-toggle="dropdown" id="bulk-actions-btn" disabled>
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" id="bulk-activate">
                                    <i class="fas fa-check"></i> Activate Selected
                                </a>
                                <a class="dropdown-item" href="#" id="bulk-deactivate">
                                    <i class="fas fa-times"></i> Deactivate Selected
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                @if($content['course_dates']->count() > 0)
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <div class="icheck-primary">
                                        <input type="checkbox" id="select-all">
                                        <label for="select-all"></label>
                                    </div>
                                </th>
                                <th>Course & Unit</th>
                                <th>Date & Time</th>
                                <th>Instructor</th>
                                <th>Status</th>
                                <th>Students</th>
                                <th style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($content['course_dates'] as $courseDate)
                            <tr>
                                <td>
                                    <div class="icheck-primary">
                                        <input type="checkbox" name="selected_dates[]"
                                               value="{{ $courseDate->id }}"
                                               id="check-{{ $courseDate->id }}"
                                               class="course-date-checkbox">
                                        <label for="check-{{ $courseDate->id }}"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="course-info">
                                        <strong>{{ $courseDate->CourseUnit->Course->title }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $courseDate->CourseUnit->title }}
                                            @if($courseDate->CourseUnit->day)
                                                (Day {{ $courseDate->CourseUnit->day }})
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-info">
                                        <strong>
                                            @if($courseDate->starts_at instanceof \Carbon\Carbon)
                                                {{ $courseDate->starts_at->format('M j, Y') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($courseDate->starts_at)->format('M j, Y') }}
                                            @endif
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            @if($courseDate->starts_at instanceof \Carbon\Carbon && $courseDate->ends_at instanceof \Carbon\Carbon)
                                                {{ $courseDate->starts_at->format('g:i A') }} -
                                                {{ $courseDate->ends_at->format('g:i A') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($courseDate->starts_at)->format('g:i A') }} -
                                                {{ \Carbon\Carbon::parse($courseDate->ends_at)->format('g:i A') }}
                                            @endif
                                        </small>
                                        @php
                                            $startDate = $courseDate->starts_at instanceof \Carbon\Carbon
                                                ? $courseDate->starts_at
                                                : \Carbon\Carbon::parse($courseDate->starts_at);
                                        @endphp
                                        @if($startDate->isToday())
                                            <br><span class="badge badge-warning">Today</span>
                                        @elseif($startDate->isTomorrow())
                                            <br><span class="badge badge-info">Tomorrow</span>
                                        @elseif($startDate->isPast())
                                            <br><span class="badge badge-secondary">Past</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($courseDate->InstUnit && $courseDate->InstUnit->User)
                                        <div class="instructor-info">
                                            <strong>{{ $courseDate->InstUnit->User->fname }} {{ $courseDate->InstUnit->User->lname }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $courseDate->InstUnit->User->email }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">No instructor assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($courseDate->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $course = $courseDate->CourseUnit->Course ?? null;
                                        // Count active CourseAuths (not completed) - students eligible for class
                                        $activeRegistered = $course ? $course->CourseAuths->whereNull('completed_at')->count() : 0;
                                        $hasStarted = $courseDate->InstUnit !== null;
                                        $actualAttending = $hasStarted ? $courseDate->StudentUnits->count() : 0;
                                    @endphp
                                    
                                    <div class="student-counts">
                                        <span class="badge badge-info">
                                            {{ $activeRegistered }} registered
                                        </span>
                                        <br>
                                        <span class="badge {{ $hasStarted ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $actualAttending }} attending
                                        </span>
                                        @if(!$hasStarted && $courseDate->StudentUnits->count() > 0)
                                            <br><small class="text-warning">⚠️ Stale data</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.course-dates.show', $courseDate) }}"
                                           class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.course-dates.edit', $courseDate) }}"
                                           class="btn btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-warning toggle-status-btn"
                                                data-id="{{ $courseDate->id }}"
                                                data-status="{{ $courseDate->is_active ? 'active' : 'inactive' }}"
                                                title="Toggle Status">
                                            <i class="fas fa-toggle-{{ $courseDate->is_active ? 'on' : 'off' }}"></i>
                                        </button>
                                        @if($courseDate->StudentUnits->count() == 0)
                                            <button type="button" class="btn btn-danger delete-btn"
                                                    data-id="{{ $courseDate->id }}"
                                                    data-title="{{ $courseDate->CourseUnit->Course->title }} - {{ $courseDate->CourseUnit->title }}"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-danger" disabled
                                                    title="Cannot delete - has enrolled students">
                                                <i class="fas fa-lock"></i>
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
                        <h4 class="text-muted">No Course Dates Found</h4>
                        <p class="text-muted">
                            @if(array_filter($content['filters']))
                                No course dates match your current filters.
                                <br>
                                <a href="{{ route('admin.course-dates.index') }}" class="btn btn-sm btn-primary mt-2">
                                    Clear Filters
                                </a>
                            @else
                                Get started by creating your first course date or using the auto generator.
                                <br>
                                <div class="mt-3">
                                    <a href="{{ route('admin.course-dates.create') }}" class="btn btn-primary mr-2">
                                        <i class="fas fa-plus"></i>
                                        Create Course Date
                                    </a>
                                    <a href="{{ route('admin.course-dates.generator') }}" class="btn btn-info">
                                        <i class="fas fa-magic"></i>
                                        Use Auto Generator
                                    </a>
                                </div>
                            @endif
                        </p>
                    </div>
                @endif
            </div>
            @if($content['course_dates']->hasPages())
                <div class="card-footer">
                    {{ $content['course_dates']->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Confirm Deletion
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this course date?</p>
                    <p><strong id="delete-title"></strong></p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete Course Date
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Forms -->
    <form id="bulk-delete-form" method="POST" action="{{ route('admin.course-dates.bulk.delete') }}" style="display: none;">
        @csrf
        <div id="bulk-delete-ids"></div>
    </form>

    <form id="bulk-toggle-form" method="POST" action="{{ route('admin.course-dates.bulk.toggle-active') }}" style="display: none;">
        @csrf
        <input type="hidden" name="action" id="bulk-action">
        <div id="bulk-toggle-ids"></div>
    </form>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Auto-submit form on filter change
    $('#course_id, #status, #date_range, #instructor_id').change(function() {
        $('#filters-form').submit();
    });

    // Select all checkbox functionality
    $('#select-all').change(function() {
        $('.course-date-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkButtons();
    });

    $('.course-date-checkbox').change(function() {
        updateBulkButtons();

        // Update select-all checkbox
        const totalCheckboxes = $('.course-date-checkbox').length;
        const checkedCheckboxes = $('.course-date-checkbox:checked').length;

        if (checkedCheckboxes === 0) {
            $('#select-all').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#select-all').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select-all').prop('indeterminate', true);
        }
    });

    function updateBulkButtons() {
        const checkedCount = $('.course-date-checkbox:checked').length;
        $('#bulk-delete-btn, #bulk-actions-btn').prop('disabled', checkedCount === 0);
    }

    // Delete button functionality
    $('.delete-btn').click(function() {
        const id = $(this).data('id');
        const title = $(this).data('title');

        $('#delete-title').text(title);
        $('#delete-form').attr('action', `/admin/course-dates/${id}`);
        $('#deleteModal').modal('show');
    });

    // Toggle status functionality
    $('.toggle-status-btn').click(function() {
        const btn = $(this);
        const id = btn.data('id');
        const currentStatus = btn.data('status');

        btn.prop('disabled', true);

        $.ajax({
            url: `/admin/course-dates/${id}/toggle-active`,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const newStatus = response.is_active ? 'active' : 'inactive';
                    const icon = response.is_active ? 'on' : 'off';

                    btn.data('status', newStatus);
                    btn.find('i').removeClass('fa-toggle-on fa-toggle-off').addClass(`fa-toggle-${icon}`);

                    // Update status badge in the same row
                    const row = btn.closest('tr');
                    const statusCell = row.find('td:nth-child(5)');
                    const badge = response.is_active ?
                        '<span class="badge badge-success">Active</span>' :
                        '<span class="badge badge-secondary">Inactive</span>';
                    statusCell.html(badge);

                    // Show success message
                    toastr.success(response.message);
                } else {
                    toastr.error('Failed to toggle status');
                }
            },
            error: function() {
                toastr.error('Error occurred while toggling status');
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });

    // Bulk delete functionality
    $('#bulk-delete-btn').click(function() {
        const selectedIds = $('.course-date-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) return;

        if (confirm(`Are you sure you want to delete ${selectedIds.length} course date(s)? This action cannot be undone.`)) {
            let idsHtml = '';
            selectedIds.forEach(id => {
                idsHtml += `<input type="hidden" name="course_date_ids[]" value="${id}">`;
            });

            $('#bulk-delete-ids').html(idsHtml);
            $('#bulk-delete-form').submit();
        }
    });

    // Bulk activate/deactivate functionality
    $('#bulk-activate, #bulk-deactivate').click(function(e) {
        e.preventDefault();

        const action = $(this).attr('id') === 'bulk-activate' ? 'activate' : 'deactivate';
        const selectedIds = $('.course-date-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) return;

        const actionText = action === 'activate' ? 'activate' : 'deactivate';

        if (confirm(`Are you sure you want to ${actionText} ${selectedIds.length} course date(s)?`)) {
            let idsHtml = '';
            selectedIds.forEach(id => {
                idsHtml += `<input type="hidden" name="course_date_ids[]" value="${id}">`;
            });

            $('#bulk-toggle-ids').html(idsHtml);
            $('#bulk-action').val(action);
            $('#bulk-toggle-form').submit();
        }
    });
});
</script>
@stop

@section('css')
<style>
.course-info strong {
    color: #495057;
}

.date-info strong {
    color: #495057;
}

.instructor-info strong {
    color: #495057;
}

.small-box .inner h3 {
    font-size: 2.2rem;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm > .btn, .btn-sm {
    padding: .375rem .5rem;
    font-size: .875rem;
}

.course-date-checkbox {
    transform: scale(1.2);
}

.student-counts {
    min-width: 120px;
}

.student-counts .badge {
    display: inline-block;
    min-width: 80px;
    margin-bottom: 2px;
}

.student-counts small.text-warning {
    font-size: 0.7rem;
    font-style: italic;
}
</style>
@stop
