@extends('adminlte::page')

@section('title', 'Admin User Details')

@section('content_header')
    <h1>
        <i class="fas fa-user-shield"></i> Admin User Details
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- User Profile Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                    </div>

                    <h3 class="profile-username text-center">{{ $admin->fname }} {{ $admin->lname }}</h3>

                    <p class="text-muted text-center">
                        @if($admin->role_id == 1)
                            <span class="badge badge-warning badge-lg">
                                <i class="fas fa-crown"></i> System Administrator
                            </span>
                        @else
                            <span class="badge badge-primary badge-lg">
                                <i class="fas fa-user-tie"></i> Administrator
                            </span>
                        @endif
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>User ID</b> <a class="float-right">{{ $admin->id }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b>
                            <span class="float-right">
                                @if($admin->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Email Verified</b>
                            <span class="float-right">
                                @if($admin->email_verified_at)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Yes
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-times"></i> No
                                    </span>
                                @endif
                            </span>
                        </li>
                    </ul>

                    <a href="{{ route('admin.admin-center.admin-users.edit', $admin->id) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> <b>Edit Profile</b>
                    </a>
                    <a href="{{ route('admin.admin-center.admin-users') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> <b>Back to List</b>
                    </a>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="card-body">
                    @if($admin->id != auth()->id())
                        <form action="{{ route('admin.admin-center.admin-users.toggle-status', $admin->id) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-{{ $admin->is_active ? 'warning' : 'success' }} btn-block">
                                <i class="fas fa-power-off"></i>
                                {{ $admin->is_active ? 'Deactivate' : 'Activate' }} Account
                            </button>
                        </form>

                        @if($admin->role_id != 1)
                        <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#changeRoleModal">
                            <i class="fas fa-user-tag"></i> Change Role
                        </button>
                        @endif

                        <form action="{{ route('admin.admin-center.admin-users.delete', $admin->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this admin user? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Delete User
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            You cannot perform actions on your own account
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- User Information Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> User Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">First Name</th>
                            <td>{{ $admin->fname }}</td>
                        </tr>
                        <tr>
                            <th>Last Name</th>
                            <td>{{ $admin->lname }}</td>
                        </tr>
                        <tr>
                            <th>Email Address</th>
                            <td>{{ $admin->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $admin->phone ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>
                                @if($admin->role_id == 1)
                                    <span class="badge badge-warning">System Administrator</span>
                                @else
                                    <span class="badge badge-primary">Administrator</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Account Status</th>
                            <td>
                                @if($admin->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Activity Timeline Card -->
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Activity Timeline</h3>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li>
                            <i class="fas fa-edit bg-info"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $admin->updated_at->diffForHumans() }}</span>
                                <h3 class="timeline-header">Profile Updated</h3>
                                <div class="timeline-body">
                                    Last profile update on {{ $admin->updated_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-user-plus bg-primary"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $admin->created_at->diffForHumans() }}</span>
                                <h3 class="timeline-header">Account Created</h3>
                                <div class="timeline-body">
                                    User account created on {{ $admin->created_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-clock bg-gray"></i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Role Modal -->
<div class="modal fade" id="changeRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.admin-center.admin-users.change-role', $admin->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="fas fa-user-tag"></i> Change User Role</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select New Role</label>
                        <select name="role_id" class="form-control" required>
                            <option value="1" {{ $admin->role_id == 1 ? 'selected' : '' }}>System Administrator</option>
                            <option value="2" {{ $admin->role_id == 2 ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> Changing the user role will affect their permissions immediately.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Change Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .timeline {
        position: relative;
        margin: 0 0 30px 0;
        padding: 0;
        list-style: none;
    }
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #ddd;
        left: 31px;
        margin: 0;
        border-radius: 2px;
    }
    .timeline > li {
        position: relative;
        margin-right: 10px;
        margin-bottom: 15px;
    }
    .timeline > li > .timeline-item {
        margin-top: 0;
        border: 0;
        border-radius: 3px;
        background: #fff;
        color: #444;
        margin-left: 60px;
        margin-right: 15px;
        padding: 10px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    }
    .timeline > li > .fas,
    .timeline > li > .far,
    .timeline > li > .fab {
        width: 30px;
        height: 30px;
        font-size: 15px;
        line-height: 30px;
        position: absolute;
        color: #666;
        background: #d2d6de;
        border-radius: 50%;
        text-align: center;
        left: 18px;
        top: 0;
    }
    .timeline-header {
        margin: 0;
        color: #555;
        border-bottom: 1px solid #f4f4f4;
        padding: 5px 0;
        font-size: 16px;
        line-height: 1.1;
    }
    .timeline-body,
    .timeline-footer {
        padding: 10px 0;
    }
    .time {
        color: #999;
        float: right;
        padding: 10px;
        font-size: 12px;
    }
</style>
@stop
