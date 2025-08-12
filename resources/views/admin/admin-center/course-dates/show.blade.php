@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Course Date Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> {{ $content['title'] }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.course-dates.edit', $content['course_date']) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Course Information -->
                        <div class="col-md-6">
                            <h5><i class="fas fa-book"></i> Course Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Course:</strong></td>
                                    <td>{{ $content['course_date']->CourseUnit->Course->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Unit:</strong></td>
                                    <td>{{ $content['course_date']->CourseUnit->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Description:</strong></td>
                                    <td>{{ $content['course_date']->CourseUnit->Course->description ?: 'No description available' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Needs Range:</strong></td>
                                    <td>
                                        @if($content['course_date']->CourseUnit->Course->needs_range)
                                            <span class="badge badge-warning">Yes</span>
                                        @else
                                            <span class="badge badge-success">No</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Schedule Information -->
                        <div class="col-md-6">
                            <h5><i class="fas fa-clock"></i> Schedule Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Start:</strong></td>
                                    <td>
                                        <div>{{ \Carbon\Carbon::parse($content['course_date']->starts_at)->format('l, F j, Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($content['course_date']->starts_at)->format('g:i A') }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>End:</strong></td>
                                    <td>
                                        <div>{{ \Carbon\Carbon::parse($content['course_date']->ends_at)->format('l, F j, Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($content['course_date']->ends_at)->format('g:i A') }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>
                                        @php
                                            $duration = \Carbon\Carbon::parse($content['course_date']->starts_at)->diffInMinutes(\Carbon\Carbon::parse($content['course_date']->ends_at));
                                            $hours = intval($duration / 60);
                                            $minutes = $duration % 60;
                                        @endphp
                                        {{ $hours }} hours and {{ $minutes }} minutes
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($content['course_date']->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif

                                        @php
                                            $now = now();
                                            $start = \Carbon\Carbon::parse($content['course_date']->starts_at);
                                            $end = \Carbon\Carbon::parse($content['course_date']->ends_at);
                                        @endphp

                                        @if($now < $start)
                                            <span class="badge badge-warning ml-1">Upcoming</span>
                                        @elseif($now >= $start && $now <= $end)
                                            <span class="badge badge-primary ml-1">In Progress</span>
                                        @else
                                            <span class="badge badge-secondary ml-1">Completed</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Instructor Information -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5><i class="fas fa-user-tie"></i> Instructor Information</h5>
                            @if($content['course_date']->InstUnit && $content['course_date']->InstUnit->User)
                                <div class="card card-outline card-info">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h6>{{ $content['course_date']->InstUnit->User->fname }} {{ $content['course_date']->InstUnit->User->lname }}</h6>
                                                <p class="mb-1"><strong>Email:</strong> {{ $content['course_date']->InstUnit->User->email }}</p>
                                                @if($content['course_date']->InstUnit->User->phone)
                                                    <p class="mb-1"><strong>Phone:</strong> {{ $content['course_date']->InstUnit->User->phone }}</p>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <span class="badge badge-info">Assigned</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> No instructor assigned to this course date.
                                    <a href="{{ route('admin.course-dates.edit', $content['course_date']) }}" class="btn btn-sm btn-warning ml-2">
                                        Assign Instructor
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Students Information -->
                        <div class="col-md-6">
                            <h5><i class="fas fa-users"></i> Enrolled Students ({{ $content['course_date']->StudentUnits->count() }})</h5>
                            @if($content['course_date']->StudentUnits->count() > 0)
                                <div class="card card-outline card-success">
                                    <div class="card-body p-2">
                                        <div class="table-responsive" style="max-height: 200px;">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($content['course_date']->StudentUnits as $studentUnit)
                                                        <tr>
                                                            <td>{{ $studentUnit->User->fname }} {{ $studentUnit->User->lname }}</td>
                                                            <td>{{ $studentUnit->User->email }}</td>
                                                            <td>
                                                                <span class="badge badge-sm badge-{{ $studentUnit->is_active ? 'success' : 'secondary' }}">
                                                                    {{ $studentUnit->is_active ? 'Active' : 'Inactive' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No students enrolled yet.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($content['course_date']->notes)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5><i class="fas fa-sticky-note"></i> Notes</h5>
                                <div class="alert alert-secondary">
                                    {{ $content['course_date']->notes }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <strong>Created:</strong> {{ $content['course_date']->created_at->format('M j, Y \a\t g:i A') }}
                                @if($content['course_date']->updated_at != $content['course_date']->created_at)
                                    <br><strong>Last Updated:</strong> {{ $content['course_date']->updated_at->format('M j, Y \a\t g:i A') }}
                                @endif
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.course-dates.edit', $content['course_date']) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button"
                                        class="btn btn-{{ $content['course_date']->is_active ? 'secondary' : 'success' }} btn-sm"
                                        id="toggle-status-btn"
                                        data-id="{{ $content['course_date']->id }}"
                                        data-active="{{ $content['course_date']->is_active ? 1 : 0 }}">
                                    <i class="fas fa-{{ $content['course_date']->is_active ? 'pause' : 'play' }}"></i>
                                    {{ $content['course_date']->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                @if($content['course_date']->StudentUnits->count() == 0)
                                    <button type="button" class="btn btn-danger btn-sm" id="delete-btn" data-id="{{ $content['course_date']->id }}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                @endif
                            </div>
                        </div>
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
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this course date?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Course Date</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table-borderless td {
            border: none;
            padding: 0.3rem 0.75rem;
        }
        .card-outline {
            border-width: 2px;
        }
        .badge-sm {
            font-size: 0.7em;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Toggle active status
            $('#toggle-status-btn').on('click', function() {
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
            $('#delete-btn').on('click', function() {
                const courseId = $(this).data('id');
                $('#deleteForm').attr('action', `/admin/course-dates/${courseId}`);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@stop
