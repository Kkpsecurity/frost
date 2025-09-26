@extends('adminlte::page')

@section('title', 'Course Management')

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
@endsection

@section('content_header')
    <x-admin.partials.titlebar
        title="Course Management"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Courses']
        ]"
    />
@endsection

@section('content')
    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($stats['total_courses']) }}</h3>
                    <p>Total Courses</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($stats['active_courses']) }}</h3>
                    <p>Active Courses</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($stats['course_authorizations']) }}</h3>
                    <p>Total Enrollments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>${{ number_format($stats['revenue'], 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Course Management Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Courses
            </h3>
            <div class="card-tools">
                <a href="{{ route('admin.courses.manage.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Course
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Filters --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="status-filter">Status Filter:</label>
                    <select id="status-filter" class="form-control form-control-sm">
                        <option value="all">All Courses</option>
                        <option value="active">Active Only</option>
                        <option value="archived">Archived Only</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="course-type-filter">Course Type:</label>
                    <select id="course-type-filter" class="form-control form-control-sm">
                        <option value="all">All Types</option>
                        <option value="D">D Course (5-day)</option>
                        <option value="G">G Course (3-day)</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label>&nbsp;</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="search-courses" placeholder="Search courses...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="clear-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DataTable --}}
            <div class="table-responsive">
                <table id="courses-table" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course Title</th>
                            <th>Price</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Enrollments</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data loaded via AJAX --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Additional Stats --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Course Type Distribution
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-calendar-week"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">D Courses (5-day)</span>
                                    <span class="info-box-number d-courses-count">Loading...</span>
                                    <span class="progress-description">Weekly intensive courses</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">G Courses (3-day)</span>
                                    <span class="info-box-number g-courses-count">Loading...</span>
                                    <span class="progress-description">Biweekly compact courses</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-graduation-cap"></i> Completion Statistics
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">In Progress</span>
                                    <span class="info-box-number">{{ number_format($stats['course_authorizations'] - $stats['completed_courses']) }}</span>
                                    <span class="progress-description">Active enrollments</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-trophy"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Completed</span>
                                    <span class="info-box-number">{{ number_format($stats['completed_courses']) }}</span>
                                    <span class="progress-description">Finished courses</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#courses-table').DataTable({
            "processing": true,
            "serverSide": false,
            "ajax": "{{ route('admin.courses.data') }}",
            "columns": [
                { "data": "id", "name": "id" },
                { "data": "title", "name": "title" },
                { "data": "price", "name": "price", "render": function(data) { return '$' + parseFloat(data).toFixed(2); } },
                { "data": "type", "name": "type" },
                { "data": "status", "name": "status" },
                { "data": "enrollments", "name": "enrollments" },
                { "data": "actions", "name": "actions", "orderable": false, "searchable": false }
            ],
            "order": [[0, "desc"]],
            "pageLength": 25,
            "language": {
                "processing": "Loading courses...",
                "emptyTable": "No courses found",
                "info": "Showing _START_ to _END_ of _TOTAL_ courses",
                "infoEmpty": "Showing 0 to 0 of 0 courses"
            }
        });

        // Filter functionality
        $('#status-filter').on('change', function() {
            var status = this.value;
            if (status === 'all') {
                table.column(4).search('').draw();
            } else {
                table.column(4).search(status).draw();
            }
        });

        $('#course-type-filter').on('change', function() {
            var type = this.value;
            if (type === 'all') {
                table.column(3).search('').draw();
            } else {
                table.column(3).search(type).draw();
            }
        });

        $('#search-courses').on('keyup', function() {
            table.search(this.value).draw();
        });

        $('#clear-search').on('click', function() {
            $('#search-courses').val('');
            table.search('').draw();
        });

        // Archive/Unarchive functionality
        $(document).on('click', '.archive-btn', function(e) {
            e.preventDefault();
            var courseId = $(this).data('id');
            var isArchived = $(this).data('archived') === 1;
            var action = isArchived ? 'unarchive' : 'archive';
            var actionText = isArchived ? 'restore' : 'archive';

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to ${actionText} this course?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Yes, ${actionText} it!`
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/courses/${courseId}/${action}`,
                        type: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Success!',
                                    `Course has been ${actionText}d.`,
                                    'success'
                                );
                                table.ajax.reload(null, false);
                                setTimeout(() => location.reload(), 1500);
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Update course type statistics
        function updateCourseTypeStats() {
            $.ajax({
                url: '{{ route('admin.courses.data') }}',
                data: { course_type: 'D', length: -1 },
                success: function(response) {
                    $('.d-courses-count').text(response.recordsTotal || 0);
                }
            });

            $.ajax({
                url: '{{ route('admin.courses.data') }}',
                data: { course_type: 'G', length: -1 },
                success: function(response) {
                    $('.g-courses-count').text(response.recordsTotal || 0);
                }
            });
        }

        // Load course type stats on page load
        updateCourseTypeStats();
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

    @if(session('error'))
    <script>
    $(document).ready(function() {
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error'
        });
    });
    </script>
    @endif
@endsection
