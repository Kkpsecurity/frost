<x-frontend.site.site-wrapper :title="($classroomData['course']->title ?? 'Course') . ' - Classroom'">
    <x-slot:head>
        <meta name="description" content="Student Classroom for {{ $classroomData['course']->title ?? 'Course' }}">
        <meta name="keywords" content="classroom,student,course,learning">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <main class="main-page-content frost-secondary-bg">
        <div class="container-fluid gap-0 p-0" style="min-height: 60vh; padding-top: 90px;">
            {{-- React App Mount Point --}}
            <div id="student-classroom-container" style="min-height:60vh;"></div>

            {{-- Classroom Props Data --}}
            <script id="student-classroom-props" type="application/json">
                {!! json_encode([
                    'student' => $classroomData['student'] ?? null,
                    'courseAuth' => $classroomData['course_auth'] ?? null,
                    'course' => $classroomData['course'] ?? null,
                    'lessons' => $classroomData['lessons'] ?? [],
                    'modality' => $classroomData['modality'] ?? 'unknown',
                    'current_day_only' => $classroomData['current_day_only'] ?? false
                ]) !!}
            </script>
        </div>
    </main>

    <x-frontend.site.partials.footer />

    <x-slot:scripts>
        @vite(['resources/js/React/Student/classroom.tsx'])
    </x-slot:scripts>
</x-frontend.site.site-wrapper>

    <!-- Course Information Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card frost-primary-bg text-white">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="card-title mb-3">
                                <i class="fas fa-book me-2"></i>
                                {{ $classroomData['course']->title }}
                            </h4>
                            @if($classroomData['course']->description)
                                <p class="card-text text-white-75">
                                    {{ $classroomData['course']->description }}
                                </p>
                            @endif

                            <!-- Course Progress -->
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="fw-bold">Course Progress</small>
                                    <small>{{ $classroomData['course_auth']->progress ?? 0 }}% Complete</small>
                                </div>
                                <div class="progress shadow-sm" style="height: 8px; border-radius: 10px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                         style="width: {{ $classroomData['course_auth']->progress ?? 0 }}%;
                                                background: linear-gradient(45deg, #28a745, #20c997);
                                                border-radius: 10px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 text-end">
                            <!-- Course Stats -->
                            <div class="course-stats">
                                <div class="mb-2">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <small>
                                        Started: {{ $classroomData['course_auth']->start_date ? \Carbon\Carbon::parse($classroomData['course_auth']->start_date)->format('M j, Y') : 'Not Started' }}
                                    </small>
                                </div>

                                @if($classroomData['course_auth']->completed_at)
                                    <div class="mb-2">
                                        <i class="fas fa-check-circle me-2 text-success"></i>
                                        <small>
                                            Completed: {{ \Carbon\Carbon::parse($classroomData['course_auth']->completed_at)->format('M j, Y') }}
                                        </small>
                                    </div>
                                @endif

                                <div class="mb-2">
                                    <i class="fas fa-clock me-2"></i>
                                    <small>
                                        Last Access: {{ $classroomData['course_auth']->updated_at->format('M j, Y g:i A') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Classroom Interface -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-white">
                <div class="card-body p-4">
                    <!-- Coming Soon Message -->
                    <div class="text-center py-5">
                        <i class="fas fa-tools fs-1 mb-4 text-muted"></i>
                        <h3 class="mb-3">Classroom Interface Coming Soon</h3>
                        <p class="text-white-50 mb-4">
                            We're working on building an amazing classroom experience for you.<br>
                            This interface will include lessons, assignments, progress tracking, and more.
                        </p>

                        <!-- Course Status Actions -->
                        <div class="d-flex justify-content-center gap-3">
                            @if(!$classroomData['course_auth']->completed_at)
                                <button class="btn btn-primary btn-lg shadow-sm">
                                    <i class="fas fa-play me-2"></i>
                                    Continue Learning
                                </button>
                            @else
                                <button class="btn btn-info btn-lg shadow-sm">
                                    <i class="fas fa-eye me-2"></i>
                                    Review Course
                                </button>
                            @endif

                            <button class="btn btn-outline-light btn-lg">
                                <i class="fas fa-download me-2"></i>
                                Download Materials
                            </button>
                        </div>
                    </div>

                    <!-- Placeholder Content Areas -->
                    <div class="row mt-5">
                        <div class="col-md-4">
                            <div class="border border-secondary rounded p-3 text-center">
                                <i class="fas fa-list-ol fs-2 mb-3 text-muted"></i>
                                <h5>Lesson List</h5>
                                <p class="text-muted small">Track your progress through course lessons</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border border-secondary rounded p-3 text-center">
                                <i class="fas fa-video fs-2 mb-3 text-muted"></i>
                                <h5>Video Player</h5>
                                <p class="text-muted small">Watch course videos and lectures</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border border-secondary rounded p-3 text-center">
                                <i class="fas fa-tasks fs-2 mb-3 text-muted"></i>
                                <h5>Assignments</h5>
                                <p class="text-muted small">Complete assignments and quizzes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Debug Information (Remove in Production) -->
@if(config('app.debug'))
    <div class="container-lg mt-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">Debug Information</h6>
                <small>
                    <strong>Course Auth ID:</strong> {{ $classroomData['course_auth']->id ?? 'N/A' }}<br>
                    <strong>Course ID:</strong> {{ $classroomData['course']->id ?? 'N/A' }}<br>
                    <strong>Student ID:</strong> {{ $classroomData['student']->id ?? 'N/A' }}<br>
                    <strong>Course Status:</strong> {{ $classroomData['course_auth']->completed_at ? 'Completed' : 'In Progress' }}
                </small>
            </div>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
    .frost-primary-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .course-stats small {
        display: block;
        line-height: 1.8;
    }

    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
</style>
@endpush
