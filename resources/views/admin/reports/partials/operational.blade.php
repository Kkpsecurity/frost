<!-- Operational Analytics Partial -->
<div class="row">

    <!-- System Usage Overview -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-area mr-2"></i>
                    System Usage Analytics
                </h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default" onclick="toggleOperationalChart('usage', 'line')">Line</button>
                        <button type="button" class="btn btn-default" onclick="toggleOperationalChart('usage', 'bar')">Bar</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="systemUsageChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Operational Summary Stats -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    System Overview
                </h3>
            </div>
            <div class="card-body">
                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Daily Active Users</span>
                        <strong id="operationalDailyUsers">0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-primary" style="width: 85%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Peak Usage Hours</span>
                        <strong id="operationalPeakHours">9 AM, 2 PM</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-success" style="width: 70%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Compliance Rate</span>
                        <strong id="operationalComplianceRate">0%</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-warning" style="width: 95%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">System Uptime</span>
                        <strong id="operationalUptime">99.9%</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-info" style="width: 99%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Browser & Device Analytics -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-desktop mr-2"></i>
                    Browser & Device Analytics
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-center">Browser Distribution</h6>
                        <canvas id="browserChart" height="200"></canvas>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-center">Device Distribution</h6>
                        <canvas id="deviceChart" height="200"></canvas>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="table-responsive">
                        <table class="table table-sm" id="browserStatsTable">
                            <thead>
                                <tr>
                                    <th>Browser</th>
                                    <th>Users</th>
                                    <th>Percentage</th>
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

    <!-- Compliance & Certification Tracking -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-certificate mr-2"></i>
                    Compliance & Certification Tracking
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="small-box bg-gradient-success">
                            <div class="inner">
                                <h3 id="newCertifications">0</h3>
                                <p>New Certifications</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-award"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="small-box bg-gradient-warning">
                            <div class="inner">
                                <h3 id="expiringCertifications">0</h3>
                                <p>Expiring Soon</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <canvas id="complianceChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Resource Utilization -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-server mr-2"></i>
                    Resource Utilization Analysis
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-book-open"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Course Capacity</span>
                                <span class="info-box-number" id="courseCapacityUtil">0%</span>
                                <div class="progress">
                                    <div class="progress-bar bg-info" id="courseCapacityBar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Instructor Utilization</span>
                                <span class="info-box-number" id="instructorUtil">0%</span>
                                <div class="progress">
                                    <div class="progress-bar bg-success" id="instructorUtilBar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-bullseye"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Range Utilization</span>
                                <span class="info-box-number" id="rangeUtil">0%</span>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" id="rangeUtilBar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <canvas id="resourceUtilizationChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- System Health Monitoring -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-heartbeat mr-2"></i>
                    System Health Monitor
                </h3>
            </div>
            <div class="card-body">
                <div class="system-health">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Database Performance</span>
                        <span class="badge badge-success">Excellent</span>
                    </div>
                    <div class="progress progress-sm mb-3">
                        <div class="progress-bar bg-success" style="width: 95%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>API Response Time</span>
                        <span class="badge badge-success">Fast</span>
                    </div>
                    <div class="progress progress-sm mb-3">
                        <div class="progress-bar bg-success" style="width: 88%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Storage Usage</span>
                        <span class="badge badge-warning">Moderate</span>
                    </div>
                    <div class="progress progress-sm mb-3">
                        <div class="progress-bar bg-warning" style="width: 67%"></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Error Rate</span>
                        <span class="badge badge-success">Low</span>
                    </div>
                    <div class="progress progress-sm mb-3">
                        <div class="progress-bar bg-success" style="width: 8%"></div>
                    </div>
                </div>

                <div class="mt-4">
                    <h6>Recent System Events</h6>
                    <div class="timeline">
                        <div class="timeline-item">
                            <span class="time text-muted">5 min ago</span>
                            <div class="timeline-body">
                                <i class="fas fa-check-circle text-success"></i>
                                Backup completed successfully
                            </div>
                        </div>
                        <div class="timeline-item">
                            <span class="time text-muted">1 hour ago</span>
                            <div class="timeline-body">
                                <i class="fas fa-sync text-info"></i>
                                System update deployed
                            </div>
                        </div>
                        <div class="timeline-item">
                            <span class="time text-muted">3 hours ago</span>
                            <div class="timeline-body">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                High CPU usage detected
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Peak Usage Analysis -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Peak Usage Analysis & Capacity Planning
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="small-box bg-gradient-primary">
                            <div class="inner">
                                <h3 id="peakConcurrentUsers">0</h3>
                                <p>Peak Concurrent Users</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-gradient-success">
                            <div class="inner">
                                <h3 id="avgResponseTime">0ms</h3>
                                <p>Avg Response Time</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-gradient-warning">
                            <div class="inner">
                                <h3 id="dataTransfer">0GB</h3>
                                <p>Data Transfer</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-gradient-info">
                            <div class="inner">
                                <h3 id="capacityRemaining">0%</h3>
                                <p>Capacity Remaining</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-battery-three-quarters"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <canvas id="peakUsageChart" class="report-chart"></canvas>
                    </div>
                    <div class="col-md-4">
                        <h6>Capacity Recommendations</h6>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Current Status:</strong> System running optimally
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Recommendation:</strong> Consider adding 2 more instructors for peak hours
                        </div>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>Good:</strong> Range capacity is sufficient for current demand
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
let systemUsageChart, browserChart, deviceChart, complianceChart, resourceUtilizationChart, peakUsageChart;

