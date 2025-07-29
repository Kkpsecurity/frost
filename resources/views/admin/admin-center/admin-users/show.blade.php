@extends('adminlte::page')

@section('title', 'Admin User Details')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Admin User Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Admin Center</li>
                <li class="breadcrumb-item"><a href="{{ route('admin.admin-center.admin-users.index') }}">Admin Users</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> {{ $admin->fname }} {{ $admin->lname }}
                    </h3>
                    <div class="card-tools">
                        @if($admin->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Avatar Column -->
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                @if($admin->avatar)
                                    <img src="{{ asset('storage/' . $admin->avatar) }}" alt="Avatar"
                                         class="img-circle elevation-2" style="width: 150px; height: 150px;">
                                @elseif($admin->use_gravatar)
                                    <img src="https://www.gravatar.com/avatar/{{ md5(strtolower($admin->email)) }}?s=150&d=identicon"
                                         alt="Gravatar" class="img-circle elevation-2" style="width: 150px; height: 150px;">
                                @else
                                    <i class="fas fa-user-circle fa-9x text-muted"></i>
                                @endif
                            </div>

                            @if($admin->use_gravatar)
                                <p class="text-muted">
                                    <i class="fas fa-external-link-alt"></i> Using Gravatar
                                </p>
                            @endif
                        </div>

                        <!-- Details Column -->
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user"></i> Personal Information</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <th style="width: 30%">First Name:</th>
                                            <td>{{ $admin->fname }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Name:</th>
                                            <td>{{ $admin->lname }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>
                                                <a href="mailto:{{ $admin->email }}">{{ $admin->email }}</a>
                                                @if($admin->email_verified_at)
                                                    <i class="fas fa-check-circle text-success ml-1" title="Verified"></i>
                                                @else
                                                    <i class="fas fa-exclamation-circle text-warning ml-1" title="Unverified"></i>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
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

                                <div class="col-md-6">
                                    <h5><i class="fas fa-shield-alt"></i> Role & Permissions</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <th style="width: 30%">Role:</th>
                                            <td>
                                                <span class="badge badge-primary">{{ $admin->role->name }}</span>
                                            </td>
                                        </tr>
                                        @if($admin->role->description)
                                        <tr>
                                            <th>Description:</th>
                                            <td>{{ $admin->role->description }}</td>
                                        </tr>
                                        @endif
                                    </table>

                                    @if($admin->role->permissions && $admin->role->permissions->count() > 0)
                                        <h6><i class="fas fa-key"></i> Permissions</h6>
                                        <div class="permissions-list">
                                            @foreach($admin->role->permissions as $permission)
                                                <span class="badge badge-secondary mr-1 mb-1">
                                                    {{ $permission->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-clock"></i> Timestamps</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <th style="width: 30%">Created:</th>
                                            <td>
                                                {{ formatUsDate($admin->created_at, 'datetime_medium') }}
                                                <small class="text-muted">({{ $admin->created_at->diffForHumans() }})</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Updated:</th>
                                            <td>
                                                {{ formatUsDate($admin->updated_at, 'datetime_medium') }}
                                                <small class="text-muted">({{ $admin->updated_at->diffForHumans() }})</small>
                                            </td>
                                        </tr>
                                        @if($admin->email_verified_at)
                                        <tr>
                                            <th>Email Verified:</th>
                                            <td>
                                                {{ formatUsDate($admin->email_verified_at) }}
                                                <small class="text-muted">({{ $admin->email_verified_at->diffForHumans() }})</small>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5><i class="fas fa-chart-line"></i> Activity Summary</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <th style="width: 30%">Total Logins:</th>
                                            <td>{{ $admin->login_count ?? 0 }}</td>
                                        </tr>
                                        @if($admin->last_login_at)
                                        <tr>
                                            <th>Last Login:</th>
                                            <td>
                                                {{ formatUsDate($admin->last_login_at) }}
                                                <small class="text-muted">({{ $admin->last_login_at->diffForHumans() }})</small>
                                            </td>
                                        </tr>
                                        @endif
                                        @if($admin->last_login_ip)
                                        <tr>
                                            <th>Last Login IP:</th>
                                            <td>{{ $admin->last_login_ip }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            @if($admin->bio || $admin->notes)
                            <hr>

                            <div class="row">
                                @if($admin->bio)
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user-edit"></i> Bio</h5>
                                    <p>{{ $admin->bio }}</p>
                                </div>
                                @endif

                                @if($admin->notes)
                                <div class="col-md-6">
                                    <h5><i class="fas fa-sticky-note"></i> Notes</h5>
                                    <p>{{ $admin->notes }}</p>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.admin-center.admin-users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.admin-center.admin-users.edit', $admin->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit User
                            </a>

                            @if($admin->is_active)
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#deactivateModal">
                                    <i class="fas fa-user-times"></i> Deactivate
                                </button>
                            @else
                                <form style="display: inline;" method="POST" action="{{ route('admin.admin-center.admin-users.activate', $admin->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to activate this user?')">
                                        <i class="fas fa-user-check"></i> Activate
                                    </button>
                                </form>
                            @endif

                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deactivate Modal -->
    @if($admin->is_active)
    <div class="modal fade" id="deactivateModal" tabindex="-1" role="dialog" aria-labelledby="deactivateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deactivateModalLabel">Deactivate User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to deactivate <strong>{{ $admin->fname }} {{ $admin->lname }}</strong>?</p>
                    <p>This will prevent them from logging in, but their account data will be preserved.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form style="display: inline;" method="POST" action="{{ route('admin.admin-center.admin-users.deactivate', $admin->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-user-times"></i> Deactivate User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to permanently delete <strong>{{ $admin->fname }} {{ $admin->lname }}</strong>?</p>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone. All user data will be permanently removed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form style="display: inline;" method="POST" action="{{ route('admin.admin-center.admin-users.destroy', $admin->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .permissions-list {
            max-height: 150px;
            overflow-y: auto;
        }

        .table th {
            border-top: none;
            font-weight: 600;
        }

        .table td {
            border-top: 1px solid #dee2e6;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Handle success messages from session
            @if(session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            @if(session('warning'))
                toastr.warning('{{ session('warning') }}');
            @endif

            @if(session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });
    </script>
@stop
