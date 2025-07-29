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
                <li class="breadcrumb-item"><a href="{{ route('admin.admin-users.index') }}">Admin Users</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        @if($admin->avatar)
                            <img class="profile-user-img img-fluid img-circle"
                                 src="{{ asset('storage/' . $admin->avatar) }}"
                                 alt="{{ $admin->full_name }}">
                        @elseif($admin->use_gravatar)
                            <img class="profile-user-img img-fluid img-circle"
                                 src="https://www.gravatar.com/avatar/{{ md5(strtolower($admin->email)) }}?s=128&d=identicon"
                                 alt="{{ $admin->full_name }}">
                        @else
                            <div class="profile-user-img img-fluid img-circle d-flex align-items-center justify-content-center bg-light"
                                 style="width: 128px; height: 128px; margin: 0 auto;">
                                <i class="fas fa-user fa-4x text-muted"></i>
                            </div>
                        @endif
                    </div>

                    <h3 class="profile-username text-center">{{ $admin->full_name }}</h3>

                    <p class="text-muted text-center">{{ $admin->Role->name ?? 'N/A' }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
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
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-warning">No</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Using Gravatar</b>
                            <span class="float-right">
                                @if($admin->use_gravatar)
                                    <span class="badge badge-info">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </span>
                        </li>
                    </ul>

                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('admin.admin-users.edit', $admin->id) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-danger btn-block" onclick="deleteAdmin({{ $admin->id }})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Admin Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Admin Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>First Name</strong>
                            <p class="text-muted">{{ $admin->fname }}</p>
                            <hr>

                            <strong>Email Address</strong>
                            <p class="text-muted">{{ $admin->email }}</p>
                            <hr>

                            <strong>Created At</strong>
                            <p class="text-muted">{{ formatUsDate($admin->created_at) }}</p>
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <strong>Last Name</strong>
                            <p class="text-muted">{{ $admin->lname }}</p>
                            <hr>

                            <strong>Role</strong>
                            <p class="text-muted">{{ $admin->Role->name ?? 'N/A' }}</p>
                            <hr>

                            <strong>Last Updated</strong>
                            <p class="text-muted">{{ formatUsDate($admin->updated_at) }}</p>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activity</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-green">{{ formatUsDate($admin->created_at, 'medium_date') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-user bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ formatUsDate($admin->created_at, 'time_only') }}</span>
                                <h3 class="timeline-header">Admin account created</h3>
                                <div class="timeline-body">
                                    Admin user account was created with role: {{ $admin->Role->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        @if($admin->updated_at != $admin->created_at)
                            <div class="time-label">
                                <span class="bg-yellow">{{ formatUsDate($admin->updated_at, 'medium_date') }}</span>
                            </div>
                            <div>
                                <i class="fas fa-edit bg-yellow"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> {{ formatUsDate($admin->updated_at, 'time_only') }}</span>
                                    <h3 class="timeline-header">Profile updated</h3>
                                    <div class="timeline-body">
                                        Admin profile information was last updated.
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .profile-user-img {
            width: 128px;
            height: 128px;
            object-fit: cover;
        }
    </style>
@stop

@section('js')
    <script>
        function deleteAdmin(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.admin-users.destroy', '') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Deleted!', response.message, 'success').then(() => {
                                    window.location.href = '{{ route('admin.admin-users.index') }}';
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        }
    </script>
@stop
