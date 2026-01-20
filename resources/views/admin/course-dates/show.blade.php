@extends('adminlte::page')

@section('title', 'Course Date Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Course Date Details</h1>
        <div>
            <a href="{{ route('admin.course-dates.edit', $content['course_date']->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- Left Column - Course Date Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Class Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Course:</dt>
                        <dd class="col-sm-8">
                            <strong>{{ $content['course_date']->CourseUnit->Course->title ?? 'N/A' }}</strong>
                        </dd>

                        <dt class="col-sm-4">Course Unit:</dt>
                        <dd class="col-sm-8">{{ $content['course_date']->CourseUnit->title ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Start Time:</dt>
                        <dd class="col-sm-8">
                            <i class="fas fa-calendar"></i>
                            {{ $content['course_date']->starts_at ? $content['course_date']->starts_at->format('M d, Y g:i A') : 'Not set' }}
                        </dd>

                        <dt class="col-sm-4">End Time:</dt>
                        <dd class="col-sm-8">
                            <i class="fas fa-calendar-check"></i>
                            {{ $content['course_date']->ends_at ? $content['course_date']->ends_at->format('M d, Y g:i A') : 'Not set' }}
                        </dd>

                        <dt class="col-sm-4">Duration:</dt>
                        <dd class="col-sm-8">
                            @if($content['course_date']->starts_at && $content['course_date']->ends_at)
                                {{ $content['course_date']->starts_at->diffInMinutes($content['course_date']->ends_at) }} minutes
                            @else
                                N/A
                            @endif
                        </dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($content['course_date']->is_active)
                                <span class="badge badge-success"><i class="fas fa-check"></i> Active</span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-times"></i> Inactive</span>
                            @endif
                        </dd>

                        @if($content['course_date']->zoom_meeting_id)
                            <dt class="col-sm-4">Zoom Meeting:</dt>
                            <dd class="col-sm-8">
                                <code>{{ $content['course_date']->zoom_meeting_id }}</code>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Instructor Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Instructor</h3>
                </div>
                <div class="card-body">
                    @if($content['course_date']->InstUnit && $content['course_date']->InstUnit->User)
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                @if($content['course_date']->InstUnit->User->avatar)
                                    <img src="{{ $content['course_date']->InstUnit->User->avatar }}"
                                         alt="Instructor"
                                         class="img-circle"
                                         style="width: 50px; height: 50px;">
                                @else
                                    <div class="img-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px;">
                                        <span style="font-size: 1.5rem;">{{ strtoupper(substr($content['course_date']->InstUnit->User->fname, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $content['course_date']->InstUnit->User->fname }} {{ $content['course_date']->InstUnit->User->lname }}</h5>
                                <p class="text-muted mb-0">{{ $content['course_date']->InstUnit->User->email }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">No instructor assigned</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Students -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Enrolled Students
                        <span class="badge badge-info">{{ $content['course_date']->StudentUnits->count() }}</span>
                    </h3>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @if($content['course_date']->StudentUnits->count() > 0)
                        <div class="list-group">
                            @foreach($content['course_date']->StudentUnits as $studentUnit)
                                @if($studentUnit->User)
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    @if($studentUnit->User->avatar)
                                                        <img src="{{ $studentUnit->User->avatar }}"
                                                             alt="Student"
                                                             class="img-circle"
                                                             style="width: 40px; height: 40px;">
                                                    @else
                                                        <div class="img-circle bg-info text-white d-flex align-items-center justify-content-center"
                                                             style="width: 40px; height: 40px;">
                                                            <span>{{ strtoupper(substr($studentUnit->User->fname, 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $studentUnit->User->fname }} {{ $studentUnit->User->lname }}</h6>
                                                    <small class="text-muted">{{ $studentUnit->User->email }}</small>
                                                </div>
                                            </div>
                                            <div>
                                                @if($studentUnit->completed_at)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> Completed
                                                    </span>
                                                @elseif($studentUnit->started_at)
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-clock"></i> In Progress
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-hourglass-start"></i> Not Started
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($studentUnit->completed_at)
                                            <small class="text-muted d-block mt-2">
                                                Completed: {{ $studentUnit->completed_at->format('M d, Y g:i A') }}
                                            </small>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>No students enrolled yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline / Activity Log -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Class Timeline</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Activity timeline coming soon...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
