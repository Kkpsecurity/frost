@extends('layouts.app')

@section('content')
    {{-- Title Bar for Navigation --}}
    <div class="title-bar bg-primary text-white">
        <div class="container">
            <div class="row align-items-center py-3">
                <div class="col-md-6">
                    <h4 class="mb-0">
                        <i class="fas fa-graduation-cap"></i>
                        Student Dashboard
                    </h4>
                    <small class="opacity-75">Welcome back, {{ Auth::user()->fname }} {{ Auth::user()->lname }}</small>
                </div>
                <div class="col-md-6 text-md-right">
                    <div class="d-flex justify-content-md-end align-items-center">
                        <div class="mr-3">
                            <small class="opacity-75">Last Login:</small>
                            <br>
                            <small>{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('M j, Y g:i A') : 'First time' }}</small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-light btn-sm dropdown-toggle"
                                    data-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-edit"></i> Edit Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            {{-- Left Sidebar - Student Attendance & Lessons --}}
            <div class="col-lg-3">
                <div class="card sticky-top" style="top: 20px;">
                    {{-- Student Attendance Status --}}
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-check"></i>
                            Student Attendance
                        </h5>
                    </div>
                    <div class="card-body p-3 bg-light">
                        <div class="attendance-status">
                            <div class="row">
                                <div class="col-sm-12">
                                    <strong>{{ Auth::user()->fname }} {{ Auth::user()->lname }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="attendance-info">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">SESSION STATUS</span>
                                    <span class="badge badge-warning" id="session-status">
                                        <i class="fas fa-clock"></i> PENDING START
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">ATTENDANCE TYPE</span>
                                    <span class="badge badge-info" id="attendance-type">
                                        <i class="fas fa-laptop"></i> ONLINE
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-muted">SESSION TIME</span>
                                    <span class="small" id="session-time">--:--:--</span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Session starts when you begin a lesson
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Lessons Section --}}
                    <div class="card-header bg-info text-white border-top">
                        <h6 class="mb-0">
                            <i class="fas fa-book"></i>
                            My Courses & Lessons
                        </h6>
                    </div>
                    <div class="card-body p-0 thin-scrollbar" style="max-height: 600px; overflow-y: auto;">
                        {{-- Current Course Progress --}}
                        @if(isset($currentCourse))
                            <div class="p-3 border-bottom bg-light">
                                <h6 class="text-muted mb-2">CURRENT COURSE</h6>
                                <div class="current-course">
                                    <strong>{{ $currentCourse['title'] ?? 'Security Fundamentals' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $currentCourse['code'] ?? 'SEC-101' }}</small>
                                    <div class="mt-2">
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success"
                                                 style="width: {{ $currentCourse['progress'] ?? 45 }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $currentCourse['progress'] ?? 45 }}% complete • {{ $currentCourse['lessons_completed'] ?? 3 }}/{{ $currentCourse['total_lessons'] ?? 8 }} lessons</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Available Lessons --}}
                        <div class="lessons-list">
                            <div class="p-3 border-bottom">
                                <h6 class="text-muted mb-2">COURSE LESSONS</h6>
                            </div>
                            {{-- Sample lessons matching the screenshot style --}}
                            <div class="lesson-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Security Officer And Private Investigator Licensure</strong>
                                        <br>
                                        <small class="text-muted">Credit Minutes: 60</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm">View</button>
                                </div>
                            </div>
                            <div class="lesson-item p-3 border-bottom bg-success bg-opacity-10">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Definitions And Legal Concepts</strong>
                                        <br>
                                        <small class="text-muted">Credit Minutes: 180</small>
                                    </div>
                                    <button class="btn btn-outline-success btn-sm">View</button>
                                </div>
                            </div>
                            <div class="lesson-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Use Of Force</strong>
                                        <br>
                                        <small class="text-muted">Credit Minutes: 180</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm">View</button>
                                </div>
                            </div>
                            <div class="lesson-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Firearms Safety</strong>
                                        <br>
                                        <small class="text-muted">Credit Minutes: 60</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm">View</button>
                                </div>
                            </div>
                            <div class="lesson-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Firearms Familiarization</strong>
                                        <br>
                                        <small class="text-muted">Credit Minutes: 60</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm">View</button>
                                </div>
                            </div>
                            <div class="lesson-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Fundamentals Of Marksmanship</strong>
                                        <br>
                                        <small class="text-muted">Credit Minutes: 120</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm">View</button>
                                </div>
                            </div>
                            <div class="lesson-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Firearms Mechanics</strong>
                                        <br>
                                        <small class="text-muted">Credit Minutes: 120</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm">View</button>
                                </div>
                            </div>
                            <div class="lesson-item p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Malfunctions</strong>
                                        <br>
                                        <small class="text-muted">Credit Minutes: 60</small>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm">View</button>
                                </div>
                            </div>
                            @forelse($lessons ?? [] as $index => $lesson)
                                <div class="lesson-item p-3 border-bottom {{ $lesson['status'] === 'current' ? 'bg-light' : '' }}">
                                    <div class="d-flex align-items-center">
                                        <div class="lesson-status mr-2">
                                            @if($lesson['status'] === 'completed')
                                                <i class="fas fa-check-circle text-success"></i>
                                            @elseif($lesson['status'] === 'current')
                                                <i class="fas fa-play-circle text-primary"></i>
                                            @elseif($lesson['status'] === 'available')
                                                <i class="far fa-circle text-info"></i>
                                            @else
                                                <i class="fas fa-lock text-muted"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="lesson-title">
                                                <strong>{{ $lesson['title'] ?? "Lesson " . ($index + 1) }}</strong>
                                            </div>
                                            <div class="lesson-meta">
                                                <small class="text-muted">
                                                    {{ $lesson['duration'] ?? '30' }} min
                                                    @if(isset($lesson['type']))
                                                        • {{ $lesson['type'] }}
                                                    @endif
                                                </small>
                                            </div>
                                            @if($lesson['status'] === 'current' || $lesson['status'] === 'available')
                                                <div class="mt-1">
                                                    <a href="{{ $lesson['url'] ?? '#' }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        {{ $lesson['status'] === 'current' ? 'Continue' : 'Start' }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-book fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">No lessons available</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Resources Section --}}
                        <div class="p-3 border-top">
                            <h6 class="text-muted mb-2">RESOURCES</h6>
                            <div class="list-group list-group-flush">
                                @forelse($resources ?? [] as $resource)
                                    <a href="{{ $resource['url'] ?? '#' }}"
                                       class="list-group-item list-group-item-action py-2 border-0">
                                        <i class="fas {{ $resource['icon'] ?? 'fa-file' }} text-info mr-2"></i>
                                        {{ $resource['title'] ?? 'Resource' }}
                                        <small class="text-muted d-block">{{ $resource['type'] ?? 'Document' }}</small>
                                    </a>
                                @empty
                                    <div class="text-center py-2">
                                        <small class="text-muted">No resources available</small>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content Area --}}
            <div class="col-lg-9">
                {{-- Welcome Message & Quick Stats --}}
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-home text-primary"></i>
                                    Welcome to Your Learning Journey
                                </h5>
                                <p class="card-text">
                                    You're doing great! Keep up the momentum and continue your progress.
                                </p>
                                @if(isset($nextLesson))
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Next:</strong> {{ $nextLesson['title'] ?? 'Continue Learning' }}
                                        <a href="{{ $nextLesson['url'] ?? '#' }}" class="btn btn-sm btn-info ml-2">
                                            Start Now
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-gradient-primary text-white">
                            <div class="card-body text-center">
                                <h3>{{ $overallProgress ?? '45' }}%</h3>
                                <p class="mb-0">Overall Progress</p>
                                <div class="progress mt-2 bg-white bg-opacity-25">
                                    <div class="progress-bar bg-white"
                                         style="width: {{ $overallProgress ?? 45 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Class Materials and Activities --}}
                <div class="row">
                    {{-- Current Activity/Lesson Content --}}
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-play-circle text-success"></i>
                                    Current Activity
                                </h5>
                                @if(isset($currentActivity))
                                    <span class="badge badge-success">Active</span>
                                @endif
                            </div>
                            <div class="card-body">
                                @if(isset($currentActivity))
                                    <div class="current-activity">
                                        <h6>{{ $currentActivity['title'] ?? 'Introduction to Cybersecurity' }}</h6>
                                        <p class="text-muted mb-3">{{ $currentActivity['description'] ?? 'Learn the fundamentals of cybersecurity and best practices.' }}</p>

                                        {{-- Activity Content --}}
                                        <div class="activity-content bg-light p-4 rounded">
                                            @if($currentActivity['type'] === 'video')
                                                <div class="video-container">
                                                    <div class="embed-responsive embed-responsive-16by9">
                                                        <div class="embed-responsive-item bg-dark d-flex align-items-center justify-content-center">
                                                            <div class="text-white text-center">
                                                                <i class="fas fa-play fa-3x mb-3"></i>
                                                                <h5>Video Lesson</h5>
                                                                <button class="btn btn-success btn-lg">
                                                                    <i class="fas fa-play"></i> Play Video
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($currentActivity['type'] === 'reading')
                                                <div class="reading-content">
                                                    <i class="fas fa-book fa-2x text-info mb-3"></i>
                                                    <h5>Reading Material</h5>
                                                    <p>{{ $currentActivity['content'] ?? 'Reading content will be displayed here.' }}</p>
                                                    <button class="btn btn-info">
                                                        <i class="fas fa-book-open"></i> Open Reading
                                                    </button>
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="fas fa-tasks fa-2x text-primary mb-3"></i>
                                                    <h5>Interactive Activity</h5>
                                                    <p class="text-muted">Complete the interactive exercises to progress.</p>
                                                    <button class="btn btn-primary btn-lg">
                                                        <i class="fas fa-arrow-right"></i> Start Activity
                                                    </button>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Activity Progress --}}
                                        <div class="activity-progress mt-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">Activity Progress</small>
                                                <small class="text-muted">{{ $currentActivity['progress'] ?? 30 }}%</small>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-success"
                                                     style="width: {{ $currentActivity['progress'] ?? 30 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                        <h5>No Active Activity</h5>
                                        <p class="text-muted">Select a lesson from the sidebar to get started.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar with Additional Info --}}
                    <div class="col-lg-4">
                        {{-- Upcoming Assignments --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-calendar-check text-warning"></i>
                                    Upcoming Assignments
                                </h6>
                            </div>
                            <div class="card-body">
                                @forelse($upcomingAssignments ?? [] as $assignment)
                                    <div class="assignment-item mb-3 p-2 border rounded">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ $assignment['title'] ?? 'Assignment' }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    Due: {{ $assignment['due_date'] ?? 'Tomorrow' }}
                                                </small>
                                            </div>
                                            <span class="badge badge-{{ $assignment['priority'] === 'high' ? 'danger' : 'info' }}">
                                                {{ $assignment['priority'] ?? 'Normal' }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-3">
                                        <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                        <p class="text-muted mb-0">All caught up!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Quick Actions --}}
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-bolt text-primary"></i>
                                    Quick Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <a href="#" class="list-group-item list-group-item-action border-0 px-0">
                                        <i class="fas fa-download text-info mr-2"></i>
                                        Download Certificate
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action border-0 px-0">
                                        <i class="fas fa-chart-line text-success mr-2"></i>
                                        View Progress Report
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action border-0 px-0">
                                        <i class="fas fa-question-circle text-warning mr-2"></i>
                                        Get Help
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action border-0 px-0">
                                        <i class="fas fa-comments text-primary mr-2"></i>
                                        Message Instructor
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden logout form --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
@endsection

