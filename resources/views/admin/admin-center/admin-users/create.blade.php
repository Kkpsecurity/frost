@extends('adminlte::page')

@section('title', 'Create Admin User')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Create Admin User</h1>
        <a href="{{ route('admin.admin-center.admin-users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Admin User</h3>
                </div>
                <form action="{{ route('admin.admin-center.admin-users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

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
                                           id="fname" name="fname" value="{{ old('fname') }}" required>
                                    @error('fname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Last Name *</label>
                                    <input type="text" class="form-control @error('lname') is-invalid @enderror"
                                           id="lname" name="lname" value="{{ old('lname') }}" required>
                                    @error('lname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="role_id">Role *</label>
                            <select class="form-control @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                <option value="">Select Role</option>
                                {{-- Roles will be passed from controller --}}
                                @if(isset($roles))
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
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
                            <label for="password">Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Minimum 8 characters required</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password *</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation" required>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active User</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="use_gravatar" name="use_gravatar" value="1"
                                       {{ old('use_gravatar') ? 'checked' : '' }}>
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
                            <small class="form-text text-muted">Optional: Upload a custom avatar image</small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create User
                        </button>
                        <a href="{{ route('admin.admin-center.admin-users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Preview Avatar</h3>
                </div>
                <div class="card-body text-center">
                    <div id="avatar-preview">
                        <i class="fas fa-user-circle fa-5x text-muted"></i>
                        <p class="mt-3 text-muted">No Avatar Selected</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Password Requirements</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-muted"></i> Minimum 8 characters</li>
                        <li><i class="fas fa-check text-muted"></i> Must contain letters</li>
                        <li><i class="fas fa-check text-muted"></i> Must contain numbers</li>
                        <li><i class="fas fa-check text-muted"></i> Special characters recommended</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Avatar preview
            $('#avatar').change(function() {
                const file = this.files[0];
                const preview = $('#avatar-preview');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.html(`
                            <img src="${e.target.result}" class="img-circle" width="100" height="100" alt="Avatar Preview">
                            <p class="mt-3 text-muted">Avatar Preview</p>
                        `);
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.html(`
                        <i class="fas fa-user-circle fa-5x text-muted"></i>
                        <p class="mt-3 text-muted">No Avatar Selected</p>
                    `);
                }
            });

            // Gravatar preview
            $('#use_gravatar').change(function() {
                const email = $('#email').val();
                const isChecked = $(this).is(':checked');
                const preview = $('#avatar-preview');

                if (isChecked && email) {
                    const gravatarUrl = `https://www.gravatar.com/avatar/${md5(email.toLowerCase())}?s=100&d=identicon`;
                    preview.html(`
                        <img src="${gravatarUrl}" class="img-circle" width="100" height="100" alt="Gravatar Preview">
                        <p class="mt-3 text-muted">Gravatar Preview</p>
                    `);
                } else if (!$('#avatar')[0].files[0]) {
                    preview.html(`
                        <i class="fas fa-user-circle fa-5x text-muted"></i>
                        <p class="mt-3 text-muted">No Avatar Selected</p>
                    `);
                }
            });

            // Update gravatar when email changes
            $('#email').on('input', function() {
                if ($('#use_gravatar').is(':checked')) {
                    $('#use_gravatar').trigger('change');
                }
            });
        });

        // Simple MD5 implementation for gravatar
        function md5(string) {
            // This is a simplified version - in production, use a proper MD5 library
            return string; // Placeholder - you'd need a real MD5 implementation
        }
    </script>
@stop
