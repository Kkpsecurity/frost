@extends('adminlte::page')

@section('title', 'Edit Admin User')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Edit Admin User</h1>
        <div>
            <a href="{{ route('admin.admin-center.admin-users.show', $admin->id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View User
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
                    <h3 class="card-title">Edit Admin User</h3>
                </div>
                <form action="{{ route('admin.admin-center.admin-users.update', $admin->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">First Name *</label>
                                    <input type="text" class="form-control @error('fname') is-invalid @enderror"
                                           id="fname" name="fname" value="{{ old('fname', $admin->fname) }}" required>
                                    @error('fname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Last Name *</label>
                                    <input type="text" class="form-control @error('lname') is-invalid @enderror"
                                           id="lname" name="lname" value="{{ old('lname', $admin->lname) }}" required>
                                    @error('lname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="role_id">Role *</label>
                            <select class="form-control @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                <option value="">Select Role</option>
                                @if(isset($roles))
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $admin->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('role_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" placeholder="Leave blank to keep current password">
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Only fill this if you want to change the password</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                   placeholder="Confirm new password">
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active User</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="use_gravatar" name="use_gravatar" value="1"
                                       {{ old('use_gravatar', $admin->use_gravatar) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="use_gravatar">Use Gravatar</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="avatar">Upload Avatar</label>
                            <input type="file" class="form-control-file @error('avatar') is-invalid @enderror"
                                   id="avatar" name="avatar" accept="image/*">
                            @error('avatar')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            @if($admin->avatar)
                                <small class="form-text text-muted">Current avatar will be replaced if you select a new file</small>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update User
                        </button>
                        <a href="{{ route('admin.admin-center.admin-users.show', $admin->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Avatar</h3>
                </div>
                <div class="card-body text-center">
                    @if($admin->avatar)
                        <img src="{{ asset('storage/' . $admin->avatar) }}" class="img-circle" width="100" height="100" alt="Current Avatar">
                        <p class="mt-3 text-muted">Custom Avatar</p>
                    @elseif($admin->use_gravatar)
                        @php
                            $gravatar = 'https://www.gravatar.com/avatar/' . md5(strtolower($admin->email)) . '?s=100&d=identicon';
                        @endphp
                        <img src="{{ $gravatar }}" class="img-circle" width="100" height="100" alt="Gravatar">
                        <p class="mt-3 text-muted">Gravatar</p>
                    @else
                        <i class="fas fa-user-circle fa-5x text-muted"></i>
                        <p class="mt-3 text-muted">No Avatar</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Information</h3>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> {{ $admin->id }}</p>
                    <p><strong>Created:</strong> {{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'N/A' }}</p>
                    <p><strong>Last Updated:</strong> {{ $admin->updated_at ? $admin->updated_at->format('M d, Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Show/hide password confirmation when password field has value
            $('#password').on('input', function() {
                const passwordValue = $(this).val();
                const confirmDiv = $('#password_confirmation').closest('.form-group');

                if (passwordValue) {
                    confirmDiv.show();
                    $('#password_confirmation').attr('required', true);
                } else {
                    confirmDiv.hide();
                    $('#password_confirmation').removeAttr('required').val('');
                }
            });

            // Trigger on page load
            $('#password').trigger('input');
        });
    </script>
@stop
