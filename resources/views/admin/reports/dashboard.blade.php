@extends('adminlte::page')

@section('title', 'Reports & Analytics - Frost')

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <!-- Analytics Overview Row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Analytics Overview
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Base Analytics & Traffic -->
                        <div class="col-lg-4">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-globe"></i> Traffic & Analytics
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Visitors</span>
                                            <span class="info-box-number" id="total-visitors">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-eye"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Page Views</span>
                                            <span class="info-box-number" id="page-views">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-danger"><i class="fas fa-chart-line"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Bounce Rate</span>
                                            <span class="info-box-number" id="bounce-rate">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-clock"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Avg Session</span>
                                            <span class="info-box-number" id="avg-session">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Reports -->
                        <div class="col-lg-4">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-dollar-sign"></i> Financial Reports
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Revenue</span>
                                            <span class="info-box-number" id="total-revenue">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-info"><i class="fas fa-calendar-month"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Monthly Revenue</span>
                                            <span class="info-box-number" id="monthly-revenue">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-shopping-cart"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Orders</span>
                                            <span class="info-box-number" id="total-orders">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="info-box">
                                        <span class="info-box-icon bg-primary"><i class="fas fa-chart-pie"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Avg Order Value</span>
                                            <span class="info-box-number" id="avg-order-value">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Classroom Analytics -->
                        <div class="col-lg-4">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-graduation-cap"></i> Classroom Analytics
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-primary"><i class="fas fa-user-graduate"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Students</span>
                                            <span class="info-box-number" id="total-students">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-success"><i class="fas fa-book"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Active Courses</span>
                                            <span class="info-box-number" id="active-courses">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-percentage"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Completion Rate</span>
                                            <span class="info-box-number" id="completion-rate">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-trophy"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Average Score</span>
                                            <span class="info-box-number" id="avg-score">Loading...</span>
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

    <!-- Charts Row -->
    <div class="row">
        <!-- Traffic Chart -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-area"></i> Traffic Overview
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="trafficChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Revenue Trends
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Performance Row -->
    <div class="row">
        <!-- Course Completion Chart -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Course Completion Rates
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="courseCompletionChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Student Progress Chart -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Student Progress
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="studentProgressChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Report Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <button class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-download"></i> Export Analytics
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-file-excel"></i> Generate Report
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-info btn-lg btn-block">
                                <i class="fas fa-calendar"></i> Schedule Report
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-warning btn-lg btn-block">
                                <i class="fas fa-cog"></i> Report Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    @vite('resources/css/admin.css')
@stop

@section('js')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        $(document).ready(function() {
            // Load dashboard data
            loadAnalyticsData();
            loadFinanceData();
            loadClassroomData();
            
            // Initialize charts
            initializeCharts();
        });

        function loadAnalyticsData() {
            $.get('{{ route("admin.reports.api.analytics.overview") }}', function(data) {
                $('#total-visitors').text(data.totalVisitors.toLocaleString());
                $('#page-views').text(data.pageViews.toLocaleString());
                $('#bounce-rate').text(data.bounceRate + '%');
                $('#avg-session').text(data.avgSessionDuration);
            }).fail(function() {
                $('.info-box-number').text('Error loading data');
            });
        }

        function loadFinanceData() {
            $.get('{{ route("admin.reports.api.finance.overview") }}', function(data) {
                $('#total-revenue').text('$' + data.totalRevenue.toLocaleString());
                $('#monthly-revenue').text('$' + data.monthlyRevenue.toLocaleString());
                $('#total-orders').text(data.totalOrders.toLocaleString());
                $('#avg-order-value').text('$' + data.avgOrderValue.toFixed(2));
            }).fail(function() {
                console.error('Failed to load finance data');
            });
        }

        function loadClassroomData() {
            $.get('{{ route("admin.reports.api.classroom.overview") }}', function(data) {
                $('#total-students').text(data.totalStudents.toLocaleString());
                $('#active-courses').text(data.activeCourses);
                $('#completion-rate').text(data.completionRate + '%');
                $('#avg-score').text(data.avgScore + '%');
            }).fail(function() {
                console.error('Failed to load classroom data');
            });
        }

        function initializeCharts() {
            // Traffic Chart
            $.get('{{ route("admin.reports.api.analytics.traffic") }}', function(data) {
                const ctx = document.getElementById('trafficChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });

            // Revenue Chart
            $.get('{{ route("admin.reports.api.finance.revenue") }}', function(data) {
                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });

            // Classroom Performance Charts
            $.get('{{ route("admin.reports.api.classroom.performance") }}', function(data) {
                // Course Completion Chart
                const ctx1 = document.getElementById('courseCompletionChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: data.courseCompletion.labels,
                        datasets: [{
                            label: 'Completion Rate (%)',
                            data: data.courseCompletion.data,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });

                // Student Progress Chart
                const ctx2 = document.getElementById('studentProgressChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'bar',
                    data: data.studentProgress,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        }
    </script>
@stop
