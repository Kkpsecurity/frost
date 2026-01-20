@extends('adminlte::page')

@section('title', 'Course Dates Scheduler')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Course Dates Scheduler</h1>
        <a href="{{ route('admin.course-dates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Schedule New Course Date
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Scheduled Course Dates</h3>
            <div class="card-tools">
                <form action="{{ route('admin.course-dates.index') }}" method="GET" class="form-inline">
                    <div class="input-group input-group-sm mr-2">
                        <select name="course_id" class="form-control">
                            <option value="">All Courses</option>
                            @if(isset($content['courses']))
                                @foreach($content['courses'] as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="input-group input-group-sm mr-2">
                        <select name="status" class="form-control">
                            <option value="all">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="past" {{ request('status') === 'past' ? 'selected' : '' }}>Past</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if(isset($content['stats']))
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Dates</span>
                                <span class="info-box-number">{{ $content['stats']['total'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Active</span>
                                <span class="info-box-number">{{ $content['stats']['active'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Upcoming</span>
                                <span class="info-box-number">{{ $content['stats']['upcoming'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-calendar-week"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">This Week</span>
                                <span class="info-box-number">{{ $content['stats']['this_week'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Instructor</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($content['course_dates'] as $courseDate)
                            <tr>
                                <td>{{ $courseDate->id }}</td>
                                <td><strong>{{ $courseDate->CourseUnit->Course->title ?? 'N/A' }}</strong></td>
                                <td>{{ $courseDate->date ? $courseDate->date->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    @if($courseDate->starts_at)
                                        {{ $courseDate->starts_at->format('g:i A') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($courseDate->InstUnit && $courseDate->InstUnit->User)
                                        {{ $courseDate->InstUnit->User->fname }} {{ $courseDate->InstUnit->User->lname }}
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($courseDate->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.course-dates.show', $courseDate->id) }}"
                                           class="btn btn-info"
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.course-dates.edit', $courseDate->id) }}"
                                           class="btn btn-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <p class="my-3">No course dates found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($content['course_dates'], 'links'))
                <div class="mt-3">
                    {{ $content['course_dates']->links() }}
                </div>
            @endif
        </div>
    </div>
@stop
