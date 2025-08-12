@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mt-3 admin-dark-card">
                <div class="card-header bg-dark" style="margin: 0 !important; padding: 15px 15px !important;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="card-title text-white mb-0">
                                <i class="fas fa-graduation-cap mr-2"></i>Course Management
                            </h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="#" class="btn btn-primary btn-sm" id="toggle-filters-btn" onclick="toggleFilters()">
                                <i class="fas fa-filter mr-1"></i><span id="filter-btn-text">Show Filters</span>
                            </a>
                            @if($content['permissions']['can_manage'] ?? false)
                                <a href="{{ route('admin.courses.management.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Add New Course
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="px-3 py-3 border-bottom admin-dark-filter filter-section hidden" id="filter-section">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label for="course-type-filter" class="text-white-50 mb-1">Course Type</label>
                            <select class="form-control form-control-sm" id="course-type-filter">
                                <option value="">All Course Types</option>
                                <option value="D" {{ request('course_type') == 'D' ? 'selected' : '' }}>D Course (5-day, Weekly)</option>
                                <option value="G" {{ request('course_type') == 'G' ? 'selected' : '' }}>G Course (3-day, Biweekly)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status-filter" class="text-white-50 mb-1">Status</label>
                            <select class="form-control form-control-sm" id="status-filter">
                                <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-white-50">
                                <i class="fas fa-info-circle"></i>
                                Showing {{ $content['stats']['total'] ?? 0 }} total courses
                                ({{ $content['stats']['active'] ?? 0 }} active, {{ $content['stats']['archived'] ?? 0 }} archived)
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Course Statistics Cards -->
                    <div class="row mx-2 my-3">
                        <div class="col-md-3">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $content['stats']['d_courses'] ?? 0 }}</h3>
                                    <p>D Courses (Active)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $content['stats']['g_courses'] ?? 0 }}</h3>
                                    <p>G Courses (Active)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $content['stats']['active'] ?? 0 }}</h3>
                                    <p>Total Active</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $content['stats']['archived'] ?? 0 }}</h3>
                                    <p>Archived</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-archive"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Courses Table -->
                    <div class="table-responsive">
                        <table id="courses-table" class="table table-bordered table-striped table-hover mb-0 table-dark admin-dark-table"
                            style="width: 100%;">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Course Type</th>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                    <th>Frequency</th>
                                    <th>Status</th>
                                    <th>Enrollments</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($content['courses'] ?? [] as $course)
                                    <tr>
                                        <td>{{ $course->id }}</td>
                                        <td>
                                            <span class="badge badge-{{ $course->getCourseTypeBadgeColor() }}">
                                                {{ $course->getCourseType() }} Course
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $course->title }}</strong>
                                            @if($course->title_long)
                                                <br><small class="text-muted">{{ Str::limit($course->title_long, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>${{ number_format($course->price, 2) }}</td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{ $course->getDurationDays() }} days
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-outline-secondary">
                                                {{ ucfirst($course->getFrequencyType()) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($course->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Archived</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $course->CourseAuths()->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.courses.management.show', $course) }}"
                                                   class="btn btn-outline-primary btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($content['permissions']['can_manage'] ?? false)
                                                    <a href="{{ route('admin.courses.management.edit', $course) }}"
                                                       class="btn btn-outline-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if($content['permissions']['can_delete'] ?? false)
                                                    <button onclick="deleteCourse({{ $course->id }})"
                                                            class="btn btn-outline-danger btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="fas fa-graduation-cap fa-3x mb-3"></i><br>
                                            No courses found matching your criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($content['courses']->hasPages())
                <div class="card-footer">
                    {{ $content['courses']->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('css')
    @vite('resources/css/admin.css')
    <style>
        .filter-section.hidden {
            display: none;
        }
        .filter-section.visible {
            display: block;
        }
        .small-box .inner h3 {
            font-size: 2.2rem;
        }
    </style>
@stop

@section('js')
    <script>
        function toggleFilters() {
            const filterSection = document.getElementById('filter-section');
            const filterBtn = document.getElementById('filter-btn-text');
            const filterIcon = document.querySelector('#toggle-filters-btn i');

            if (filterSection.classList.contains('hidden')) {
                filterSection.classList.remove('hidden');
                filterSection.classList.add('visible');
                filterBtn.textContent = 'Hide Filters';
                filterIcon.className = 'fas fa-filter mr-1';
            } else {
                filterSection.classList.remove('visible');
                filterSection.classList.add('hidden');
                filterBtn.textContent = 'Show Filters';
                filterIcon.className = 'fas fa-filter-slash mr-1';
            }
        }

        // Filter change handlers
        document.getElementById('course-type-filter').addEventListener('change', function() {
            updateFilters();
        });

        document.getElementById('status-filter').addEventListener('change', function() {
            updateFilters();
        });

        function updateFilters() {
            const courseType = document.getElementById('course-type-filter').value;
            const status = document.getElementById('status-filter').value;

            const url = new URL(window.location);

            if (courseType) {
                url.searchParams.set('course_type', courseType);
            } else {
                url.searchParams.delete('course_type');
            }

            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }

            window.location.href = url.toString();
        }

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
                        url: "{{ url('admin/courses/management') }}/" + id + "/archive",
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
                        url: "{{ url('admin/courses/management') }}/" + id + "/restore",
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

        function deleteCourse(id) {
            Swal.fire({
                title: 'Delete Course?',
                text: "This action cannot be undone! Are you sure you want to permanently delete this course?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Use the management index route and append the course ID
                    let deleteUrl = "{{ url('admin/courses/management') }}/" + id;

                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.message, 'success');
                                location.reload();
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                const response = xhr.responseJSON;
                                Swal.fire('Cannot Delete!', response.message, 'warning');
                            } else {
                                Swal.fire('Error!', 'Something went wrong!', 'error');
                            }
                        }
                    });
                }
            });
        }
    </script>
@stop
