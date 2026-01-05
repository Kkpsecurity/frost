@props(['widgets'])

{{-- Welcome Message --}}
<div class="row">
    <div class="col-12">
        <div class="callout callout-info">
            <h5><i class="fas fa-hand-wave"></i> Welcome Back, {{ auth()->user()->first_name ?? 'Admin' }}!</h5>
            <p class="mb-0">{{ dateGreeter() }}</p>
        </div>
    </div>
</div>

{{-- Overview Stats Row --}}
<div class="row">
    {{-- Student Stats --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $widgets['student_stats']['total_students'] ?? 0 }}</h3>
                <p>Total Students</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <a href="#" class="small-box-footer">
                Active: {{ $widgets['student_stats']['active_students'] ?? 0 }}
                <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    {{-- Today's Attendance --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $widgets['student_stats']['attendance_today'] ?? 0 }}</h3>
                <p>Today's Attendance</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="#" class="small-box-footer">
                Online: {{ $widgets['student_stats']['online_today'] ?? 0 }} |
                Offline: {{ $widgets['student_stats']['offline_today'] ?? 0 }}
                <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    {{-- Instructor Stats --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $widgets['instructor_stats']['teaching_today'] ?? 0 }}</h3>
                <p>Instructors Teaching Today</p>
            </div>
            <div class="icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <a href="#" class="small-box-footer">
                Total Active: {{ $widgets['instructor_stats']['active'] ?? 0 }}
                <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    {{-- Support Issues --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $widgets['support_stats']['pending_verifications'] ?? 0 }}</h3>
                <p>Pending Verifications</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="#" class="small-box-footer">
                Verification Rate: {{ $widgets['support_stats']['verification_rate'] ?? 100 }}%
                <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

{{-- Secondary Stats Row --}}
<div class="row">
    {{-- Classes This Week --}}
    <div class="col-lg-3 col-6">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-calendar-week"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Classes This Week</span>
                <span class="info-box-number">{{ $widgets['class_stats']['this_week'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- Classes This Month --}}
    <div class="col-lg-3 col-6">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Classes This Month</span>
                <span class="info-box-number">{{ $widgets['class_stats']['this_month'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- Completed Courses --}}
    <div class="col-lg-3 col-6">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-graduation-cap"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Completed Courses</span>
                <span class="info-box-number">{{ $widgets['student_stats']['completed_courses'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    {{-- Average Students Per Class --}}
    <div class="col-lg-3 col-6">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Avg Students/Class</span>
                <span class="info-box-number">{{ $widgets['instructor_stats']['avg_students_per_class'] ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row">
    {{-- Attendance Trend Chart --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    Attendance Trend (Last 7 Days)
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="attendanceTrendChart" height="150"></canvas>
            </div>
        </div>
    </div>

    {{-- Course Progress Chart --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Course Progress Distribution
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 250px;">
                    <canvas id="progressChart"></canvas>
                </div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-4 text-center">
                            <div class="progress-group">
                                <span class="progress-text text-success">Completed</span>
                                <span class="progress-number"><b>{{ $widgets['progress_metrics']['percentages']['completed'] ?? 0 }}%</b></span>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="progress-group">
                                <span class="progress-text text-warning">In Progress</span>
                                <span class="progress-number"><b>{{ $widgets['progress_metrics']['percentages']['in_progress'] ?? 0 }}%</b></span>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="progress-group">
                                <span class="progress-text text-secondary">Not Started</span>
                                <span class="progress-number"><b>{{ $widgets['progress_metrics']['percentages']['not_started'] ?? 0 }}%</b></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Top Courses Chart --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Top Courses This Month
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="coursesDistributionChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Attendance Trend Chart
    const attendanceCtx = document.getElementById('attendanceTrendChart');
    if (attendanceCtx) {
        new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: @json($widgets['attendance_trend']['labels'] ?? []),
                datasets: [
                    {
                        label: 'Online',
                        data: @json($widgets['attendance_trend']['online'] ?? []),
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Offline',
                        data: @json($widgets['attendance_trend']['offline'] ?? []),
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Progress Pie Chart
    const progressCtx = document.getElementById('progressChart');
    if (progressCtx) {
        new Chart(progressCtx, {
            type: 'doughnut',
            data: {
                labels: @json($widgets['progress_metrics']['labels'] ?? []),
                datasets: [{
                    data: @json($widgets['progress_metrics']['data'] ?? []),
                    backgroundColor: ['#28a745', '#ffc107', '#6c757d'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right'
                    }
                }
            }
        });
    }

    // Courses Distribution Bar Chart
    const coursesCtx = document.getElementById('coursesDistributionChart');
    if (coursesCtx) {
        new Chart(coursesCtx, {
            type: 'bar',
            data: {
                labels: @json($widgets['courses_distribution']['labels'] ?? []),
                datasets: [{
                    label: 'Classes Taught',
                    data: @json($widgets['courses_distribution']['data'] ?? []),
                    backgroundColor: '#007bff',
                    borderColor: '#0056b3',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
