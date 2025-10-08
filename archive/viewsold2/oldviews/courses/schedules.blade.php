{{-- Course Schedules Page - Uses Site Layout Component --}}
{{-- Page data is passed from the ScheduleController --}}

<x-site.layout :title="$content['title'] ?? 'Course Schedules'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'View available dates and schedules for our security training courses. Find the perfect time to start your security career.' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security course schedules, training dates, class calendar' }}">
    </x-slot:head>

    <x-site.partials.header />

    <main class="main-page-content">
        <div class="frost-courses-section py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="text-white">Course Schedules</h2>
                    <h5 class="text-white-50">View available dates and times for our security training courses</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <div class="schedule-sidebar">
                        <h5 class="text-white mb-4">Course Types</h5>
                        <div class="course-filter-buttons">
                            <button class="btn btn-outline-light btn-sm mb-2 w-100 active" data-course="all">All Courses</button>
                            <button class="btn btn-outline-light btn-sm mb-2 w-100" data-course="d40">Class D (Armed) - D40</button>
                            <button class="btn btn-outline-light btn-sm mb-2 w-100" data-course="g28">Class G (Unarmed) - G28</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9 col-md-8">
                    <div class="schedule-calendar-container">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            <!-- Live Classroom Status Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="text-center mb-4">
                        <h2 class="text-white mb-3">Live Classroom Status</h2>
                        <p class="text-white-50">Real-time status of our security training courses</p>
                    </div>
                </div>
            </div>

            <div class="row all-services">
                <!-- Class D (Armed) Status Panel -->
                <div class="col-md-6 col-xs-12 relative mb-4">
                    <div class="well-services p-4 classroom-status-card d40-panel">
                        <div class="status-header text-center mb-3">
                            <div class="status-icon-container mb-3">
                                <div class="big-status-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="status-indicator-badge" id="d40-status">
                                    <span class="status-dot offline"></span>
                                    <span class="status-text">OFFLINE</span>
                                </div>
                            </div>
                            <h4 class="class-title">Class D (Armed Security)</h4>
                            <p class="class-subtitle">D40 Training Program</p>
                        </div>

                        <div class="classroom-status-details">
                            <div class="current-session" id="d40-current" style="display: none;">
                                <div class="session-info-card">
                                    <h6><strong>Current Session:</strong></h6>
                                    <p class="session-date" id="d40-current-date">-</p>
                                    <div class="session-progress mt-2">
                                        <div class="progress-bar-container">
                                            <div class="progress-fill" id="d40-progress" style="width: 0%"></div>
                                        </div>
                                        <span class="progress-text" id="d40-progress-text">Day 1 of 5</span>
                                    </div>
                                </div>
                            </div>

                            <div class="next-session" id="d40-next">
                                <div class="next-info-card">
                                    <h6><strong>Next Session:</strong></h6>
                                    <p class="next-date" id="d40-next-date">September 2, 2025</p>
                                    <div class="countdown-badge">
                                        <span id="d40-countdown">10 days remaining</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="classroom-info-footer">
                            <div class="info-row">
                                <span class="info-label">Capacity:</span>
                                <span class="info-value" id="d40-capacity">18/20</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Instructor:</span>
                                <span class="info-value">John Martinez</span>
                            </div>
                        </div>

                        <div class="action-buttons text-center mt-3">
                            <a href="/courses/detail/1" class="btn btn-outline-light btn-sm me-2">Course Details</a>
                            <a href="/enroll" class="btn btn-primary btn-sm">Enroll Now</a>
                        </div>
                    </div>
                </div>

                <!-- Class G (Unarmed) Status Panel -->
                <div class="col-md-6 col-xs-12 relative mb-4">
                    <div class="well-services p-4 classroom-status-card g28-panel">
                        <div class="status-header text-center mb-3">
                            <div class="status-icon-container mb-3">
                                <div class="big-status-icon">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div class="status-indicator-badge" id="g28-status">
                                    <span class="status-dot offline"></span>
                                    <span class="status-text">OFFLINE</span>
                                </div>
                            </div>
                            <h4 class="class-title">Class G (Unarmed Security)</h4>
                            <p class="class-subtitle">G28 Training Program</p>
                        </div>

                        <div class="classroom-status-details">
                            <div class="current-session" id="g28-current" style="display: none;">
                                <div class="session-info-card">
                                    <h6><strong>Current Session:</strong></h6>
                                    <p class="session-date" id="g28-current-date">-</p>
                                    <div class="session-progress mt-2">
                                        <div class="progress-bar-container">
                                            <div class="progress-fill" id="g28-progress" style="width: 0%"></div>
                                        </div>
                                        <span class="progress-text" id="g28-progress-text">Day 1 of 3</span>
                                    </div>
                                </div>
                            </div>

                            <div class="next-session" id="g28-next">
                                <div class="next-info-card">
                                    <h6><strong>Next Session:</strong></h6>
                                    <p class="next-date" id="g28-next-date">August 26, 2025</p>
                                    <div class="countdown-badge">
                                        <span id="g28-countdown">3 days remaining</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="classroom-info-footer">
                            <div class="info-row">
                                <span class="info-label">Capacity:</span>
                                <span class="info-value" id="g28-capacity">15/16</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Instructor:</span>
                                <span class="info-value">Sarah Williams</span>
                            </div>
                        </div>

                        <div class="action-buttons text-center mt-3">
                            <a href="/courses/detail/2" class="btn btn-outline-light btn-sm me-2">Course Details</a>
                            <a href="/enroll" class="btn btn-primary btn-sm">Enroll Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>

    <x-site.partials.footer />

    <x-slot:styles>
    <style>
        .schedule-sidebar {
            background: rgba(27, 38, 84, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .course-filter-buttons .btn.active {
            background: var(--frost-info-color, #17a2b8);
            border-color: var(--frost-info-color, #17a2b8);
            color: white;
        }

        .schedule-calendar-container {
            background: rgba(27, 38, 84, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        /* FullCalendar theme customizations */
        .fc {
            background: transparent;
        }

        .fc .fc-view-harness {
            background: transparent;
        }

        /* Header styling */
        .fc-header-toolbar {
            background: var(--frost-primary-color, #212a3e);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .fc-header-toolbar h2 {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .fc-button-group .fc-button {
            background: var(--frost-secondary-color, #394867);
            border-color: var(--frost-secondary-color, #394867);
            color: white;
        }

        .fc-button-group .fc-button:hover {
            background: var(--frost-info-color, #17a2b8);
            border-color: var(--frost-info-color, #17a2b8);
        }

        .fc-button-group .fc-button:focus {
            box-shadow: 0 0 0 2px rgba(23, 162, 184, 0.3);
        }

        .fc-button-group .fc-button-active {
            background: var(--frost-info-color, #17a2b8) !important;
            border-color: var(--frost-info-color, #17a2b8) !important;
        }

        /* Calendar grid */
        .fc-daygrid {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
        }

        .fc-col-header-cell {
            background: var(--frost-secondary-color, #394867);
            color: white;
            font-weight: 600;
            padding: 15px 8px;
        }

        .fc-daygrid-day {
            background: white;
            border-color: rgba(57, 72, 103, 0.2);
        }

        .fc-daygrid-day:hover {
            background: rgba(23, 162, 184, 0.1);
        }

        .fc-daygrid-day.fc-day-today {
            background: rgba(23, 162, 184, 0.2);
        }

        .fc-daygrid-day-number {
            color: var(--frost-primary-color, #212a3e);
            font-weight: 600;
        }

        /* Events styling */
        .fc-event {
            background: var(--frost-info-color, #17a2b8);
            border-color: var(--frost-info-color, #17a2b8);
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .fc-event:hover {
            background: var(--frost-highlight-color, #fede59);
            border-color: var(--frost-highlight-color, #fede59);
            color: var(--frost-primary-color, #212a3e);
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .schedule-sidebar {
                margin-bottom: 30px;
            }

            .fc-header-toolbar {
                flex-direction: column;
                gap: 10px;
            }

            .fc-header-toolbar .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
            }
        }

        /* Classroom Status Cards - Based on existing well-services design */
        .classroom-status-card {
            background: rgba(27, 38, 84, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            min-height: 480px;
            display: flex;
            flex-direction: column;
        }

        .classroom-status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .status-header {
            color: #ffffff;
        }

        .status-icon-container {
            position: relative;
        }

        .big-status-icon {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .big-status-icon i {
            font-size: 2.5rem;
            color: #fede59;
        }

        .d40-panel .big-status-icon i {
            color: #17a2b8;
        }

        .g28-panel .big-status-icon i {
            color: #fede59;
        }

        .status-indicator-badge {
            position: absolute;
            top: -5px;
            right: 50%;
            transform: translateX(50%);
            background: rgba(0, 0, 0, 0.8);
            padding: 5px 12px;
            border-radius: 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-dot.live {
            background: #28a745;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
            animation: pulse-live 2s infinite;
        }

        .status-dot.offline {
            background: #dc3545;
            box-shadow: 0 0 5px rgba(220, 53, 69, 0.3);
        }

        @keyframes pulse-live {
            0% { box-shadow: 0 0 10px rgba(40, 167, 69, 0.5); }
            50% { box-shadow: 0 0 20px rgba(40, 167, 69, 0.8); }
            100% { box-shadow: 0 0 10px rgba(40, 167, 69, 0.5); }
        }

        .status-text {
            color: #ffffff;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.8px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .class-title {
            color: #ffffff;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
        }

        .class-subtitle {
            color: #fede59;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .classroom-status-details {
            flex: 1;
            padding: 20px 0;
        }

        .session-info-card, .next-info-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 20px;
            color: #ffffff;
        }

        .session-info-card h6, .next-info-card h6 {
            color: #fede59;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .session-date, .next-date {
            color: #ffffff;
            font-weight: 600;
            font-size: 1.1rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .progress-bar-container {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-fill {
            background: linear-gradient(90deg, #17a2b8 0%, #fede59 100%);
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .progress-text {
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .countdown-badge {
            background: linear-gradient(45deg, #17a2b8, #fede59);
            border-radius: 8px;
            padding: 10px 16px;
            text-align: center;
            margin-top: 12px;
        }

        .countdown-badge span {
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .classroom-info-footer {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 0 0 15px 15px;
            padding: 15px 20px;
            margin: 20px -20px -20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            color: #ffffff;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: #fede59;
            font-size: 0.9rem;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .info-value {
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .action-buttons .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 18px;
            font-size: 0.9rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .action-buttons .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.5);
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
        }

        .action-buttons .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.7);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .action-buttons .btn-primary {
            background: linear-gradient(45deg, #17a2b8, #fede59);
            border: none;
            color: #ffffff;
        }

        .action-buttons .btn-primary:hover {
            background: linear-gradient(45deg, #fede59, #17a2b8);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
        }
    </style>
    </x-slot:styles>

    <x-slot:scripts>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            // Sample events data - replace with your actual events
            var events = [
                {
                    title: 'Class D (Armed Security)',
                    start: '2025-08-25',
                    end: '2025-08-29',
                    backgroundColor: '#17a2b8',
                    borderColor: '#17a2b8',
                    extendedProps: {
                        course: 'd40',
                        type: 'Armed Security Training'
                    }
                },
                {
                    title: 'Class G (Unarmed Security)',
                    start: '2025-08-26',
                    end: '2025-08-28',
                    backgroundColor: '#fede59',
                    borderColor: '#fede59',
                    textColor: '#212a3e',
                    extendedProps: {
                        course: 'g28',
                        type: 'Unarmed Security Training'
                    }
                },
                {
                    title: 'Class D (Armed Security)',
                    start: '2025-09-02',
                    end: '2025-09-06',
                    backgroundColor: '#17a2b8',
                    borderColor: '#17a2b8',
                    extendedProps: {
                        course: 'd40',
                        type: 'Armed Security Training'
                    }
                },
                {
                    title: 'Class G (Unarmed Security)',
                    start: '2025-09-09',
                    end: '2025-09-11',
                    backgroundColor: '#fede59',
                    borderColor: '#fede59',
                    textColor: '#212a3e',
                    extendedProps: {
                        course: 'g28',
                        type: 'Unarmed Security Training'
                    }
                }
            ];

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listWeek'
                },
                events: events,
                eventClick: function(info) {
                    alert('Course: ' + info.event.title +
                          '\nType: ' + info.event.extendedProps.type +
                          '\nStart: ' + info.event.start.toLocaleDateString() +
                          '\nEnd: ' + info.event.end.toLocaleDateString());
                },
                height: 'auto',
                aspectRatio: 1.35
            });

            calendar.render();

            // Course filter functionality
            document.querySelectorAll('[data-course]').forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    document.querySelectorAll('[data-course]').forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');

                    var courseFilter = this.dataset.course;

                    // Filter events based on course type
                    var filteredEvents = courseFilter === 'all' ? events :
                        events.filter(event => event.extendedProps.course === courseFilter);

                    // Remove all events and add filtered ones
                    calendar.removeAllEvents();
                    calendar.addEventSource(filteredEvents);
                });
            });

            // Load events from server if available
            @if(isset($allEvents))
                var serverEvents = @json($allEvents);
                if (serverEvents && serverEvents.length > 0) {
                    calendar.removeAllEvents();
                    calendar.addEventSource(serverEvents);
                }
            @endif

            // Initialize classroom status checking
            initializeClassroomStatus();
        });

        // Classroom Status Management
        function initializeClassroomStatus() {
            checkClassroomStatus();

            // Update status every 60 seconds
            setInterval(checkClassroomStatus, 60000);

            // Update countdown timers every second
            setInterval(updateCountdownTimers, 1000);
        }

        function checkClassroomStatus() {
            var today = new Date();
            var currentDate = today.toISOString().split('T')[0];

            // Sample events data for checking (replace with actual server data)
            var events = [
                {
                    title: 'Class D (Armed Security)',
                    start: '2025-08-25',
                    end: '2025-08-29',
                    course: 'd40',
                    instructor: 'John Martinez',
                    capacity: { current: 18, max: 20 }
                },
                {
                    title: 'Class G (Unarmed Security)',
                    start: '2025-08-26',
                    end: '2025-08-28',
                    course: 'g28',
                    instructor: 'Sarah Williams',
                    capacity: { current: 15, max: 16 }
                },
                {
                    title: 'Class D (Armed Security)',
                    start: '2025-09-02',
                    end: '2025-09-06',
                    course: 'd40',
                    instructor: 'Mike Rodriguez',
                    capacity: { current: 12, max: 20 }
                },
                {
                    title: 'Class G (Unarmed Security)',
                    start: '2025-09-09',
                    end: '2025-09-11',
                    course: 'g28',
                    instructor: 'Lisa Chen',
                    capacity: { current: 8, max: 16 }
                }
            ];

            // Check D40 status
            var d40Current = events.find(event =>
                event.course === 'd40' &&
                currentDate >= event.start &&
                currentDate <= event.end
            );

            updateClassStatus('d40', d40Current, events);

            // Check G28 status
            var g28Current = events.find(event =>
                event.course === 'g28' &&
                currentDate >= event.start &&
                currentDate <= event.end
            );

            updateClassStatus('g28', g28Current, events);
        }

        function updateClassStatus(classType, currentSession, allEvents) {
            var statusDot = document.querySelector(`#${classType}-status .status-dot`);
            var statusText = document.querySelector(`#${classType}-status .status-text`);
            var currentDiv = document.getElementById(`${classType}-current`);
            var nextDiv = document.getElementById(`${classType}-next`);

            if (currentSession) {
                // Class is currently live
                statusDot.className = 'status-dot live';
                statusText.textContent = 'LIVE';

                currentDiv.style.display = 'block';
                nextDiv.style.display = 'none';

                // Update current session info
                document.getElementById(`${classType}-current-date`).textContent =
                    formatDateRange(currentSession.start, currentSession.end);

                // Calculate progress
                var startDate = new Date(currentSession.start);
                var endDate = new Date(currentSession.end);
                var today = new Date();
                var totalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                var currentDay = Math.ceil((today - startDate) / (1000 * 60 * 60 * 24)) + 1;
                var progressPercent = (currentDay / totalDays) * 100;

                document.getElementById(`${classType}-progress`).style.width = progressPercent + '%';
                document.getElementById(`${classType}-progress-text`).textContent =
                    `Day ${currentDay} of ${totalDays}`;

                // Update capacity
                document.getElementById(`${classType}-capacity`).textContent =
                    `${currentSession.capacity.current}/${currentSession.capacity.max}`;
            } else {
                // Class is offline, show next session
                statusDot.className = 'status-dot offline';
                statusText.textContent = 'OFFLINE';

                currentDiv.style.display = 'none';
                nextDiv.style.display = 'block';

                // Find next session
                var today = new Date();
                var nextSession = allEvents
                    .filter(event => event.course === classType && new Date(event.start) > today)
                    .sort((a, b) => new Date(a.start) - new Date(b.start))[0];

                if (nextSession) {
                    document.getElementById(`${classType}-next-date`).textContent =
                        formatDate(nextSession.start);

                    // Update capacity for next session
                    document.getElementById(`${classType}-capacity`).textContent =
                        `${nextSession.capacity.current}/${nextSession.capacity.max}`;
                }
            }
        }

        function updateCountdownTimers() {
            updateCountdown('d40');
            updateCountdown('g28');
        }

        function updateCountdown(classType) {
            var nextDateElement = document.getElementById(`${classType}-next-date`);
            var countdownElement = document.getElementById(`${classType}-countdown`);

            if (nextDateElement && countdownElement) {
                var nextDate = new Date(nextDateElement.textContent);
                var today = new Date();
                var timeDiff = nextDate.getTime() - today.getTime();

                if (timeDiff > 0) {
                    var daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
                    countdownElement.textContent = daysDiff === 1 ? '1 day remaining' : `${daysDiff} days remaining`;
                } else {
                    countdownElement.textContent = 'Starting soon';
                }
            }
        }

        function formatDate(dateString) {
            var date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function formatDateRange(startString, endString) {
            var startDate = new Date(startString);
            var endDate = new Date(endString);

            return startDate.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric'
            }) + ' - ' + endDate.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        }
    </script>
    </x-slot:scripts>

</x-site.layout>
