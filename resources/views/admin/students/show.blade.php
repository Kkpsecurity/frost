@extends('adminlte::page')

@section('title', 'Student Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Student Details</h1>
        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Students
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- Student Profile Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ $student->avatar_url ?? 'https://via.placeholder.com/128' }}"
                             alt="Student profile picture">
                    </div>
                    <h3 class="profile-username text-center">
                        {{ $student->fname }} {{ $student->lname }}
                    </h3>
                    <p class="text-muted text-center">
                        User ID: <strong>#{{ $student->id }}</strong>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Email</b>
                            <span class="float-right">{{ $student->email }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b>
                            <span class="float-right">
                                @if($student->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Courses Enrolled</b>
                            <span class="float-right">{{ $student->courseAuths->count() }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Units Enrolled</b>
                            <span class="float-right">{{ $studentUnits->count() }}</span>
                        </li>
                    </ul>

                    <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Course Information -->
            @if($student->courseAuths->isNotEmpty())
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-graduation-cap"></i> Enrolled Courses
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Course ID</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Enrolled Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->courseAuths as $courseAuth)
                                        <tr>
                                            <td>{{ $courseAuth->id }}</td>
                                            <td>{{ $courseAuth->title ?? 'N/A' }}</td>
                                            <td>
                                                @if($courseAuth->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $courseAuth->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Student Units -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book"></i> Enrolled Units
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Unit</th>
                                <th>Course Date</th>
                                <th>Status</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studentUnits as $studentUnit)
                                <tr>
                                    <td>
                                        <strong>{{ $studentUnit->courseUnit->title ?? 'Unit #' . $studentUnit->course_unit_id }}</strong>
                                    </td>
                                    <td>
                                        @if($studentUnit->courseDate)
                                            {{ $studentUnit->courseDate->date->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">Not scheduled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($studentUnit->completed)
                                            <span class="badge badge-success">Completed</span>
                                        @else
                                            <span class="badge badge-warning">In Progress</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Simplified progress calculation
                                            $progress = $studentUnit->completed ? 100 : 50;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: {{ $progress }}%"
                                                 aria-valuenow="{{ $progress }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                {{ $progress }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No units enrolled
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Recent Activity
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="time-label">
                            <span class="bg-primary">Account Created</span>
                        </div>
                        <div>
                            <i class="fas fa-user bg-info"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock"></i> {{ $student->created_at->diffForHumans() }}
                                </span>
                                <h3 class="timeline-header">Student registered</h3>
                                <div class="timeline-body">
                                    Account created on {{ $student->created_at->format('F d, Y g:i A') }}
                                </div>
                            </div>
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
            border: 3px solid #adb5bd;
            margin: 0 auto;
            padding: 3px;
            width: 100px;
        }
    </style>
@stop
