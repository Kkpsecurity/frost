@extends('adminlte::page')

@section('title', 'Role Permissions')

@section('content_header')
    <h1>Role Permissions</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Roles</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($roles as $role)
                        <div class="col-lg-4 col-md-6">
                            <div class="card card-outline {{ $role->id == 1 ? 'card-danger' : ($role->id == 2 ? 'card-warning' : ($role->id == 4 ? 'card-info' : 'card-primary')) }}">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas {{ $role->id == 1 ? 'fa-crown' : ($role->id == 2 ? 'fa-user-shield' : ($role->id == 4 ? 'fa-chalkboard-teacher' : 'fa-user')) }}"></i>
                                        {{ $role->name }}
                                    </h3>
                                    <div class="card-tools">
                                        <span class="badge badge-{{ $role->id == 1 ? 'danger' : ($role->id == 2 ? 'warning' : ($role->id == 4 ? 'info' : 'primary')) }}">
                                            ID: {{ $role->id }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Role ID:</dt>
                                        <dd class="col-sm-7">{{ $role->id }}</dd>

                                        <dt class="col-sm-5">Name:</dt>
                                        <dd class="col-sm-7">{{ $role->name }}</dd>

                                        @if($role->description)
                                        <dt class="col-sm-5">Description:</dt>
                                        <dd class="col-sm-7">{{ $role->description }}</dd>
                                        @endif

                                        <dt class="col-sm-5">Guard:</dt>
                                        <dd class="col-sm-7">{{ $role->guard_name ?? 'web' }}</dd>

                                        <dt class="col-sm-5">Users:</dt>
                                        <dd class="col-sm-7">
                                            <span class="badge badge-info">
                                                {{ \App\Models\User::where('role_id', $role->id)->count() }}
                                            </span>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-sm btn-primary" disabled>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-info" disabled>
                                        <i class="fas fa-key"></i> Permissions
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Information -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Role Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th>Guard</th>
                                    <th>Total Users</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>
                                        <span class="badge badge-{{ $role->id == 1 ? 'danger' : ($role->id == 2 ? 'warning' : ($role->id == 4 ? 'info' : 'primary')) }}">
                                            {{ $role->name }}
                                        </span>
                                    </td>
                                    <td>{{ $role->description ?? 'N/A' }}</td>
                                    <td>{{ $role->guard_name ?? 'web' }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ \App\Models\User::where('role_id', $role->id)->count() }}
                                        </span>
                                    </td>
                                    <td>{{ $role->created_at ? $role->created_at->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Descriptions -->
    <div class="row">
        <div class="col-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book"></i> Role Descriptions & Capabilities
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-crown text-danger"></i> Super Admin</h5>
                            <ul>
                                <li>Full system access</li>
                                <li>Manage all users and roles</li>
                                <li>System configuration</li>
                                <li>Database management</li>
                                <li>Payment gateway settings</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-user-shield text-warning"></i> Admin</h5>
                            <ul>
                                <li>User management</li>
                                <li>Course management</li>
                                <li>Reports and analytics</li>
                                <li>Limited system settings</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-chalkboard-teacher text-info"></i> Instructor</h5>
                            <ul>
                                <li>Teach classes</li>
                                <li>Manage classroom sessions</li>
                                <li>Grade students</li>
                                <li>View student progress</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-user-graduate text-primary"></i> Student</h5>
                            <ul>
                                <li>Enroll in courses</li>
                                <li>Attend classes</li>
                                <li>Submit assignments</li>
                                <li>View progress</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
