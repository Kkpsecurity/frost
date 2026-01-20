@extends('adminlte::page')

@section('title', 'Reports & Analytics')

@section('content_header')
    <h1>
        <i class="fas fa-chart-bar"></i> Reports & Analytics
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="reports-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="student-tab" data-toggle="pill" href="#student-reports" role="tab" aria-controls="student-reports" aria-selected="true">
                                <i class="fas fa-user-graduate mr-2"></i> Student Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="instructor-tab" data-toggle="pill" href="#instructor-reports" role="tab" aria-controls="instructor-reports" aria-selected="false">
                                <i class="fas fa-chalkboard-teacher mr-2"></i> Instructor Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="financial-tab" data-toggle="pill" href="#financial-reports" role="tab" aria-controls="financial-reports" aria-selected="false">
                                <i class="fas fa-dollar-sign mr-2"></i> Financial Reports
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="reports-tabContent">
                        <!-- Student Reports Tab -->
                        <div class="tab-pane fade show active" id="student-reports" role="tabpanel" aria-labelledby="student-tab">
                            <!-- Weekly Charts Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h4 class="mb-3"><i class="fas fa-chart-line mr-2"></i>Weekly Enrollment & Revenue Trends</h4>
                                </div>

                                <!-- Weekly Sales Chart -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-dollar-sign text-success mr-2"></i>Weekly Sales Total
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="weeklySalesChart" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Weekly Enrollment Chart -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-users text-info mr-2"></i>Weekly Student Enrollments
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="weeklyEnrollmentChart" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Class D vs G Breakdown -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-shield-alt text-primary mr-2"></i>Class D (Unarmed) Enrollments
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="classDChart" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-gun text-danger mr-2"></i>Class G (Armed) Enrollments
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="classGChart" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Reports Section -->
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="mb-3"><i class="fas fa-file-alt mr-2"></i>Detailed Reports</h4>
                                </div>

                                <!-- Student Analytics -->
                                <div class="col-md-6 mb-4">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3><i class="fas fa-user-graduate"></i></h3>
                                            <p>Student Analytics</p>
                                            <small class="text-white-50">Enrollment, progress, and completion data</small>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <a href="javascript:void(0)" class="small-box-footer" onclick="loadReport('students')">
                                            View Report <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- Course Performance -->
                                <div class="col-md-6 mb-4">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3><i class="fas fa-book"></i></h3>
                                            <p>Course Performance</p>
                                            <small class="text-white-50">Course completion rates and student outcomes</small>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <a href="javascript:void(0)" class="small-box-footer" onclick="loadReport('courses')">
                                            View Report <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructor Reports Tab -->
                        <div class="tab-pane fade" id="instructor-reports" role="tabpanel" aria-labelledby="instructor-tab">
                            <!-- Instructor Charts Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h4 class="mb-3"><i class="fas fa-chart-bar mr-2"></i>Instructor Performance Metrics</h4>
                                </div>

                                <!-- Total Students per Instructor -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-users text-primary mr-2"></i>Students Per Instructor
                                            </h5>
                                        </div>
                                        <div class="card-body" style="height: 400px;">
                                            <canvas id="studentsPerInstructorChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Classes Taught per Instructor -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-chalkboard text-info mr-2"></i>Classes Taught
                                            </h5>
                                        </div>
                                        <div class="card-body" style="height: 400px;">
                                            <canvas id="classesTaughtChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attendance Rate by Instructor -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-check-circle text-success mr-2"></i>Attendance Rates
                                            </h5>
                                        </div>
                                        <div class="card-body" style="height: 400px;">
                                            <canvas id="attendanceRateChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Course Type Distribution per Instructor -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-pie-chart text-warning mr-2"></i>Course Type Distribution
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="courseTypeDistributionChart" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Reports Section -->
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="mb-3"><i class="fas fa-file-alt mr-2"></i>Detailed Reports</h4>
                                </div>

                                <!-- Instructor Performance -->
                                <div class="col-md-6 mb-4">
                                    <div class="small-box bg-primary">
                                        <div class="inner">
                                            <h3><i class="fas fa-chalkboard-teacher"></i></h3>
                                            <p>Instructor Performance</p>
                                            <small class="text-white-50">Class delivery, attendance, and ratings</small>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <a href="javascript:void(0)" class="small-box-footer" onclick="loadReport('instructors')">
                                            View Report <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- Operational Analytics -->
                                <div class="col-md-6 mb-4">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3><i class="fas fa-cogs"></i></h3>
                                            <p>Operational Analytics</p>
                                            <small class="text-white-50">System usage and classroom management</small>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-server"></i>
                                        </div>
                                        <a href="javascript:void(0)" class="small-box-footer" onclick="loadReport('operational')">
                                            View Report <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Reports Tab -->
                        <div class="tab-pane fade" id="financial-reports" role="tabpanel" aria-labelledby="financial-tab">
                            <!-- Financial Performance Metrics -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h4 class="mb-3"><i class="fas fa-dollar-sign mr-2"></i>Financial Performance Metrics</h4>
                                </div>

                                <!-- Monthly Revenue Chart -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-chart-line text-success mr-2"></i>Monthly Revenue Trend
                                            </h5>
                                        </div>
                                        <div class="card-body" style="height: 350px;">
                                            <canvas id="monthlyRevenueChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Methods Distribution -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-credit-card text-info mr-2"></i>Payment Methods
                                            </h5>
                                        </div>
                                        <div class="card-body" style="height: 350px;">
                                            <canvas id="paymentMethodsChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Revenue by Course Type -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-book text-primary mr-2"></i>Revenue by Course Type
                                            </h5>
                                        </div>
                                        <div class="card-body" style="height: 350px;">
                                            <canvas id="revenueByCourseChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Average Order Value -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-calculator text-warning mr-2"></i>Average Order Value Trend
                                            </h5>
                                        </div>
                                        <div class="card-body" style="height: 350px;">
                                            <canvas id="avgOrderValueChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Reports Section -->
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="mb-3"><i class="fas fa-file-alt mr-2"></i>Detailed Financial Reports</h4>
                                </div>

                                <!-- Revenue Reports -->
                                <div class="col-md-6 mb-4">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3><i class="fas fa-dollar-sign"></i></h3>
                                            <p>Revenue Reports</p>
                                            <small class="text-white-50">Total revenue, payment tracking</small>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <a href="javascript:void(0)" class="small-box-footer" onclick="loadReport('financial')">
                                            View Report <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- Transaction Analysis -->
                                <div class="col-md-6 mb-4">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3><i class="fas fa-exchange-alt"></i></h3>
                                            <p>Transaction Analysis</p>
                                            <small class="text-white-50">Order details and payment methods</small>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-receipt"></i>
                                        </div>
                                        <a href="javascript:void(0)" class="small-box-footer" onclick="loadReport('financial')">
                                            View Report <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Display Area -->
                    <div id="report-display-area" class="mt-4" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title" id="report-title"></h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="exportReport()">
                                        <i class="fas fa-download"></i> Export
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="closeReport()">
                                        <i class="fas fa-times"></i> Close
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="report-content">
                                    <div class="text-center py-5">
                                        <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
                                        <p class="mt-3">Loading report data...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
