@extends('adminlte::page')

@section('title', 'Edit Admin User')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Edit Admin User</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item">Admin Center</li>
                <li class="breadcrumb-item"><a href="{{ route('admin.admin-center.admin-users.index') }}">Admin Users</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="admin-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-toggle="pill" href="#general" role="tab" aria-controls="general" aria-selected="true">
                                <i class="fas fa-user"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="password-tab" data-toggle="pill" href="#password" role="tab" aria-controls="password" aria-selected="false">
                                <i class="fas fa-lock"></i> Password
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="avatar-tab" data-toggle="pill" href="#avatar" role="tab" aria-controls="avatar" aria-selected="false">
                                <i class="fas fa-image"></i> Avatar
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">

                    <!-- General Tab -->
                    <div class="tab-content" id="admin-tabContent">
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <form action="{{ route('admin.admin-center.admin-users.update', $admin->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fname">First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('fname') is-invalid @enderror"
                                                   id="fname" name="fname" value="{{ old('fname', $admin->fname) }}" required>
                                            @error('fname')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lname">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('lname') is-invalid @enderror"
                                                   id="lname" name="lname" value="{{ old('lname', $admin->lname) }}" required>
                                            @error('lname')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="role_id">Role <span class="text-danger">*</span></label>
                                            <select class="form-control @error('role_id') is-invalid @enderror"
                                                    id="role_id" name="role_id" required>
                                                <option value="">Select Role</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->id }}" {{ old('role_id', $admin->role_id) == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="is_active"
                                                       name="is_active" value="1" {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_active">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update General Info
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                            @if(session('password_success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    {{ session('password_success') }}
                                </div>
                            @endif

                            <form action="{{ route('admin.admin-center.admin-users.password', $admin->id) }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="current_password">Current Password</label>
                                            <input type="password" class="form-control @error('current_password', 'password') is-invalid @enderror"
                                                   id="current_password" name="current_password">
                                            @error('current_password', 'password')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">New Password</label>
                                            <input type="password" class="form-control @error('password', 'password') is-invalid @enderror"
                                                   id="password" name="password">
                                            @error('password', 'password')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Password must be at least 8 characters long.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirm New Password</label>
                                            <input type="password" class="form-control"
                                                   id="password_confirmation" name="password_confirmation">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Avatar Tab -->
                        <div class="tab-pane fade" id="avatar" role="tabpanel" aria-labelledby="avatar-tab">
                            @if(session('avatar_success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    {{ session('avatar_success') }}
                                </div>
                            @endif

                            <form action="{{ route('admin.admin-center.admin-users.avatar', $admin->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="avatar">Upload New Avatar</label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('avatar', 'avatar') is-invalid @enderror"
                                                           id="avatar" name="avatar" accept="image/*">
                                                    <label class="custom-file-label" for="avatar">Choose file</label>
                                                </div>
                                            </div>
                                            @error('avatar', 'avatar')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB.
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="use_gravatar"
                                                       name="use_gravatar" value="1" {{ old('use_gravatar', $admin->use_gravatar) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="use_gravatar">Use Gravatar</label>
                                            </div>
                                            <small class="form-text text-muted">
                                                If enabled, will use Gravatar based on email address.
                                            </small>
                                        </div>

                                        @if($admin->avatar)
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="remove_avatar"
                                                           name="remove_avatar" value="1">
                                                    <label class="custom-control-label" for="remove_avatar">Remove current avatar</label>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-info">
                                                <i class="fas fa-image"></i> Update Avatar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-center">
                                            <label>Current Avatar</label>
                                            <div class="mb-3">
                                                @if($admin->avatar)
                                                    <img src="{{ asset('storage/' . $admin->avatar) }}" alt="Current Avatar"
                                                         class="img-circle elevation-2" style="width: 128px; height: 128px;">
                                                @elseif($admin->use_gravatar)
                                                    <img src="https://www.gravatar.com/avatar/{{ md5(strtolower($admin->email)) }}?s=128&d=identicon"
                                                         alt="Gravatar" class="img-circle elevation-2" style="width: 128px; height: 128px;">
                                                @else
                                                    <i class="fas fa-user-circle fa-8x text-muted"></i>
                                                @endif
                                            </div>

                                            <label>Preview</label>
                                            <div>
                                                <img id="avatar-preview" src="" alt="Avatar Preview"
                                                     class="img-circle elevation-2" style="width: 128px; height: 128px; display: none;">
                                                <p class="text-muted" id="avatar-placeholder">
                                                    <i class="fas fa-eye fa-2x text-muted"></i><br>
                                                    Preview will appear here
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('admin.admin-center.admin-users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('admin.admin-center.admin-users.show', $admin->id) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .nav-tabs .nav-link {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Custom file input
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);

                // Preview avatar
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#avatar-preview').attr('src', e.target.result).show();
                        $('#avatar-placeholder').hide();
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Form validation feedback
            $('form').on('submit', function() {
                $(this).find('button[type="submit"]').prop('disabled', true);
            });

            // Handle success messages from session
            @if(session('success'))
                toastr.success('{{ session('success') }}');
            @endif
        });
    </script>
@stop
