@extends('adminlte::page')

@section('title', $content['title'])

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-calendar-alt"></i>
            {{ $content['title'] }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.course-dates.index') }}" class="btn btn-default">
                <i class="fas fa-list"></i>
                List View
            </a>
            <a href="{{ route('admin.course-dates.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i>
                Add Course Date
            </a>
            <a href="{{ route('admin.course-dates.generator') }}" class="btn btn-info">
                <i class="fas fa-magic"></i>
                Auto Generate
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Calendar Controls -->
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" id="prev-month">
                        <i class="fas fa-chevron-left"></i>
                        Previous
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="current-month" style="min-width: 200px;">
                        <span id="current-month-text">{{ now()->format('F Y') }}</span>
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="next-month">
                        Next
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="btn-group ml-3">
                    <button type="button" class="btn btn-outline-secondary active" data-view="month">Month</button>
                    <button type="button" class="btn btn-outline-secondary" data-view="week">Week</button>
                    <button type="button" class="btn btn-outline-secondary" data-view="day">Day</button>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <!-- Course Type Legend -->
                <div class="course-legend">
                    <span class="badge badge-primary mr-2">D40 Courses</span>
                    <span class="badge badge-success mr-2">G28 Courses</span>
                    <span class="badge badge-warning">Other Courses</span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-day"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Today</span>
                        <span class="info-box-number">{{ $content['stats']['today_courses'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-calendar-week"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">This Week</span>
                        <span class="info-box-number">{{ $content['stats']['week_courses'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">This Month</span>
                        <span class="info-box-number">{{ $content['stats']['month_courses'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Inactive</span>
                        <span class="info-box-number">{{ $content['stats']['inactive_courses'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Calendar -->
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body p-0">
                        <!-- Calendar will be rendered here -->
                        <div id="calendar" style="min-height: 600px;"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-3">
                <!-- Today's Schedule -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock"></i>
                            Today's Schedule
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($content['today_courses'] && $content['today_courses']->isNotEmpty())
                            @foreach($content['today_courses'] as $courseDate)
                            <div class="course-item mb-2 p-2 border rounded">
                                <div class="d-flex justify-content-between">
                                    <strong class="course-title">
                                        {{ $courseDate->CourseUnit->Course->title }}
                                    </strong>
                                    <small class="time-badge badge badge-info">
                                        {{ $courseDate->starts_at->format('g:i A') }}
                                    </small>
                                </div>
                                <small class="text-muted">
                                    {{ $courseDate->CourseUnit->title }}
                                    @if($courseDate->CourseUnit->day)
                                        (Day {{ $courseDate->CourseUnit->day }})
                                    @endif
                                </small>
                                <div class="mt-1">
                                    <a href="{{ route('admin.course-dates.show', $courseDate) }}"
                                       class="btn btn-xs btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">
                                <i class="fas fa-calendar-times fa-2x mb-2"></i><br>
                                No courses scheduled for today
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success btn-sm" onclick="createCourseDate()">
                                <i class="fas fa-plus"></i> Add Course Date
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="generateWeek()">
                                <i class="fas fa-magic"></i> Generate This Week
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" onclick="generateMonth()">
                                <i class="fas fa-calendar-plus"></i> Generate This Month
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="cleanupCourses()">
                                <i class="fas fa-broom"></i> Cleanup Invalid
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Date Modal -->
    <div class="modal fade" id="courseDateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Course Date Details</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Course date details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="edit-course-date">Edit</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
.course-legend .badge {
    font-size: 0.85em;
}

.course-item {
    transition: all 0.2s ease;
}

.course-item:hover {
    background-color: #f8f9fa;
    border-color: #007bff !important;
}

.course-title {
    font-size: 0.9em;
    color: #495057;
}

.time-badge {
    font-size: 0.75em;
}

/* FullCalendar Customizations */
.fc-event {
    border-radius: 3px;
    border: none;
    padding: 2px 4px;
    font-size: 0.8em;
}

.fc-event-title {
    font-weight: 600;
}

.fc-daygrid-event {
    margin: 1px;
}

/* Course type colors */
.course-d40 {
    background-color: #007bff;
    color: white;
}

.course-g28 {
    background-color: #28a745;
    color: white;
}

.course-other {
    background-color: #ffc107;
    color: #212529;
}

.course-inactive {
    background-color: #6c757d;
    color: white;
    opacity: 0.7;
}

#calendar {
    font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.fc-toolbar-title {
    font-size: 1.5em;
    font-weight: 600;
}
</style>
@stop

@section('js')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: '',
            center: '',
            right: ''
        },
        height: 'auto',
        events: function(fetchInfo, successCallback, failureCallback) {
            // Fetch course dates for the current view
            fetch(`{{ route('admin.course-dates.api.calendar') }}?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                .then(response => response.json())
                .then(data => {
                    const events = data.course_dates.map(courseDate => {
                        let className = 'course-other';
                        const courseTitle = courseDate.course_unit.course.title.toLowerCase();

                        if (courseTitle.includes('d40') || courseTitle.includes('d ')) {
                            className = 'course-d40';
                        } else if (courseTitle.includes('g28') || courseTitle.includes('g ')) {
                            className = 'course-g28';
                        }

                        if (!courseDate.is_active) {
                            className += ' course-inactive';
                        }

                        return {
                            id: courseDate.id,
                            title: `${courseDate.course_unit.course.title} - ${courseDate.course_unit.title}`,
                            start: courseDate.starts_at,
                            end: courseDate.ends_at,
                            className: className,
                            extendedProps: {
                                courseDate: courseDate
                            }
                        };
                    });

                    successCallback(events);
                })
                .catch(error => {
                    console.error('Error fetching calendar events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            showCourseDateModal(info.event.extendedProps.courseDate);
        },
        dateClick: function(info) {
            // Allow creating new course date on date click
            createCourseDateForDate(info.dateStr);
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.setAttribute('title',
                `${info.event.title}\n${info.event.startStr} - ${info.event.endStr}`
            );
        }
    });

    calendar.render();

    // Navigation buttons
    document.getElementById('prev-month').addEventListener('click', function() {
        calendar.prev();
        updateCurrentMonthText();
    });

    document.getElementById('next-month').addEventListener('click', function() {
        calendar.next();
        updateCurrentMonthText();
    });

    document.getElementById('current-month').addEventListener('click', function() {
        calendar.today();
        updateCurrentMonthText();
    });

    // View buttons
    document.querySelectorAll('[data-view]').forEach(button => {
        button.addEventListener('click', function() {
            const view = this.getAttribute('data-view');
            let calendarView = 'dayGridMonth';

            if (view === 'week') calendarView = 'timeGridWeek';
            else if (view === 'day') calendarView = 'timeGridDay';

            calendar.changeView(calendarView);

            // Update active button
            document.querySelectorAll('[data-view]').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });

    function updateCurrentMonthText() {
        const currentDate = calendar.getDate();
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        const monthText = monthNames[currentDate.getMonth()] + ' ' + currentDate.getFullYear();
        document.getElementById('current-month-text').textContent = monthText;
    }

    window.showCourseDateModal = function(courseDate) {
        const modal = $('#courseDateModal');
        const modalBody = modal.find('.modal-body');

        modalBody.html(`
            <div class="row">
                <div class="col-md-6">
                    <h5>${courseDate.course_unit.course.title}</h5>
                    <p class="text-muted">${courseDate.course_unit.title}</p>
                    ${courseDate.course_unit.day ? `<p><strong>Day:</strong> ${courseDate.course_unit.day}</p>` : ''}
                </div>
                <div class="col-md-6">
                    <p><strong>Date:</strong> ${new Date(courseDate.starts_at).toLocaleDateString()}</p>
                    <p><strong>Time:</strong> ${new Date(courseDate.starts_at).toLocaleTimeString()} - ${new Date(courseDate.ends_at).toLocaleTimeString()}</p>
                    <p><strong>Status:</strong> ${courseDate.is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>'}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <h6>Students Enrolled: ${courseDate.student_units_count || 0}</h6>
                    <h6>Instructor: ${courseDate.inst_unit ? (courseDate.inst_unit.created_by ? courseDate.inst_unit.created_by.fname + ' ' + courseDate.inst_unit.created_by.lname : 'Not Assigned') : 'Not Assigned'}</h6>
                </div>
            </div>
        `);

        // Set up edit button
        document.getElementById('edit-course-date').onclick = function() {
            window.location.href = `{{ url('admin/course-dates') }}/${courseDate.id}/edit`;
        };

        modal.modal('show');
    };

    window.createCourseDate = function() {
        window.location.href = '{{ route("admin.course-dates.create") }}';
    };

    window.createCourseDateForDate = function(dateStr) {
        window.location.href = `{{ route("admin.course-dates.create") }}?date=${dateStr}`;
    };

    window.generateWeek = function() {
        if (confirm('Generate course dates for this week?')) {
            // Implementation for week generation
            alert('Week generation would be implemented here');
        }
    };

    window.generateMonth = function() {
        if (confirm('Generate course dates for this month?')) {
            // Implementation for month generation
            alert('Month generation would be implemented here');
        }
    };

    window.cleanupCourses = function() {
        if (confirm('Clean up invalid course dates? This action cannot be undone.')) {
            // Implementation for cleanup
            alert('Cleanup would be implemented here');
        }
    };
});
</script>
@stop