let currentReportType = null;
let weeklySalesChart = null;
let weeklyEnrollmentChart = null;
let classDChart = null;
let classGChart = null;

// Instructor charts
let studentsPerInstructorChart = null;
let classesTaughtChart = null;
let attendanceRateChart = null;
let courseTypeDistributionChart = null;

// Financial charts
let monthlyRevenueChart = null;
let paymentMethodsChart = null;
let revenueByCourseChart = null;
let avgOrderValueChart = null;

// Initialize charts on page load
$(document).ready(function() {
    // Set global Chart.js defaults for brighter text
    Chart.defaults.color = '#e0e0e0'; // Brighter default text color
    Chart.defaults.font.size = 13;
    Chart.defaults.plugins.legend.labels.color = '#f8f9fa';
    Chart.defaults.plugins.legend.labels.font = {
        size: 13,
        weight: '500'
    };
    Chart.defaults.plugins.tooltip.titleColor = '#ffffff';
    Chart.defaults.plugins.tooltip.bodyColor = '#ffffff';
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.9)';
    Chart.defaults.plugins.tooltip.titleFont = {
        size: 14,
        weight: 'bold'
    };
    Chart.defaults.plugins.tooltip.bodyFont = {
        size: 13
    };

    // Scale/Axis defaults
    Chart.defaults.scale.ticks.color = '#dee2e6';
    Chart.defaults.scale.grid.color = 'rgba(255, 255, 255, 0.1)';

    initializeCharts();
    loadWeeklyData();

    // Load instructor data when instructor tab is clicked
    $('a[href="#instructor-reports"]').on('shown.bs.tab', function (e) {
        console.log('Instructor tab shown, checking if data needs to be loaded...');
        console.log('Current labels:', studentsPerInstructorChart ? studentsPerInstructorChart.data.labels : 'Charts not initialized');

        // Force chart resize after tab is visible
        setTimeout(function() {
            if (studentsPerInstructorChart) {
                console.log('Forcing chart resize and render...');
                studentsPerInstructorChart.resize();
                studentsPerInstructorChart.render();
                classesTaughtChart.resize();
                classesTaughtChart.render();
                attendanceRateChart.resize();
                attendanceRateChart.render();
                courseTypeDistributionChart.resize();
                courseTypeDistributionChart.render();
            }
        }, 150);

        if (!studentsPerInstructorChart || !studentsPerInstructorChart.data.labels.length) {
            console.log('Loading instructor data...');
            loadInstructorData();
        } else {
            console.log('Instructor data already loaded');
        }
    });

    // Check if instructor tab is already active on page load
    if ($('#instructor-reports').hasClass('active')) {
        console.log('Instructor tab is active on load, loading data...');
        setTimeout(function() {
            loadInstructorData();
        }, 500);
    }

    // Load financial data when financial tab is clicked
    $('a[href="#financial-reports"]').on('shown.bs.tab', function (e) {
        console.log('Financial tab shown, checking if data needs to be loaded...');

        // Force chart resize after tab is visible
        setTimeout(function() {
            if (monthlyRevenueChart) {
                console.log('Forcing financial chart resize and render...');
                monthlyRevenueChart.resize();
                monthlyRevenueChart.render();
                paymentMethodsChart.resize();
                paymentMethodsChart.render();
                revenueByCourseChart.resize();
                revenueByCourseChart.render();
                avgOrderValueChart.resize();
                avgOrderValueChart.render();
            }
        }, 150);

        if (!monthlyRevenueChart || !monthlyRevenueChart.data.labels.length) {
            console.log('Loading financial data...');
            loadFinancialData();
        } else {
            console.log('Financial data already loaded');
        }
    });

    // Check if financial tab is already active on page load
    if ($('#financial-reports').hasClass('active')) {
        console.log('Financial tab is active on load, loading data...');
        setTimeout(function() {
            loadFinancialData();
        }, 500);
    }
});

