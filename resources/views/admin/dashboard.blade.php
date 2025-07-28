@extends('adminlte::page')

@section('title', 'Admin Dashboard - Frost')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0 text-white">Admin Dashboard</h1>
        <div class="text-muted">
            <i class="fas fa-calendar-alt me-1"></i>
            {{ dateGreeter() }}
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Total Users Stats -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\User::count() }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Manage Users <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Total Admins (System + Regular) Stats -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\User::whereIn('role_id', [1, 2])->count() }}</h3>
                    <p>Total Admins</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Manage Admins <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Instructors Stats -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\User::where('role_id', 3)->count() }}</h3>
                    <p>Instructors</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Manage Instructors <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Support Staff Stats -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ \App\Models\User::where('role_id', 4)->count() }}</h3>
                    <p>Support Staff</p>
                </div>
                <div class="icon">
                    <i class="fas fa-headset"></i>
                </div>
                <a href="#" class="small-box-footer">
                </a>
            </div>
        </div>
    </div>

    <!-- Support System Section (Mock Data) -->
    <div class="row mt-4">
        <div class="col-12">
            <h4 class="mb-3">
                <i class="fas fa-life-ring text-primary"></i>
                Support System Overview
                <small class="text-muted">(Coming Soon)</small>
            </h4>
        </div>
    </div>

    <div class="row">
        <!-- Support Tickets Stats -->
        <div class="col-lg-2 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>23</h3>
                    <p>Open Tickets</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <a href="#" class="small-box-footer">
                    View Tickets <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>156</h3>
                    <p>Resolved Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="#" class="small-box-footer">
                    View Resolved <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>8</h3>
                    <p>Urgent Tickets</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="#" class="small-box-footer">
                    View Urgent <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>2.4h</h3>
                    <p>Avg Response</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="#" class="small-box-footer">
                    View Metrics <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>94%</h3>
                    <p>Satisfaction</p>
                </div>
                <div class="icon">
                    <i class="fas fa-smile"></i>
                </div>
                <a href="#" class="small-box-footer">
                    View Feedback <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-2 col-6">
            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>{{ \App\Models\User::where('role_id', 4)->count() }}</h3>
                    <p>Agents Online</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-headset"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Manage Agents <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Support Charts Row -->
    <div class="row">
        <!-- Ticket Trends Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-1"></i>
                        Support Ticket Trends (Last 30 Days)
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <!-- Mock Chart Area -->
                        <div style="height: 250px; background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 5px; display: flex; align-items: center; justify-content: center; border: 2px dashed #dee2e6;">
                            <div class="text-center">
                                <i class="fas fa-chart-line fa-3x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Ticket Trends Chart</p>
                                <small class="text-muted">Will show ticket volume, resolution rates, and response times</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Categories -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tags mr-1"></i>
                        Ticket Categories
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="progress-group">
                        <span class="progress-text">Technical Issues</span>
                        <span class="float-right"><b>38</b>/100</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-danger" style="width: 38%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Account Problems</span>
                        <span class="float-right"><b>25</b>/100</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning" style="width: 25%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Billing Questions</span>
                        <span class="float-right"><b>18</b>/100</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-info" style="width: 18%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Feature Requests</span>
                        <span class="float-right"><b>12</b>/100</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" style="width: 12%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">General Inquiries</span>
                        <span class="float-right"><b>7</b>/100</span>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" style="width: 7%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Support Tickets -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-1"></i>
                        Recent Support Tickets
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-danger">8 Urgent</span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Subject</th>
                                    <th>User</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Agent</th>
                                    <th>Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge badge-secondary">#1247</span></td>
                                    <td>Cannot access course materials</td>
                                    <td>john.doe@example.com</td>
                                    <td><span class="badge badge-danger">Urgent</span></td>
                                    <td><span class="badge badge-warning">In Progress</span></td>
                                    <td>Support Agent 1</td>
                                    <td>2 minutes ago</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-secondary">#1246</span></td>
                                    <td>Payment failed for subscription</td>
                                    <td>jane.smith@example.com</td>
                                    <td><span class="badge badge-warning">High</span></td>
                                    <td><span class="badge badge-primary">Open</span></td>
                                    <td>Support Agent 2</td>
                                    <td>15 minutes ago</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-secondary">#1245</span></td>
                                    <td>Feature request: Dark mode</td>
                                    <td>mike.wilson@example.com</td>
                                    <td><span class="badge badge-info">Low</span></td>
                                    <td><span class="badge badge-success">Resolved</span></td>
                                    <td>Support Agent 1</td>
                                    <td>1 hour ago</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-secondary">#1244</span></td>
                                    <td>Reset password not working</td>
                                    <td>sarah.jones@example.com</td>
                                    <td><span class="badge badge-warning">High</span></td>
                                    <td><span class="badge badge-success">Resolved</span></td>
                                    <td>Support Agent 3</td>
                                    <td>2 hours ago</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-secondary">#1243</span></td>
                                    <td>Course certificate download issue</td>
                                    <td>alex.brown@example.com</td>
                                    <td><span class="badge badge-info">Medium</span></td>
                                    <td><span class="badge badge-warning">In Progress</span></td>
                                    <td>Support Agent 2</td>
                                    <td>3 hours ago</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="#" class="btn btn-primary btn-sm">View All Tickets</a>
                    <a href="#" class="btn btn-success btn-sm">Create New Ticket</a>
                </div>
            </div>
        </div>

        <!-- Support Agent Status -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-1"></i>
                        Support Agents
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-success">{{ \App\Models\User::where('role_id', 4)->count() }} Online</span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="users-list clearfix">
                        <li>
                            <img src="https://via.placeholder.com/40x40/28a745/fff?text=A1" alt="Agent">
                            <span class="users-list-name">Support Agent 1</span>
                            <span class="users-list-date"><i class="fas fa-circle text-success"></i> Online</span>
                        </li>
                        <li>
                            <img src="https://via.placeholder.com/40x40/007bff/fff?text=A2" alt="Agent">
                            <span class="users-list-name">Support Agent 2</span>
                            <span class="users-list-date"><i class="fas fa-circle text-success"></i> Online</span>
                        </li>
                        <li>
                            <img src="https://via.placeholder.com/40x40/ffc107/fff?text=A3" alt="Agent">
                            <span class="users-list-name">Support Agent 3</span>
                            <span class="users-list-date"><i class="fas fa-circle text-warning"></i> Away</span>
                        </li>
                        <li>
                            <img src="https://via.placeholder.com/40x40/6c757d/fff?text=A4" alt="Agent">
                            <span class="users-list-name">Support Agent 4</span>
                            <span class="users-list-date"><i class="fas fa-circle text-secondary"></i> Offline</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6 text-center">
                            <div class="description-block border-right">
                                <span class="description-percentage text-success">
                                    <i class="fas fa-caret-up"></i> 15%
                                </span>
                                <h5 class="description-header">12</h5>
                                <span class="description-text">Tickets Today</span>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="description-block">
                                <span class="description-percentage text-success">
                                    <i class="fas fa-caret-up"></i> 8%
                                </span>
                                <h5 class="description-header">2.1h</h5>
                                <span class="description-text">Avg Response</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Users -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Users</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(\App\Models\User::exists())
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No users registered yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Information</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Laravel Version:</dt>
                        <dd class="col-sm-8">{{ app()->version() }}</dd>

                        <dt class="col-sm-4">PHP Version:</dt>
                        <dd class="col-sm-8">{{ PHP_VERSION }}</dd>

                        <dt class="col-sm-4">Environment:</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                                {{ app()->environment() }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Debug Mode:</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ config('app.debug') ? 'danger' : 'success' }}">
                                {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Logged in as:</dt>
                        <dd class="col-sm-8">{{ auth('admin')->user()->name }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus me-2"></i>Add New User
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-success btn-block">
                                <i class="fas fa-book-plus me-2"></i>Create Course
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-info btn-block">
                                <i class="fas fa-chart-bar me-2"></i>View Reports
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-warning btn-block">
                                <i class="fas fa-cogs me-2"></i>System Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .content-header h1 {
            color: #495057;
            font-weight: 600;
        }

        .small-box .icon {
            top: 10px;
            right: 10px;
        }

        .card-title {
            font-weight: 600;
        }

        .badge {
            font-size: 0.75rem;
        }

        .btn-block {
            width: 100%;
        }
    </style>
@stop

@section('js')
    <script> console.log('AdminLTE Dashboard loaded!'); </script>
@stop
