@extends('adminlte::page')

@section('title', 'Admin Users')

@section('content_header')
    <h1>
        <i class="fas fa-user-shield"></i> Admin Users Management
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Admin & System Administrator Users</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.admin-center.admin-users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Admin
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $adminUsers->total() }}</h3>
                                    <p>Total Admins</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $adminUsers->where('is_active', true)->count() }}</h3>
                                    <p>Active Admins</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $adminUsers->where('role_id', 1)->count() }}</h3>
                                    <p>System Admins</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-crown"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $adminUsers->where('role_id', 2)->count() }}</h3>
                                    <p>Administrators</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Users Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adminUsers as $admin)
                                <tr>
                                    <td>{{ $admin->id }}</td>
                                    <td>
                                        <strong>{{ $admin->fname }} {{ $admin->lname }}</strong>
                                    </td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        @if($admin->role_id == 1)
                                            <span class="badge badge-warning">
                                                <i class="fas fa-crown"></i> SysAdmin
                                            </span>
                                        @elseif($admin->role_id == 2)
                                            <span class="badge badge-primary">
                                                <i class="fas fa-user-tie"></i> Administrator
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($admin->is_active)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Active
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times"></i> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($admin->last_login)
                                            {{ \Carbon\Carbon::parse($admin->last_login)->diffForHumans() }}
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.admin-center.admin-users.show', $admin->id) }}"
                                               class="btn btn-sm btn-info"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.admin-center.admin-users.edit', $admin->id) }}"
                                               class="btn btn-sm btn-primary"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($admin->role_id != 1 && $admin->id != auth()->id())
                                            <button type="button"
                                                    class="btn btn-sm btn-warning"
                                                    title="Change Role"
                                                    data-toggle="modal"
                                                    data-target="#changeRoleModal{{ $admin->id }}">
                                                <i class="fas fa-user-tag"></i>
                                            </button>
                                            @endif
                                            @if($admin->id != auth()->id())
                                            <form action="{{ route('admin.admin-center.admin-users.delete', $admin->id) }}"
                                                  method="POST"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this admin user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <p class="text-muted py-4">No admin users found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $adminUsers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Role Modals -->
@foreach($adminUsers as $admin)
@if($admin->role_id != 1 && $admin->id != auth()->id())
<div class="modal fade" id="changeRoleModal{{ $admin->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.admin-center.admin-users.change-role', $admin->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="fas fa-user-tag"></i> Change Role for {{ $admin->fname }} {{ $admin->lname }}</h5>
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
                        <strong>Warning:</strong> This will change the user's permissions immediately.
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
@endif
@endforeach
@stop

@section('css')
<style>
    .small-box .icon {
        font-size: 60px;
    }
    .small-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
</style>
@stop

@section('js')
<script>
    console.log('Admin Users page loaded');
</script>
@stop
