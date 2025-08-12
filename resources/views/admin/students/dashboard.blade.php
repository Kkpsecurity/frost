@extends('adminlte::page')

@section('title', 'Student Management')

@section('content_header')
    <h1>Student Management System</h1>
    <p class="text-muted">Educational and classroom management of students</p>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Student Management Tabs --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Students Overview</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addStudentModal">
                            <i class="fas fa-plus"></i> Add Student
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Navigation Tabs --}}
                    <ul class="nav nav-tabs" id="studentTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab">
                                <i class="fas fa-chart-line"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="enrollments-tab" data-toggle="tab" href="#enrollments" role="tab">
                                <i class="fas fa-graduation-cap"></i> Course Enrollments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="activity-tab" data-toggle="tab" href="#activity" role="tab">
                                <i class="fas fa-users"></i> Classroom Activity
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="progress-tab" data-toggle="tab" href="#progress" role="tab">
                                <i class="fas fa-chart-bar"></i> Learning Progress
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="records-tab" data-toggle="tab" href="#records" role="tab">
                                <i class="fas fa-certificate"></i> Academic Records
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="communication-tab" data-toggle="tab" href="#communication" role="tab">
                                <i class="fas fa-comments"></i> Communication
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="financial-tab" data-toggle="tab" href="#financial" role="tab">
                                <i class="fas fa-dollar-sign"></i> Financial
                            </a>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content mt-3" id="studentTabContent">
                        {{-- Tab 1: General Overview --}}
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="row">
                                {{-- Stats Cards with Real Data --}}
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3 id="totalStudents">{{ $content['educational_stats']['total_students'] ?? 0 }}</h3>
                                            <p>Total Students</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3 id="activeEnrollments">{{ $content['educational_stats']['active_enrollments'] ?? 0 }}</h3>
                                            <p>Active Enrollments</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-book-open"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3 id="avgProgress">{{ $content['educational_stats']['avg_progress'] ?? 0 }}%</h3>
                                            <p>Avg Progress</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3 id="completedCourses">{{ $content['educational_stats']['completed_courses'] ?? 0 }}</h3>
                                            <p>Completed Courses</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-trophy"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Students Table --}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Students List</h3>
                                        </div>
                                        <div class="card-body">
                                            {!! $content['SwiftCrud']['html'] !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Course Enrollments --}}
                        <div class="tab-pane fade" id="enrollments" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Course Enrollment Management</h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted">Detailed course enrollment management interface will be implemented here.</p>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Active courses list</li>
                                                <li><i class="fas fa-check text-success"></i> Course progress per enrollment</li>
                                                <li><i class="fas fa-check text-success"></i> Enrollment dates and status</li>
                                                <li><i class="fas fa-check text-success"></i> Course completion certificates</li>
                                                <li><i class="fas fa-check text-success"></i> Transfer between courses</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 3: Classroom Activity --}}
                        <div class="tab-pane fade" id="activity" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Real-time Classroom Activity</h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted">Real-time and historical classroom data interface will be implemented here.</p>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Live class attendance</li>
                                                <li><i class="fas fa-check text-success"></i> Participation metrics</li>
                                                <li><i class="fas fa-check text-success"></i> Assignment submissions</li>
                                                <li><i class="fas fa-check text-success"></i> Quiz/test results</li>
                                                <li><i class="fas fa-check text-success"></i> Instructor interactions</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 4: Learning Progress --}}
                        <div class="tab-pane fade" id="progress" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Educational Analytics & Progress Tracking</h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted">Educational analytics and progress tracking interface will be implemented here.</p>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Learning path visualization</li>
                                                <li><i class="fas fa-check text-success"></i> Completion percentages</li>
                                                <li><i class="fas fa-check text-success"></i> Time spent in courses</li>
                                                <li><i class="fas fa-check text-success"></i> Performance analytics</li>
                                                <li><i class="fas fa-check text-success"></i> Learning milestones</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 5: Academic Records --}}
                        <div class="tab-pane fade" id="records" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Official Academic Documentation</h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted">Official academic documentation interface will be implemented here.</p>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Transcripts</li>
                                                <li><i class="fas fa-check text-success"></i> Certificates earned</li>
                                                <li><i class="fas fa-check text-success"></i> Grade history</li>
                                                <li><i class="fas fa-check text-success"></i> Academic standing</li>
                                                <li><i class="fas fa-check text-success"></i> Compliance tracking</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 6: Communication --}}
                        <div class="tab-pane fade" id="communication" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Student-specific Communication Tools</h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted">Student-specific communication tools interface will be implemented here.</p>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Messages from instructors</li>
                                                <li><i class="fas fa-check text-success"></i> Announcements</li>
                                                <li><i class="fas fa-check text-success"></i> Support tickets</li>
                                                <li><i class="fas fa-check text-success"></i> Parent/guardian communications</li>
                                                <li><i class="fas fa-check text-success"></i> Educational notifications</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 7: Financial --}}
                        <div class="tab-pane fade" id="financial" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Education-related Financial Information</h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted">Education-related financial information interface will be implemented here.</p>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> Course fees and payments</li>
                                                <li><i class="fas fa-check text-success"></i> Scholarship information</li>
                                                <li><i class="fas fa-check text-success"></i> Educational discounts</li>
                                                <li><i class="fas fa-check text-success"></i> Payment plans for courses</li>
                                                <li><i class="fas fa-check text-success"></i> Refund requests</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Student Modal --}}
