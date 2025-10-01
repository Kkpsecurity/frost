@extends('adminlte::page')

@section('title', 'View Admin User')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>View Admin User</h1>
        <div>
            <a href="{{ route('admin.admin-center.admin-users.edit', $admin->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit User
            </a>
            <a href="{{ route('admin.admin-center.admin-users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Admin User Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>ID:</strong>
                            <p class="text-muted">{{ $admin->id }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <p>
                                @if($admin->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>First Name:</strong>
                            <p class="text-muted">{{ $admin->fname }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Last Name:</strong>
                            <p class="text-muted">{{ $admin->lname }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <strong>Email:</strong>
                            <p class="text-muted">{{ $admin->email }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Role:</strong>
                            <p class="text-muted">
                                @if($admin->Role)
                                    <span class="badge badge-info">{{ $admin->Role->name }}</span>
                                @else
                                    <span class="badge badge-secondary">No Role</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong>Use Gravatar:</strong>
                            <p class="text-muted">
                                @if($admin->use_gravatar)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Created:</strong>
                            <p class="text-muted">{{ $admin->created_at ? $admin->created_at->format('M d, Y \a\t H:i') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Last Updated:</strong>
                            <p class="text-muted">{{ $admin->updated_at ? $admin->updated_at->format('M d, Y \a\t H:i') : 'N/A' }}</p>
                        </div>
                    </div>

                    @php
                        $studentInfo = is_array($admin->student_info) ? $admin->student_info : json_decode($admin->student_info, true);
                    @endphp
                    @if($studentInfo && is_array($studentInfo))
                    <hr>
                    <h5>Additional Information</h5>
                    <div class="row">
                        @if(isset($studentInfo['phone']))
                        <div class="col-md-6">
                            <strong>Phone:</strong>
                            <p class="text-muted">{{ $studentInfo['phone'] }}</p>
                        </div>
                        @endif
                        @if(isset($studentInfo['dob']))
                        <div class="col-md-6">
                            <strong>Date of Birth:</strong>
                            <p class="text-muted">{{ $studentInfo['dob'] }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Avatar</h3>
                </div>
                <div class="card-body text-center">
                    @if($admin->avatar)
                        <img src="{{ asset('storage/' . $admin->avatar) }}" class="img-circle" width="100" height="100" alt="Avatar">
                    @elseif($admin->use_gravatar)
                        @php
                            $gravatar = 'https://www.gravatar.com/avatar/' . md5(strtolower($admin->email)) . '?s=100&d=identicon';
                        @endphp
                        <img src="{{ $gravatar }}" class="img-circle" width="100" height="100" alt="Gravatar">
                    @else
                        <i class="fas fa-user-circle fa-5x text-muted"></i>
                    @endif
                    <p class="mt-3 text-muted">
                        @if($admin->avatar)
                            Custom Avatar
                        @elseif($admin->use_gravatar)
                            Gravatar
                        @else
                            No Avatar
                        @endif
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.admin-center.admin-users.edit', $admin->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                        @if($admin->is_active)
                            <button class="btn btn-warning btn-sm" onclick="toggleStatus({{ $admin->id }}, false)">
                                <i class="fas fa-pause"></i> Deactivate
                            </button>
                        @else
                            <button class="btn btn-success btn-sm" onclick="toggleStatus({{ $admin->id }}, true)">
                                <i class="fas fa-play"></i> Activate
                            </button>
                        @endif
                        <button class="btn btn-danger btn-sm" onclick="deleteAdmin({{ $admin->id }})">
                            <i class="fas fa-trash"></i> Delete User
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function toggleStatus(adminId, activate) {
            if (confirm('Are you sure you want to ' + (activate ? 'activate' : 'deactivate') + ' this admin user?')) {
                // TODO: Implement status toggle AJAX call
                console.log('Toggle status for admin ID:', adminId, 'to:', activate);
            }
        }

        function deleteAdmin(adminId) {
            if (confirm('Are you sure you want to delete this admin user? This action cannot be undone.')) {
                // TODO: Implement delete AJAX call
                console.log('Delete admin ID:', adminId);
            }
        }
    </script>
@stop
