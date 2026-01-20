@extends('adminlte::page')

@section('title', 'Edit Instructor')

@section('content_header')
    <h1>Edit Instructor</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Profile Information Card -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Profile Information</h3>
                </div>
                <form action="{{ route('admin.admin-center.instructors.update', $instructor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
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
                                    <input type="text" class="form-control @error('fname') is-invalid @enderror"
                                           id="fname" name="fname" value="{{ old('fname', $instructor->fname) }}" required>
                                    @error('fname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lname">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('lname') is-invalid @enderror"
                                           id="lname" name="lname" value="{{ old('lname', $instructor->lname) }}" required>
                                    @error('lname')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $instructor->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone', $instructor->student_info['phone'] ?? '') }}">
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Account Status</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active"
                                       name="is_active" value="1" {{ old('is_active', $instructor->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Active Account
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Inactive instructors cannot log in to the system
                            </small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" placeholder="Leave blank to keep current password">
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Only enter a password if you want to change it
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation" placeholder="Confirm new password">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Instructor
                        </button>
                        <a href="{{ route('admin.admin-center.instructor-management') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Role & Permissions Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Roles & Permissions</h3>
                </div>
                <form action="{{ route('admin.admin-center.instructors.update', $instructor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="form-group">
                            <label>Assigned Roles</label>
                            @foreach($roles as $role)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                           id="role_{{ $role->id }}"
                                           name="roles[]"
                                           value="{{ $role->id }}"
                                           {{ $instructor->roles->contains($role->id) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="role_{{ $role->id }}">
                                        <strong>{{ $role->name }}</strong>
                                        @if($role->description)
                                            <br><small class="text-muted">{{ $role->description }}</small>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <small>
                                Select the roles you want to assign to this instructor.
                                Multiple roles can be selected.
                            </small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-info btn-block">
                            <i class="fas fa-save"></i> Update Roles
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Stats Card -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Quick Stats</h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">User ID:</dt>
                        <dd class="col-sm-6">{{ $instructor->id }}</dd>

                        <dt class="col-sm-6">Classes Taught:</dt>
                        <dd class="col-sm-6">
                            <span class="badge badge-info">{{ $instructor->instUnits->count() }}</span>
                        </dd>

                        <dt class="col-sm-6">Joined:</dt>
                        <dd class="col-sm-6">{{ $instructor->created_at->format('M d, Y') }}</dd>

                        <dt class="col-sm-6">Last Updated:</dt>
                        <dd class="col-sm-6">{{ $instructor->updated_at->diffForHumans() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@stop