<div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Student</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Student enrollment interface will be implemented here.</p>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .nav-tabs .nav-link {
        border-radius: 0;
        border: none;
        border-bottom: 2px solid transparent;
    }
    .nav-tabs .nav-link.active {
        border-bottom: 2px solid #007bff;
        background-color: transparent;
        color: #007bff;
    }
    .tab-content {
        min-height: 400px;
    }
    .small-box .icon {
        opacity: 0.15;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Tab switching logic
    $('#studentTabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // Student row click handler
    $(document).on('click', '.student-row', function(e) {
        e.preventDefault();
        const studentId = $(this).data('student-id');
        if (studentId) {
            loadStudentDetails(studentId);
        }
    });

    // Load student details via AJAX
    function loadStudentDetails(studentId) {
        $.ajax({
            url: `/admin/students/details/${studentId}`,
            method: 'GET',
            dataType: 'json',
            beforeSend: function() {
                // Show loading state
                showLoadingState();
            },
            success: function(data) {
                updateStudentTabs(data);
                // Switch to a more detailed view or modal
                $('#studentDetailModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error loading student details:', error);
                alert('Failed to load student details. Please try again.');
            },
            complete: function() {
                hideLoadingState();
            }
        });
    }

    // Update tab contents with student data
    function updateStudentTabs(data) {
        // Update overview tab with specific student stats
        updateOverviewTab(data);
        // Update enrollment tab
        updateEnrollmentTab(data.enrollments);
        // Update financial tab
        updateFinancialTab(data.orders);
    }

    function updateOverviewTab(data) {
        const student = data.student;
        const stats = data.stats;

        let overviewHtml = `
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                     src="${student.avatar || '/images/default-avatar.png'}"
                                     alt="Student Avatar">
                            </div>
                            <h3 class="profile-username text-center">${student.fname} ${student.lname}</h3>
                            <p class="text-muted text-center">${student.email}</p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Student ID</b> <a class="float-right">${student.id}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Enrollments</b> <a class="float-right">${stats.total_enrollments}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Active Courses</b> <a class="float-right">${stats.active_enrollments}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Completed</b> <a class="float-right">${stats.completed_courses}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Student Information</h3>
                        </div>
                        <div class="card-body">
                            <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                            <p class="text-muted">${student.email}</p>
                            <hr>
                            <strong><i class="fas fa-calendar mr-1"></i> Registered</strong>
                            <p class="text-muted">${new Date(student.created_at).toLocaleDateString()}</p>
                            <hr>
                            <strong><i class="fas fa-check-circle mr-1"></i> Status</strong>
                            <p class="text-muted">
                                <span class="badge ${student.is_active ? 'badge-success' : 'badge-danger'}">
                                    ${student.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#studentDetailOverview').html(overviewHtml);
    }

    function updateEnrollmentTab(enrollments) {
        let enrollmentHtml = '<div class="table-responsive"><table class="table table-striped">';
        enrollmentHtml += '<thead><tr><th>Course</th><th>Enrolled</th><th>Status</th><th>Progress</th></tr></thead><tbody>';

        enrollments.forEach(function(enrollment) {
            const status = enrollment.expired_at ? 'Expired' :
                          enrollment.completed_at ? 'Completed' : 'Active';
            const statusClass = enrollment.expired_at ? 'danger' :
                               enrollment.completed_at ? 'success' : 'primary';

            enrollmentHtml += `
                <tr>
                    <td>${enrollment.course ? enrollment.course.name : 'N/A'}</td>
                    <td>${new Date(enrollment.created_at).toLocaleDateString()}</td>
                    <td><span class="badge badge-${statusClass}">${status}</span></td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar" style="width: ${enrollment.progress || 0}%"></div>
                        </div>
                    </td>
                </tr>
            `;
        });

        enrollmentHtml += '</tbody></table></div>';
        $('#studentDetailEnrollments').html(enrollmentHtml);
    }

    function updateFinancialTab(orders) {
        let orderHtml = '<div class="table-responsive"><table class="table table-striped">';
        orderHtml += '<thead><tr><th>Order ID</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead><tbody>';

        orders.forEach(function(order) {
            orderHtml += `
                <tr>
                    <td>#${order.id}</td>
                    <td>${new Date(order.created_at).toLocaleDateString()}</td>
                    <td>$${order.total || '0.00'}</td>
                    <td><span class="badge badge-${order.status === 'paid' ? 'success' : 'warning'}">${order.status || 'pending'}</span></td>
                </tr>
            `;
        });

        orderHtml += '</tbody></table></div>';
        $('#studentDetailFinancial').html(orderHtml);
    }

    function showLoadingState() {
        $('.card-body').append('<div id="loadingOverlay" class="overlay"><i class="fas fa-spinner fa-spin"></i></div>');
    }

    function hideLoadingState() {
        $('#loadingOverlay').remove();
    }

    console.log('Student Management System initialized with enhanced functionality');
});
</script>

<!-- Student Detail Modal -->
<div class="modal fade" id="studentDetailModal" tabindex="-1" role="dialog" aria-labelledby="studentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentDetailModalLabel">Student Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="studentDetailTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="detail-overview-tab" data-toggle="tab" href="#studentDetailOverview" role="tab">Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="detail-enrollments-tab" data-toggle="tab" href="#studentDetailEnrollments" role="tab">Enrollments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="detail-financial-tab" data-toggle="tab" href="#studentDetailFinancial" role="tab">Financial</a>
                    </li>
                </ul>
                <div class="tab-content mt-3" id="studentDetailTabContent">
                    <div class="tab-pane fade show active" id="studentDetailOverview" role="tabpanel">
                        <p>Loading student overview...</p>
                    </div>
                    <div class="tab-pane fade" id="studentDetailEnrollments" role="tabpanel">
                        <p>Loading enrollment data...</p>
                    </div>
                    <div class="tab-pane fade" id="studentDetailFinancial" role="tabpanel">
                        <p>Loading financial data...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Edit Student</button>
            </div>
        </div>
    </div>
</div>
@stop
