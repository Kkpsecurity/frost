@extends('adminlte::page')

@section('title', 'Create New Admin User')

@section('content_header')
    <h1>
        <i class="fas fa-user-plus"></i> Create New Admin User
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New Administrator Account</h3>
                </div>

                <form action="{{ route('admin.admin-center.admin-users.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <h5><i class="icon fas fa-ban"></i> Errors!</h5>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> You are creating a new administrator account with elevated privileges.
                            Make sure to choose the appropriate role and provide accurate information.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">First Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('fname') is-invalid @enderror"
                                           id="fname"
                                           name="fname"
                                           value="{{ old('fname') }}"
                                           required
                                           placeholder="Enter first name">
                                    @error('fname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Last Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('lname') is-invalid @enderror"
                                           id="lname"
                                           name="lname"
                                           value="{{ old('lname') }}"
                                           required
                                           placeholder="Enter last name">
                                    @error('lname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   placeholder="admin@example.com">
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   placeholder="(555) 123-4567">
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="role_id">User Role <span class="text-danger">*</span></label>
                            <select class="form-control @error('role_id') is-invalid @enderror"
                                    id="role_id"
                                    name="role_id"
                                    required>
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">
                                <strong>System Administrator:</strong> Full system access including user management<br>
                                <strong>Administrator:</strong> General administrative access
                            </small>
                        </div>

                        <hr>

                        <h5 class="mb-3">Account Security</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           required
                                           placeholder="Enter password">
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Minimum 8 characters</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           required
                                           placeholder="Confirm password">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-key"></i>
                            <strong>Security Tip:</strong> Use a strong password with a mix of uppercase, lowercase, numbers, and special characters.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Create Admin User
                        </button>
                        <a href="{{ route('admin.admin-center.admin-users') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
