@extends('adminlte::page')

@section('title', 'Admin Dashboard - Frost')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Admin Dashboard</h1>
        <div class="text-muted">
            <i class="fas fa-calendar-alt me-1"></i>
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Users Stats -->
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

        <!-- Admins Stats -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\Admin::count() }}</h3>
                    <p>Admin Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Manage Admins <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Courses Stats -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>0</h3>
                    <p>Active Courses</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Manage Courses <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>$0</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="#" class="small-box-footer">
                    View Reports <i class="fas fa-arrow-circle-right"></i>
                </a>
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
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>53<sup style="font-size: 20px">%</sup></h3>
                                    <p>Bounce Rate</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>44</h3>
                                    <p>User Registrations</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>65</h3>
                                    <p>Unique Visitors</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('AdminLTE Dashboard loaded!'); </script>
@stop
