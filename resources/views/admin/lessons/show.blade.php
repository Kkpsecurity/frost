@extends('adminlte::page')

@section('title', $content['title'] . ' - Frost Admin')

@section('content_header')
    <x-admin.partials.titlebar
        :title="$content['title']"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Lesson Management', 'url' => route('admin.lessons.index')],
            ['title' => $content['lesson']->title]
        ]"
    />
@endsection

@section('content')
    <div class="row">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Lesson Information --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book"></i> {{ $content['lesson']->title }}
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.lessons.edit', $content['lesson']) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Lesson
                            </a>
                            <a href="{{ route('admin.lessons.units', $content['lesson']) }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-cogs"></i> Manage Units
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteLesson()">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Lesson ID:</dt>
                                <dd class="col-sm-7">#{{ $content['lesson']->id }}</dd>

                                <dt class="col-sm-5">Title:</dt>
                                <dd class="col-sm-7">{{ $content['lesson']->title }}</dd>

                                <dt class="col-sm-5">Credit Minutes:</dt>
                                <dd class="col-sm-7">
                                    {{ $content['lesson']->credit_minutes }} minutes
                                    <small class="text-muted">({{ $content['stats']['credit_hours'] }} hours)</small>
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-5">Video Length:</dt>
                                <dd class="col-sm-7">
                                    @if($content['lesson']->video_seconds > 0)
                                        {{ gmdate('H:i:s', $content['lesson']->video_seconds) }}
                                        <small class="text-muted">({{ $content['stats']['video_hours'] }} hours)</small>
                                    @else
                                        <span class="text-muted">No video</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-5">Self-Study Time:</dt>
                                <dd class="col-sm-7">
                                    @if($content['lesson']->video_seconds > 0)
                                        {{ gmdate('H:i:s', max(0, $content['lesson']->video_seconds - 300)) }}
                                        <small class="text-muted">(video - 5 min)</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course Unit Assignments --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-link"></i> Course Unit Assignments
                        <span class="badge badge-info">{{ $content['stats']['course_units_count'] }}</span>
                    </h3>
                </div>
                <div class="card-body">
                    @if($content['lessons_by_course']->count() > 0)
                        @foreach($content['lessons_by_course'] as $courseTitle => $courseUnitLessons)
                            <div class="course-group mb-4">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-graduation-cap"></i> {{ $courseTitle }}
                                </h5>

                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Course Unit</th>
                                                <th>Progress Minutes</th>
                                                <th>Instructor Time</th>
                                                <th>Ordering</th>
                                                <th width="100">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($courseUnitLessons->sortBy('ordering') as $cul)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $cul->CourseUnit->title }}</strong>
                                                        @if($cul->CourseUnit->admin_title)
                                                            <br><small class="text-muted">{{ $cul->CourseUnit->admin_title }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            {{ $cul->progress_minutes }} min
                                                        </span>
                                                        <br><small class="text-muted">{{ $cul->ProgressHours() }} hours</small>
                                                    </td>
                                                    <td>
                                                        @if($cul->instr_seconds > 0)
                                                            <span class="badge badge-secondary">
                                                                {{ gmdate('H:i:s', $cul->instr_seconds) }}
                                                            </span>
                                                            <br><small class="text-muted">{{ $cul->InstructorHours() }} hours</small>
                                                        @else
                                                            <span class="text-muted">None</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-outline-secondary">
                                                            #{{ $cul->ordering }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="removeAssignment({{ $cul->id }})"
                                                                title="Remove from this unit">
                                                            <i class="fas fa-unlink"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-unlink fa-3x text-muted mb-3"></i>
                            <h5>No Course Unit Assignments</h5>
                            <p class="text-muted">This lesson is not currently assigned to any course units.</p>
                            <a href="{{ route('admin.lessons.units', $content['lesson']) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Assignments
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Exam Questions --}}
            @if($content['lesson']->ExamQuestions->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-question-circle"></i> Associated Exam Questions
                            <span class="badge badge-warning">{{ $content['stats']['exam_questions_count'] }}</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> This lesson has {{ $content['stats']['exam_questions_count'] }} associated exam question(s).
                            The lesson cannot be deleted while exam questions are linked to it.
                        </div>

                        <div class="list-group">
                            @foreach($content['lesson']->ExamQuestions->take(10) as $question)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Question #{{ $question->id }}</h6>
                                        <p class="mb-1">{{ Str::limit(strip_tags($question->question_text), 100) }}</p>
                                        <small class="text-muted">
                                            Exam: {{ $question->Exam->title ?? 'Unknown' }}
                                        </small>
                                    </div>
                                    <span class="badge badge-primary badge-pill">{{ $question->points }} pts</span>
                                </div>
                            @endforeach

                            @if($content['lesson']->ExamQuestions->count() > 10)
                                <div class="list-group-item text-center">
                                    <small class="text-muted">
                                        ...and {{ $content['lesson']->ExamQuestions->count() - 10 }} more questions
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Statistics --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Statistics
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-success">
                                    <i class="fas fa-link"></i>
                                </span>
                                <h5 class="description-header">{{ $content['stats']['course_units_count'] }}</h5>
                                <span class="description-text">Course Units</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="description-block">
                                <span class="description-percentage text-info">
                                    <i class="fas fa-graduation-cap"></i>
                                </span>
                                <h5 class="description-header">{{ $content['stats']['courses_count'] }}</h5>
                                <span class="description-text">Courses</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-warning">
                                    <i class="fas fa-question-circle"></i>
                                </span>
                                <h5 class="description-header">{{ $content['stats']['exam_questions_count'] }}</h5>
                                <span class="description-text">Exam Questions</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="description-block">
                                <span class="description-percentage text-primary">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <h5 class="description-header">{{ $content['stats']['total_progress_minutes'] }}</h5>
                                <span class="description-text">Total Progress Min</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.lessons.edit', $content['lesson']) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-edit"></i> Edit Lesson Information
                        </a>
                        <a href="{{ route('admin.lessons.units', $content['lesson']) }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-cogs"></i> Manage Course Unit Assignments
                        </a>
                        <a href="{{ route('admin.lessons.create') }}" class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> Create Similar Lesson
                        </a>
                        <hr>
                        <a href="{{ route('admin.lessons.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Back to Lesson List
                        </a>
                    </div>
                </div>
            </div>

            {{-- Related Information --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Related Information
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row small">
                        <dt class="col-sm-6">Created:</dt>
                        <dd class="col-sm-6">
                            <span class="text-muted">No timestamp</span>
                        </dd>

                        <dt class="col-sm-6">Modified:</dt>
                        <dd class="col-sm-6">
                            <span class="text-muted">No timestamp</span>
                        </dd>

                        <dt class="col-sm-6">Status:</dt>
                        <dd class="col-sm-6">
                            <span class="badge badge-success">Active</span>
                        </dd>

                        <dt class="col-sm-6">Usage:</dt>
                        <dd class="col-sm-6">
                            @if($content['stats']['course_units_count'] > 0)
                                <span class="badge badge-info">In Use</span>
                            @else
                                <span class="badge badge-warning">Unused</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-trash text-danger"></i> Confirm Delete
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the lesson <strong>"{{ $content['lesson']->title }}"</strong>?</p>

                    @if($content['stats']['exam_questions_count'] > 0)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This lesson has {{ $content['stats']['exam_questions_count'] }} associated exam question(s).
                            The lesson cannot be deleted until these questions are removed or reassigned.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This action cannot be undone. All course unit assignments will also be removed.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    @if($content['stats']['exam_questions_count'] == 0)
                        <form method="POST" action="{{ route('admin.lessons.destroy', $content['lesson']) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete Lesson
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function deleteLesson() {
            $('#deleteModal').modal('show');
        }

        function removeAssignment(courseUnitLessonId) {
            if (confirm('Are you sure you want to remove this lesson from the course unit?')) {
                // Create a form to submit the removal
                const form = $('<form>', {
                    method: 'POST',
                    action: `/admin/lessons/course-unit-lessons/${courseUnitLessonId}/remove`
                });

                form.append($('<input>', { type: 'hidden', name: '_token', value: $('meta[name="csrf-token"]').attr('content') }));
                form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));

                $('body').append(form);
                form.submit();
            }
        }

        $(document).ready(function() {
            // Initialize tooltips
            $('[title]').tooltip();

            // Smooth scroll to sections
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();

                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 500);
                }
            });
        });
    </script>
@endsection

@section('css')
    <style>
        .course-group {
            border-left: 4px solid #007bff;
            padding-left: 1rem;
        }

        .description-block {
            text-align: center;
        }

        .description-header {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0.5rem 0;
        }

        .description-percentage {
            font-size: 1.2rem;
        }

        .description-text {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .border-right {
            border-right: 1px solid #dee2e6 !important;
        }

        .badge-outline-secondary {
            color: #6c757d;
            border: 1px solid #6c757d;
            background: transparent;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .btn-group .btn {
                margin-bottom: 0.25rem;
            }

            .description-block {
                margin-bottom: 1rem;
            }

            .border-right {
                border-right: none !important;
                border-bottom: 1px solid #dee2e6 !important;
                padding-bottom: 1rem;
            }
        }
    </style>
@endsection
