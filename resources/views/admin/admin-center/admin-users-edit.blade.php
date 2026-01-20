@extends('adminlte::page')

@section('title', 'Edit Admin User')

@section('content_header')
    <h1>
        <i class="fas fa-user-edit"></i> Edit Admin User
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit User Information</h3>
                </div>

                <form action="{{ route('admin.admin-center.admin-users.update', $admin->id) }}" method="POST">
                    @csrf
                    @method('PUT')

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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fname">First Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('fname') is-invalid @enderror"
                                           id="fname"
                                           name="fname"
                                           value="{{ old('fname', $admin->fname) }}"
                                           required>
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
                                           value="{{ old('lname', $admin->lname) }}"
                                           required>
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
                                   value="{{ old('email', $admin->email) }}"
                                   required>
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
                                   value="{{ old('phone', $admin->phone) }}"
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
                                    required
                                    @if($admin->id == auth()->id()) disabled @endif>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $admin->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @if($admin->id == auth()->id())
                                <small class="text-muted">You cannot change your own role</small>
                                <input type="hidden" name="role_id" value="{{ $admin->role_id }}">
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $admin->is_active) ? 'checked' : '' }}
                                       @if($admin->id == auth()->id()) disabled @endif>
                                <label class="custom-control-label" for="is_active">
                                    Account Active
                                </label>
                            </div>
                            @if($admin->id == auth()->id())
                                <small class="text-muted">You cannot deactivate your own account</small>
                                <input type="hidden" name="is_active" value="1">
                            @endif
                        </div>

                        <hr>

                        <h5 class="mb-3">Change Password (Optional)</h5>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Leave password fields empty if you don't want to change the password
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           placeholder="Enter new password">
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Minimum 8 characters</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.admin-center.admin-users.show', $admin->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <a href="{{ route('admin.admin-center.admin-users') }}" class="btn btn-outline-secondary float-right">
                            <i class="fas fa-list"></i> Back to List
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
