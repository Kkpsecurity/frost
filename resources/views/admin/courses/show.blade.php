@extends('adminlte::page')

@section('title', 'Course Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Course Details</h1>
        <div>
            <a href="{{ route('admin.courses.management.edit', $content['course']->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Course
            </a>
            <a href="{{ route('admin.courses.management.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Courses
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- Course Info Card -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <h3 class="profile-username text-center">
                        {{ $content['course']->title }}
                    </h3>

                    @if($content['course']->title_long)
                        <p class="text-muted text-center">{{ $content['course']->title_long }}</p>
                    @endif

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Course ID</b>
                            <span class="float-right">{{ $content['course']->id }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Price</b>
                            <span class="float-right">${{ number_format($content['course']->price, 2) }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Duration</b>
                            <span class="float-right">{{ $content['course']->total_minutes }} minutes</span>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b>
                            <span class="float-right">
                                @if($content['course']->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Archived</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item">
                            <b>Created</b>
                            <span class="float-right">
                                @if($content['course']->created_at)
                                    {{ $content['course']->created_at->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </li>
                    </ul>

                    <a href="{{ route('admin.courses.management.edit', $content['course']->id) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Edit Course
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Enrollment Statistics -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Enrollment Statistics
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Enrollments</span>
                                    <span class="info-box-number">{{ $content['course_auths_count'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-user-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Enrollments</span>
                                    <span class="info-box-number">{{ $content['active_enrollments'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Units -->
            @if(isset($content['course_units']) && $content['course_units']->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-book"></i> Course Units
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Unit Title</th>
                                    <th>Admin Title</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($content['course_units'] as $unit)
                                    <tr>
                                        <td>{{ $unit->ordering }}</td>
                                        <td><strong>{{ $unit->title }}</strong></td>
                                        <td>{{ $unit->admin_title ?? 'N/A' }}</td>
                                        <td>
                                            @if($unit->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-book"></i> Course Units
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">No course units configured yet.</p>
                    </div>
                </div>
            @endif

            <!-- Additional Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Additional Information
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        @if($content['course']->exam_id)
                            <dt class="col-sm-4">Exam ID</dt>
                            <dd class="col-sm-8">{{ $content['course']->exam_id }}</dd>
                        @endif

                        @if($content['course']->eq_spec_id)
                            <dt class="col-sm-4">Exam Question Spec ID</dt>
                            <dd class="col-sm-8">{{ $content['course']->eq_spec_id }}</dd>
                        @endif

                        @if($content['course']->zoom_creds_id)
                            <dt class="col-sm-4">Zoom Credentials ID</dt>
                            <dd class="col-sm-8">{{ $content['course']->zoom_creds_id }}</dd>
                        @endif

                        <dt class="col-sm-4">Needs Range</dt>
                        <dd class="col-sm-8">
                            @if($content['course']->needs_range)
                                <span class="badge badge-info">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Last Updated</dt>
                        <dd class="col-sm-8">
                            @if($content['course']->updated_at)
                                {{ $content['course']->updated_at->format('F d, Y g:i A') }}
                            @else
                                N/A
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .info-box-number {
            font-size: 2rem;
        }
    </style>
