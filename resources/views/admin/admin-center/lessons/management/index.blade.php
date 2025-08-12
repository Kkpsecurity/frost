@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Page Header with Create Button -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <h3><i class="fas fa-play-circle"></i> {{ $content['title'] }}</h3>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('admin.lessons.management.create') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i> Create Lesson
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $content['stats']['total'] }}</h3>
                            <p>Total Lessons</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $content['stats']['with_units'] }}</h3>
                            <p>Assigned to Units</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $content['stats']['without_units'] }}</h3>
                            <p>Unassigned</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format($content['stats']['total_minutes']) }}</h3>
                            <p>Total Minutes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lessons Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> All Lessons
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-secondary">{{ $content['lessons']->total() }} total</span>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    @if($content['lessons']->count() > 0)
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Lesson Title</th>
                                    <th>Credit Minutes</th>
                                    <th>Video Duration</th>
                                    <th>Course Units</th>
                                    <th>Courses</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($content['lessons'] as $lesson)
                                    <tr>
                                        <td>
                                            <strong>{{ $lesson->title }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $lesson->credit_minutes }} min</span>
                                            <small class="text-muted d-block">{{ $lesson->CreditHours() }} hours</small>
                                        </td>
                                        <td>
                                            @if($lesson->video_seconds > 0)
                                                <span class="badge badge-success">{{ gmdate('H:i:s', $lesson->video_seconds) }}</span>
                                            @else
                                                <span class="text-muted">No video</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $lesson->CourseUnits->count() > 0 ? 'primary' : 'secondary' }}">
                                                {{ $lesson->CourseUnits->count() }} unit{{ $lesson->CourseUnits->count() != 1 ? 's' : '' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $courses = $lesson->CourseUnits->pluck('Course')->unique('id');
                                            @endphp
                                            @if($courses->count() > 0)
                                                @foreach($courses->take(2) as $course)
                                                    <span class="badge badge-outline badge-primary">{{ $course->title }}</span>
                                                @endforeach
                                                @if($courses->count() > 2)
                                                    <span class="badge badge-light">+{{ $courses->count() - 2 }} more</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No courses</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.lessons.management.show', $lesson) }}"
                                                   class="btn btn-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.lessons.management.edit', $lesson) }}"
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-danger btn-sm delete-btn"
                                                        data-id="{{ $lesson->id }}"
                                                        data-title="{{ $lesson->title }}"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-play-circle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No lessons found</h5>
                            <p class="text-muted">Create your first lesson to get started.</p>
                            <a href="{{ route('admin.lessons.management.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Lesson
                            </a>
                        </div>
                    @endif
                </div>
                @if($content['lessons']->hasPages())
                    <div class="card-footer">
                        {{ $content['lessons']->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the lesson <strong id="delete-lesson-title"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone. The lesson will be removed from all course units.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Lesson</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box .inner h3 {
            font-size: 2.2rem;
        }
        .table td {
            vertical-align: middle;
        }
        .btn-group-sm > .btn {
            margin-right: 2px;
        }
        .badge-outline {
            border: 1px solid;
            background: transparent;
        }
        /* Custom grid for 4-column layout */
        @media (min-width: 992px) {
            .col-lg-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Delete confirmation
            $('.delete-btn').on('click', function() {
                const lessonId = $(this).data('id');
                const lessonTitle = $(this).data('title');

                $('#delete-lesson-title').text(lessonTitle);
                $('#deleteForm').attr('action', `/admin/lessons/management/${lessonId}`);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@stop
