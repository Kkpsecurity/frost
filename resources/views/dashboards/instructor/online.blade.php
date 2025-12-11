@extends('adminlte::page')

@section('title', 'Instructor Dashboard - Online Class Mode')

@section('content_header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-0">
                <i class="fas fa-video text-success"></i>
                Instructor Classroom
            </h1>
            <p class="text-muted mb-0">Managing live online class session</p>
        </div>
        <div class="col-auto">
            <span class="badge badge-success badge-lg pulse-badge">
                <i class="fas fa-broadcast-tower"></i> LIVE
            </span>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        {{-- Left Sidebar - Lessons and Resources --}}
        <div class="col-lg-3">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-primary">
                    <h3 class="card-title text-white">
                        <i class="fas fa-list"></i>
                        Lessons & Resources
                    </h3>
                </div>
                <div class="card-body p-0 thin-scrollbar" style="max-height: 600px; overflow-y: auto;">
                    {{-- Current Lesson --}}
                    <div class="p-3 bg-light border-bottom">
                        <h6 class="text-muted mb-2">CURRENT LESSON</h6>
                        <div class="current-lesson">
                            <div class="d-flex align-items-center">
                                <div class="lesson-indicator bg-success"></div>
                                <div>
                                    <strong>Network Security Basics</strong>
                                    <br>
                                    <small class="text-muted">Module 3 • 90 min</small>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 65%"></div>
                                </div>
                                <small class="text-muted">65% complete</small>
                            </div>
                        </div>
                    </div>

                    {{-- Lesson List --}}
                    <div class="lesson-list">
                        {{-- Lesson 1 - Completed --}}
                        <div class="lesson-item p-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="lesson-number">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div class="ml-2">
                                        <div class="lesson-title">Introduction to Security</div>
                                        <small class="text-muted">45 min</small>
                                    </div>
                                </div>
                                <div class="lesson-actions">
                                    <!-- Completed lessons don't have play button -->
                                </div>
                            </div>
                        </div>

                        {{-- Lesson 2 - Completed --}}
                        <div class="lesson-item p-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="lesson-number">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div class="ml-2">
                                        <div class="lesson-title">Threat Landscape</div>
                                        <small class="text-muted">60 min</small>
                                    </div>
                                </div>
                                <div class="lesson-actions">
                                    <!-- Completed lessons don't have play button -->
                                </div>
                            </div>
                        </div>

                        {{-- Lesson 3 - Current --}}
                        <div class="lesson-item p-3 border-bottom bg-light">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="lesson-number">
                                        <i class="fas fa-play-circle text-primary"></i>
                                    </div>
                                    <div class="ml-2">
                                        <div class="lesson-title">Network Security Basics</div>
                                        <small class="text-muted">90 min</small>
                                    </div>
                                </div>
                                <div class="lesson-actions">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Lesson 4 - Future --}}
                        <div class="lesson-item p-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="lesson-number">
                                        <span class="badge badge-secondary">4</span>
                                    </div>
                                    <div class="ml-2">
                                        <div class="lesson-title">Firewall Configuration</div>
                                        <small class="text-muted">75 min</small>
                                    </div>
                                </div>
                                <div class="lesson-actions">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Lesson 5 - Future --}}
                        <div class="lesson-item p-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="lesson-number">
                                        <span class="badge badge-secondary">5</span>
                                    </div>
                                    <div class="ml-2">
                                        <div class="lesson-title">Vulnerability Assessment</div>
                                        <small class="text-muted">120 min</small>
                                    </div>
                                </div>
                                <div class="lesson-actions">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Resources Section --}}
                    <div class="p-3 border-top">
                        <h6 class="text-muted mb-2">RESOURCES</h6>
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action py-2 border-0">
                                <i class="fas fa-file-pdf text-primary mr-2"></i>
                                Network Security Guide
                                <small class="text-muted d-block">PDF Document</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-2 border-0">
                                <i class="fas fa-video text-primary mr-2"></i>
                                Security Best Practices
                                <small class="text-muted d-block">Video Tutorial</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-2 border-0">
                                <i class="fas fa-link text-primary mr-2"></i>
                                External Resources
                                <small class="text-muted d-block">Web Link</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action py-2 border-0">
                                <i class="fas fa-file-alt text-primary mr-2"></i>
                                Lab Instructions
                                <small class="text-muted d-block">Text Document</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Center Area - Zoom Player and Tools --}}
        <div class="col-lg-6">
            {{-- Zoom Player Container --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-video text-primary"></i>
                        Live Class Session
                    </h5>
                    <div class="class-controls">
                        <button class="btn btn-sm btn-success" id="start-zoom">
                            <i class="fas fa-video"></i> Start Zoom
                        </button>
                        <button class="btn btn-sm btn-warning" id="mute-all">
                            <i class="fas fa-microphone-slash"></i>
                        </button>
                        <button class="btn btn-sm btn-info" id="share-screen">
                            <i class="fas fa-desktop"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="zoom-container" style="height: 400px; background: #000; display: flex; align-items: center; justify-content: center;">
                        <div class="text-white text-center">
                            <i class="fas fa-video fa-3x mb-3"></i>
                            <h5>Zoom Meeting Container</h5>
                            <p>Click "Start Zoom" to begin the live session</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Class Chat --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-comments text-info"></i>
                        Class Chat
                        <span class="badge badge-info ml-2">5</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div id="chat-messages" class="thin-scrollbar" style="height: 250px; overflow-y: auto; padding: 15px;">
                        {{-- Sample Chat Message 1 --}}
                        <div class="chat-message mb-2">
                            <div class="d-flex">
                                <div class="chat-avatar mr-2">
                                    <img src="/images/default-avatar.png"
                                         class="rounded-circle" width="32" height="32">
                                </div>
                                <div class="chat-content">
                                    <div class="chat-header">
                                        <strong>John Smith</strong>
                                        <small class="text-muted ml-2">2 min ago</small>
                                    </div>
                                    <div class="chat-text">Can you repeat the last slide about network protocols?</div>
                                </div>
                            </div>
                        </div>

                        {{-- Sample Chat Message 2 --}}
                        <div class="chat-message mb-2">
                            <div class="d-flex">
                                <div class="chat-avatar mr-2">
                                    <img src="/images/default-avatar.png"
                                         class="rounded-circle" width="32" height="32">
                                </div>
                                <div class="chat-content">
                                    <div class="chat-header">
                                        <strong>Sarah Johnson</strong>
                                        <small class="text-muted ml-2">5 min ago</small>
                                    </div>
                                    <div class="chat-text">Great explanation! The firewall examples were very helpful.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Sample Chat Message 3 --}}
                        <div class="chat-message mb-2">
                            <div class="d-flex">
                                <div class="chat-avatar mr-2">
                                    <img src="/images/default-avatar.png"
                                         class="rounded-circle" width="32" height="32">
                                </div>
                                <div class="chat-content">
                                    <div class="chat-header">
                                        <strong>Mike Davis</strong>
                                        <small class="text-muted ml-2">8 min ago</small>
                                    </div>
                                    <div class="chat-text">I have a question about port scanning techniques.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Sample Chat Message 4 --}}
                        <div class="chat-message mb-2">
                            <div class="d-flex">
                                <div class="chat-avatar mr-2">
                                    <img src="/images/default-avatar.png"
                                         class="rounded-circle" width="32" height="32">
                                </div>
                                <div class="chat-content">
                                    <div class="chat-header">
                                        <strong>Emily Wilson</strong>
                                        <small class="text-muted ml-2">10 min ago</small>
                                    </div>
                                    <div class="chat-text">Hello everyone! Ready for today's lesson.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chat-input border-top p-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type a message...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sidebar - Student List --}}
        <div class="col-lg-3">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-success">
                    <h3 class="card-title text-white">
                        <i class="fas fa-users"></i>
                        Students in Class
                        <span class="badge badge-light ml-2">24</span>
                    </h3>
                </div>
                <div class="card-body p-0 thin-scrollbar" style="max-height: 600px; overflow-y: auto;">
                    @forelse($studentsInClass ?? [] as $student)
                        <div class="student-item p-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="student-avatar mr-3">
                                        <img src="{{ $student['avatar'] ?? '/images/default-avatar.png' }}"
                                             class="rounded-circle" width="40" height="40">
                                        <div class="student-status {{ $student['status'] ?? 'online' }}"></div>
                                    </div>
                                    <div class="student-info">
                                        <div class="student-name">{{ $student['name'] ?? 'Student Name' }}</div>
                                        <small class="text-muted">{{ $student['email'] ?? 'student@email.com' }}</small>
                                        <div class="student-progress">
                                            <div class="progress" style="height: 3px;">
                                                <div class="progress-bar bg-info" style="width: {{ $student['progress'] ?? 75 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="student-actions">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                data-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#">
                                                <i class="fas fa-comment"></i> Message
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="fas fa-microphone-slash"></i> Mute
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="fas fa-eye"></i> View Progress
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash fa-2x text-muted mb-3"></i>
                            <p class="text-muted">No students in class</p>
                        </div>
                    @endforelse
                </div>

                {{-- Student Tools --}}
                <div class="card-footer">
                    <div class="row">
                        <div class="col-6">
                            <button class="btn btn-sm btn-outline-info btn-block">
                                <i class="fas fa-hand-paper"></i> Raise Hands
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-sm btn-outline-warning btn-block">
                                <i class="fas fa-poll"></i> Quick Poll
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .pulse-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .lesson-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .lesson-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .lesson-item:hover {
            background-color: #f8f9fa !important;
        }

        .lesson-number {
            min-width: 30px;
        }

        .student-avatar {
            position: relative;
        }

        .student-status {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .student-status.online {
            background-color: #28a745;
        }

        .student-status.away {
            background-color: #ffc107;
        }

        .student-status.offline {
            background-color: #6c757d;
        }

        .sticky-top {
            z-index: 1020;
        }

        .chat-message {
            opacity: 0;
            animation: fadeIn 0.3s ease-in forwards;
        }

        @keyframes fadeIn {
            to { opacity: 1; }
        }

        .class-controls .btn {
            margin-left: 5px;
        }

        .current-lesson {
            background: white;
            border-radius: 8px;
            padding: 15px;
            border: 2px solid #28a745;
        }
    </style>
@endsection

@section('js')
    <script>
        // Initialize chat functionality
        $(document).ready(function() {
            // Auto-scroll chat to bottom
            $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);

            // Handle chat input
            $('.chat-input input').on('keypress', function(e) {
                if (e.which === 13) {
                    sendChatMessage();
                }
            });

            $('.chat-input button').on('click', sendChatMessage);
        });

        function sendChatMessage() {
            const input = $('.chat-input input');
            const message = input.val().trim();

            if (message) {
                // Add message to chat (replace with AJAX call)
                const chatHtml = `
                    <div class="chat-message mb-2">
                        <div class="d-flex">
                            <div class="chat-avatar mr-2">
                                <img src="/images/instructor-avatar.png" class="rounded-circle" width="32" height="32">
                            </div>
                            <div class="chat-content">
                                <div class="chat-header">
                                    <strong>Instructor</strong>
                                    <small class="text-muted ml-2">now</small>
                                </div>
                                <div class="chat-text">${message}</div>
                            </div>
                        </div>
                    </div>
                `;

                $('#chat-messages').append(chatHtml);
                $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
                input.val('');
            }
        }

        // Zoom controls
        $('#start-zoom').on('click', function() {
            // Initialize Zoom SDK here
            console.log('Starting Zoom session...');
            $(this).text('Zoom Active').removeClass('btn-success').addClass('btn-warning');
        });
    </script>
@endsection
