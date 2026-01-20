@extends('adminlte::page')

@section('title', 'Instructor Details')

@section('content_header')
    <h1>Instructor Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        @if($instructor->avatar)
                            <img class="profile-user-img img-fluid img-circle"
                                 src="{{ $instructor->avatar }}"
                                 alt="Instructor Avatar">
                        @else
                            <div class="profile-user-img img-fluid img-circle bg-info d-flex align-items-center justify-content-center"
                                 style="width: 100px; height: 100px; margin: 0 auto;">
                                <h2 class="text-white mb-0">{{ strtoupper(substr($instructor->fname, 0, 1)) }}{{ strtoupper(substr($instructor->lname, 0, 1)) }}</h2>
                            </div>
                        @endif
                    </div>

                    <h3 class="profile-username text-center">{{ $instructor->fullname() }}</h3>

                    <p class="text-muted text-center">Instructor</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Status</b>
                            <span class="float-right">
                                @if($instructor->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Classes Taught</b>
                            <span class="float-right badge badge-info">{{ $instructor->instUnits->count() }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Member Since</b>
                            <span class="float-right">{{ $instructor->created_at->format('M d, Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.admin-center.instructors.edit', $instructor->id) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Instructor
                    </a>

                    <form action="{{ route('admin.admin-center.instructors.toggle-status', $instructor->id) }}" method="POST" class="mt-2">
                        @csrf
                        @if($instructor->is_active)
                            <button type="submit" class="btn btn-warning btn-block" onclick="return confirm('Deactivate this instructor?')">
                                <i class="fas fa-ban"></i> Deactivate
                            </button>
                        @else
                            <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Activate this instructor?')">
                                <i class="fas fa-check"></i> Activate
                            </button>
                        @endif
                    </form>

                    <a href="{{ route('admin.admin-center.instructor-management') }}" class="btn btn-default btn-block mt-2">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <!-- Roles Card -->
            @if($instructor->roles->count() > 0)
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Assigned Roles</h3>
                </div>
                <div class="card-body">
                    @foreach($instructor->roles as $role)
                        <span class="badge badge-info mb-1">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-8">
            <!-- Contact Information Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-address-card"></i> Contact Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Full Name:</dt>
                        <dd class="col-sm-8">{{ $instructor->fullname() }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">
                            <a href="mailto:{{ $instructor->email }}">{{ $instructor->email }}</a>
                        </dd>

                        <dt class="col-sm-4">Phone:</dt>
                        <dd class="col-sm-8">{{ $instructor->student_info['phone'] ?? 'Not provided' }}</dd>

                        <dt class="col-sm-4">User ID:</dt>
                        <dd class="col-sm-8">{{ $instructor->id }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Teaching Activity Card -->
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chalkboard-teacher"></i> Teaching Activity</h3>
                </div>
                <div class="card-body">
                    @if($instructor->instUnits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Class ID</th>
                                        <th>Course Date</th>
                                        <th>Created</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($instructor->instUnits->take(10) as $instUnit)
                                    <tr>
                                        <td>{{ $instUnit->id }}</td>
                                        <td>
                                            @if($instUnit->CourseDate)
                                                <a href="{{ route('admin.course-dates.show', $instUnit->course_date_id) }}">
                                                    {{ $instUnit->CourseDate->course_id ?? 'N/A' }}
                                                </a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $instUnit->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($instUnit->completed_at)
                                                <span class="badge badge-success">Completed</span>
                                            @else
                                                <span class="badge badge-info">In Progress</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($instructor->instUnits->count() > 10)
                            <p class="text-muted text-center mt-2">
                                Showing 10 of {{ $instructor->instUnits->count() }} classes
                            </p>
                        @endif
                    @else
                        <p class="text-muted">No classes taught yet.</p>
                    @endif
                </div>
            </div>

            <!-- Activity Timeline Card -->
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-history"></i> Activity Timeline</h3>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li>
                            <i class="fas fa-edit bg-info"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $instructor->updated_at->diffForHumans() }}</span>
                                <h3 class="timeline-header">Profile Updated</h3>
                                <div class="timeline-body">
                                    Last profile update on {{ $instructor->updated_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-user-plus bg-primary"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $instructor->created_at->diffForHumans() }}</span>
                                <h3 class="timeline-header">Account Created</h3>
                                <div class="timeline-body">
                                    Instructor account created on {{ $instructor->created_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                        </li>

                        <li>
                            <i class="fas fa-clock bg-gray"></i>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
