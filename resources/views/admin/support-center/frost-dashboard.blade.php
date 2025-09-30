@extends('adminlte::page')

@section('title', 'Support Dashboard')

@section('content_header')
    <!-- Force cache refresh: {{ now()->timestamp }} -->
    <div class="row align-items-center mb-4">
        <div class="col text-center">
            <h1 class="h2 mb-3">
                <i class="fas fa-headset text-info mr-3"></i>
                Support Center
            </h1>
            <p class="text-muted mb-0 lead">How can we help you today?</p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="online-status-btn" title="No Student Selected">
                    <i class="fas fa-circle text-secondary" style="font-size: 0.7em;" id="online-status-icon"></i>
                    <span id="online-status-text">Offline</span>
                </button>
                <button type="button" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-clock"></i> {{ now()->format('g:i A') }}
                </button>
                <a href="/admin/frost-support/debug-db" class="btn btn-outline-warning btn-sm" target="_blank">
                    <i class="fas fa-bug"></i> Debug
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        {{-- Main Student Search Section --}}
        <div class="col-12">
            {{-- Student Search Card --}}
            <div class="card">
                <div class="card-body p-4">
                    {{-- Search Interface Container --}}
                    <div id="search-container">
                        <div class="text-center mb-4">
                            <div class="search-icon mb-3">
                                <i class="fas fa-search fa-2x text-primary"></i>
                            </div>
                            <h4 class="card-title mb-2">Find a Student</h4>
                            <p class="text-muted mb-4">Search by name, email, or phone number to get started</p>
                        </div>
                        <div class="row justify-content-center mb-4">
                            <div class="col-lg-8 col-xl-6">
                                <label class="form-label text-muted small font-weight-bold">SEARCH STUDENTS</label>
                                <div class="input-group input-group-lg">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="student-search-input" class="form-control border-left-0 pl-0"
                                           placeholder="Enter student name, email, or phone number...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary px-4" type="button" id="search-students">
                                            <i class="fas fa-search mr-2"></i>Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Search Results --}}
                        <div id="search-results" class="mt-4" style="display: none;">
                            <hr>
                            <h5 class="mb-3">
                                <i class="fas fa-users text-muted"></i>
                                Found Students <span id="results-count" class="badge badge-info">0</span>
                            </h5>
                            <div class="row" id="student-cards">
                                {{-- Student cards will be populated via JavaScript --}}
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

                    {{-- Selected Student Details --}}
                    <div id="student-details" class="mt-4" style="display: none;">
                        <hr>
                        <div class="row">
                            <div class="col-lg-3 col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-user"></i> Student Profile
                                        </h6>
                                        <button class="btn btn-sm btn-outline-secondary float-right" onclick="backToSearch()">
                                            <i class="fas fa-arrow-left"></i> Back to Search
                                        </button>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="student-avatar mb-3">
                                            <i class="fas fa-user-circle fa-4x text-muted"></i>
                                        </div>
                                        <h5 id="student-name">Student Name</h5>
                                        <p class="text-muted" id="student-email">email@example.com</p>
                                        <div class="mb-3">
                                            <span id="student-status" class="badge badge-success">Active</span>
                                            <span id="student-online-status" class="badge badge-secondary ml-2">Offline</span>
                                        </div>
                                        <div id="student-unit-info" class="mb-3" style="display: none;">
                                            <small class="text-info">
                                                <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                <span id="current-unit-name"></span>
                                            </small>
                                        </div>
                                        <div class="btn-group-vertical w-100">
                                            <button class="btn btn-outline-primary btn-sm mb-2" onclick="resetPassword()">
                                                <i class="fas fa-key mr-2"></i>Reset Password
                                            </button>
                                            <button class="btn btn-outline-info btn-sm mb-2" onclick="sendMessage()">
                                                <i class="fas fa-envelope mr-2"></i>Send Message
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm mb-2" onclick="showBanModal()">
                                                <i class="fas fa-ban mr-2"></i>Ban from Class
                                            </button>
                                            <button class="btn btn-outline-warning btn-sm mb-2" onclick="showKickModal()">
                                                <i class="fas fa-user-times mr-2"></i>Kick Out for Day
                                            </button>
                                            <button class="btn btn-outline-success btn-sm mb-2" onclick="showDncModal()">
                                                <i class="fas fa-redo mr-2"></i>Reinstate DNC
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm" onclick="editStudent()">
                                                <i class="fas fa-edit mr-2"></i>Edit Profile
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#activity-tab" role="tab">
                                                    <i class="fas fa-clock"></i> Activity
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#lessons-tab" role="tab">
                                                    <i class="fas fa-book"></i> Lessons
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#exams-tab" role="tab">
                                                    <i class="fas fa-file-alt"></i> Exams
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#validations-tab" role="tab">
                                                    <i class="fas fa-check-circle"></i> Validations
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#history-tab" role="tab">
                                                    <i class="fas fa-history"></i> Class History
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="card-tools">
                                            <div class="btn-group btn-group-sm" id="activity-toggle" style="display: none;">
                                                <button type="button" class="btn btn-outline-primary active" id="today-btn">
                                                    <i class="fas fa-calendar-day"></i> Today
                                                </button>
                                                <button type="button" class="btn btn-outline-primary" id="history-btn">
                                                    <i class="fas fa-calendar"></i> Full History
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active" id="activity-tab" role="tabpanel">
                                                <div id="student-activity">
                                                    <div class="activity-header mb-3" style="display: none;">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <h6 class="mb-0">Student Activity</h6>
                                                                <small class="text-muted" id="activity-period">Today's Activity</small>
                                                            </div>
                                                            <div class="col-auto">
                                                                <span class="badge badge-info" id="activity-count">0 activities</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="activity-timeline">
                                                        <!-- Activity timeline will be loaded here -->
                                                        <div class="text-center py-4" id="activity-loading">
                                                            <i class="fas fa-spinner fa-spin fa-2x text-muted mb-2"></i>
                                                            <p class="text-muted">Loading student activity...</p>
                                                        </div>
                                                    </div>
                                                    <div id="activity-empty" class="text-center py-5" style="display: none;">
                                                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                                        <h6 class="text-muted">No Activity Found</h6>
                                                        <p class="text-muted mb-0">No student activity for the selected period.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="lessons-tab" role="tabpanel">
                                                <div id="student-lessons">
                                                    <div class="text-center">
                                                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                                        <h6 class="text-muted">Select Lessons Tab</h6>
                                                        <p class="text-muted mb-0">Click to load student lesson progress including classroom and self-study lessons.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="exams-tab" role="tabpanel">
                                                <div id="student-exams">
                                                    <div class="text-center">
                                                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                                        <h6 class="text-muted">Select Exams Tab</h6>
                                                        <p class="text-muted mb-0">Click to load student exam attempts, scores, and certification progress.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="validations-tab" role="tabpanel">
                                                <div id="student-validations">
                                                    <div class="text-center">
                                                        <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                                        <h6 class="text-muted">Select Validations Tab</h6>
                                                        <p class="text-muted mb-0">Click to load student validation checks, certifications, and verification status.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="history-tab" role="tabpanel">
                                                <div id="student-class-history">
                                                    <!-- Class history content will be loaded here -->
                                                    <p class="text-muted">Loading class history...</p>
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
    </div>

    {{-- Ban from Class Modal --}}
    <div class="modal fade" id="banModal" tabindex="-1" role="dialog" aria-labelledby="banModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="banModalLabel">
                        <i class="fas fa-ban mr-2"></i>Ban Student from Class
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Warning:</strong> This will immediately disable the student's access to the selected course.
                    </div>

                    <form id="banForm">
                        <div class="form-group">
                            <label for="studentNameDisplay"><strong>Student:</strong></label>
                            <p id="studentNameDisplay" class="form-control-plaintext bg-light p-2 rounded"></p>
                        </div>

                                                <div class=\"form-group\">\n                            <label for=\"courseSelect\"><strong>Select Course to Ban From:</strong> <span class=\"text-danger\">*</span></label>\n                            <select class=\"form-control\" id=\"courseSelect\" required>\n                                <option value=\"\">Loading courses...</option>\n                            </select>\n                            <small class=\"form-text text-muted\">Only active course authorizations are shown. Banned courses are hidden.</small>\n                        </div>

                        <div class="form-group">
                            <label for="banReason"><strong>Ban Reason:</strong> <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="banReason" rows="3"
                                placeholder="Enter the reason for banning this student from the course..."
                                required></textarea>
                            <small class="form-text text-muted">This reason will be recorded and may be visible to the student</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirmBan" required>
                                <label class="custom-control-label" for="confirmBan">
                                    I confirm that I want to ban this student from the selected course
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmBanBtn" onclick="banStudentFromCourse()">
                        <i class="fas fa-ban mr-2"></i>Ban Student
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        /* Search card styling - keep AdminLTE theme colors */
        .search-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .search-icon i {
            color: white !important;
        }

        /* Form labels */
        .form-label {
            letter-spacing: 0.5px;
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
        }

        /* Search input styling */
        .input-group-lg .form-control {
            font-size: 1.1rem;
            padding: 0.875rem 1rem;
            border: 2px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .input-group-lg .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .input-group-text {
            border: 2px solid #e3e6f0;
            border-right: none;
        }

        .input-group-lg .btn {
            font-size: 1.1rem;
            padding: 0.875rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            font-weight: 600;
        }

        .input-group-lg .btn:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-1px);
        }

        /* Search results styling */
        #search-results {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .table th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Status badges */
        .badge {
            padding: 0.4em 0.8em;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Loading and empty states */
        #search-loading,
        #no-results {
            padding: 3rem 0;
        }

        #search-loading i,
        #no-results i {
            opacity: 0.6;
            color: #667eea;
        }

        /* Status buttons */
        .btn-group .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 2rem 1rem !important;
            }

            .col-lg-8, .col-xl-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .input-group-lg .form-control,
            .input-group-lg .btn {
                font-size: 1rem;
                padding: 0.75rem 1rem;
            }

            .col-lg-3 {
                margin-bottom: 1.5rem;
            }
        }

        /* Student card styling */
        .student-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Activity toggle buttons */
        #activity-toggle .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .card-header-tabs {
            margin-bottom: 0;
        }

        .activity-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 0.75rem;
        }

        /* Enhanced timeline styling */
        .timeline {
            position: relative;
            padding-left: 0;
            list-style: none;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1rem;
            padding-left: 3rem;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 0;
            bottom: -1rem;
            width: 2px;
            background-color: #e3e6f0;
        }

        .timeline-item:last-child:before {
            display: none;
        }

        .timeline-item i {
            position: absolute;
            left: 0.5rem;
            top: 0.25rem;
            width: 2rem;
            height: 2rem;
            line-height: 2rem;
            text-align: center;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #e3e6f0;
            font-size: 0.875rem;
            z-index: 1;
        }

        .timeline-item .timeline-content {
            background: rgba(255, 255, 255, 0.8);
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 0.5rem;
        }

        .timeline-item:hover .timeline-content {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        /* Icon colors for different activity types */
        .timeline-item .bg-success {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: white;
        }

        .timeline-item .bg-primary {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: white;
        }

        .timeline-item .bg-info {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
            color: white;
        }

        .timeline-item .bg-warning {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529;
        }

        .timeline-item .bg-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white;
        }

        /* Ban modal styling */\n        #banModal .modal-header {\n            border-bottom: 2px solid rgba(255, 255, 255, 0.2);\n        }\n        \n        #banModal .alert-warning {\n            border-left: 4px solid #ffc107;\n        }\n        \n        #banModal #confirmBanBtn:disabled {\n            opacity: 0.6;\n            cursor: not-allowed;\n        }\n        \n        /* Kick modal styling */\n        #kickModal .modal-header {\n            border-bottom: 2px solid rgba(0, 0, 0, 0.1);\n        }\n        \n        #kickModal .alert-info {\n            border-left: 4px solid #17a2b8;\n        }\n        \n        #kickModal #confirmKickBtn:disabled {\n            opacity: 0.6;\n            cursor: not-allowed;\n        }\n        \n        #kickModal .input-group-text {\n            background-color: #f8f9fa;\n            border-color: #ced4da;\n        }\n        \n        /* DNC modal styling */\n        #dncModal .modal-header {\n            border-bottom: 2px solid rgba(255, 255, 255, 0.2);\n        }\n        \n        #dncModal .alert-info {\n            border-left: 4px solid #17a2b8;\n        }\n        \n        #dncModal #confirmReinstateBtn:disabled {\n            opacity: 0.6;\n            cursor: not-allowed;\n        }\n        \n        #dncModal .bg-light {\n            border: 1px solid #e9ecef;\n        }\n        \n        /* Search container transitions */\n        #search-container, #student-details {\n            transition: all 0.3s ease;\n        }\n        \n        /* Ensure proper spacing when switching views */\n        .card-body {\n            min-height: 400px;\n        }\n        \n        /* Online/Offline status styling */\n        .badge.badge-success {\n            background-color: #28a745 !important;\n        }\n        \n        .badge.badge-secondary {\n            background-color: #6c757d !important;\n        }\n        \n        /* Student card online indicator */\n        .student-card .position-absolute {\n            border-radius: 50px;\n            box-shadow: 0 2px 4px rgba(0,0,0,0.1);\n        }\n        \n        .student-card:hover {\n            transform: translateY(-2px);\n            box-shadow: 0 4px 8px rgba(0,0,0,0.15);\n            transition: all 0.2s ease;\n        }

        /* Progress item styling */
        .progress-item {
            padding: 10px 0;
        }

        .progress {
            height: 6px;
        }

        /* Lesson card styling */
        .lesson-card {
            transition: all 0.2s ease;
            border-left-width: 4px !important;
        }

        .lesson-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .lesson-card.border-primary {
            border-left-color: #007bff !important;
        }

        .lesson-card.border-light-blue {
            border-left-color: #87CEEB !important;
        }

        .badge-light-blue {
            background-color: #87CEEB;
            color: #2c3e50;
        }

        .lesson-title {
            color: #2c3e50;
            font-weight: 600;
        }

        .lesson-badges .badge {
            margin-right: 0.25rem;
            font-size: 0.75rem;
        }

        .lesson-dates {
            border-left: 2px solid #e9ecef;
            padding-left: 1rem;
        }

        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 0.75rem;
        }

        .lessons-summary .card {
            transition: transform 0.2s ease;
        }

        .lessons-summary .card:hover {
            transform: translateY(-3px);
        }

        .lessons-summary .card-body {
            position: relative;
            overflow: hidden;
        }

        .lessons-summary .card-body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.5s ease;
            opacity: 0;
        }

        .lessons-summary .card:hover .card-body::before {
            animation: shimmer 1s ease-in-out;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); opacity: 0; }
        }

        /* Simple list view styling */
        .lesson-list-item {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            border-left-width: 4px !important;
        }

        .lesson-list-item:hover {
            background-color: #f8f9fa;
            transform: translateX(3px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .border-left-primary {
            border-left-color: #007bff !important;
        }

        .border-left-light-blue {
            border-left-color: #87CEEB !important;
        }

        .lesson-title-simple {
            font-weight: 500;
            color: #2c3e50;
        }

        .list-group-item {
            border: none;
        }

        /* Toggle buttons styling */
        .btn-group-sm .btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.75rem;
        }

        /* Validation Tab Specific Styles */
        .student-validations-container {
            padding: 1rem 0;
        }

        .course-validation-section {
            border-bottom: 2px solid #e3e6f0;
            padding-bottom: 2rem;
        }

        .course-validation-section:last-child {
            border-bottom: none;
        }

        /* ID Card Validation Styles */
        .id-card-photo-container {
            position: relative;
            display: inline-block;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 250px;
        }

        .id-card-photo {
            width: 100%;
            height: 150px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .id-card-photo:hover {
            transform: scale(1.02);
        }

        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(108, 117, 125, 0.8);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        /* Headshot List Group Styles */
        .headshot-photo-container-sm {
            position: relative;
            display: inline-block;
            border-radius: 50%;
            overflow: hidden;
            width: 50px;
            height: 50px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .headshot-photo-sm {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .headshot-photo-sm:hover {
            transform: scale(1.1);
        }

        .photo-overlay-sm {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(108, 117, 125, 0.8);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }

        .headshot-date-info {
            line-height: 1.2;
        }

        .status-indicator {
            text-align: center;
        }

        /* Status-based styling for list items */
        .list-group-item.status-approved {
            border-left: 4px solid #1cc88a;
            background-color: rgba(28, 200, 138, 0.05);
        }

        .list-group-item.status-rejected {
            border-left: 4px solid #e74a3b;
            background-color: rgba(231, 74, 59, 0.05);
        }

        .list-group-item.status-pending {
            border-left: 4px solid #f6c23e;
            background-color: rgba(246, 194, 62, 0.05);
        }

        .list-group-item.status-missing {
            border-left: 4px solid #6c757d;
            background-color: rgba(108, 117, 125, 0.05);
        }

        /* Validation form styles */
        .validation-actions {
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f9fc;
            border-radius: 8px;
        }

        .validation-actions-sm {
            margin-top: 0.5rem;
        }

        .reject-form {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: #fff3f3;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }

        .validation-details {
            font-size: 0.9rem;
        }

        /* Badge styling for validations */
        .badge-group .badge {
            margin-left: 0.25rem;
        }

        .badge-sm {
            font-size: 0.75rem;
            padding: 0.3em 0.6em;
        }

        .alert-sm {
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }

        /* Modal image styling */
        #imageModal .modal-body img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        /* Responsive adjustments for validations */
        @media (max-width: 768px) {
            .id-card-photo {
                height: 120px;
            }

            .headshot-photo-container-sm {
                width: 40px;
                height: 40px;
            }

            .badge-group {
                margin-top: 0.5rem;
            }

            .badge-group .badge {
                display: block;
                margin: 0.25rem 0;
            }

            .validation-actions .btn-group {
                display: flex;
                flex-direction: column;
            }

            .validation-actions .btn-group .btn {
                margin-bottom: 0.25rem;
            }
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            let searchTimeout;
            let selectedStudent = null;
            let selectedStudentId = null;

            // Handle tab switching to show/hide activity toggle
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                const targetTab = $(e.target).attr('href');

                if (targetTab === '#activity-tab' && selectedStudent) {
                    $('#activity-toggle').show();
                } else {
                    $('#activity-toggle').hide();
                }
            });

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

            function performSearch() {
                const query = $('#student-search-input').val().trim();

                if (query.length < 2) {
                    showNoResults();
                    return;
                }

                showLoading();

                // Make AJAX request to search endpoint
                $.ajax({
                    url: '/admin/frost-support/search-students',
                    method: 'GET',
                    data: {
                        query: query
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

                $('#results-count').text(students.length);
                let html = '';

                students.forEach(function(student) {
                    // Get online status color and icon
                    const onlineStatusColor = student.online_status === 'online' ? 'success' : 'secondary';
                    const onlineStatusIcon = student.online_status === 'online' ? 'fa-circle' : 'fa-circle';
                    const onlineStatusText = student.online_status === 'online' ? 'Online' : 'Offline';
                    const unitInfo = student.current_unit ?
                        `<small class="text-info d-block"><i class="fas fa-chalkboard-teacher"></i> ${student.current_unit.unit_name}</small>` : '';

                    html += `
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card student-card" data-student-id="${student.id}" style="cursor: pointer;">
                                <div class="card-body text-center">
                                    <div class="mb-2 position-relative">
                                        <i class="fas fa-user-circle fa-3x text-muted"></i>
                                        <span class="badge badge-${onlineStatusColor} position-absolute"
                                              style="top: 0; right: 35%; font-size: 0.6rem;">
                                            <i class="fas ${onlineStatusIcon} mr-1"></i>${onlineStatusText}
                                        </span>
                                    </div>
                                    <h6 class="card-title">${student.name}</h6>
                                    <p class="card-text text-muted small mb-1">${student.email}</p>
                                    ${unitInfo}
                                    <span class="badge badge-${getStatusColor(student.status)}">${getStatusText(student.status)}</span>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> ${student.last_activity || 'Never'}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#student-cards').html(html);
                $('#search-results').show();

                // Add click handlers for student cards
                $('.student-card').on('click', function() {
                    const studentId = $(this).data('student-id');
                    const student = students.find(s => s.id == studentId);
                    selectStudent(student);
                });
            }

            // Use the global selectStudent function
            function selectStudent(student) {
                window.selectStudent(student);
            }

            function loadStudentActivity(studentId) {
                $('#activity-loading').show();
                $('#activity-timeline, #activity-empty, .activity-header').hide();

                // Only show toggle if activity tab is currently active
                if ($('#activity-tab').hasClass('show active')) {
                    $('#activity-toggle').show();
                }

                // Set up toggle event handlers
                $('#today-btn, #history-btn').off('click').on('click', function() {
                    const isToday = $(this).attr('id') === 'today-btn';
                    $('#today-btn, #history-btn').removeClass('active');
                    $(this).addClass('active');

                    const period = isToday ? 'today' : 'all';
                    $('#activity-period').text(isToday ? "Today's Activity" : 'Full Activity History');

                    fetchStudentActivity(studentId, period);
                });

                // Load today's activity by default
                fetchStudentActivity(studentId, 'today');
            }            function fetchStudentActivity(studentId, period = 'today') {
                $('#activity-loading').show();
                $('#activity-timeline, #activity-empty').hide();

                // Make AJAX request to get student activity
                $.ajax({
                    url: '/admin/frost-support/student/' + studentId + '/activity',
                    method: 'GET',
                    data: { period: period },
                    success: function(response) {
                        $('#activity-loading').hide();

                        if (response.success && response.data.length > 0) {
                            displayActivityTimeline(response.data, period);
                        } else {
                            showEmptyActivity(period);
                        }
                    },
                    error: function() {
                        $('#activity-loading').hide();
                        // Show mock data for demo purposes
                        displayMockActivity(period);
                    }
                });
            }

            function displayActivityTimeline(activities, period) {
                $('.activity-header').show();
                $('#activity-count').text(activities.length + ' activit' + (activities.length === 1 ? 'y' : 'ies'));

                let html = '<div class="timeline">';

                activities.forEach(function(activity) {
                    const iconClass = getActivityIcon(activity.type);
                    const colorClass = getActivityColor(activity.type);
                    const timeAgo = formatActivityTime(activity.created_at, period);

                    html += `
                        <div class="timeline-item">
                            <i class="fas ${iconClass} ${colorClass}"></i>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">${activity.title}</h6>
                                        <p class="mb-1 text-muted small">${activity.description}</p>
                                        ${activity.details ? '<p class="mb-0 text-info small"><i class="fas fa-info-circle"></i> ' + activity.details + '</p>' : ''}
                                    </div>
                                    <small class="text-muted">${timeAgo}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                $('#activity-timeline').html(html).show();
            }

            function displayMockActivity(period) {
                const mockActivities = [
                    {
                        type: 'login',
                        title: 'Student Login',
                        description: 'Logged into the learning platform',
                        details: 'IP: 192.168.1.100',
                        created_at: period === 'today' ? '2025-09-27 14:30:00' : '2025-09-27 14:30:00'
                    },
                    {
                        type: 'lesson_start',
                        title: 'Started Lesson',
                        description: 'Began "Introduction to Cybersecurity"',
                        details: 'Course: Security Fundamentals',
                        created_at: period === 'today' ? '2025-09-27 14:35:00' : '2025-09-27 14:35:00'
                    },
                    {
                        type: 'lesson_complete',
                        title: 'Completed Lesson',
                        description: 'Finished "Introduction to Cybersecurity"',
                        details: 'Score: 95% | Duration: 25 minutes',
                        created_at: period === 'today' ? '2025-09-27 15:00:00' : '2025-09-27 15:00:00'
                    }
                ];

                if (period === 'all') {
                    mockActivities.push(
                        {
                            type: 'login',
                            title: 'Student Login',
                            description: 'Logged into the learning platform',
                            details: 'IP: 192.168.1.100',
                            created_at: '2025-09-26 10:15:00'
                        },
                        {
                            type: 'quiz_complete',
                            title: 'Quiz Completed',
                            description: 'Completed "Security Basics Quiz"',
                            details: 'Score: 88% | Attempts: 1',
                            created_at: '2025-09-26 11:30:00'
                        }
                    );
                }

                displayActivityTimeline(mockActivities, period);
            }

            function showEmptyActivity(period) {
                $('.activity-header').hide();
                $('#activity-empty').show();

                const message = period === 'today' ?
                    'No activity recorded for today.' :
                    'No activity found in the system.';

                $('#activity-empty p').text(message);
            }

            function getActivityIcon(type) {
                const icons = {
                    'login': 'fa-sign-in-alt',
                    'logout': 'fa-sign-out-alt',
                    'lesson_start': 'fa-play',
                    'lesson_complete': 'fa-check-circle',
                    'quiz_start': 'fa-question-circle',
                    'quiz_complete': 'fa-trophy',
                    'download': 'fa-download',
                    'upload': 'fa-upload',
                    'message': 'fa-envelope',
                    'support': 'fa-life-ring'
                };
                return icons[type] || 'fa-info-circle';
            }

            function getActivityColor(type) {
                const colors = {
                    'login': 'bg-success',
                    'logout': 'bg-secondary',
                    'lesson_start': 'bg-info',
                    'lesson_complete': 'bg-success',
                    'quiz_start': 'bg-warning',
                    'quiz_complete': 'bg-primary',
                    'download': 'bg-info',
                    'upload': 'bg-warning',
                    'message': 'bg-primary',
                    'support': 'bg-danger'
                };
                return colors[type] || 'bg-info';
            }

            function formatActivityTime(dateTime, period) {
                const date = new Date(dateTime);
                const now = new Date();
                const diffMs = now - date;
                const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                const diffMinutes = Math.floor(diffMs / (1000 * 60));

                if (period === 'today') {
                    if (diffMinutes < 60) {
                        return diffMinutes + 'm ago';
                    } else {
                        return diffHours + 'h ago';
                    }
                } else {
                    const yesterday = new Date();
                    yesterday.setDate(yesterday.getDate() - 1);

                    if (date.toDateString() === now.toDateString()) {
                        return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    } else if (date.toDateString() === yesterday.toDateString()) {
                        return 'Yesterday ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    } else {
                        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    }
                }
            }

            function loadStudentHistory(studentId) {
                $('#student-class-history').html('<p class="text-muted">Loading class history...</p>');

                setTimeout(function() {
                    $('#student-class-history').html(`
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Attendance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>2025-09-26</td>
                                        <td>Security Fundamentals</td>
                                        <td><span class="badge badge-success">Present</span></td>
                                        <td>2.5 hrs</td>
                                    </tr>
                                    <tr>
                                        <td>2025-09-25</td>
                                        <td>Security Fundamentals</td>
                                        <td><span class="badge badge-danger">Absent</span></td>
                                        <td>0 hrs</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `);
                }, 1000);
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
                $('#search-results, #search-loading, #no-results, #student-details').hide();
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

            // Global action handlers
            window.backToSearch = function() {
                $('#student-details').hide();
                $('#search-container').show();
                $('#activity-toggle').hide();
                selectedStudent = null;
                selectedStudentId = null;
            };

            window.resetPassword = function() {
                if (selectedStudent && confirm('Send password reset link to ' + selectedStudent.name + '?')) {
                    console.log('Resetting password for student:', selectedStudent.id);
                    // Implement password reset functionality
                }
            };

            window.sendMessage = function() {
                if (selectedStudent) {
                    console.log('Opening message dialog for student:', selectedStudent.id);
                    // Implement messaging functionality
                }
            };

            window.editStudent = function() {
                if (selectedStudent) {
                    window.location.href = `/admin/students/${selectedStudent.id}/edit`;
                }
            };

            window.showBanModal = function() {
                if (selectedStudent) {
                    // Set student name in modal
                    $('#studentNameDisplay').text(selectedStudent.name + ' (' + selectedStudent.email + ')');

                    // Load student's courses
                    loadStudentCourses(selectedStudent.id);

                    // Reset form
                    $('#banForm')[0].reset();
                    $('#confirmBan').prop('checked', false);
                    $('#confirmBanBtn').prop('disabled', true);

                    // Show modal
                    $('#banModal').modal('show');
                }
            };

            window.loadStudentCourses = function(studentId) {
                $('#courseSelect').html('<option value="">Loading courses...</option>');

                $.ajax({
                    url: '/admin/frost-support/student/' + studentId + '/courses',
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            let options = '<option value="">Select a course to ban from...</option>';
                            response.data.forEach(function(courseAuth) {
                                if (!courseAuth.disabled_at) { // Only show active courses
                                    options += `<option value="${courseAuth.id}">${courseAuth.course_title} (${courseAuth.status})</option>`;
                                }
                            });
                            $('#courseSelect').html(options);

                            if (response.data.filter(ca => !ca.disabled_at).length === 0) {
                                $('#courseSelect').html('<option value="">No active courses found</option>');
                            }
                        } else {
                            $('#courseSelect').html('<option value="">No courses found</option>');
                        }
                    },
                    error: function() {
                        $('#courseSelect').html('<option value="">Error loading courses</option>');
                    }
                });
            };

            window.banStudentFromCourse = function() {
                const courseAuthId = $('#courseSelect').val();
                const reason = $('#banReason').val().trim();
                const confirmed = $('#confirmBan').is(':checked');

                if (!courseAuthId) {
                    alert('Please select a course to ban the student from.');
                    return;
                }

                if (!reason) {
                    alert('Please enter a reason for the ban.');
                    return;
                }

                if (!confirmed) {
                    alert('Please confirm that you want to ban this student.');
                    return;
                }

                // Disable button and show loading
                $('#confirmBanBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

                $.ajax({
                    url: '/admin/frost-support/ban-student-course',
                    method: 'POST',
                    data: {
                        course_auth_id: courseAuthId,
                        disabled_reason: reason,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#banModal').modal('hide');

                            // Show success message
                            alert('Student has been successfully banned from the course.');

                            // Refresh student data
                            if (selectedStudentId) {
                                loadStudentActivity(selectedStudentId);
                                loadStudentLessons(selectedStudentId);
                                loadStudentHistory(selectedStudentId);
                            }
                        } else {
                            alert('Error: ' + (response.message || 'Failed to ban student from course'));
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to ban student from course';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert('Error: ' + errorMessage);
                    },
                    complete: function() {
                        // Re-enable button
                        $('#confirmBanBtn').prop('disabled', false).html('<i class="fas fa-ban mr-2"></i>Ban Student');
                    }
                });
            };

            // Enable/disable ban button based on confirmation checkbox
            $(document).on('change', '#confirmBan', function() {
                $('#confirmBanBtn').prop('disabled', !$(this).is(':checked'));
            });

            // Kick Out functionality
            window.showKickModal = function() {
                if (selectedStudent) {
                    // Set student name in modal
                    $('#studentNameDisplayKick').text(selectedStudent.name + ' (' + selectedStudent.email + ')');

                    // Load student's units
                    loadStudentUnits(selectedStudent.id);

                    // Reset form
                    $('#kickForm')[0].reset();
                    $('#kickDuration').val(24); // Default 24 hours
                    $('#confirmKick').prop('checked', false);
                    $('#confirmKickBtn').prop('disabled', true);

                    // Show modal
                    $('#kickModal').modal('show');
                }
            };

            window.loadStudentUnits = function(studentId) {
                $('#unitSelect').html('<option value="">Loading units...</option>');

                $.ajax({
                    url: '/admin/frost-support/student/' + studentId + '/units',
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            let options = '<option value="">Select a unit to kick from...</option>';
                            response.data.forEach(function(studentUnit) {
                                if (!studentUnit.ejected_at) { // Only show non-ejected units
                                    options += `<option value="${studentUnit.id}">${studentUnit.unit_name} (${studentUnit.status})</option>`;
                                }
                            });
                            $('#unitSelect').html(options);

                            if (response.data.filter(su => !su.ejected_at).length === 0) {
                                $('#unitSelect').html('<option value="">No active units found</option>');
                            }
                        } else {
                            $('#unitSelect').html('<option value="">No units found</option>');
                        }
                    },
                    error: function() {
                        $('#unitSelect').html('<option value="">Error loading units</option>');
                    }
                });
            };

            window.kickStudentFromUnit = function() {
                const studentUnitId = $('#unitSelect').val();
                const reason = $('#kickReason').val().trim();
                const duration = parseInt($('#kickDuration').val());
                const confirmed = $('#confirmKick').is(':checked');

                if (!studentUnitId) {
                    alert('Please select a unit to kick the student from.');
                    return;
                }

                if (!reason) {
                    alert('Please enter a reason for the kick.');
                    return;
                }

                if (!duration || duration < 1 || duration > 168) {
                    alert('Please enter a valid duration (1-168 hours).');
                    return;
                }

                if (!confirmed) {
                    alert('Please confirm that you want to kick this student.');
                    return;
                }

                // Disable button and show loading
                $('#confirmKickBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

                $.ajax({
                    url: '/admin/frost-support/kick-student-unit',
                    method: 'POST',
                    data: {
                        student_unit_id: studentUnitId,
                        ejected_for: reason,
                        duration_hours: duration,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#kickModal').modal('hide');

                            // Show success message
                            const durationText = duration === 24 ? '1 day' : duration + ' hours';
                            alert(`Student has been kicked out for ${durationText}. They can return on ${response.data.can_return_at}.`);

                            // Refresh student data
                            if (selectedStudentId) {
                                loadStudentActivity(selectedStudentId);
                                loadStudentLessons(selectedStudentId);
                                loadStudentHistory(selectedStudentId);
                            }
                        } else {
                            alert('Error: ' + (response.message || 'Failed to kick student from unit'));
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to kick student from unit';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert('Error: ' + errorMessage);
                    },
                    complete: function() {
                        // Re-enable button
                        $('#confirmKickBtn').prop('disabled', false).html('<i class="fas fa-user-times mr-2"></i>Kick Out Student');
                    }
                });
            };

            // Enable/disable kick button based on confirmation checkbox
            $(document).on('change', '#confirmKick', function() {
                $('#confirmKickBtn').prop('disabled', !$(this).is(':checked'));
            });

            // DNC Reinstatement functionality
            window.showDncModal = function() {
                if (selectedStudent) {
                    // Set student name in modal
                    $('#studentNameDisplayDnc').text(selectedStudent.name + ' (' + selectedStudent.email + ')');

                    // Load student's DNC lessons
                    loadStudentDncLessons(selectedStudent.id);

                    // Reset form
                    $('#dncForm')[0].reset();
                    $('#confirmReinstate').prop('checked', false);
                    $('#confirmReinstateBtn').prop('disabled', true);

                    // Show modal
                    $('#dncModal').modal('show');
                }
            };

            window.loadStudentDncLessons = function(studentId) {
                $('#lessonSelect').html('<option value="">Loading DNC lessons...</option>');

                $.ajax({
                    url: '/admin/frost-support/student/' + studentId + '/dnc-lessons',
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            let options = '<option value="">Select a DNC lesson to reinstate...</option>';
                            response.data.forEach(function(studentLesson) {
                                const dncDate = new Date(studentLesson.dnc_at).toLocaleDateString();
                                options += `<option value="${studentLesson.id}">${studentLesson.lesson_title} (DNC: ${dncDate})</option>`;
                            });
                            $('#lessonSelect').html(options);
                        } else {
                            $('#lessonSelect').html('<option value="">No DNC lessons found</option>');
                        }
                    },
                    error: function() {
                        $('#lessonSelect').html('<option value="">Error loading lessons</option>');
                    }
                });
            };

            window.reinstateStudentLesson = function() {
                const studentLessonId = $('#lessonSelect').val();
                const reason = $('#reinstateReason').val().trim();
                const confirmed = $('#confirmReinstate').is(':checked');

                if (!studentLessonId) {
                    alert('Please select a DNC lesson to reinstate.');
                    return;
                }

                if (!reason) {
                    alert('Please enter a reason for the reinstatement.');
                    return;
                }

                if (!confirmed) {
                    alert('Please confirm that you want to reinstate this student.');
                    return;
                }

                // Disable button and show loading
                $('#confirmReinstateBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');

                $.ajax({
                    url: '/admin/frost-support/reinstate-student-lesson',
                    method: 'POST',
                    data: {
                        student_lesson_id: studentLessonId,
                        reinstate_reason: reason,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#dncModal').modal('hide');

                            // Show success message
                            alert('Student has been successfully reinstated to the lesson. DNC status cleared.');

                            // Refresh student data
                            if (selectedStudentId) {
                                loadStudentActivity(selectedStudentId);
                                loadStudentLessons(selectedStudentId);
                                loadStudentHistory(selectedStudentId);
                            }
                        } else {
                            alert('Error: ' + (response.message || 'Failed to reinstate student lesson'));
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to reinstate student lesson';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert('Error: ' + errorMessage);
                    },
                    complete: function() {
                        // Re-enable button
                        $('#confirmReinstateBtn').prop('disabled', false).html('<i class="fas fa-redo mr-2"></i>Reinstate Student');
                    }
                });
            };

            // Enable/disable reinstate button based on confirmation checkbox
            $(document).on('change', '#confirmReinstate', function() {
                $('#confirmReinstateBtn').prop('disabled', !$(this).is(':checked'));
            });
        });
    </script>

    {{-- Kick Out for Day Modal --}}
    <div class="modal fade" id="kickModal" tabindex="-1" role="dialog" aria-labelledby="kickModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="kickModalLabel">
                        <i class="fas fa-user-times mr-2"></i>Kick Student Out for Day
                    </h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Temporary Action:</strong> This will eject the student for the specified duration. They can return after the time expires.
                    </div>

                    <form id="kickForm">
                        <div class="form-group">
                            <label for="studentNameDisplayKick"><strong>Student:</strong></label>
                            <p id="studentNameDisplayKick" class="form-control-plaintext bg-light p-2 rounded"></p>
                        </div>

                        <div class="form-group">
                            <label for="unitSelect"><strong>Select Unit to Kick From:</strong> <span class="text-danger">*</span></label>
                            <select class="form-control" id="unitSelect" required>
                                <option value="">Loading units...</option>
                            </select>
                            <small class="form-text text-muted">Only active student unit enrollments are shown</small>
                        </div>

                        <div class="form-group">
                            <label for="kickReason"><strong>Kick Reason:</strong> <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="kickReason" rows="3"
                                placeholder="Enter the reason for temporarily removing this student..."
                                required></textarea>
                            <small class="form-text text-muted">This will be recorded and may be visible to the student</small>
                        </div>

                        <div class="form-group">
                            <label for="kickDuration"><strong>Duration:</strong></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="kickDuration" value="24" min="1" max="168" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">hours</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Default: 24 hours (1 day). Max: 168 hours (1 week)</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirmKick" required>
                                <label class="custom-control-label" for="confirmKick">
                                    I confirm that I want to temporarily kick this student out
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmKickBtn" onclick="kickStudentFromUnit()">
                        <i class="fas fa-user-times mr-2"></i>Kick Out Student
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Reinstate DNC Student Modal --}}
    <div class="modal fade" id="dncModal" tabindex="-1" role="dialog" aria-labelledby="dncModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="dncModalLabel">
                        <i class="fas fa-redo mr-2"></i>Reinstate DNC Student
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>DNC (Did Not Complete):</strong> This will clear the DNC status and allow the student back into the lesson.
                    </div>

                    <form id="dncForm">
                        <div class="form-group">
                            <label for="studentNameDisplayDnc"><strong>Student:</strong></label>
                            <p id="studentNameDisplayDnc" class="form-control-plaintext bg-light p-2 rounded"></p>
                        </div>

                        <div class="form-group">
                            <label for="lessonSelect"><strong>Select DNC Lesson to Reinstate:</strong> <span class="text-danger">*</span></label>
                            <select class="form-control" id="lessonSelect" required>
                                <option value="">Loading DNC lessons...</option>
                            </select>
                            <small class="form-text text-muted">Only lessons marked as DNC are shown</small>
                        </div>

                        <div class="form-group">
                            <label for="reinstateReason"><strong>Reinstatement Reason:</strong> <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reinstateReason" rows="3"
                                placeholder="Enter the reason for reinstating this student to the lesson..."
                                required></textarea>
                            <small class="form-text text-muted">Explain why the DNC status should be cleared</small>
                        </div>

                        <div class="form-group">
                            <div class="bg-light p-3 rounded">
                                <h6 class="mb-2"><i class="fas fa-clock text-info mr-2"></i>DNC Context</h6>
                                <p class="mb-1 small"><strong>5-Minute Grace Period:</strong> Students have 5 minutes after lesson start to join</p>
                                <p class="mb-1 small"><strong>Late Entry:</strong> Students arriving late enter waiting room and get DNC status</p>
                                <p class="mb-0 small"><strong>Reinstatement:</strong> Clears DNC and grants full lesson access</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirmReinstate" required>
                                <label class="custom-control-label" for="confirmReinstate">
                                    I confirm that I want to reinstate this student and clear their DNC status
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="confirmReinstateBtn" onclick="reinstateStudentLesson()">
                        <i class="fas fa-redo mr-2"></i>Reinstate Student
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    console.log('Frost Support Dashboard JavaScript loaded');

    // Function to update the top-right online status indicator
    window.updateOnlineStatusIndicator = function(status, context = '') {
        const statusBtn = $('#online-status-btn');
        const statusIcon = $('#online-status-icon');
        const statusText = $('#online-status-text');

        if (status === 'online') {
            statusBtn.removeClass('btn-outline-secondary').addClass('btn-outline-success');
            statusIcon.removeClass('text-secondary').addClass('text-success');
            statusText.text('Online');
        } else {
            statusBtn.removeClass('btn-outline-success').addClass('btn-outline-secondary');
            statusIcon.removeClass('text-success').addClass('text-secondary');
            statusText.text('Offline');
        }

        // Update title attribute for context
        if (context) {
            statusBtn.attr('title', context);
        }
    };

    // Set initial status to offline (no student selected) - now after function is defined
    updateOnlineStatusIndicator('offline', 'No Student Selected');




    // Function to handle search
    window.searchStudents = function() {
        const query = $('#student-search-input').val();
        if (query.length < 2) {
            alert('Please enter at least 2 characters to search');
            return;
        }

        console.log('Searching for:', query);

        // Show loading state
        $('#search-loading').show();
        $('#search-results').hide();
        $('#no-results').hide();

        // Make AJAX request to search students
        $.ajax({
            url: '/admin/frost-support/search',
            method: 'POST',
            data: {
                query: query,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#search-loading').hide();
                console.log('Search response:', response);

                if (response.success && response.data.length > 0) {
                    displaySearchResults(response.data);
                } else {
                    $('#no-results').show();
                }
            },
            error: function(xhr) {
                $('#search-loading').hide();
                console.error('Search failed:', xhr);
                alert('Search failed. Please try again.');
            }
        });
    };

    // Function to display search results
    window.displaySearchResults = function(students) {
        console.log('Displaying search results:', students);
        const container = $('#student-cards');
        container.empty();

        students.forEach(function(student) {
            const onlineStatusBadge = student.online_status === 'online'
                ? '<span class="badge badge-success">Online</span>'
                : '<span class="badge badge-secondary">Offline</span>';

            // Escape student data for safe HTML injection
            const studentDataEscaped = JSON.stringify(student).replace(/"/g, '&quot;');

            const studentCard = `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 student-card" style="cursor: pointer;" onclick="selectStudent(${studentDataEscaped})">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">${student.name}</h6>
                                ${onlineStatusBadge}
                            </div>
                            <p class="card-text text-muted small mb-1">${student.email}</p>
                            <p class="card-text text-muted small mb-0">
                                <i class="fas fa-graduation-cap mr-1"></i>
                                ${student.total_courses} courses • Last activity: ${student.last_activity}
                            </p>
                        </div>
                    </div>
                </div>
            `;
            container.append(studentCard);
        });

        $('#results-count').text(students.length);
        $('#search-results').show();
        console.log('Search results displayed successfully');
    };

    // Bind search button and enter key
    $('#search-students').click(searchStudents);
    $('#student-search-input').keypress(function(e) {
        if (e.which === 13) {
            searchStudents();
        }
    });

    // Debug: Test if elements exist
    console.log('Search button exists:', $('#search-students').length > 0);
    console.log('Search input exists:', $('#student-search-input').length > 0);
    console.log('Student details element exists:', $('#student-details').length > 0);
    console.log('Search container exists:', $('#search-container').length > 0);

    // Function to load student lessons when Lessons tab is clicked
    window.loadStudentLessons = function(studentId) {
        if (!studentId) {
            console.error('loadStudentLessons called with no studentId');
            return;
        }

        console.log('🔄 Loading lessons for student:', studentId, 'via AJAX call to:', '/admin/frost-support/student/' + studentId + '/lessons');
        $('#student-lessons').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-muted mb-2"></i><p class="text-muted">Loading lessons...</p></div>');

        $.ajax({
            url: '/admin/frost-support/student/' + studentId + '/lessons',
            method: 'GET',
            success: function(response) {
                console.log('📚 Lessons AJAX response:', response);
                if (response.success) {
                    console.log('✅ Successfully loaded', response.data.length, 'lessons');
                    displayStudentLessons(response.data, response.summary);
                } else {
                    $('#student-lessons').html(`
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                            <h6 class="text-muted">Error Loading Lessons</h6>
                            <p class="text-muted mb-0">${response.message || 'Failed to load student lessons'}</p>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                console.error('❌ Failed to load lessons AJAX call:', xhr.status, xhr.statusText, xhr.responseText);
                $('#student-lessons').html(`
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                        <h6 class="text-muted">Failed to Load Lessons</h6>
                        <p class="text-muted mb-0">Please try again later</p>
                    </div>
                `);
            }
        });
    };

    // Function to display student lessons
    window.displayStudentLessons = function(lessons, summary) {
        if (!lessons || lessons.length === 0) {
            $('#student-lessons').html(`
                <div class="text-center">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">No Lessons Found</h6>
                    <p class="text-muted mb-0">This student hasn't taken any lessons yet.</p>
                </div>
            `);
            return;
        }

        let lessonsHtml = `
            <div class="lessons-summary mb-4">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="card bg-primary text-white border-0">
                            <div class="card-body py-3">
                                <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                                <h4 class="mb-1">${summary.student_unit_lessons}</h4>
                                <small>Classroom Lessons</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card bg-info text-white border-0">
                            <div class="card-body py-3">
                                <i class="fas fa-user-graduate fa-2x mb-2"></i>
                                <h4 class="mb-1">${summary.self_study_lessons}</h4>
                                <small>Self-Study Lessons</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card bg-success text-white border-0">
                            <div class="card-body py-3">
                                <i class="fas fa-list fa-2x mb-2"></i>
                                <h4 class="mb-1">${summary.combined_lessons}</h4>
                                <small>Total Displayed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="card bg-warning text-white border-0">
                            <div class="card-body py-3">
                                <i class="fas fa-chart-line fa-2x mb-2"></i>
                                <h4 class="mb-1">${Math.round((lessons.filter(l => l.status === 'completed').length / lessons.length) * 100)}%</h4>
                                <small>Completion Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add a toggle for display mode
        lessonsHtml += `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Student's Lessons Progress</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary active" id="list-view-btn">
                        <i class="fas fa-list mr-1"></i>List View
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="card-view-btn">
                        <i class="fas fa-th-large mr-1"></i>Card View
                    </button>
                </div>
            </div>
        `;

        lessonsHtml += '<div class="lessons-list" id="lessons-container">';

        // Simple list view (default)
        lessonsHtml += '<div id="simple-list-view">';
        lessons.forEach(function(lesson) {
            const statusBadge = getLessonStatusBadge(lesson.status);
            const typeIcon = lesson.type === 'student_unit'
                ? '<i class="fas fa-chalkboard-teacher text-primary mr-2"></i>'
                : '<i class="fas fa-user-graduate text-info mr-2"></i>';

            lessonsHtml += `
                <div class="list-group-item d-flex justify-content-between align-items-center lesson-list-item ${lesson.type === 'student_unit' ? 'border-left-primary' : 'border-left-light-blue'}">
                    <div class="d-flex align-items-center">
                        ${typeIcon}
                        <span class="lesson-title-simple">${lesson.lesson_title}</span>
                    </div>
                    ${statusBadge}
                </div>
            `;
        });
        lessonsHtml += '</div>';

        // Detailed card view (hidden by default)
        lessonsHtml += '<div id="detailed-card-view" style="display: none;">';
        lessons.forEach(function(lesson) {
            const statusBadge = getLessonStatusBadge(lesson.status);
            const typeBadge = lesson.type === 'student_unit'
                ? '<span class="badge badge-primary"><i class="fas fa-chalkboard-teacher mr-1"></i>Classroom</span>'
                : '<span class="badge badge-light-blue"><i class="fas fa-user-graduate mr-1"></i>Self-Study</span>';

            const priorityIndicator = lesson.is_primary
                ? '<span class="badge badge-warning ml-1" title="Primary lesson record"><i class="fas fa-star"></i> Primary</span>'
                : lesson.is_fallback
                ? '<span class="badge badge-secondary ml-1" title="Fallback after classroom lesson failed"><i class="fas fa-redo"></i> Fallback</span>'
                : '';

            const unitInfo = lesson.unit_name ?
                `<div class="text-muted small mb-1"><i class="fas fa-building mr-1"></i><strong>Unit:</strong> ${lesson.unit_name}</div>` : '';

            const lessonDescription = lesson.lesson_description ?
                `<div class="text-muted small mb-2">${lesson.lesson_description}</div>` : '';

            const scoreInfo = lesson.score !== null ?
                `<div class="text-center">
                    <span class="badge badge-${lesson.score >= 80 ? 'success' : lesson.score >= 60 ? 'warning' : 'danger'} badge-lg">
                        ${lesson.score}%
                    </span>
                </div>` : '';

            const progressInfo = lesson.progress > 0 ?
                `<div class="mt-2">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Progress</span>
                        <span>${lesson.progress}%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-${lesson.progress >= 100 ? 'success' : lesson.progress >= 50 ? 'info' : 'warning'}"
                             style="width: ${lesson.progress}%"></div>
                    </div>
                </div>` : '';

            const dncInfo = lesson.dnc_at ?
                `<div class="alert alert-danger py-2 mt-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>DNC:</strong> ${lesson.dnc_reason || 'Did not complete'}
                    <small class="d-block">${new Date(lesson.dnc_at).toLocaleString()}</small>
                </div>` : '';

            const failedInfo = lesson.failed_at && !lesson.dnc_at ?
                `<div class="alert alert-warning py-2 mt-2">
                    <i class="fas fa-times-circle mr-1"></i>
                    <strong>Failed:</strong> Lesson marked as failed
                    <small class="d-block">${new Date(lesson.failed_at).toLocaleString()}</small>
                </div>` : '';

            const durationInfo = lesson.duration ?
                `<small class="text-muted d-block"><i class="fas fa-clock mr-1"></i>Duration: ${Math.floor(lesson.duration / 60)}m ${lesson.duration % 60}s</small>` : '';

            lessonsHtml += `
                <div class="card mb-3 lesson-card ${lesson.type === 'student_unit' ? 'border-primary' : 'border-light-blue'}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 lesson-title">${lesson.lesson_title}</h6>
                                        <div class="lesson-badges">
                                            ${statusBadge}
                                            ${typeBadge}
                                            ${priorityIndicator}
                                        </div>
                                    </div>
                                </div>
                                ${unitInfo}
                                ${lessonDescription}
                                ${progressInfo}
                                ${dncInfo}
                                ${failedInfo}
                                ${durationInfo}
                            </div>
                            <div class="col-md-4">
                                ${scoreInfo}
                                <div class="lesson-dates mt-3">
                                    ${lesson.started_at ? `<div class="small text-muted mb-1">
                                        <i class="fas fa-play mr-1 text-info"></i>
                                        <strong>Started:</strong><br>
                                        ${new Date(lesson.started_at).toLocaleString()}
                                    </div>` : ''}
                                    ${lesson.completed_at ? `<div class="small text-muted mb-1">
                                        <i class="fas fa-check-circle mr-1 text-success"></i>
                                        <strong>Completed:</strong><br>
                                        ${new Date(lesson.completed_at).toLocaleString()}
                                    </div>` : ''}
                                    <div class="small text-muted">
                                        <i class="fas fa-calendar-plus mr-1"></i>
                                        <strong>Created:</strong><br>
                                        ${new Date(lesson.created_at).toLocaleString()}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        lessonsHtml += '</div>'; // Close detailed-card-view

        lessonsHtml += '</div>'; // Close lessons-container
        $('#student-lessons').html(lessonsHtml);

        // Add toggle functionality
        $('#list-view-btn').click(function() {
            $(this).addClass('active').siblings().removeClass('active');
            $('#simple-list-view').show();
            $('#detailed-card-view').hide();
        });

        $('#card-view-btn').click(function() {
            $(this).addClass('active').siblings().removeClass('active');
            $('#simple-list-view').hide();
            $('#detailed-card-view').show();
        });
    };

    // Function to get status badge for lessons
    window.getLessonStatusBadge = function(status) {
        switch(status) {
            case 'completed':
                return '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Completed</span>';
            case 'in_progress':
                return '<span class="badge badge-warning"><i class="fas fa-clock mr-1"></i>In Progress</span>';
            case 'pending':
                return '<span class="badge badge-info"><i class="fas fa-hourglass-half mr-1"></i>Pending</span>';
            case 'failed':
                return '<span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>Failed</span>';
            case 'dnc':
                return '<span class="badge badge-dark"><i class="fas fa-exclamation-triangle mr-1"></i>DNC</span>';
            case 'not_started':
                return '<span class="badge badge-secondary"><i class="fas fa-play-circle mr-1"></i>Not Started</span>';
            default:
                return '<span class="badge badge-light"><i class="fas fa-question-circle mr-1"></i>Unknown</span>';
        }
    };

    // Handle tab clicks to load appropriate content
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const targetTab = $(e.target).attr('href');
        const currentStudentId = $('#student-details').data('student-id');

        console.log('🔄 Tab clicked:', targetTab, 'Student ID:', currentStudentId);

        if (targetTab === '#lessons-tab' && currentStudentId) {
            console.log('📚 Loading lessons tab for student:', currentStudentId);
            loadStudentLessons(currentStudentId);
        } else if (targetTab === '#activity-tab' && currentStudentId) {
            loadStudentActivity(currentStudentId, 'today');
        } else if (targetTab === '#exams-tab' && currentStudentId) {
            loadStudentExams(currentStudentId);
        } else if (targetTab === '#validations-tab' && currentStudentId) {
            loadStudentValidations(currentStudentId);
        } else if (targetTab === '#history-tab' && currentStudentId) {
            loadStudentClassHistory(currentStudentId);
        }
    });

    // Function to load student activity when Activity tab is clicked
    window.loadStudentActivity = function(studentId, period = 'today') {
        if (!studentId) return;

        console.log('Loading activity for student:', studentId, 'period:', period);
        $('#student-activity').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-muted mb-2"></i><p class="text-muted">Loading student activity...</p></div>');

        $.ajax({
            url: '/admin/frost-support/student/' + studentId + '/activity',
            method: 'GET',
            data: { period: period },
            success: function(response) {
                if (response.success) {
                    displayStudentActivity(response.data, response.period);
                } else {
                    $('#student-activity').html(`
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                            <h6 class="text-muted">Error Loading Activity</h6>
                            <p class="text-muted mb-0">${response.message || 'Failed to load student activity'}</p>
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Failed to load activity:', xhr);
                $('#student-activity').html(`
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                        <h6 class="text-muted">Failed to Load Activity</h6>
                        <p class="text-muted mb-0">Please try again later</p>
                    </div>
                `);
            }
        });
    };

    // Function to display student activity
    window.displayStudentActivity = function(activities, period) {
        if (!activities || activities.length === 0) {
            $('#student-activity').html(`
                <div class="text-center">
                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">No Activity Found</h6>
                    <p class="text-muted mb-0">No student activity for the selected period.</p>
                </div>
            `);
            return;
        }

        // Show activity header and controls
        $('.activity-header').show();
        $('#activity-toggle').show();
        $('#activity-period').text(period === 'today' ? "Today's Activity" : 'Full History');
        $('#activity-count').text(activities.length + (activities.length === 1 ? ' activity' : ' activities'));

        let activityHtml = '<div class="timeline">';

        activities.forEach(function(activity, index) {
            const timeAgo = new Date(activity.created_at).toLocaleString();
            const iconClass = getActivityIcon(activity.type);
            const colorClass = getActivityColor(activity.type);

            activityHtml += `
                <div class="timeline-item">
                    <i class="fas ${iconClass} ${colorClass}"></i>
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${activity.title}</h6>
                                <p class="mb-1 text-muted small">${activity.description}</p>
                                ${activity.details ? `<p class="mb-0 text-info small"><i class="fas fa-info-circle"></i> ${activity.details}</p>` : ''}
                            </div>
                            <small class="text-muted ml-2">${timeAgo}</small>
                        </div>
                    </div>
                </div>
            `;
        });

        activityHtml += '</div>';
        $('#student-activity').html(activityHtml);
    };

    // Helper functions for activity display
    window.getActivityIcon = function(type) {
        switch(type) {
            case 'login': return 'fas fa-sign-in-alt';
            case 'logout': return 'fas fa-sign-out-alt';
            case 'lesson_start': return 'fas fa-play';
            case 'lesson_complete': return 'fas fa-check';
            case 'quiz_start': return 'fas fa-question-circle';
            case 'quiz_complete': return 'fas fa-trophy';
            case 'download': return 'fas fa-download';
            case 'upload': return 'fas fa-upload';
            case 'message': return 'fas fa-envelope';
            case 'support': return 'fas fa-life-ring';
            default: return 'fas fa-info-circle';
        }
    };

    window.getActivityBadge = function(type) {
        switch(type) {
            case 'login': return 'badge-success';
            case 'logout': return 'badge-secondary';
            case 'lesson_complete': return 'badge-success';
            case 'quiz_complete': return 'badge-primary';
            case 'support': return 'badge-warning';
            default: return 'badge-info';
        }
    };

    window.getBorderColor = function(type) {
        switch(type) {
            case 'login': return 'success';
            case 'logout': return 'secondary';
            case 'lesson_complete': return 'success';
            case 'quiz_complete': return 'primary';
            case 'support': return 'warning';
            default: return 'info';
        }
    };

    // Function to load class history (placeholder)
    window.loadStudentClassHistory = function(studentId) {
        console.log('Loading class history for student:', studentId);
        $('#student-class-history').html(`
            <div class="text-center">
                <i class="fas fa-history fa-2x text-info mb-3"></i>
                <h6 class="text-muted">Class History</h6>
                <p class="text-muted mb-0">Class history functionality coming soon...</p>
            </div>
        `);
    };

    // Auto-load activity when student is selected
    window.selectStudent = function(student) {
        console.log('👤 Student selected:', student);
        console.log('📌 Setting student ID:', student.id);

        // Store student ID for tab functionality
        if (typeof setCurrentStudentId === 'function') {
            setCurrentStudentId(student.id);
            console.log('✅ Called setCurrentStudentId function');
        } else {
            $('#student-details').data('student-id', student.id);
            console.log('✅ Set student-id data attribute directly');
        }

        // Verify the student ID was set
        const storedId = $('#student-details').data('student-id');
        console.log('🔍 Verified stored student ID:', storedId);

        // Update the online status indicator based on selected student
        if (student && student.online_status === 'online') {
            updateOnlineStatusIndicator('online', `${student.name} is currently online`);
        } else if (student && student.online_status === 'offline') {
            updateOnlineStatusIndicator('offline', `${student.name} is currently offline`);
        } else {
            updateOnlineStatusIndicator('offline', 'Student status unknown');
        }

        // Hide search and show student details
        console.log('Hiding search container, showing student details');
        $('#search-container').hide();
        $('#student-details').show();

        // Populate student profile information
        $('#student-name').text(student.name || 'Unknown Student');
        $('#student-email').text(student.email || 'No email');

        // Update status badges
        if (student.status === 'active') {
            $('#student-status').removeClass('badge-secondary').addClass('badge-success').text('Active');
        } else {
            $('#student-status').removeClass('badge-success').addClass('badge-secondary').text('Inactive');
        }

        if (student.online_status === 'online') {
            $('#student-online-status').removeClass('badge-secondary').addClass('badge-success').text('Online');
        } else {
            $('#student-online-status').removeClass('badge-success').addClass('badge-secondary').text('Offline');
        }

        // Show unit info if available
        if (student.current_unit) {
            $('#current-unit-name').text(student.current_unit.unit_name);
            $('#student-unit-info').show();
        } else {
            $('#student-unit-info').hide();
        }

        // Auto-load data for all tabs
        setTimeout(function() {
            loadStudentActivity(student.id, 'today');
            loadStudentLessons(student.id);
            loadStudentHistory(student.id);
        }, 100);

        console.log('Student selection completed');
    };

    // Function to get activity color based on type
    window.getActivityColor = function(activityType) {
        const colors = {
            'login': 'success',
            'logout': 'secondary',
            'lesson_start': 'info',
            'lesson_complete': 'success',
            'lesson_failed': 'danger',
            'quiz_start': 'warning',
            'quiz_complete': 'success',
            'quiz_failed': 'danger',
            'download': 'info',
            'upload': 'primary',
            'message': 'info',
            'support': 'warning',
            'default': 'secondary'
        };
        return colors[activityType] || colors['default'];
    };

    // Function to load student history
    window.loadStudentHistory = function(studentId) {
        console.log('🔄 Loading history for student:', studentId);
        $('#student-history').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading history...</span>
                </div>
                <p class="mt-2">Loading student history...</p>
            </div>
        `);

        // TODO: Replace with actual AJAX call to backend
        setTimeout(function() {
            $('#student-history').html(`
                <div class="text-center">
                    <i class="fas fa-history fa-2x text-info mb-3"></i>
                    <h5>Student History</h5>
                    <p class="text-muted">Course history, progress tracking, and milestone achievements will be displayed here.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        This feature is being implemented. Coming soon!
                    </div>
                </div>
            `);
        }, 1000);
    };

    // Function to load student exams with comprehensive metadata
    window.loadStudentExams = function(studentId) {
        console.log('🔄 Loading exams for student:', studentId, 'via AJAX call to:', `/admin/frost-support/student/${studentId}/exams`);

        $('#student-exams').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading exams...</span>
                </div>
                <p class="mt-2">Loading comprehensive exam data...</p>
            </div>
        `);

        $.ajax({
            url: `/admin/frost-support/student/${studentId}/exams`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('✅ Exams data loaded successfully:', response);
                displayStudentExams(response.data, response.summary);
            },
            error: function(xhr, status, error) {
                console.error('❌ Failed to load exams AJAX call:', xhr.status, xhr.statusText, xhr.responseJSON);
                $('#student-exams').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Error Loading Exams:</strong>
                        <p class="mb-0">${xhr.responseJSON?.message || 'Failed to load exam information'}</p>
                    </div>
                `);
            }
        });
    };

    // Function to display comprehensive exam information
    function displayStudentExams(examData, summary) {
        if (!examData || examData.length === 0) {
            $('#student-exams').html(`
                <div class="text-center py-4">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Exam Courses Found</h5>
                    <p class="text-muted">This student has no courses with exams.</p>
                </div>
            `);
            return;
        }

        let summaryHtml = '';
        if (summary) {
            summaryHtml = `
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-clipboard-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Courses</span>
                                <span class="info-box-number">${summary.total_courses_with_exams}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Passed</span>
                                <span class="info-box-number">${summary.courses_passed}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Failed</span>
                                <span class="info-box-number">${summary.courses_failed}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Not Attempted</span>
                                <span class="info-box-number">${summary.courses_not_attempted}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        let coursesHtml = examData.map(course => {
            const statusBadge = getExamStatusBadge(course.course_status, course.statistics);
            const eligibilityBadge = getEligibilityBadge(course.eligibility);

            return `
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-graduation-cap mr-2"></i>
                                ${course.course_title}
                            </h5>
                            <div>
                                ${statusBadge}
                                ${eligibilityBadge}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        ${getExamInfoSection(course.exam_info)}
                        ${getStatisticsSection(course.statistics)}
                        ${getLatestAttemptSection(course.latest_attempt)}
                        ${getAllAttemptsSection(course.all_attempts)}
                        ${getEligibilitySection(course.eligibility)}
                    </div>
                </div>
            `;
        }).join('');

        $('#student-exams').html(summaryHtml + coursesHtml);
    }

    // Helper functions for exam display
    function getExamStatusBadge(status, stats) {
        const badges = {
            'passed': '<span class="badge badge-success"><i class="fas fa-check"></i> Passed</span>',
            'failed': '<span class="badge badge-danger"><i class="fas fa-times"></i> Failed</span>',
            'active': stats.total_attempts > 0 ?
                '<span class="badge badge-warning"><i class="fas fa-clock"></i> Attempted</span>' :
                '<span class="badge badge-info"><i class="fas fa-book"></i> Enrolled</span>',
            'expired': '<span class="badge badge-secondary"><i class="fas fa-calendar-times"></i> Expired</span>',
            'disabled': '<span class="badge badge-dark"><i class="fas fa-ban"></i> Disabled</span>',
            'not_started': '<span class="badge badge-light"><i class="fas fa-pause"></i> Not Started</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">Unknown</span>';
    }

    function getEligibilityBadge(eligibility) {
        const badges = {
            'eligible': '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Eligible</span>',
            'admin_authorized': '<span class="badge badge-primary"><i class="fas fa-user-shield"></i> Admin Auth</span>',
            'already_passed': '<span class="badge badge-info"><i class="fas fa-trophy"></i> Completed</span>',
            'lessons_incomplete': '<span class="badge badge-warning"><i class="fas fa-book-open"></i> Lessons Pending</span>',
            'waiting_period': '<span class="badge badge-secondary"><i class="fas fa-hourglass-half"></i> Waiting</span>',
            'course_inactive': '<span class="badge badge-dark"><i class="fas fa-pause-circle"></i> Inactive</span>',
            'not_eligible': '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Not Eligible</span>'
        };
        return badges[eligibility.status] || '<span class="badge badge-secondary">Unknown</span>';
    }

    function getExamInfoSection(examInfo) {
        return `
            <div class="row mb-3">
                <div class="col-md-12">
                    <h6><i class="fas fa-info-circle text-info"></i> Exam Information</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Questions:</small><br>
                            <strong>${examInfo.num_questions}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">To Pass:</small><br>
                            <strong>${examInfo.num_to_pass} (${examInfo.passing_percentage}%)</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Max Attempts:</small><br>
                            <strong>${examInfo.max_attempts}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Time Limit:</small><br>
                            <strong>${examInfo.time_limit_hours ? examInfo.time_limit_hours + ' hours' : 'None'}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function getStatisticsSection(stats) {
        return `
            <div class="row mb-3">
                <div class="col-md-12">
                    <h6><i class="fas fa-chart-bar text-success"></i> Attempt Statistics</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Total Attempts:</small><br>
                            <strong class="text-primary">${stats.total_attempts}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Passed:</small><br>
                            <strong class="text-success">${stats.passed_attempts}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Failed:</small><br>
                            <strong class="text-danger">${stats.failed_attempts}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Remaining:</small><br>
                            <strong class="text-info">${stats.remaining_attempts}</strong>
                        </div>
                    </div>
                    ${stats.best_score ? `
                        <div class="mt-2">
                            <small class="text-muted">Best Score:</small>
                            <span class="badge badge-${stats.best_score.is_passed ? 'success' : 'warning'} ml-1">
                                ${stats.best_score.raw_score} (${stats.best_score.percentage}%)
                            </span>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    function getLatestAttemptSection(attempt) {
        if (!attempt) {
            return '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No exam attempts yet</div>';
        }

        const statusClass = {
            'passed': 'success',
            'failed': 'danger',
            'expired': 'warning',
            'in_progress': 'info'
        };

        return `
            <div class="row mb-3">
                <div class="col-md-12">
                    <h6><i class="fas fa-clock text-warning"></i> Latest Attempt</h6>
                    <div class="card border-left-${statusClass[attempt.status] || 'secondary'}">
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted">Started:</small><br>
                                    <small>${new Date(attempt.started_at).toLocaleString()}</small>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Status:</small><br>
                                    <span class="badge badge-${statusClass[attempt.status] || 'secondary'}">${attempt.status}</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Score:</small><br>
                                    <strong>${attempt.score || 'N/A'}</strong>
                                    ${attempt.score_percentage ? `<small class="text-muted"> (${attempt.score_percentage}%)</small>` : ''}
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Result:</small><br>
                                    <span class="badge badge-${attempt.is_passed ? 'success' : 'danger'}">
                                        ${attempt.is_passed ? 'PASSED' : 'FAILED'}
                                    </span>
                                </div>
                            </div>
                            ${attempt.next_attempt_at ? `
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <small class="text-muted">Next Attempt Available:</small><br>
                                        <small class="text-info">${new Date(attempt.next_attempt_at).toLocaleString()}</small>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function getAllAttemptsSection(attempts) {
        if (!attempts || attempts.length === 0) {
            return '';
        }

        const attemptsHtml = attempts.map((attempt, index) => `
            <tr>
                <td>${attempts.length - index}</td>
                <td>${new Date(attempt.started_at).toLocaleString()}</td>
                <td>
                    <span class="badge badge-${attempt.is_passed ? 'success' : 'danger'}">
                        ${attempt.status.toUpperCase()}
                    </span>
                </td>
                <td>${attempt.score || 'N/A'}</td>
                <td>${attempt.score_percentage ? attempt.score_percentage + '%' : 'N/A'}</td>
                <td>${attempt.time_taken || 'N/A'}</td>
                <td>
                    <span class="badge badge-${attempt.is_passed ? 'success' : 'danger'}">
                        ${attempt.is_passed ? 'PASSED' : 'FAILED'}
                    </span>
                </td>
            </tr>
        `).join('');

        return `
            <div class="row">
                <div class="col-md-12">
                    <h6><i class="fas fa-history text-secondary"></i> All Attempts History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Duration</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${attemptsHtml}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }

    function getEligibilitySection(eligibility) {
        return `
            <div class="row">
                <div class="col-md-12">
                    <h6><i class="fas fa-shield-alt text-primary"></i> Eligibility Status</h6>
                    <div class="alert alert-${eligibility.can_take_exam ? 'success' : 'warning'} py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${eligibility.status.replace('_', ' ').toUpperCase()}</strong>
                                <p class="mb-0"><small>${eligibility.reason}</small></p>
                            </div>
                            <div>
                                ${eligibility.admin_override ? '<span class="badge badge-warning"><i class="fas fa-user-shield"></i> Admin Override</span>' : ''}
                                ${eligibility.can_take_exam ? '<span class="badge badge-success"><i class="fas fa-check"></i> Can Take Exam</span>' : '<span class="badge badge-danger"><i class="fas fa-times"></i> Cannot Take Exam</span>'}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Function to load student validations
    window.loadStudentValidations = function(studentId) {
        console.log('Loading validations for student:', studentId);
        $('#student-validations').html(`
            <div class="text-center">
                <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading validations...</span>
                </div>
                <p class="mt-2">Loading student validations...</p>
            </div>
        `);

        // Make AJAX call to get validation data
        $.ajax({
            url: `/admin/frost-support/student/${studentId}/validations`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('Validations loaded successfully:', response);
                if (response.success && response.data) {
                    displayStudentValidations(response.data);
                } else {
                    showValidationsError('No validation data available for this student.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading validations:', error);
                console.error('Response:', xhr.responseText);
                let errorMessage = 'Failed to load validation data';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                showValidationsError(errorMessage);
            }
        });
    };

    // Function to display validation data
    function displayStudentValidations(data) {
        const student = data.student;
        const validations = data.validations;
        const summary = data.summary;

        let html = `
            <div class="student-validations-container">
                <!-- Student Header -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <img src="${student.avatar}" alt="${student.name}" class="rounded-circle mr-3" style="width: 50px; height: 50px;">
                            <div>
                                <h5 class="mb-1">${student.name}</h5>
                                <small class="text-muted">${student.email}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="badge-group">
                            <span class="badge badge-secondary">ID Cards: ${summary.id_cards_submitted}/${summary.total_courses}</span>
                            <span class="badge badge-info">Headshots: ${summary.total_headshots_submitted}/${summary.total_headshots_required}</span>
                        </div>
                    </div>
                </div>

                <!-- Course Validation Sections -->
                <div class="validation-courses">
        `;

        if (validations.length === 0) {
            html += `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>No Enrollments Found</strong><br>
                    This student has no course enrollments requiring validation.
                </div>
            `;
        } else {
            validations.forEach(function(courseValidation) {
                html += generateCourseValidationSection(courseValidation);
            });
        }

        html += `
                </div>
            </div>
        `;

        $('#student-validations').html(html);
    }

    // Function to generate course validation section
    function generateCourseValidationSection(courseValidation) {
        const idCard = courseValidation.id_card_validation;
        const headshots = courseValidation.headshot_validations;
        const summary = courseValidation.validation_summary;

        let html = `
            <div class="course-validation-section mb-5">
                <!-- Course Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap text-primary"></i>
                        ${courseValidation.course_title}
                    </h5>
                    <div class="badge-group">
                        <span class="badge ${getCourseStatusBadgeClass(courseValidation.course_status)}">${courseValidation.course_status}</span>
                        <span class="badge badge-secondary">Enrolled: ${formatDate(courseValidation.enrollment_date)}</span>
                    </div>
                </div>

                <!-- ID Card Validation - Card Format -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-id-card text-warning"></i>
                            ID Card Verification (Course Auth: ${idCard.course_auth_id})
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="id-card-photo-container">
                                    <img src="${idCard.photo_url}" alt="ID Card" class="id-card-photo"
                                         onclick="showImageModal('${idCard.photo_url}', 'ID Card - ${courseValidation.course_title}')">
                                    ${!idCard.has_photo ? '<div class="photo-overlay"><i class="fas fa-camera"></i><br>No Photo</div>' : ''}
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="validation-details">
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <strong>Status:</strong><br>
                                            ${getValidationStatusBadge(idCard.status, idCard.has_photo)}
                                        </div>
                                        <div class="col-sm-4">
                                            <strong>ID Type:</strong><br>
                                            <span class="text-muted">${idCard.id_type || 'Not specified'}</span>
                                        </div>
                                        <div class="col-sm-4">
                                            <strong>Has Photo:</strong><br>
                                            <span class="${idCard.has_photo ? 'text-success' : 'text-danger'}">
                                                <i class="fas fa-${idCard.has_photo ? 'check' : 'times'}"></i>
                                                ${idCard.has_photo ? 'Yes' : 'No'}
                                            </span>
                                        </div>
                                    </div>
                                    ${idCard.reject_reason ? `
                                        <div class="alert alert-danger alert-sm">
                                            <strong>Rejection Reason:</strong> ${idCard.reject_reason}
                                        </div>
                                    ` : ''}

                                    <!-- Validation Actions -->
                                    ${idCard.has_photo && idCard.status === 'pending' ? `
                                        <div class="validation-actions">
                                            <form class="validation-form" data-validation-id="${idCard.id}" data-type="approve">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <select name="id_type" class="form-control form-control-sm" required>
                                                            <option value="">Select ID Type</option>
                                                            <option value="Drivers License">Driver's License</option>
                                                            <option value="State Issued ID">State Issued ID</option>
                                                            <option value="Student ID">Student ID</option>
                                                            <option value="Military / Govt ID">Military / Govt ID</option>
                                                            <option value="Passport">Passport</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                            <button type="button" class="btn btn-danger" onclick="showRejectForm(${idCard.id})">
                                                                <i class="fas fa-times"></i> Reject
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="reject-form" id="reject-form-${idCard.id}" style="display: none;">
                                                <form class="validation-form mt-2" data-validation-id="${idCard.id}" data-type="reject">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <textarea name="reject_reason" class="form-control form-control-sm"
                                                                      placeholder="Enter rejection reason..." required rows="2"></textarea>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-times"></i> Confirm Reject
                                                            </button>
                                                            <button type="button" class="btn btn-secondary btn-sm ml-1" onclick="hideRejectForm(${idCard.id})">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Headshots - List Group Format -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-camera text-info"></i>
                            Daily Headshot Verification
                            <span class="badge badge-secondary ml-2">${summary.headshots_submitted}/${summary.total_days} submitted</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
        `;

        if (headshots.length === 0) {
            html += `
                        <div class="alert alert-info m-3">
                            <i class="fas fa-info-circle"></i>
                            No daily attendance records found for this course.
                        </div>
            `;
        } else {
            html += `<div class="list-group list-group-flush">`;

            // Sort headshots by date
            headshots.sort((a, b) => new Date(a.date) - new Date(b.date));

            headshots.forEach(function(headshot) {
                const statusClass = getValidationStatusClass(headshot.status);
                const statusIcon = getValidationStatusIcon(headshot.status);

                html += `
                            <div class="list-group-item ${statusClass}">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <div class="headshot-photo-container-sm">
                                            <img src="${headshot.photo_url}" alt="Headshot ${headshot.date}"
                                                 class="headshot-photo-sm"
                                                 onclick="showImageModal('${headshot.photo_url}', 'Headshot - ${headshot.day_name} ${headshot.date}')">
                                            ${!headshot.has_photo ? '<div class="photo-overlay-sm"><i class="fas fa-camera"></i></div>' : ''}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="headshot-date-info">
                                            <div class="font-weight-bold">${headshot.day_name}</div>
                                            <small class="text-muted">${formatDate(headshot.date)}</small>
                                            <br><small class="text-info">Unit ID: ${headshot.student_unit_id}</small>
                                            ${headshot.unit_status ? `
                                                <br><small class="badge badge-sm ${getUnitStatusBadgeClass(headshot.unit_status)}">${headshot.unit_status}</small>
                                            ` : ''}
                                            ${headshot.lesson_count ? `
                                                <br><small class="text-muted">${headshot.completed_lessons}/${headshot.lesson_count} lessons</small>
                                            ` : ''}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="status-indicator">
                                            <i class="${statusIcon}"></i>
                                            ${getValidationStatusBadge(headshot.status, headshot.has_photo)}
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        ${headshot.reject_reason ? `
                                            <div class="alert alert-danger alert-sm mb-2">
                                                <strong>Rejected:</strong> ${headshot.reject_reason}
                                            </div>
                                        ` : ''}

                                        ${headshot.ejected_at ? `
                                            <div class="alert alert-warning alert-sm mb-2">
                                                <strong>Ejected:</strong> ${headshot.ejected_at}<br>
                                                <small>Reason: ${headshot.ejected_reason || 'Not specified'}</small>
                                            </div>
                                        ` : ''}

                                        ${!headshot.unit_completed && !headshot.ejected_at && headshot.lesson_count > 0 ? `
                                            <div class="alert alert-info alert-sm mb-2">
                                                <strong>Day Status:</strong> ${headshot.unit_status}<br>
                                                <small>Progress: ${headshot.completed_lessons}/${headshot.lesson_count} lessons completed</small>
                                            </div>
                                        ` : ''}

                                        <!-- Headshot Validation Actions -->
                                        ${headshot.has_photo && headshot.status === 'pending' ? `
                                            <div class="validation-actions-sm">
                                                <form class="validation-form d-inline" data-validation-id="${headshot.id}" data-type="approve">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-danger btn-sm ml-1" onclick="showRejectForm(${headshot.id})">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                                <div class="reject-form mt-2" id="reject-form-${headshot.id}" style="display: none;">
                                                    <form class="validation-form" data-validation-id="${headshot.id}" data-type="reject">
                                                        <div class="input-group input-group-sm">
                                                            <textarea name="reject_reason" class="form-control"
                                                                      placeholder="Enter rejection reason..." required rows="1"></textarea>
                                                            <div class="input-group-append">
                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="fas fa-times"></i> Reject
                                                                </button>
                                                                <button type="button" class="btn btn-secondary" onclick="hideRejectForm(${headshot.id})">
                                                                    Cancel
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                `;
            });

            html += `</div>`;
        }

        html += `
                    </div>
                </div>
            </div>
        `;

        return html;
    }    // Helper functions for validation display
    function getValidationStatusBadge(status, hasPhoto) {
        if (!hasPhoto) {
            return '<span class="badge badge-secondary">No Photo</span>';
        }

        switch(status) {
            case 'approved':
                return '<span class="badge badge-success">Approved</span>';
            case 'rejected':
                return '<span class="badge badge-danger">Rejected</span>';
            case 'pending':
                return '<span class="badge badge-warning">Pending</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
    }

    function getValidationStatusClass(status) {
        switch(status) {
            case 'approved': return 'status-approved';
            case 'rejected': return 'status-rejected';
            case 'pending': return 'status-pending';
            case 'missing': return 'status-missing';
            default: return 'status-unknown';
        }
    }

    function getValidationStatusIcon(status) {
        switch(status) {
            case 'approved': return 'fas fa-check-circle text-success';
            case 'rejected': return 'fas fa-times-circle text-danger';
            case 'pending': return 'fas fa-clock text-warning';
            case 'missing': return 'fas fa-question-circle text-muted';
            default: return 'fas fa-question-circle text-muted';
        }
    }

    function getCourseStatusBadgeClass(status) {
        switch(status.toLowerCase()) {
            case 'active': return 'badge-success';
            case 'completed': return 'badge-primary';
            case 'expired': return 'badge-danger';
            case 'inactive': return 'badge-secondary';
            default: return 'badge-secondary';
        }
    }

    function getUnitStatusBadgeClass(status) {
        switch(status.toLowerCase()) {
            case 'completed': return 'badge-success';
            case 'ejected': return 'badge-danger';
            case 'in_progress': return 'badge-warning';
            case 'started': return 'badge-info';
            case 'not_started': return 'badge-secondary';
            default: return 'badge-secondary';
        }
    }

    // Function to show validation error
    function showValidationsError(message) {
        $('#student-validations').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Error Loading Validations</strong><br>
                ${message}
            </div>
        `);
    }

    // Function to show image in modal
    function showImageModal(imageUrl, title) {
        // Create modal if it doesn't exist
        if ($('#imageModal').length === 0) {
            $('body').append(`
                <div class="modal fade" id="imageModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Image Preview</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <img id="modalImage" src="" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }

        $('#imageModal .modal-title').text(title);
        $('#modalImage').attr('src', imageUrl).attr('alt', title);
        $('#imageModal').modal('show');
    }

    // Functions for validation forms
    function showRejectForm(validationId) {
        $(`#reject-form-${validationId}`).slideDown();
    }

    function hideRejectForm(validationId) {
        $(`#reject-form-${validationId}`).slideUp();
        $(`#reject-form-${validationId} textarea`).val('');
    }

    // Handle validation form submissions
    $(document).on('submit', '.validation-form', function(e) {
        e.preventDefault();

        const $form = $(this);
        const validationId = $form.data('validation-id');
        const type = $form.data('type');
        const $submitBtn = $form.find('button[type="submit"]');

        // Disable submit button
        $submitBtn.prop('disabled', true);
        const originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        // Prepare form data
        const formData = {
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if (type === 'approve') {
            const idType = $form.find('select[name="id_type"]').val();
            if (idType) {
                formData.id_type = idType;
            }
        } else if (type === 'reject') {
            formData.reject_reason = $form.find('textarea[name="reject_reason"]').val();
        }

        // Make AJAX request
        $.ajax({
            url: `/admin/frost-support/validation/${validationId}/${type}`,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }

                    // Reload validations to show updated status
                    loadStudentValidations($('#student-details').data('student-id'));
                } else {
                    const errorMsg = response.message || 'Failed to process validation';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMsg);
                    } else {
                        alert(errorMsg);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error processing validation:', error);
                const errorMessage = xhr.responseJSON?.message || 'Failed to process validation';
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
            },
            complete: function() {
                // Re-enable submit button
                $submitBtn.prop('disabled', false);
                $submitBtn.html(originalText);
            }
        });
    });

    // Store student ID when student is selected for tab functionality
    window.setCurrentStudentId = function(studentId) {
        $('#student-details').data('student-id', studentId);
    };

    // Helper functions for validation display
    window.formatDate = function(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    };

    window.formatDateTime = function(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        });
    };

    // Also add the missing selectStudent function that got removed
    // This is called when a student card is clicked
    if (typeof window.selectStudent === 'undefined') {
        // Add the selectStudent function from the previous working version
        console.log('Adding selectStudent function');
    }
});
</script>
@endpush
