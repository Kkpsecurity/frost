<!-- Instructor Performance Partial -->
<div class="row">

    <!-- Instructor Performance Overview -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chalkboard-teacher mr-2"></i>
                    Instructor Performance Overview
                </h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default" onclick="toggleInstructorChart('performance', 'bar')">Bar</button>
                        <button type="button" class="btn btn-default" onclick="toggleInstructorChart('performance', 'line')">Line</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="instructorPerformanceChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Instructor Summary Stats -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Instructor Overview
                </h3>
            </div>
            <div class="card-body">
                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Instructors</span>
                        <strong id="instructorTotalInstructors">0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Active Classes</span>
                        <strong id="instructorActiveClasses">0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-success" style="width: 75%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Avg Class Size</span>
                        <strong id="instructorAvgClassSize">0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-info" style="width: 60%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Attendance Rate</span>
                        <strong id="instructorAttendanceRate">0%</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-warning" style="width: 85%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Top Performing Instructors -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-award mr-2"></i>
                    Top Performing Instructors
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="topInstructorsTable">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th>Classes</th>
                                <th>Completion</th>
                                <th>Rating</th>
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

    <!-- Class Attendance Trends -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Class Attendance Trends
                </h3>
            </div>
            <div class="card-body">
                <canvas id="attendanceTrendsChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Range Training Analytics -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bullseye mr-2"></i>
                    Range Training Analytics
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">Firearms Training Only</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="info-box bg-gradient-success">
                            <span class="info-box-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Active Ranges</span>
                                <span class="info-box-number" id="activeRanges">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-gradient-warning">
                            <span class="info-box-icon">
                                <i class="fas fa-calendar"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Scheduled Sessions</span>
                                <span class="info-box-number" id="scheduledSessions">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-gradient-info">
                            <span class="info-box-icon">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Completed Sessions</span>
                                <span class="info-box-number" id="completedSessions">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="rangeAnalyticsTable">
                        <thead>
                            <tr>
                                <th>Range Name</th>
                                <th>Location</th>
                                <th>Scheduled</th>
                                <th>Completed</th>
                                <th>Utilization</th>
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

    <!-- Instructor Workload Distribution -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-balance-scale mr-2"></i>
                    Workload Distribution
                </h3>
            </div>
            <div class="card-body">
                <canvas id="workloadDistributionChart" class="report-chart"></canvas>

                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-success">
                            <i class="fas fa-circle"></i>
                            Optimal Load
                        </span>
                        <strong id="optimalLoadCount">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-warning">
                            <i class="fas fa-circle"></i>
                            High Load
                        </span>
                        <strong id="highLoadCount">0</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-danger">
                            <i class="fas fa-circle"></i>
                            Overloaded
                        </span>
                        <strong id="overloadedCount">0</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Instructor Schedule Efficiency -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-2"></i>
                    Instructor Schedule Efficiency & Student Feedback
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="small-box bg-gradient-primary">
                            <div class="inner">
                                <h3 id="totalTeachingHours">0</h3>
                                <p>Total Teaching Hours</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-gradient-success">
                            <div class="inner">
                                <h3 id="avgClassDuration">0h</h3>
                                <p>Avg Class Duration</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-gradient-warning">
                            <div class="inner">
                                <h3 id="utilizationRate">0%</h3>
                                <p>Utilization Rate</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-gradient-info">
                            <div class="inner">
                                <h3 id="avgStudentRating">0.0</h3>
                                <p>Avg Student Rating</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-gradient-danger">
                            <div class="inner">
                                <h3 id="cancelledClasses">0</h3>
                                <p>Cancelled Classes</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-gradient-secondary">
                            <div class="inner">
                                <h3 id="makeupSessions">0</h3>
                                <p>Makeup Sessions</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-redo"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="instructorEfficiencyTable">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th>Teaching Hours</th>
                                <th>Classes Taught</th>
                                <th>Completion Rate</th>
                                <th>Student Rating</th>
                                <th>Attendance Rate</th>
                                <th>Efficiency Score</th>
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
let instructorPerformanceChart, attendanceTrendsChart, workloadDistributionChart;