@section('styles')
    <style>
        .title-bar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .sticky-top {
            z-index: 1020;
        }

        .lesson-item {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .lesson-item:hover {
            background-color: #f8f9fa !important;
        }

        .lesson-status {
            min-width: 20px;
        }

        .current-course {
            background: white;
            border-radius: 8px;
            padding: 15px;
            border: 2px solid #17a2b8;
        }

        .activity-content {
            border-left: 4px solid #28a745;
        }

        .assignment-item {
            transition: transform 0.2s ease;
        }

        .assignment-item:hover {
            transform: translateX(5px);
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }

        .video-container {
            border-radius: 8px;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .title-bar .col-md-6 {
                text-align: center !important;
            }

            .title-bar .d-flex {
                justify-content: center !important;
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Handle lesson navigation
            $('.lesson-item').on('click', function() {
                if (!$(this).find('.fa-lock').length) {
                    const lessonUrl = $(this).find('a').attr('href');
                    if (lessonUrl && lessonUrl !== '#') {
                        window.location.href = lessonUrl;
                    }
                }
            });

            // Handle activity interactions
            $('.btn-success, .btn-info, .btn-primary').on('click', function(e) {
                if ($(this).attr('href') === undefined) {
                    e.preventDefault();
                    // Handle activity start/continue logic here
                    console.log('Activity action triggered');
                }
            });

            // Auto-save progress periodically
            setInterval(function() {
                // Save current progress to backend
                console.log('Auto-saving progress...');
            }, 30000);
        });
    </script>
@endsection
