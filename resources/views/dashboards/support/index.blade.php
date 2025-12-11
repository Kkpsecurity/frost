@extends('adminlte::page')

@section('title', 'Support Dashboard')

@section('content_header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="fas fa-headset text-info"></i>
                Support Dashboard
            </h1>
            <p class="text-muted mb-0">Quick access to support tools and student assistance</p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-success">
                    <i class="fas fa-phone"></i> Available
                </button>
                <button type="button" class="btn btn-outline-info">
                    <i class="fas fa-clock"></i> {{ now()->format('g:i A') }}
                </button>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row justify-content-center">
        {{-- Main Student Search Section --}}
        <div class="col-lg-8">
            {{-- Student Search Card --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search"></i>
                        How can I help you?
                    </h3>
                    <p class="card-subtitle text-muted mb-0">Search for a student to get started</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group input-group-lg">
                                <input type="text" id="student-search-input" class="form-control"
                                       placeholder="Search by name, email, or student ID...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="search-students">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control form-control-lg" id="course-filter">
                                <option value="">All Courses</option>
                                <option value="security-fundamentals">Security Fundamentals</option>
                                <option value="advanced-security">Advanced Security</option>
                                <option value="cyber-defense">Cyber Defense</option>
                            </select>
                        </div>
                    </div>

                    {{-- Search Results --}}
                    <div id="search-results" class="mt-4" style="display: none;">
                        <hr>
                        <h5 class="mb-3">
                            <i class="fas fa-users text-muted"></i>
                            Search Results
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Course Status</th>
                                        <th>Class Status</th>
                                        <th>Last Activity</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="search-results-body">
                                    {{-- Results will be populated via JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Loading State --}}
                    <div id="search-loading" class="text-center mt-4" style="display: none;">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted mb-2"></i>
                        <p class="text-muted">Searching students...</p>
                    </div>

                    {{-- No Results State --}}
                    <div id="no-results" class="text-center mt-4" style="display: none;">
                        <i class="fas fa-search fa-2x text-muted mb-3"></i>
                        <h5 class="text-muted">No students found</h5>
                        <p class="text-muted">Try adjusting your search terms or course filter.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        /* Main search card styling */
        .card-header {
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
            color: white;
            border-bottom: none;
        }

        .card-header .card-title {
            color: white;
            font-weight: 600;
        }

        .card-subtitle {
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 0.9rem;
        }

        /* Search input styling */
        .input-group-lg .form-control {
            font-size: 1.1rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem 0 0 0.5rem;
        }

        .input-group-lg .btn {
            font-size: 1.1rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0 0.5rem 0.5rem 0;
        }

        /* Search results table */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        /* Status badges */
        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.85em;
        }

        /* Loading and empty states */
        #search-loading i,
        #no-results i {
            opacity: 0.6;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .col-md-8, .col-md-4 {
                margin-bottom: 1rem;
            }

            .input-group-lg .form-control,
            .input-group-lg .btn {
                font-size: 1rem;
            }
        }

        /* Card hover effect */
        .card {
            transition: box-shadow 0.15s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            let searchTimeout;

            // Search button click handler
            $('#search-students').on('click', function() {
                performSearch();
            });

            // Enter key handler for search input
            $('#student-search-input').on('keypress', function(e) {
                if (e.which === 13) {
                    performSearch();
                }
            });

            // Real-time search as user types (debounced)
            $('#student-search-input').on('input', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val().trim();

                if (query.length === 0) {
                    hideAllStates();
                    return;
                }

                if (query.length >= 2) {
                    searchTimeout = setTimeout(function() {
                        performSearch();
                    }, 500);
                }
            });

            // Course filter change handler
            $('#course-filter').on('change', function() {
                const query = $('#student-search-input').val().trim();
                if (query.length >= 2) {
                    performSearch();
                }
            });

            function performSearch() {
                const query = $('#student-search-input').val().trim();
                const courseFilter = $('#course-filter').val();

                if (query.length < 2) {
                    showNoResults();
                    return;
                }

                showLoading();

                // Make AJAX request to search endpoint
                $.ajax({
                    url: '/admin/support/api/search-students',
                    method: 'POST',
                    data: {
                        query: query,
                        course: courseFilter,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            displayResults(response.data);
                        } else {
                            showNoResults();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Search failed:', error);
                        showNoResults();
                    }
                });
            }

            function displayResults(students) {
                hideAllStates();

                let html = '';
                students.forEach(function(student) {
                    html += `
                        <tr>
                            <td>
                                <div>
                                    <strong>${student.name}</strong>
                                    <br>
                                    <small class="text-muted">${student.email}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-${getStatusColor(student.status)}">${getStatusText(student.status)}</span>
                                <br>
                                <small class="text-muted">${student.total_courses || 0} course(s)</small>
                            </td>
                            <td>
                                <span class="badge badge-info">Not in Class</span>
                            </td>
                            <td>
                                <small>${student.last_activity || 'Never'}</small>
                            </td>
                            <td class="text-right">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary"
                                            onclick="viewStudent(${student.id})"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info"
                                            onclick="resetPassword(${student.id})"
                                            title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success"
                                            onclick="messageStudent(${student.id})"
                                            title="Send Message">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                $('#search-results-body').html(html);
                $('#search-results').show();
            }

            function showLoading() {
                hideAllStates();
                $('#search-loading').show();
            }

            function showNoResults() {
                hideAllStates();
                $('#no-results').show();
            }

            function hideAllStates() {
                $('#search-results, #search-loading, #no-results').hide();
            }

            function getStatusColor(status) {
                switch(status) {
                    case 'active': return 'success';
                    case 'inactive': return 'secondary';
                    case 'suspended': return 'danger';
                    case 'completed': return 'info';
                    default: return 'secondary';
                }
            }

            function getStatusText(status) {
                switch(status) {
                    case 'active': return 'Active';
                    case 'inactive': return 'Inactive';
                    case 'suspended': return 'Suspended';
                    case 'completed': return 'Completed';
                    default: return 'Unknown';
                }
            }

            // Action handlers
            window.viewStudent = function(studentId) {
                window.location.href = `/admin/students/${studentId}`;
            };

            window.resetPassword = function(studentId) {
                if (confirm('Send password reset link to this student?')) {
                    // Implement password reset functionality
                    console.log('Resetting password for student:', studentId);
                }
            };

            window.messageStudent = function(studentId) {
                // Implement messaging functionality
                console.log('Opening message dialog for student:', studentId);
            };
        });
    </script>
@endsection