function updateOperationalCharts(data) {
    updateOperationalSummary(data.summary);
    updateSystemUsageChart(data.system_usage);
    updateBrowserDeviceCharts(data.system_usage);
    updateComplianceChart(data.compliance_data);
    updateResourceUtilizationChart(data.resource_utilization);
}

function updateOperationalSummary(summary) {
    $('#operationalDailyUsers').text(summary.daily_active_users || 0);
    $('#operationalPeakHours').text((summary.peak_usage_hours || []).join(', ') || 'N/A');
    $('#operationalComplianceRate').text((summary.compliance_rate || 0).toFixed(1) + '%');
    $('#operationalUptime').text(summary.system_uptime || '99.9%');
}

function updateSystemUsageChart(systemUsage) {
    const ctx = document.getElementById('systemUsageChart').getContext('2d');

    if (systemUsageChart) {
        systemUsageChart.destroy();
    }

    // Simulated hourly usage data
    const hours = [];
    const users = [];
    for (let i = 0; i < 24; i++) {
        hours.push(i + ':00');
        users.push(Math.floor(Math.random() * 150) + 50);
    }

    systemUsageChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: hours,
            datasets: [{
                label: 'Active Users',
                data: users,
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

function updateBrowserDeviceCharts(systemUsage) {
    // Browser Chart
    const browserCtx = document.getElementById('browserChart').getContext('2d');

    if (browserChart) {
        browserChart.destroy();
    }

    const browserData = systemUsage.browser_analytics || [
        { browser: 'Chrome', count: 150 },
        { browser: 'Firefox', count: 45 },
        { browser: 'Safari', count: 30 },
        { browser: 'Edge', count: 25 }
    ];

    const browserLabels = browserData.map(item => item.browser);
    const browserCounts = browserData.map(item => item.count);

    browserChart = new Chart(browserCtx, {
        type: 'doughnut',
        data: {
            labels: browserLabels,
            datasets: [{
                data: browserCounts,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        fontSize: 10
                    }
                }
            }
        }
    });

    // Device Chart
    const deviceCtx = document.getElementById('deviceChart').getContext('2d');

    if (deviceChart) {
        deviceChart.destroy();
    }

    const deviceData = systemUsage.device_analytics || {
        'Desktop': 75,
        'Mobile': 20,
        'Tablet': 5
    };

    deviceChart = new Chart(deviceCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(deviceData),
            datasets: [{
                data: Object.values(deviceData),
                backgroundColor: ['#17a2b8', '#6f42c1', '#fd7e14'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        fontSize: 10
                    }
                }
            }
        }
    });

    // Update browser stats table
    const tbody = $('#browserStatsTable tbody');
    tbody.empty();

    const totalUsers = browserCounts.reduce((sum, count) => sum + count, 0);

    browserData.forEach(browser => {
        const percentage = totalUsers > 0 ? ((browser.count / totalUsers) * 100).toFixed(1) : 0;
        tbody.append(`
            <tr>
                <td>${browser.browser}</td>
                <td>${browser.count}</td>
                <td>${percentage}%</td>
            </tr>
        `);
    });
}

