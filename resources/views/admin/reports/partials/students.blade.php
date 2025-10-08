<!-- Student Analytics Partial -->
<div class="row">

    <!-- Enrollment Trends Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-plus mr-2"></i>
                    Student Enrollment Trends
                </h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default" onclick="toggleStudentChart('enrollment', 'line')">Line</button>
                        <button type="button" class="btn btn-default" onclick="toggleStudentChart('enrollment', 'bar')">Bar</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="enrollmentChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Student Summary Stats -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Student Overview
                </h3>
            </div>
            <div class="card-body">
                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Students</span>
                        <strong id="studentTotalStudents">0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Active Enrollments</span>
                        <strong id="studentActiveEnrollments">0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-success" style="width: 80%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Completed Courses</span>
                        <strong id="studentCompletedCourses">0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-info" style="width: 65%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Avg Completion Rate</span>
                        <strong id="studentAvgCompletionRate">0%</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-warning" style="width: 75%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Course Completion Rates -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    Course Completion Rates
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="completionRatesTable">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Enrolled</th>
                                <th>Completed</th>
                                <th>Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Progress Distribution -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Student Progress Distribution
                </h3>
            </div>
            <div class="card-body">
                <canvas id="progressDistributionChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Engagement Metrics -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-2"></i>
                    Student Engagement Metrics
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-clock"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Avg Session</span>
                                <span class="info-box-number" id="avgSessionDuration">0 min</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-play"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Lessons/Session</span>
                                <span class="info-box-number" id="avgLessonsPerSession">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-calendar"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Most Active Day</span>
                                <span class="info-box-number" id="mostActiveDay">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Heatmap -->
                <div class="mt-4">
                    <h5>Weekly Activity Pattern</h5>
                    <canvas id="activityHeatmapChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Geographic Distribution -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Geographic Distribution
                </h3>
            </div>
            <div class="card-body">
                <div class="geographic-stats">
                    <div id="geographicData">
                        <!-- Data populated by JavaScript -->
                    </div>
                </div>
                <canvas id="geographicChart" class="mt-3"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Student Retention Analysis -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-area mr-2"></i>
                    Student Retention & Progress Analysis
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="small-box bg-gradient-primary">
                            <div class="inner">
                                <h3 id="newStudentsCount">0</h3>
                                <p>New Students</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-gradient-success">
                            <div class="inner">
                                <h3 id="activeStudentsCount">0</h3>
                                <p>Active This Week</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-gradient-warning">
                            <div class="inner">
                                <h3 id="graduatesCount">0</h3>
                                <p>Recent Graduates</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-gradient-info">
                            <div class="inner">
                                <h3 id="retentionRate">0%</h3>
                                <p>Retention Rate</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <canvas id="retentionChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

</div>

<script>
let enrollmentChart, progressDistributionChart, activityHeatmapChart, geographicChart, retentionChart;

function updateStudentCharts(data) {
    updateStudentSummary(data.summary);
    updateEnrollmentChart(data.enrollment_trends);
    updateCompletionRatesTable(data.completion_rates);
    updateProgressDistributionChart(data.progress_analytics);
    updateEngagementMetrics(data.engagement_metrics);
    updateGeographicChart(data.geographic_distribution);
}

function updateStudentSummary(summary) {
    $('#studentTotalStudents').text(summary.total_students || 0);
    $('#studentActiveEnrollments').text(summary.active_enrollments || 0);
    $('#studentCompletedCourses').text(summary.completed_courses || 0);
    $('#studentAvgCompletionRate').text((summary.avg_completion_rate || 0).toFixed(1) + '%');
}

function updateEnrollmentChart(enrollmentData) {
    const ctx = document.getElementById('enrollmentChart').getContext('2d');

    if (enrollmentChart) {
        enrollmentChart.destroy();
    }

    const labels = enrollmentData?.map(item => item.date) || [];
    const enrollments = enrollmentData?.map(item => item.enrollments) || [];

    enrollmentChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Daily Enrollments',
                data: enrollments,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
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
                    beginAtZero: true
                }
            }
        }
    });
}

function updateCompletionRatesTable(completionRates) {
    const tbody = $('#completionRatesTable tbody');
    tbody.empty();

    if (completionRates && completionRates.length > 0) {
        completionRates.slice(0, 10).forEach(course => {
            const completionRate = parseFloat(course.completion_rate || 0);
            const badgeClass = completionRate >= 80 ? 'success' : completionRate >= 60 ? 'warning' : 'danger';

            tbody.append(`
                <tr>
                    <td class="text-truncate" style="max-width: 150px;" title="${course.course_name}">
                        ${course.course_name}
                    </td>
                    <td>${course.total_enrollments}</td>
                    <td>${course.completed}</td>
                    <td>
                        <span class="badge badge-${badgeClass}">
                            ${completionRate.toFixed(1)}%
                        </span>
                    </td>
                </tr>
            `);
        });
    } else {
        tbody.append('<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>');
    }
}

function updateProgressDistributionChart(progressData) {
    const ctx = document.getElementById('progressDistributionChart').getContext('2d');

    if (progressDistributionChart) {
        progressDistributionChart.destroy();
    }

    // Calculate totals for progress distribution
    let totalCompleted = 0, totalInProgress = 0, totalEnrollments = 0;

    if (progressData && progressData.length > 0) {
        progressData.forEach(course => {
            totalCompleted += parseInt(course.completed || 0);
            totalInProgress += parseInt(course.in_progress || 0);
            totalEnrollments += parseInt(course.total_enrollments || 0);
        });
    }

    progressDistributionChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'In Progress', 'Not Started'],
            datasets: [{
                data: [totalCompleted, totalInProgress, Math.max(0, totalEnrollments - totalCompleted - totalInProgress)],
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
                    position: 'bottom'
                }
            }
        }
    });
}

function updateEngagementMetrics(engagementData) {
    if (engagementData) {
        $('#avgSessionDuration').text((engagementData.average_session_duration || 0) + ' min');

        if (engagementData.student_activity_patterns) {
            $('#avgLessonsPerSession').text(engagementData.student_activity_patterns.avg_lessons_per_session || 0);
            $('#mostActiveDay').text(engagementData.student_activity_patterns.most_active_day || 'N/A');
        }
    }
}

function updateGeographicChart(geographicData) {
    const geographicDiv = $('#geographicData');
    geographicDiv.empty();

    if (geographicData && typeof geographicData === 'object') {
        const total = Object.values(geographicData).reduce((sum, count) => sum + count, 0);

        Object.entries(geographicData).forEach(([state, count]) => {
            const percentage = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
            geographicDiv.append(`
                <div class="d-flex justify-content-between mb-2">
                    <span>${state}</span>
                    <span><strong>${count}</strong> (${percentage}%)</span>
                </div>
            `);
        });

        // Update geographic chart
        const ctx = document.getElementById('geographicChart').getContext('2d');

        if (geographicChart) {
            geographicChart.destroy();
        }

        const labels = Object.keys(geographicData);
        const counts = Object.values(geographicData);
        const colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8'];

        geographicChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 1,
                    borderColor: '#fff'
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
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

function toggleStudentChart(chartName, type) {
    if (chartName === 'enrollment' && enrollmentChart) {
        enrollmentChart.config.type = type;
        enrollmentChart.update();
    }
}
</script>