function updateInstructorCharts(data) {
    updateInstructorSummary(data.summary);
    updateInstructorPerformanceChart(data.instructor_performance);
    updateAttendanceTrendsChart(data.attendance_analytics);
    updateRangeAnalyticsTable(data.range_analytics);
    updateInstructorEfficiencyTable(data.instructor_performance);
}

function updateInstructorSummary(summary) {
    $('#instructorTotalInstructors').text(summary.total_instructors || 0);
    $('#instructorActiveClasses').text(summary.active_classes || 0);
    $('#instructorAvgClassSize').text((summary.avg_class_size || 0).toFixed(1));
    $('#instructorAttendanceRate').text((summary.attendance_rate || 0).toFixed(1) + '%');
}

function updateInstructorPerformanceChart(performanceData) {
    const ctx = document.getElementById('instructorPerformanceChart').getContext('2d');

    if (instructorPerformanceChart) {
        instructorPerformanceChart.destroy();
    }

    const labels = performanceData?.slice(0, 8).map(instructor => instructor.instructor_name.split(' ')[0]) || [];
    const classesTaught = performanceData?.slice(0, 8).map(instructor => instructor.classes_taught) || [];
    const completionRates = performanceData?.slice(0, 8).map(instructor => parseFloat(instructor.completion_rate)) || [];

    instructorPerformanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Classes Taught',
                    data: classesTaught,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                },
                {
                    label: 'Completion Rate (%)',
                    data: completionRates,
                    type: 'line',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2,
                    yAxisID: 'y1'
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
                    beginAtZero: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Classes Taught'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    max: 100,
                    title: {
                        display: true,
                        text: 'Completion Rate (%)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Update top instructors table
    updateTopInstructorsTable(performanceData);
}

function updateTopInstructorsTable(performanceData) {
    const tbody = $('#topInstructorsTable tbody');
    tbody.empty();

    if (performanceData && performanceData.length > 0) {
        // Sort by completion rate for top performers
        const sortedInstructors = [...performanceData].sort((a, b) =>
            parseFloat(b.completion_rate) - parseFloat(a.completion_rate)
        );

        sortedInstructors.slice(0, 8).forEach(instructor => {
            const completionRate = parseFloat(instructor.completion_rate || 0);
            const badgeClass = completionRate >= 90 ? 'success' : completionRate >= 80 ? 'warning' : 'danger';
            const rating = (Math.random() * 2 + 3).toFixed(1); // Simulated rating 3.0-5.0

            tbody.append(`
                <tr>
                    <td class="text-truncate" style="max-width: 120px;" title="${instructor.instructor_name}">
                        ${instructor.instructor_name}
                    </td>
                    <td>${instructor.classes_taught}</td>
                    <td>
                        <span class="badge badge-${badgeClass}">
                            ${completionRate.toFixed(1)}%
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="mr-1">${rating}</span>
                            <div class="stars">
                                ${generateStarRating(rating)}
                            </div>
                        </div>
                    </td>
                </tr>
            `);
        });
    } else {
        tbody.append('<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>');
    }
}

function updateAttendanceTrendsChart(attendanceData) {
    const ctx = document.getElementById('attendanceTrendsChart').getContext('2d');

    if (attendanceTrendsChart) {
        attendanceTrendsChart.destroy();
    }

    const labels = attendanceData?.map(item => item.class_date) || [];
    const attendanceRates = attendanceData?.map(item => parseFloat(item.attendance_rate)) || [];

    attendanceTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Attendance Rate (%)',
                data: attendanceRates,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
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
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
}

function updateRangeAnalyticsTable(rangeData) {
    const tbody = $('#rangeAnalyticsTable tbody');
    tbody.empty();

    let totalScheduled = 0, totalCompleted = 0;

    if (rangeData && rangeData.length > 0) {
        rangeData.forEach(range => {
            const scheduled = parseInt(range.scheduled_sessions || 0);
            const completed = parseInt(range.completed_sessions || 0);
            const utilization = scheduled > 0 ? ((completed / scheduled) * 100) : 0;

            totalScheduled += scheduled;
            totalCompleted += completed;

            const utilizationClass = utilization >= 80 ? 'success' : utilization >= 60 ? 'warning' : 'danger';

            tbody.append(`
                <tr>
                    <td class="text-truncate" style="max-width: 150px;" title="${range.range_name}">
                        ${range.range_name}
                    </td>
                    <td class="text-truncate" style="max-width: 200px;" title="${range.range_address}">
                        ${range.range_address}
                    </td>
                    <td>${scheduled}</td>
                    <td>${completed}</td>
                    <td>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-${utilizationClass}" style="width: ${utilization}%"></div>
                        </div>
                        <small>${utilization.toFixed(1)}%</small>
                    </td>
                </tr>
            `);
        });
    } else {
        tbody.append('<tr><td colspan="5" class="text-center text-muted">No range data available</td></tr>');
    }

    // Update range summary
    $('#activeRanges').text(rangeData?.length || 0);
    $('#scheduledSessions').text(totalScheduled);
    $('#completedSessions').text(totalCompleted);

    // Update workload distribution chart (simulated data)
    updateWorkloadDistributionChart();
}

function updateWorkloadDistributionChart() {
    const ctx = document.getElementById('workloadDistributionChart').getContext('2d');

    if (workloadDistributionChart) {
        workloadDistributionChart.destroy();
    }

    // Simulated workload distribution data
    const optimal = Math.floor(Math.random() * 15) + 10;
    const high = Math.floor(Math.random() * 8) + 3;
    const overloaded = Math.floor(Math.random() * 3) + 1;

    $('#optimalLoadCount').text(optimal);
    $('#highLoadCount').text(high);
    $('#overloadedCount').text(overloaded);

    workloadDistributionChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Optimal Load', 'High Load', 'Overloaded'],
            datasets: [{
                data: [optimal, high, overloaded],
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

function updateInstructorEfficiencyTable(performanceData) {
    const tbody = $('#instructorEfficiencyTable tbody');
    tbody.empty();

    if (performanceData && performanceData.length > 0) {
        performanceData.forEach(instructor => {
            const completionRate = parseFloat(instructor.completion_rate || 0);
            const teachingHours = Math.floor(Math.random() * 40) + 20; // Simulated
            const studentRating = (Math.random() * 2 + 3).toFixed(1); // 3.0-5.0
            const attendanceRate = Math.floor(Math.random() * 20) + 80; // 80-100%
            const efficiencyScore = ((completionRate + attendanceRate + (parseFloat(studentRating) * 20)) / 3).toFixed(1);

            const efficiencyClass = efficiencyScore >= 90 ? 'success' : efficiencyScore >= 80 ? 'warning' : 'danger';

            tbody.append(`
                <tr>
                    <td class="text-truncate" style="max-width: 150px;" title="${instructor.instructor_name}">
                        ${instructor.instructor_name}
                    </td>
                    <td>${teachingHours}h</td>
                    <td>${instructor.classes_taught}</td>
                    <td>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-info" style="width: ${completionRate}%"></div>
                        </div>
                        <small>${completionRate.toFixed(1)}%</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="mr-1">${studentRating}</span>
                            <div class="stars">
                                ${generateStarRating(studentRating)}
                            </div>
                        </div>
                    </td>
                    <td>${attendanceRate}%</td>
                    <td>
                        <span class="badge badge-${efficiencyClass}">
                            ${efficiencyScore}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary btn-sm" title="View Profile">
                                <i class="fas fa-user"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" title="Send Message">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });
    }

    // Update summary metrics (simulated)
    $('#totalTeachingHours').text(Math.floor(Math.random() * 500) + 300);
    $('#avgClassDuration').text((Math.random() * 2 + 2).toFixed(1) + 'h');
    $('#utilizationRate').text((Math.random() * 20 + 75).toFixed(1) + '%');
    $('#avgStudentRating').text((Math.random() * 1 + 4).toFixed(1));
    $('#cancelledClasses').text(Math.floor(Math.random() * 10) + 2);
    $('#makeupSessions').text(Math.floor(Math.random() * 15) + 5);
}

function generateStarRating(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    let stars = '';

    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star text-warning"></i>';
    }

    if (hasHalfStar) {
        stars += '<i class="fas fa-star-half-alt text-warning"></i>';
    }

    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="far fa-star text-muted"></i>';
    }

    return stars;
}

function toggleInstructorChart(chartName, type) {
    if (chartName === 'performance' && instructorPerformanceChart) {
        instructorPerformanceChart.config.type = type;
        instructorPerformanceChart.update();
    }
}
</script>