function updateComplianceChart(complianceData) {
    const ctx = document.getElementById('complianceChart').getContext('2d');

    if (complianceChart) {
        complianceChart.destroy();
    }

    $('#newCertifications').text(complianceData.new_certifications || 0);
    $('#expiringCertifications').text(complianceData.expiring_certifications || 0);

    const complianceRate = complianceData.overall_compliance_rate || 0;
    const nonCompliant = 100 - complianceRate;

    complianceChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Compliant', 'Non-Compliant'],
            datasets: [{
                data: [complianceRate, nonCompliant],
                backgroundColor: ['#28a745', '#dc3545'],
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

function updateResourceUtilizationChart(resourceData) {
    const courseCapacity = resourceData.course_capacity_utilization || 78.5;
    const instructorUtil = resourceData.instructor_utilization || 85.2;
    const rangeUtil = resourceData.range_utilization || 62.8;

    $('#courseCapacityUtil').text(courseCapacity.toFixed(1) + '%');
    $('#instructorUtil').text(instructorUtil.toFixed(1) + '%');
    $('#rangeUtil').text(rangeUtil.toFixed(1) + '%');

    $('#courseCapacityBar').css('width', courseCapacity + '%');
    $('#instructorUtilBar').css('width', instructorUtil + '%');
    $('#rangeUtilBar').css('width', rangeUtil + '%');

    const ctx = document.getElementById('resourceUtilizationChart').getContext('2d');

    if (resourceUtilizationChart) {
        resourceUtilizationChart.destroy();
    }

    // Simulated weekly utilization data
    const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const courseUtil = days.map(() => Math.floor(Math.random() * 30) + 60);
    const instructorUtilData = days.map(() => Math.floor(Math.random() * 20) + 75);
    const rangeUtilData = days.map(() => Math.floor(Math.random() * 40) + 40);

    resourceUtilizationChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: days,
            datasets: [
                {
                    label: 'Course Capacity',
                    data: courseUtil,
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    borderWidth: 2
                },
                {
                    label: 'Instructor Utilization',
                    data: instructorUtilData,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2
                },
                {
                    label: 'Range Utilization',
                    data: rangeUtilData,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2
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

    // Update peak usage metrics (simulated)
    updatePeakUsageMetrics();
}

function updatePeakUsageMetrics() {
    $('#peakConcurrentUsers').text(Math.floor(Math.random() * 100) + 200);
    $('#avgResponseTime').text(Math.floor(Math.random() * 50) + 150 + 'ms');
    $('#dataTransfer').text((Math.random() * 10 + 15).toFixed(1) + 'GB');
    $('#capacityRemaining').text(Math.floor(Math.random() * 20) + 25 + '%');

    // Update peak usage chart
    updatePeakUsageChart();
}

function updatePeakUsageChart() {
    const ctx = document.getElementById('peakUsageChart').getContext('2d');

    if (peakUsageChart) {
        peakUsageChart.destroy();
    }

    // Simulated daily peak usage for the past week
    const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const peakUsers = days.map(() => Math.floor(Math.random() * 100) + 150);
    const avgUsers = days.map(() => Math.floor(Math.random() * 50) + 100);

    peakUsageChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: days,
            datasets: [
                {
                    label: 'Peak Users',
                    data: peakUsers,
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Average Users',
                    data: avgUsers,
                    backgroundColor: 'rgba(0, 123, 255, 0.8)',
                    borderColor: 'rgba(0, 123, 255, 1)',
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
                }
            }
        }
    });
}

function toggleOperationalChart(chartName, type) {
    if (chartName === 'usage' && systemUsageChart) {
        systemUsageChart.config.type = type;
        systemUsageChart.update();
    }
}
</script>
