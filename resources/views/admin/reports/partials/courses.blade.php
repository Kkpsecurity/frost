<!-- Course Performance Partial -->
<div class="row">

    <!-- Course Popularity Ranking -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-star mr-2"></i>
                    Course Popularity Ranking
                </h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default" onclick="toggleCourseChart('popularity', 'bar')">Bar</button>
                        <button type="button" class="btn btn-default" onclick="toggleCourseChart('popularity', 'horizontalBar')">Horizontal</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="coursePopularityChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Course Summary Stats -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-book mr-2"></i>
                    Course Overview
                </h3>
            </div>
            <div class="card-body">
                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Courses</span>
                        <strong id="courseTotalCourses">0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Lessons</span>
                        <strong id="courseTotalLessons">0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Avg Course Duration</span>
                        <strong id="courseAvgDuration">0 days</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-info" style="width: 70%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Most Popular</span>
                        <strong id="courseMostPopular" class="text-truncate" style="max-width: 120px;">N/A</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-warning" style="width: 95%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Course Revenue Performance -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-dollar-sign mr-2"></i>
                    Course Revenue Performance
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="courseRevenueTable">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Sales</th>
                                <th>Revenue</th>
                                <th>Avg Price</th>
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

    <!-- Lesson Engagement Analytics -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-play-circle mr-2"></i>
                    Top Lesson Engagement
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="lessonEngagementTable">
                        <thead>
                            <tr>
                                <th>Lesson</th>
                                <th>Attempts</th>
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

</div>

<div class="row mt-4">

    <!-- Course Schedule Utilization -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Course Schedule Utilization
                </h3>
            </div>
            <div class="card-body">
                <canvas id="scheduleUtilizationChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Course Completion Timeline -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-2"></i>
                    Completion Timeline
                </h3>
            </div>
            <div class="card-body">
                <div class="timeline-stats">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">
                            <i class="fas fa-bolt text-success"></i>
                            Fast Track (≤7 days)
                        </span>
                        <strong id="fastTrackCount">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">
                            <i class="fas fa-walking text-warning"></i>
                            Standard (8-30 days)
                        </span>
                        <strong id="standardTrackCount">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">
                            <i class="fas fa-turtle text-danger"></i>
                            Extended (>30 days)
                        </span>
                        <strong id="extendedTrackCount">0</strong>
                    </div>
                </div>
                <canvas id="completionTimelineChart" class="mt-3"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Course Performance Matrix -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-th mr-2"></i>
                    Course Performance Matrix
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="info-box bg-gradient-success">
                            <span class="info-box-icon">
                                <i class="fas fa-trophy"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">High Performers</span>
                                <span class="info-box-number" id="highPerformersCount">0</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 85%"></div>
                                </div>
                                <span class="progress-description">
                                    >80% completion rate
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-gradient-warning">
                            <span class="info-box-icon">
                                <i class="fas fa-chart-line"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Good Performers</span>
                                <span class="info-box-number" id="goodPerformersCount">0</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 65%"></div>
                                </div>
                                <span class="progress-description">
                                    60-80% completion rate
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-gradient-danger">
                            <span class="info-box-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Needs Attention</span>
                                <span class="info-box-number" id="needsAttentionCount">0</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 40%"></div>
                                </div>
                                <span class="progress-description">
                                    <40% completion rate
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-gradient-info">
                            <span class="info-box-icon">
                                <i class="fas fa-star"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">New Courses</span>
                                <span class="info-box-number" id="newCoursesCount">0</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 25%"></div>
                                </div>
                                <span class="progress-description">
                                    <30 days old
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="coursePerformanceMatrix">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Enrollments</th>
                                <th>Completion Rate</th>
                                <th>Avg Time to Complete</th>
                                <th>Revenue</th>
                                <th>Performance Status</th>
                                <th>Actions</th>
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

</div>

<script>
let coursePopularityChart, scheduleUtilizationChart, completionTimelineChart;

function updateCourseCharts(data) {
    updateCourseSummary(data.summary);
    updateCoursePopularityChart(data.course_popularity);
    updateCourseRevenueTable(data.course_revenue);
    updateLessonEngagementTable(data.lesson_engagement);
    updateScheduleUtilizationChart(data.schedule_utilization);
    updateCoursePerformanceMatrix(data.course_popularity, data.course_revenue);
}

function updateCourseSummary(summary) {
    $('#courseTotalCourses').text(summary.total_courses || 0);
    $('#courseTotalLessons').text(summary.total_lessons || 0);
    $('#courseAvgDuration').text((summary.avg_course_duration || 0) + ' days');
    $('#courseMostPopular').text(summary.most_popular_course || 'N/A').attr('title', summary.most_popular_course || 'N/A');
}

function updateCoursePopularityChart(popularityData) {
    const ctx = document.getElementById('coursePopularityChart').getContext('2d');

    if (coursePopularityChart) {
        coursePopularityChart.destroy();
    }

    const labels = popularityData?.slice(0, 10).map(course => course.course_name) || [];
    const enrollments = popularityData?.slice(0, 10).map(course => course.enrollment_count) || [];

    coursePopularityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Enrollments',
                data: enrollments,
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
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
                    beginAtZero: true
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
}

