@extends('adminlte::page')

@section('title', 'Course Management')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Course Management</h1>
        <a href="{{ route('admin.courses.management.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Course
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Courses</h3>
            <div class="card-tools">
                <form action="{{ route('admin.courses.management.index') }}" method="GET" class="form-inline">
                    <div class="input-group input-group-sm mr-2">
                        <select name="course_type" class="form-control">
                            <option value="">All Course Types</option>
                            <option value="D" {{ request('course_type') === 'D' ? 'selected' : '' }}>D Courses</option>
                            <option value="G" {{ request('course_type') === 'G' ? 'selected' : '' }}>G Courses</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm mr-2">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
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
                            <span class="info-box-icon bg-info"><i class="fas fa-book"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Courses</span>
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
                            <span class="info-box-icon bg-primary"><i class="fas fa-d"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">D Courses</span>
                                <span class="info-box-number">{{ $content['stats']['d_courses'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-g"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">G Courses</span>
                                <span class="info-box-number">{{ $content['stats']['g_courses'] }}</span>
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
                            <th>Title</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($content['courses'] as $course)
                            <tr>
                                <td>{{ $course->id }}</td>
                                <td><strong>{{ $course->title }}</strong></td>
                                <td>${{ number_format($course->price, 2) }}</td>
                                <td>{{ $course->total_minutes }} min</td>
                                <td>
                                    @if($course->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Archived</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.courses.management.show', $course->id) }}"
                                           class="btn btn-info"
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.courses.management.edit', $course->id) }}"
                                           class="btn btn-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    <p class="my-3">No courses found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($content['courses'], 'links'))
                <div class="mt-3">
                    {{ $content['courses']->links() }}
                </div>
            @endif
        </div>
    </div>
@stop
