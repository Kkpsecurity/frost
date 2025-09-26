@section('css')
    <style>
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .course-selector-card .card-body {
            padding: 1rem;
        }

        .form-inline .form-group {
            align-items: center;
        }

        @media (max-width: 768px) {
            .form-inline {
                flex-direction: column;
                align-items: stretch;
            }

            .form-inline .form-group {
                margin-bottom: 1rem;
                margin-right: 0 !important;
            }
        }
    </style>
@endsection

@extends('adminlte::page')

@section('title', 'Course Enrollments - ' . $course->title)

@section('content_header')
    <x-admin.partials.titlebar
        title="Course Enrollments"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Courses', 'url' => route('admin.courses.dashboard')],
            ['title' => $course->title, 'url' => route('admin.courses.manage.view', $course)],
            ['title' => 'Enrollments']
        ]"
    />
@endsection

@section('content')
    {{-- Course Selector --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card course-selector-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Course Selector
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.courses.manage.enrollments', $course) }}" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="course-select" class="mr-2">Select Course:</label>
                            <select id="course-select" name="course_id" class="form-control">
                                @foreach($allCourses ?? [] as $availableCourse)
                                    <option value="{{ $availableCourse->id }}"
                                            {{ $availableCourse->id == $course->id ? 'selected' : '' }}>
                                        {{ $availableCourse->title }}
                                        ({{ $availableCourse->course_auths_count ?? 0 }} enrollments)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> View Enrollments
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Course Information Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book"></i> {{ $course->title }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.courses.manage.view', $course) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Course
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Course Type:</strong>
                            <span class="badge badge-{{ $course->type == 'D' ? 'primary' : 'success' }}">
                                {{ $course->type }} Course
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>Price:</strong>
                            ${{ number_format((float)$course->price ?? 0, 2) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Total Enrollments:</strong>
                            {{ $enrollments->total() }}
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong>
                            <span class="badge badge-{{ $course->is_active ? 'success' : 'secondary' }}">
                                {{ $course->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     {{-- Enrollment Statistics --}}
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Enrollments</span>
                    <span class="info-box-number">{{ $enrollments->total() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completed</span>
                    <span class="info-box-number">{{ $enrollments->where('completed_at', '!=', null)->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">In Progress</span>
                    <span class="info-box-number">{{ $enrollments->where('completed_at', null)->where('disabled_at', null)->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-ban"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Disabled</span>
                    <span class="info-box-number">{{ $enrollments->where('disabled_at', '!=', null)->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Enrollments Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users"></i> Course Enrollments ({{ $enrollments->total() }} total)
            </h3>
        </div>
        <div class="card-body">
            @if($enrollments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Enrollment Date</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Order Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment->id }}</td>
                                <td>
                                    @if($enrollment->User)
                                        {{ $enrollment->User->fullname() }}
                                    @else
                                        <span class="text-muted">User not found</span>
                                    @endif
                                </td>
                                <td>
                                    @if($enrollment->User)
                                        <a href="mailto:{{ $enrollment->User->email }}">{{ $enrollment->User->email }}</a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($enrollment->created_at)->format('M d, Y g:i A') }}</td>
                                <td>
                                    @if($enrollment->completed_at)
                                        <span class="badge badge-success">Completed</span>
                                        <small class="d-block text-muted">{{ \Carbon\Carbon::parse($enrollment->completed_at)->format('M d, Y') }}</small>
                                    @elseif($enrollment->disabled_at)
                                        <span class="badge badge-danger">Disabled</span>
                                        <small class="d-block text-muted">{{ \Carbon\Carbon::parse($enrollment->disabled_at)->format('M d, Y') }}</small>
                                    @else
                                        <span class="badge badge-warning">In Progress</span>
                                    @endif
                                </td>
                                <td>
                                    @if($enrollment->is_passed)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Passed
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-clock"></i> In Progress
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($enrollment->Order)
                                        <small>
                                            Order #{{ $enrollment->Order->id }}<br>
                                            ${{ number_format($enrollment->Order->total_price, 2) }}<br>
                                            @if($enrollment->Order->completed_at)
                                                <span class="text-success">Paid</span>
                                            @else
                                                <span class="text-warning">Pending</span>
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">No order</span>
                                    @endif
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="pagination-info">
                        Showing {{ $enrollments->firstItem() ?? 0 }} to {{ $enrollments->lastItem() ?? 0 }}
                        of {{ $enrollments->total() }} results
                    </div>
                    <div class="pagination-links">
                        {{ $enrollments->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5>No Enrollments Found</h5>
                    <p class="text-muted">This course doesn't have any student enrollments yet.</p>
                </div>
            @endif
        </div>
    </div>


@endsection

@section('css')
    <style>
        .info-box {
            margin-bottom: 0;
        }

        /* Course selector styling */
        #course-select {
            min-width: 300px;
        }

        /* Pagination styling */
        .pagination-info {
            color: #6c757d;
            font-size: 14px;
        }

        .pagination-links .pagination {
            margin: 0;
        }

        .pagination-links .page-link {
            color: #007bff;
            background-color: #fff;
            border: 1px solid #dee2e6;
        }

        .pagination-links .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

        .pagination-links .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
        }

        /* Make pagination responsive */
        @media (max-width: 768px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 15px;
            }

            .pagination-info {
                text-align: center;
            }
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Course selector enhancement
            $('#course-select').on('change', function() {
                const selectedCourseId = $(this).val();
                if (selectedCourseId && selectedCourseId != {{ $course->id }}) {
                    // Show loading state
                    $(this).prop('disabled', true);
                    $('body').append('<div class="overlay"><div class="text-center"><i class="fas fa-3x fa-sync-alt fa-spin"></i><br><strong>Loading enrollments...</strong></div></div>');

                    // Use Laravel route for better URL generation
                    const url = "{{ route('admin.courses.manage.enrollments', ':courseId') }}".replace(':courseId', selectedCourseId);
                    window.location.href = url;
                }
            });

            // Initialize tooltips
            $('[title]').tooltip();

            // Add confirmation for disabled students
            $('.btn-danger').on('click', function(e) {
                e.preventDefault();
                const action = $(this).data('action') || 'perform this action';
                if (confirm(`Are you sure you want to ${action}?`)) {
                    // Perform action
                    window.location.href = $(this).attr('href');
                }
            });
        });
    </script>
@endsection
