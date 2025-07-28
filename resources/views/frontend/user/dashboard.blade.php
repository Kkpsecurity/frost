@extends('layouts.frontend')

@section('title', 'Dashboard - Frost')

@section('content')
<!-- Dashboard Header -->
<section class="dashboard-header py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="dashboard-title">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="dashboard-subtitle">Your learning journey continues here</p>
            </div>
        </div>
    </div>
</section>

<!-- Dashboard Content -->
<section class="dashboard-content py-5">
    <div class="container">
        <div class="row">
            <!-- Stats Cards -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-book text-primary"></i>
                    </div>
                    <div class="stats-info">
                        <h3>5</h3>
                        <p>Enrolled Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-trophy text-warning"></i>
                    </div>
                    <div class="stats-info">
                        <h3>3</h3>
                        <p>Completed Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-certificate text-success"></i>
                    </div>
                    <div class="stats-info">
                        <h3>2</h3>
                        <p>Certificates Earned</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-clock text-info"></i>
                    </div>
                    <div class="stats-info">
                        <h3>24</h3>
                        <p>Hours Studied</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Current Courses -->
            <div class="col-lg-8 mb-4">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h4>Current Courses</h4>
                    </div>
                    <div class="card-body">
                        <div class="course-item mb-3">
                            <div class="course-info">
                                <h5>Laravel Web Development</h5>
                                <p class="text-muted">Learn to build modern web applications with Laravel</p>
                                <div class="progress mb-2">
                                    <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small>75% Complete</small>
                            </div>
                            <div class="course-actions">
                                <a href="#" class="btn btn-primary btn-sm">Continue</a>
                            </div>
                        </div>

                        <div class="course-item mb-3">
                            <div class="course-info">
                                <h5>Vue.js Frontend Development</h5>
                                <p class="text-muted">Master modern frontend development with Vue.js</p>
                                <div class="progress mb-2">
                                    <div class="progress-bar" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small>45% Complete</small>
                            </div>
                            <div class="course-actions">
                                <a href="#" class="btn btn-primary btn-sm">Continue</a>
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="#" class="btn btn-outline-primary">View All Courses</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4 mb-4">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h4>Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="quick-action-item">
                            <a href="#" class="btn btn-outline-primary btn-block mb-3">
                                <i class="fas fa-search me-2"></i>Browse Courses
                            </a>
                        </div>
                        <div class="quick-action-item">
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-block mb-3">
                                <i class="fas fa-user me-2"></i>Edit Profile
                            </a>
                        </div>
                        <div class="quick-action-item">
                            <a href="#" class="btn btn-outline-info btn-block mb-3">
                                <i class="fas fa-download me-2"></i>Download Certificates
                            </a>
                        </div>
                        <div class="quick-action-item">
                            <a href="{{ route('contact') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-question-circle me-2"></i>Get Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h4>Recent Activity</h4>
                    </div>
                    <div class="card-body">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-play-circle text-primary"></i>
                            </div>
                            <div class="activity-info">
                                <p><strong>Completed lesson:</strong> "Laravel Routing Basics" in Laravel Web Development</p>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-trophy text-warning"></i>
                            </div>
                            <div class="activity-info">
                                <p><strong>Achievement unlocked:</strong> "First Course Completed"</p>
                                <small class="text-muted">1 day ago</small>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-user-plus text-success"></i>
                            </div>
                            <div class="activity-info">
                                <p><strong>Enrolled in:</strong> Vue.js Frontend Development</p>
                                <small class="text-muted">3 days ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-bottom: 1px solid #e2e8f0;
    }

    .dashboard-title {
        color: #1e293b;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .dashboard-subtitle {
        color: #64748b;
        font-size: 1.1rem;
        margin: 0;
    }

    .stats-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    .stats-icon {
        margin-right: 1rem;
    }

    .stats-info h3 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: #1e293b;
    }

    .stats-info p {
        margin: 0;
        color: #64748b;
        font-weight: 500;
    }

    .dashboard-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 10px 10px 0 0 !important;
        padding: 1rem 1.5rem;
    }

    .card-header h4 {
        margin: 0;
        color: #1e293b;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .course-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .course-info {
        flex: 1;
    }

    .course-info h5 {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .course-actions {
        margin-left: 1rem;
    }

    .quick-action-item .btn {
        width: 100%;
        text-align: left;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-weight: 500;
    }

    .activity-item {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        margin-right: 1rem;
        width: 40px;
        text-align: center;
    }

    .activity-info p {
        margin: 0;
        color: #374151;
    }

    .activity-info small {
        color: #6b7280;
    }
</style>
@endpush
@endsection
