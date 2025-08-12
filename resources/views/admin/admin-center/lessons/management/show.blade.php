@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-play-circle"></i> {{ $content['lesson']->title }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info ml-2">{{ $content['lesson']->credit_minutes }} minutes</span>
                        @if($content['lesson']->video_seconds > 0)
                            <span class="badge badge-success ml-1">{{ gmdate('H:i:s', $content['lesson']->video_seconds) }} video</span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Lesson Information Section -->
                        <div class="col-md-8">
                            <h4>Lesson Information</h4>
                            <div class="row">
                                <!-- Lesson Details Column -->
                                <div class="col-md-6">
                                    <h5>Lesson Details</h5>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="150">Lesson ID</th>
                                            <td>{{ $content['lesson']->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Title</th>
                                            <td>{{ $content['lesson']->title }}</td>
                                        </tr>
                                        <tr>
                                            <th>Credit Minutes</th>
                                            <td>
                                                <span class="badge badge-info">{{ $content['lesson']->credit_minutes }} minutes</span>
                                                <small class="text-muted d-block">{{ $content['lesson']->CreditHours() }} credit hours</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Video Duration</th>
                                            <td>
                                                @if($content['lesson']->video_seconds > 0)
                                                    <span class="badge badge-success">{{ gmdate('H:i:s', $content['lesson']->video_seconds) }}</span>
                                                    <small class="text-muted d-block">{{ $content['lesson']->video_seconds }} seconds</small>
                                                @else
                                                    <span class="text-muted">No video duration set</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($content['lesson']->video_seconds > 0)
                                        <tr>
                                            <th>Self Study Time</th>
                                            <td>
                                                @php
                                                    $selfStudySeconds = $content['lesson']->SelfStudyMinSeconds();
                                                @endphp
                                                @if($selfStudySeconds > 0)
                                                    <span class="badge badge-warning">{{ gmdate('H:i:s', $selfStudySeconds) }}</span>
                                                    <small class="text-muted d-block">Video duration minus 5 minutes</small>
                                                @else
                                                    <span class="text-muted">Video too short for self study</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>

                                <!-- Course Assignments Column -->
                                <div class="col-md-6">
                                    <h5>Course Assignments</h5>
                                    @if($content['lesson']->CourseUnitLessons->count() > 0)
                                        <div class="card">
                                            <div class="card-body p-0">
                                                @foreach($content['lesson']->CourseUnitLessons as $assignment)
                                                    <div class="border-bottom p-3">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <h6 class="mb-1">{{ $assignment->CourseUnit->Course->title }}</h6>
                                                                <p class="mb-1 text-sm">
                                                                    <strong>Unit:</strong> {{ $assignment->CourseUnit->title }}
                                                                    @if($assignment->CourseUnit->admin_title)
                                                                        <br><small class="text-muted">{{ $assignment->CourseUnit->admin_title }}</small>
                                                                    @endif
                                                                </p>
                                                                <p class="mb-0 text-sm">
                                                                    <strong>Progress:</strong> {{ $assignment->progress_minutes }} minutes
                                                                    @if($assignment->progress_minutes != $content['lesson']->credit_minutes)
                                                                        <span class="badge badge-warning badge-sm ml-1">Override</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                            <div class="text-right">
                                                                <span class="badge badge-secondary">Order #{{ $assignment->ordering }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <div class="p-3 bg-light">
                                                    <strong>
                                                        Total: {{ $content['lesson']->CourseUnitLessons->count() }} assignment{{ $content['lesson']->CourseUnitLessons->count() != 1 ? 's' : '' }}
                                                        across {{ $content['stats']['courses_count'] }} course{{ $content['stats']['courses_count'] != 1 ? 's' : '' }}
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> This lesson is not assigned to any course units yet.
                                            <a href="{{ route('admin.lessons.management.edit', $content['lesson']) }}" class="btn btn-sm btn-primary ml-2">
                                                Assign to Course Units
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Column -->
                        <div class="col-md-4">
                            <h4>Lesson Statistics</h4>

                            <!-- Course Units -->
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-list"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Course Units</span>
                                    <span class="info-box-number">{{ $content['stats']['course_units_count'] }}</span>
                                </div>
                            </div>

                            <!-- Courses -->
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-graduation-cap"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Courses</span>
                                    <span class="info-box-number">{{ $content['stats']['courses_count'] }}</span>
                                </div>
                            </div>

                            <!-- Total Progress Minutes -->
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Progress Minutes</span>
                                    <span class="info-box-number">{{ $content['stats']['total_progress_minutes'] }}</span>
                                </div>
                            </div>

                            <!-- Exam Questions -->
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Exam Questions</span>
                                    <span class="info-box-number">{{ $content['stats']['exam_questions_count'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <strong>Lesson ID:</strong> {{ $content['lesson']->id }}
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.lessons.management.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Lessons
                                </a>
                                <a href="{{ route('admin.lessons.management.edit', $content['lesson']) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Lesson
                                </a>
                                @if($content['stats']['exam_questions_count'] == 0)
                                    <button type="button" class="btn btn-danger" id="delete-btn" data-id="{{ $content['lesson']->id }}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                @else
                                    <button type="button" class="btn btn-danger" disabled title="Cannot delete lesson with exam questions">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
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
                    <p>Are you sure you want to delete the lesson <strong>{{ $content['lesson']->title }}</strong>?</p>
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
        .info-box {
            margin-bottom: 15px;
        }
        .table-borderless td {
            border: none;
            padding: 0.3rem 0.75rem;
        }
        .card-outline {
            border-width: 2px;
        }
        .text-sm {
            font-size: 0.875rem;
        }
        .lesson-assignments .card {
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }
        .lesson-assignments .border-bottom:last-child {
            border-bottom: none !important;
        }
        .badge-sm {
            font-size: 0.7em;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Delete confirmation
            $('#delete-btn').on('click', function() {
                const lessonId = $(this).data('id');
                $('#deleteForm').attr('action', `/admin/lessons/management/${lessonId}`);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@stop
