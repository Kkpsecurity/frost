@extends('adminlte::page')

@section('title', 'Instructor Dashboard - Offline Mode')

@section('content_header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="fas fa-chalkboard-teacher text-primary"></i>
                Welcome to Florida Online Dashboard
            </h1>
            <p class="text-muted mb-0">Today's lesson board and class overview</p>
        </div>
        <div class="col-auto">
            <span class="badge badge-secondary badge-lg">
                <i class="fas fa-bulletin-board"></i> Bulletin Board
            </span>
        </div>
    </div>
@endsection

@section('content')
    {{-- Today's Lessons Board --}}
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-day"></i>
                        Today's Lessons - {{ now()->format('l, F j, Y') }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info">3 Lessons</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Time</th>
                                    <th>Course</th>
                                    <th>Lesson</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Sample lesson 1 --}}
                                <tr>
                                    <td>
                                        <strong class="text-primary">09:00 AM</strong><br>
                                        <small class="text-muted">2 hours</small>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>Security Fundamentals</strong><br>
                                            <small class="text-muted">SEC-101</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>Network Security Basics</strong><br>
                                            <small class="text-muted">Module 3</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            24 students
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">Scheduled</span>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Sample lesson 2 --}}
                                <tr>
                                    <td>
                                        <strong class="text-primary">02:00 PM</strong><br>
                                        <small class="text-muted">90 min</small>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>Advanced Cyber Defense</strong><br>
                                            <small class="text-muted">SEC-201</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>Incident Response Planning</strong><br>
                                            <small class="text-muted">Module 5</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            18 students
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">Completed</span>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Sample lesson 3 --}}
                                <tr>
                                    <td>
                                        <strong class="text-primary">04:30 PM</strong><br>
                                        <small class="text-muted">45 min</small>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>Penetration Testing</strong><br>
                                            <small class="text-muted">SEC-301</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>Vulnerability Scanning Lab</strong><br>
                                            <small class="text-muted">Module 7</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            12 students
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">In Progress</span>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Key Class Details and Data --}}
    <div class="row">
        {{-- Class Overview --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Class Overview
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>45</h3>
                                    <p>Total Students</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>6</h3>
                                    <p>Active Courses</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>87%</h3>
                                    <p>Completion Rate</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>12</h3>
                                    <p>Pending Grades</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-plus text-success me-3"></i>
                            <div>
                                <strong>Create New Lesson</strong>
                                <br>
                                <small class="text-muted">Add a new lesson to your course</small>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-upload text-info me-3"></i>
                            <div>
                                <strong>Upload Resources</strong>
                                <br>
                                <small class="text-muted">Add materials and documents</small>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-clipboard-list text-warning me-3"></i>
                            <div>
                                <strong>Grade Assignments</strong>
                                <br>
                                <small class="text-muted">Review and grade student work</small>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-envelope text-primary me-3"></i>
                            <div>
                                <strong>Message Students</strong>
                                <br>
                                <small class="text-muted">Send announcements or updates</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i>
                        Recent Activity
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        {{-- Sample Activity 1 --}}
                        <div class="timeline-item">
                            <i class="fas fa-check-circle bg-success"></i>
                            <div class="timeline-content">
                                <h4>Lesson Completed</h4>
                                <p>Network Security Basics lesson completed by 24 students</p>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                        </div>

                        {{-- Sample Activity 2 --}}
                        <div class="timeline-item">
                            <i class="fas fa-upload bg-info"></i>
                            <div class="timeline-content">
                                <h4>Assignment Submitted</h4>
                                <p>12 new assignments submitted for Security Fundamentals</p>
                                <small class="text-muted">4 hours ago</small>
                            </div>
                        </div>

                        {{-- Sample Activity 3 --}}
                        <div class="timeline-item">
                            <i class="fas fa-star bg-warning"></i>
                            <div class="timeline-content">
                                <h4>Course Rating</h4>
                                <p>Advanced Cyber Defense received 5-star rating from students</p>
                                <small class="text-muted">1 day ago</small>
                            </div>
                        </div>

                        {{-- Sample Activity 4 --}}
                        <div class="timeline-item">
                            <i class="fas fa-plus bg-primary"></i>
                            <div class="timeline-content">
                                <h4>New Course Created</h4>
                                <p>Penetration Testing Fundamentals course published</p>
                                <small class="text-muted">2 days ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-left: 2px solid #e0e0e0;
        }
        .timeline-item:last-child {
            border-left: none;
        }
        .timeline-item i {
            position: absolute;
            left: -45px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            color: white;
        }
        .timeline-content {
            padding-left: 20px;
        }
        .timeline-content h4 {
            margin-bottom: 5px;
            font-size: 1rem;
        }
        .timeline-content p {
            margin-bottom: 5px;
        }
        .small-box .inner h3 {
            font-size: 2rem;
        }
    </style>
@endsection