function updateCourseRevenueTable(revenueData) {
    const tbody = $('#courseRevenueTable tbody');
    tbody.empty();

    if (revenueData && revenueData.length > 0) {
        revenueData.slice(0, 8).forEach(course => {
            tbody.append(`
                <tr>
                    <td class="text-truncate" style="max-width: 150px;" title="${course.course_name}">
                        ${course.course_name}
                    </td>
                    <td>${course.sales_count}</td>
                    <td>$${numberWithCommas(course.total_revenue)}</td>
                    <td>$${parseFloat(course.avg_price).toFixed(2)}</td>
                </tr>
            `);
        });
    } else {
        tbody.append('<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>');
    }
}

function updateLessonEngagementTable(engagementData) {
    const tbody = $('#lessonEngagementTable tbody');
    tbody.empty();

    if (engagementData && engagementData.length > 0) {
        engagementData.slice(0, 8).forEach(lesson => {
            const completionRate = parseFloat(lesson.completion_rate || 0);
            const badgeClass = completionRate >= 80 ? 'success' : completionRate >= 60 ? 'warning' : 'danger';

            tbody.append(`
                <tr>
                    <td class="text-truncate" style="max-width: 150px;" title="${lesson.lesson_name}">
                        ${lesson.lesson_name}
                    </td>
                    <td>${lesson.total_attempts}</td>
                    <td>${lesson.completed_attempts}</td>
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

function updateScheduleUtilizationChart(utilizationData) {
    const ctx = document.getElementById('scheduleUtilizationChart').getContext('2d');

    if (scheduleUtilizationChart) {
        scheduleUtilizationChart.destroy();
    }

    const labels = utilizationData?.map(course => course.course_name) || [];
    const scheduledClasses = utilizationData?.map(course => course.scheduled_classes) || [];
    const completedClasses = utilizationData?.map(course => course.completed_classes) || [];

    scheduleUtilizationChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Scheduled Classes',
                    data: scheduledClasses,
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Completed Classes',
                    data: completedClasses,
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
}

function updateCoursePerformanceMatrix(popularityData, revenueData) {
    const tbody = $('#coursePerformanceMatrix tbody');
    tbody.empty();

    let highPerformers = 0, goodPerformers = 0, needsAttention = 0, newCourses = 0;

    if (popularityData && popularityData.length > 0) {
        popularityData.forEach(course => {
            // Simulate completion rate and other metrics for demo
            const completionRate = Math.floor(Math.random() * 100);
            const avgTime = Math.floor(Math.random() * 45) + 5;
            const revenue = revenueData?.find(r => r.course_name === course.course_name)?.total_revenue || 0;

            let performanceStatus, statusClass;
            if (completionRate >= 80) {
                performanceStatus = 'High Performer';
                statusClass = 'success';
                highPerformers++;
            } else if (completionRate >= 60) {
                performanceStatus = 'Good Performer';
                statusClass = 'warning';
                goodPerformers++;
            } else if (completionRate >= 40) {
                performanceStatus = 'Average';
                statusClass = 'info';
            } else {
                performanceStatus = 'Needs Attention';
                statusClass = 'danger';
                needsAttention++;
            }

            tbody.append(`
                <tr>
                    <td class="text-truncate" style="max-width: 200px;" title="${course.course_name}">
                        ${course.course_name}
                    </td>
                    <td>${course.enrollment_count}</td>
                    <td>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-${statusClass}" style="width: ${completionRate}%"></div>
                        </div>
                        <small>${completionRate}%</small>
                    </td>
                    <td>${avgTime} days</td>
                    <td>$${numberWithCommas(revenue)}</td>
                    <td>
                        <span class="badge badge-${statusClass}">
                            ${performanceStatus}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary btn-sm" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" title="Export Data">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });
    }

    // Update performance counters
    $('#highPerformersCount').text(highPerformers);
    $('#goodPerformersCount').text(goodPerformers);
    $('#needsAttentionCount').text(needsAttention);
    $('#newCoursesCount').text(Math.floor(Math.random() * 5) + 1); // Simulated

    // Update completion timeline (simulated data)
    updateCompletionTimelineChart();
}

function updateCompletionTimelineChart() {
    const ctx = document.getElementById('completionTimelineChart').getContext('2d');

    if (completionTimelineChart) {
        completionTimelineChart.destroy();
    }

    // Simulated data for demo
    const fastTrack = Math.floor(Math.random() * 50) + 20;
    const standard = Math.floor(Math.random() * 100) + 50;
    const extended = Math.floor(Math.random() * 30) + 10;

    $('#fastTrackCount').text(fastTrack);
    $('#standardTrackCount').text(standard);
    $('#extendedTrackCount').text(extended);

    completionTimelineChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Fast Track', 'Standard', 'Extended'],
            datasets: [{
                data: [fastTrack, standard, extended],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderWidth: 2,
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
            }
        }
    });
}

function toggleCourseChart(chartName, type) {
    if (chartName === 'popularity' && coursePopularityChart) {
        coursePopularityChart.config.type = type;
        coursePopularityChart.update();
    }
}
</script>
