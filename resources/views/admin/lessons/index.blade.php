@extends('adminlte::page')

@section('title', 'Lesson Management')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Lesson Management</h1>
        <a href="{{ route('admin.lessons.management.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Lesson
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Lessons</h3>
        </div>
        <div class="card-body">
            @if(isset($content['stats']))
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Lessons</span>
                                <span class="info-box-number">{{ $content['stats']['total'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-link"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">With Units</span>
                                <span class="info-box-number">{{ $content['stats']['with_units'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Minutes</span>
                                <span class="info-box-number">{{ number_format($content['stats']['total_minutes']) }}</span>
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
                            <th>Duration</th>
                            <th>Course Units</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($content['lessons'] as $lesson)
                            <tr>
                                <td>{{ $lesson->id }}</td>
                                <td><strong>{{ $lesson->title }}</strong></td>
                                <td>{{ $lesson->credit_minutes }} min</td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $lesson->CourseUnits->count() }} units
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.lessons.management.edit', $lesson->id) }}"
                                           class="btn btn-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <p class="my-3">No lessons found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($content['lessons'], 'links'))
                <div class="mt-3">
                    {{ $content['lessons']->links() }}
                </div>
            @endif
        </div>
    </div>
@stop
