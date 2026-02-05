@extends('adminlte::page')

@section('title', 'Students Management')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-user-graduate"></i> Students Management</h1>
        <div>
            <button class="btn btn-primary" onclick="exportStudents()">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-2 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Total Students</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['active'] }}</h3>
                    <p>Active</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['inactive'] }}</h3>
                    <p>Inactive</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['enrolled'] }}</h3>
                    <p>Enrolled</p>
                </div>
                <div class="icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ $stats['verified'] }}</h3>
                    <p>Verified</p>
                </div>
                <div class="icon">
                    <i class="fas fa-envelope-open"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3>{{ $stats['new_this_month'] }}</h3>
                    <p>New This Month</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Advanced Filters</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <form action="{{ route('admin.students.index') }}" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Name, Email, or ID..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Email Verified</label>
                            <select name="verified" class="form-control">
                                <option value="all" {{ request('verified') === 'all' ? 'selected' : '' }}>All</option>
                                <option value="verified" {{ request('verified') === 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="unverified" {{ request('verified') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Enrollment</label>
                            <select name="enrollment" class="form-control">
                                <option value="all" {{ request('enrollment') === 'all' ? 'selected' : '' }}>All</option>
                                <option value="enrolled" {{ request('enrollment') === 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                <option value="not_enrolled" {{ request('enrollment') === 'not_enrolled' ? 'selected' : '' }}>Not Enrolled</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date Range</label>
                            <div class="input-group">
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="max-width: 48%;">
                                <span class="input-group-text">to</span>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="max-width: 48%;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Students</h3>
            <div class="card-tools">
                <button class="btn btn-sm btn-primary" onclick="toggleBulkActions()">
                    <i class="fas fa-tasks"></i> Bulk Actions
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Bulk Actions Bar (hidden by default) -->
            <div id="bulkActionsBar" style="display: none;" class="mb-3 p-3 bg-light border rounded">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <span id="selectedCount">0</span> student(s) selected
                    </div>
                    <div class="col-md-8 text-right">
                        <button class="btn btn-success btn-sm" onclick="bulkActivate()">
                            <i class="fas fa-check"></i> Activate Selected
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="bulkDeactivate()">
                            <i class="fas fa-ban"></i> Deactivate Selected
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="clearSelection()">
                            <i class="fas fa-times"></i> Clear Selection
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped" id="studentsTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" class="bulk-checkbox-master">
                            </th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Courses</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>
                                    <input type="checkbox" class="bulk-checkbox" value="{{ $student->id }}">
                                </td>
                                <td><strong>#{{ $student->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle mr-2">
                                            {{ strtoupper(substr($student->fname, 0, 1) . substr($student->lname, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $student->fname }} {{ $student->lname }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $student->email }}">{{ $student->email }}</a>
                                </td>
                                <td>
                                    @if($student->courseAuths->count() > 0)
                                        <span class="badge badge-primary">
                                            {{ $student->courseAuths->count() }}
                                        </span>
                                        <small class="text-muted d-block">
                                            {{ $student->courseAuths->where('completed_at', '!=', null)->count() }} completed
                                        </small>
                                    @else
                                        <span class="badge badge-secondary">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if($student->is_active)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($student->email_verified_at)
                                        <span class="badge badge-success" title="{{ $student->email_verified_at->format('M d, Y H:i') }}">
                                            <i class="fas fa-envelope-open"></i> Yes
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-envelope"></i> No
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $student->created_at->format('M d, Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $student->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.students.show', $student->id) }}"
                                           class="btn btn-info"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.students.edit', $student->id) }}"
                                           class="btn btn-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-primary"
                                                onclick="quickToggleStatus({{ $student->id }}, {{ $student->is_active ? 'false' : 'true' }})"
                                                title="{{ $student->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $student->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                    <p class="mb-0">No students found.</p>
                                    @if(request()->has('search') || request()->has('status'))
                                        <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-secondary mt-2">
                                            Clear Filters
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($students->hasPages())
            <div class="card-footer clearfix">
                <div class="float-left">
                    Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                </div>
                <div class="float-right">
                    {{ $students->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
@stop

@section('css')
    <style>
        .avatar-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        .table td {
            vertical-align: middle;
        }
        .small-box {
            border-radius: 10px;
        }
        .bg-purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
        }
        .bg-teal {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
            color: white !important;
        }
        .input-group-text {
            padding: 0.375rem 0.75rem;
        }
    </style>
@stop

@section('js')
    <script>
        // Bulk actions functionality
        let selectedStudents = [];

        function toggleBulkActions() {
            const bar = $('#bulkActionsBar');
            bar.toggle();
            if (!bar.is(':visible')) {
                clearSelection();
            }
        }

        $('#selectAll').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.bulk-checkbox').prop('checked', isChecked);
            updateSelectedStudents();
        });

        $(document).on('change', '.bulk-checkbox', function() {
            updateSelectedStudents();
        });

        function updateSelectedStudents() {
            selectedStudents = [];
            $('.bulk-checkbox:checked').each(function() {
                selectedStudents.push($(this).val());
            });
            $('#selectedCount').text(selectedStudents.length);
            $('#bulkActionsBar').toggle(selectedStudents.length > 0);
        }

        function clearSelection() {
            $('.bulk-checkbox, #selectAll').prop('checked', false);
            updateSelectedStudents();
        }

        function bulkActivate() {
            bulkStatusUpdate(true);
        }

        function bulkDeactivate() {
            bulkStatusUpdate(false);
        }

        function bulkStatusUpdate(status) {
            if (selectedStudents.length === 0) {
                alert('Please select at least one student');
                return;
            }

            const action = status ? 'activate' : 'deactivate';
            if (!confirm(`Are you sure you want to ${action} ${selectedStudents.length} student(s)?`)) {
                return;
            }

            $.ajax({
                url: '{{ route("admin.students.bulk-status") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    student_ids: selectedStudents,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to update students');
                }
            });
        }

        function quickToggleStatus(studentId, newStatus) {
            $.ajax({
                url: '{{ route("admin.students.bulk-status") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    student_ids: [studentId],
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    alert('Failed to update student status');
                }
            });
        }

        function exportStudents() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '{{ route("admin.students.export") }}?' + params.toString();
        }

        // Auto-submit search on clear
        $('input[name="search"]').on('search', function() {
            if (this.value === '') {
                $(this).closest('form').submit();
            }
        });

        // Initialize tooltips
        $('[title]').tooltip();
    </script>
@stop