function initializeCharts() {
    // Weekly Sales Chart
    const salesCtx = document.getElementById('weeklySalesChart').getContext('2d');
    weeklySalesChart = new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Revenue ($)',
                data: [],
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: $' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Weekly Enrollment Chart
    const enrollCtx = document.getElementById('weeklyEnrollmentChart').getContext('2d');
    weeklyEnrollmentChart = new Chart(enrollCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Total Enrollments',
                data: [],
                backgroundColor: 'rgba(23, 162, 184, 0.2)',
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
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

    // Class D Chart
    const classDCtx = document.getElementById('classDChart').getContext('2d');
    classDChart = new Chart(classDCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Class D Enrollments',
                data: [],
                backgroundColor: 'rgba(0, 123, 255, 0.8)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2
            }]
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

    // Class G Chart
    const classGCtx = document.getElementById('classGChart').getContext('2d');
    classGChart = new Chart(classGCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Class G Enrollments',
                data: [],
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 2
            }]
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

    // Instructor Charts
    // Students Per Instructor Chart
    const studentsPerInstructorCtx = document.getElementById('studentsPerInstructorChart').getContext('2d');
    studentsPerInstructorChart = new Chart(studentsPerInstructorCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Total Students',
                data: [],
                backgroundColor: 'rgba(0, 123, 255, 0.8)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            layout: {
                padding: {
                    left: 10,
                    right: 20,
                    top: 10,
                    bottom: 10
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                y: {
                    ticks: {
                        autoSkip: false,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Classes Taught Chart
    const classesTaughtCtx = document.getElementById('classesTaughtChart').getContext('2d');
    classesTaughtChart = new Chart(classesTaughtCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Classes Taught',
                data: [],
                backgroundColor: 'rgba(23, 162, 184, 0.8)',
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            layout: {
                padding: {
                    left: 10,
                    right: 20,
                    top: 10,
                    bottom: 10
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                y: {
                    ticks: {
                        autoSkip: false,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Attendance Rate Chart
    const attendanceRateCtx = document.getElementById('attendanceRateChart').getContext('2d');
    attendanceRateChart = new Chart(attendanceRateCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Attendance Rate (%)',
                data: [],
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            layout: {
                padding: {
                    left: 10,
                    right: 20,
                    top: 10,
                    bottom: 10
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Attendance: ' + context.parsed.x.toFixed(1) + '%';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                y: {
                    ticks: {
                        autoSkip: false,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Course Type Distribution Chart (Doughnut)
    const courseTypeDistributionCtx = document.getElementById('courseTypeDistributionChart').getContext('2d');
    courseTypeDistributionChart = new Chart(courseTypeDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Class D (Unarmed)', 'Class G (Armed)', 'Other'],
            datasets: [{
                label: 'Course Types',
                data: [],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(108, 117, 125, 0.8)'
                ],
                borderColor: [
                    'rgba(0, 123, 255, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 125, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Financial Charts
    // Monthly Revenue Chart
    const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    monthlyRevenueChart = new Chart(monthlyRevenueCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Revenue ($)',
                data: [],
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
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
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Payment Methods Chart
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    paymentMethodsChart = new Chart(paymentMethodsCtx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                label: 'Payment Methods',
                data: [],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(108, 117, 125, 0.8)'
                ],
                borderWidth: 2
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

    // Revenue by Course Chart
    const revenueByCourseCtx = document.getElementById('revenueByCourseChart').getContext('2d');
    revenueByCourseChart = new Chart(revenueByCourseCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Revenue ($)',
                data: [],
                backgroundColor: 'rgba(0, 123, 255, 0.8)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Average Order Value Chart
    const avgOrderValueCtx = document.getElementById('avgOrderValueChart').getContext('2d');
    avgOrderValueChart = new Chart(avgOrderValueCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Avg Order Value ($)',
                data: [],
                backgroundColor: 'rgba(255, 193, 7, 0.2)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
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
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });
}

function loadWeeklyData() {
    // Fetch weekly enrollment and sales data
    $.ajax({
        url: '/admin/reports/api/weekly-data',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                updateCharts(response.data);
            } else {
                console.error('Failed to load weekly data:', response.error);
                loadSampleData(); // Fallback to sample data
            }
        },
        error: function(xhr) {
            console.error('Error loading weekly data:', xhr);
            loadSampleData(); // Fallback to sample data
        }
    });
}

function loadSampleData() {
    // Sample data for demonstration (last 8 weeks)
    const weeks = [];
    const today = new Date();

    for (let i = 7; i >= 0; i--) {
        const weekDate = new Date(today);
        weekDate.setDate(today.getDate() - (i * 7));
        weeks.push(formatWeekLabel(weekDate));
    }

    const sampleData = {
        weeks: weeks,
        sales: [2400, 3100, 2800, 3500, 4200, 3800, 4500, 5100],
        totalEnrollments: [18, 24, 22, 28, 32, 30, 35, 40],
        classDEnrollments: [12, 16, 15, 18, 22, 20, 23, 26],
        classGEnrollments: [6, 8, 7, 10, 10, 10, 12, 14]
    };

    updateCharts(sampleData);
}

function formatWeekLabel(date) {
    const month = date.toLocaleString('default', { month: 'short' });
    const day = date.getDate();
    return `${month} ${day}`;
}

function updateCharts(data) {
    // Update Weekly Sales Chart
    weeklySalesChart.data.labels = data.weeks;
    weeklySalesChart.data.datasets[0].data = data.sales;
    weeklySalesChart.update();

    // Update Weekly Enrollment Chart
    weeklyEnrollmentChart.data.labels = data.weeks;
    weeklyEnrollmentChart.data.datasets[0].data = data.totalEnrollments;
    weeklyEnrollmentChart.update();

    // Update Class D Chart
    classDChart.data.labels = data.weeks;
    classDChart.data.datasets[0].data = data.classDEnrollments;
    classDChart.update();

    // Update Class G Chart
    classGChart.data.labels = data.weeks;
    classGChart.data.datasets[0].data = data.classGEnrollments;
    classGChart.update();
}

function loadInstructorData() {
    // Fetch instructor performance data
    $.ajax({
        url: '/admin/reports/api/instructor-data',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                updateInstructorCharts(response.data);
            } else {
                console.error('Failed to load instructor data:', response.error);
                loadSampleInstructorData(); // Fallback to sample data
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading instructor data:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });

            // Show user-friendly error
            if (xhr.status === 404) {
                console.warn('Instructor data endpoint not found - using sample data');
            } else if (xhr.status === 500) {
                console.error('Server error loading instructor data - using sample data');
            }

            loadSampleInstructorData(); // Fallback to sample data
        }
    });
}

function loadSampleInstructorData() {
    // Sample instructor data for demonstration
    const sampleData = {
        instructors: ['John Smith', 'Sarah Johnson', 'Mike Davis', 'Emily Brown', 'Tom Wilson'],
        studentsPerInstructor: [45, 38, 52, 41, 35],
        classesTaught: [12, 10, 15, 11, 9],
        attendanceRates: [92.5, 88.3, 95.1, 90.7, 87.2],
        courseTypeDistribution: [85, 62, 8] // D, G, Other
    };

    updateInstructorCharts(sampleData);
}

function updateInstructorCharts(data) {
    console.log('Updating instructor charts with data:', data);

    // Check if charts exist
    if (!studentsPerInstructorChart) {
        console.error('studentsPerInstructorChart is not initialized');
        return;
    }

    // Update Students Per Instructor Chart
    studentsPerInstructorChart.data.labels = data.instructors || [];
    studentsPerInstructorChart.data.datasets[0].data = data.studentsPerInstructor || [];
    console.log('Students Per Instructor - Labels:', studentsPerInstructorChart.data.labels);
    console.log('Students Per Instructor - Data:', studentsPerInstructorChart.data.datasets[0].data);
    studentsPerInstructorChart.update();

    // Update Classes Taught Chart
    classesTaughtChart.data.labels = data.instructors || [];
    classesTaughtChart.data.datasets[0].data = data.classesTaught || [];
    console.log('Classes Taught - Labels:', classesTaughtChart.data.labels);
    console.log('Classes Taught - Data:', classesTaughtChart.data.datasets[0].data);
    classesTaughtChart.update();

    // Update Attendance Rate Chart
    attendanceRateChart.data.labels = data.instructors || [];
    attendanceRateChart.data.datasets[0].data = data.attendanceRates || [];
    console.log('Attendance Rate - Labels:', attendanceRateChart.data.labels);
    console.log('Attendance Rate - Data:', attendanceRateChart.data.datasets[0].data);
    attendanceRateChart.update();

    // Update Course Type Distribution Chart
    courseTypeDistributionChart.data.datasets[0].data = data.courseTypeDistribution || [0, 0, 0];
    console.log('Course Distribution - Data:', courseTypeDistributionChart.data.datasets[0].data);
    courseTypeDistributionChart.update();

    // Force resize and render after update to ensure proper rendering in tab
    setTimeout(function() {
        console.log('Forcing chart resize after data update...');
        studentsPerInstructorChart.resize();
        studentsPerInstructorChart.render();
        classesTaughtChart.resize();
        classesTaughtChart.render();
        attendanceRateChart.resize();
        attendanceRateChart.render();
        courseTypeDistributionChart.resize();
        courseTypeDistributionChart.render();
    }, 150);

    console.log('All instructor charts updated successfully');
}

function loadFinancialData() {
    console.log('Loading financial data from API...');

    $.ajax({
        url: '/admin/reports/api/financial-data',
        method: 'GET',
        success: function(response) {
            console.log('Financial data received:', response);
            if (response.success && response.data) {
                updateFinancialCharts(response.data);
            } else {
                console.error('Failed to load financial data');
                loadSampleFinancialData();
            }
        },
        error: function(xhr) {
            console.error('Error loading financial data:', xhr);
            console.log('Loading sample financial data...');
            loadSampleFinancialData();
        }
    });
}

function loadSampleFinancialData() {
    console.log('Using sample financial data');

    const sampleData = {
        monthlyRevenue: {
            labels: ['Aug 2025', 'Sep 2025', 'Oct 2025', 'Nov 2025', 'Dec 2025', 'Jan 2026'],
            data: [45000, 52000, 48000, 61000, 58000, 49000]
        },
        paymentMethods: {
            labels: ['Credit Card', 'Cash', 'Check', 'Bank Transfer', 'Other'],
            data: [450, 180, 95, 75, 25]
        },
        revenueByCourse: {
            labels: ['Class D - Unarmed', 'Class G - Armed', 'Refresher Course', 'Online Training', 'Other'],
            data: [125000, 185000, 45000, 28000, 12000]
        },
        avgOrderValue: {
            labels: ['Aug 2025', 'Sep 2025', 'Oct 2025', 'Nov 2025', 'Dec 2025', 'Jan 2026'],
            data: [145.50, 152.30, 148.75, 159.20, 155.80, 151.40]
        }
    };

    updateFinancialCharts(sampleData);
}

function updateFinancialCharts(data) {
    console.log('Updating financial charts with data:', data);

    // Check if charts exist
    if (!monthlyRevenueChart) {
        console.error('Financial charts are not initialized');
        return;
    }

    // Update Monthly Revenue Chart
    monthlyRevenueChart.data.labels = data.monthlyRevenue.labels || [];
    monthlyRevenueChart.data.datasets[0].data = data.monthlyRevenue.data || [];
    monthlyRevenueChart.update();

    // Update Payment Methods Chart
    paymentMethodsChart.data.labels = data.paymentMethods.labels || [];
    paymentMethodsChart.data.datasets[0].data = data.paymentMethods.data || [];
    paymentMethodsChart.update();

    // Update Revenue by Course Chart
    revenueByCourseChart.data.labels = data.revenueByCourse.labels || [];
    revenueByCourseChart.data.datasets[0].data = data.revenueByCourse.data || [];
    revenueByCourseChart.update();

    // Update Average Order Value Chart
    avgOrderValueChart.data.labels = data.avgOrderValue.labels || [];
    avgOrderValueChart.data.datasets[0].data = data.avgOrderValue.data || [];
    avgOrderValueChart.update();

    // Force resize after update to ensure proper rendering in tab
    setTimeout(function() {
        console.log('Forcing financial chart resize after data update...');
        monthlyRevenueChart.resize();
        monthlyRevenueChart.render();
        paymentMethodsChart.resize();
        paymentMethodsChart.render();
        revenueByCourseChart.resize();
        revenueByCourseChart.render();
        avgOrderValueChart.resize();
        avgOrderValueChart.render();
    }, 150);

    console.log('All financial charts updated successfully');
}

function loadReport(reportType) {
    currentReportType = reportType;
    const reportTitles = {
        'financial': 'Financial Reports',
        'students': 'Student Analytics',
        'courses': 'Course Performance',
        'instructors': 'Instructor Reports',
        'operational': 'Operational Analytics'
    };

    // Show the report display area
    $('#report-display-area').slideDown();
    $('#report-title').text(reportTitles[reportType]);

    // Show loading spinner
    $('#report-content').html(`
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
            <p class="mt-3">Loading report data...</p>
        </div>
    `);

    // Fetch report data
    $.ajax({
        url: `/admin/reports/api/${reportType}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                displayReportData(reportType, response.data);
            } else {
                showError('Failed to load report data');
            }
        },
        error: function(xhr) {
            showError('Error loading report: ' + (xhr.responseJSON?.error || 'Unknown error'));
        }
    });
}

function displayReportData(reportType, data) {
    let html = '<div class="row">';

    // Display based on report type
    if (reportType === 'financial' && data.summary) {
        html += `
            <div class="col-md-4">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Revenue</span>
                        <span class="info-box-number">$${parseFloat(data.summary.total_revenue).toFixed(2)}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Enrollments</span>
                        <span class="info-box-number">${data.summary.total_enrollments}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Avg per Enrollment</span>
                        <span class="info-box-number">$${parseFloat(data.summary.avg_revenue_per_enrollment).toFixed(2)}</span>
                    </div>
                </div>
            </div>
        `;
    }

    html += '</div>';
    html += '<p class="text-muted"><em>Detailed report visualization coming soon...</em></p>';
    html += '<pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">' +
            JSON.stringify(data, null, 2) + '</pre>';

    $('#report-content').html(html);
}

function showError(message) {
    $('#report-content').html(`
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            ${message}
        </div>
    `);
}

function closeReport() {
    $('#report-display-area').slideUp();
    currentReportType = null;
}

function exportReport() {
    if (!currentReportType) {
        alert('No report loaded');
        return;
    }

    $.ajax({
        url: `/admin/reports/api/${currentReportType}/export`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                window.location.href = response.download_url;
            } else {
                alert('Export failed: ' + response.error);
            }
        },
        error: function(xhr) {
            alert('Export error: ' + (xhr.responseJSON?.error || 'Unknown error'));
        }
    });
}
</script>
@stop

@section('css')
<style>
    .small-box .icon {
        font-size: 60px;
    }
    .small-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }

    /* Enhanced Tab Styling */
    #reports-tabs .nav-link {
        font-size: 16px;
        font-weight: 600;
        padding: 12px 30px;
        border: none;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    #reports-tabs .nav-link:hover {
        color: #007bff;
        background-color: #f8f9fa;
    }

    #reports-tabs .nav-link.active {
        color: #007bff;
        background-color: #fff;
        border-bottom: 3px solid #007bff;
    }

    #reports-tabs .nav-link i {
        font-size: 18px;
    }

    .card-tabs {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }

    .tab-content {
        padding-top: 20px;
    }

    .small-box small {
        display: block;
        margin-top: 5px;
        font-size: 12px;
    }

    /* Force chart canvas containers to have proper dimensions */
    .card-body canvas {
        display: block !important;
        width: 100% !important;
        height: 100% !important;
    }

    /* Ensure tab content is visible when active */
    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block !important;
    .card-body canvas {
        background-color: #2d3338;
        border-radius: 4px;
        padding: 10px;
    }

    .card-header h5 {
        font-weight: 600;
    }

    .card-header i {
        font-size: 1.1em;
    }

    /* Section Headers */
    .tab-pane h4 {
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .tab-pane h4 i {
        color: #007bff;
    }

    /* Chart card styling for better visibility */
    .card {
        background-color: #343a40;
        border: 1px solid #454d55;
    }

    .card-header {
        background-color: #3a4149;
        border-bottom: 1px solid #454d55;
        color: #ffffff;
    }

    .card-body {
        background-color: #343a40;
    }

    .card-title {
        color: #ffffff !important;
    }
</style>
@stop
