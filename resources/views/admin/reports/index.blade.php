@extends('adminlte::page')

@section('title', 'Reports & Analytics Dashboard')

@section('content_header')
    <x-admin.partials.titlebar title="Reports & Analytics" :breadcrumbs="[['title' => 'Admin', 'url' => url('admin')], ['title' => 'Reports & Analytics']]" />
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1>
            <i class="fas fa-chart-bar mr-2"></i>
            Reports & Analytics
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-download mr-1"></i>
                Export Reports
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="#" onclick="exportReport('financial', 'pdf')">
                    <i class="fas fa-file-pdf mr-2"></i>Financial Report (PDF)
                </a>
                <a class="dropdown-item" href="#" onclick="exportReport('student', 'csv')">
                    <i class="fas fa-file-csv mr-2"></i>Student Analytics (CSV)
                </a>
                <a class="dropdown-item" href="#" onclick="exportReport('course', 'excel')">
                    <i class="fas fa-file-excel mr-2"></i>Course Performance (Excel)
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="showExportModal()">
                    <i class="fas fa-cog mr-2"></i>Custom Export
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Date Range Selector -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Report Period Selection
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Report Period</label>
                                <select id="reportPeriod" class="form-control">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly" selected>Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" id="startDate" class="form-control" value="{{ date('Y-m-01') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" id="endDate" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="button" class="btn btn-primary" onclick="updateReports()">
                                    <i class="fas fa-sync mr-1"></i>
                                    Update Reports
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="totalRevenue">$0</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="totalStudents">0</h3>
                    <p>Active Students</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="completionRate">0%</h3>
                    <p>Completion Rate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="activeInstructors">0</h3>
                    <p>Active Instructors</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="financial-tab" data-toggle="pill" href="#financial" role="tab">
                                <i class="fas fa-chart-line mr-1"></i>
                                Financial Analytics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="students-tab" data-toggle="pill" href="#students" role="tab">
                                <i class="fas fa-user-graduate mr-1"></i>
                                Student Analytics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="courses-tab" data-toggle="pill" href="#courses" role="tab">
                                <i class="fas fa-book mr-1"></i>
                                Course Performance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="instructors-tab" data-toggle="pill" href="#instructors" role="tab">
                                <i class="fas fa-chalkboard-teacher mr-1"></i>
                                Instructor Performance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="operational-tab" data-toggle="pill" href="#operational" role="tab">
                                <i class="fas fa-cogs mr-1"></i>
                                Operational Analytics
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">

                        <!-- Financial Analytics Tab -->
                        <div class="tab-pane fade show active" id="financial" role="tabpanel">
                            @include('admin.reports.partials.financial')
                        </div>

                        <!-- Student Analytics Tab -->
                        <div class="tab-pane fade" id="students" role="tabpanel">
                            @include('admin.reports.partials.students')
                        </div>

                        <!-- Course Performance Tab -->
                        <div class="tab-pane fade" id="courses" role="tabpanel">
                            @include('admin.reports.partials.courses')
                        </div>

                        <!-- Instructor Performance Tab -->
                        <div class="tab-pane fade" id="instructors" role="tabpanel">
                            @include('admin.reports.partials.instructors')
                        </div>

                        <!-- Operational Analytics Tab -->
                        <div class="tab-pane fade" id="operational" role="tabpanel">
                            @include('admin.reports.partials.operational')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-download mr-2"></i>
                    Export Custom Report
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="form-group">
                        <label>Report Type</label>
                        <select id="exportReportType" class="form-control" required>
                            <option value="">Select Report Type</option>
                            <option value="financial">Financial Analytics</option>
                            <option value="student">Student Analytics</option>
                            <option value="course">Course Performance</option>
                            <option value="instructor">Instructor Performance</option>
                            <option value="operational">Operational Analytics</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Export Format</label>
                        <select id="exportFormat" class="form-control" required>
                            <option value="">Select Format</option>
                            <option value="pdf">PDF Document</option>
                            <option value="csv">CSV Spreadsheet</option>
                            <option value="excel">Excel Workbook</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Period</label>
                        <select id="exportPeriod" class="form-control" required>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="processExport()">
                    <i class="fas fa-download mr-1"></i>
                    Generate Export
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<style>
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }

    .report-chart {
        height: 400px;
    }

    .metric-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    .tab-content {
        min-height: 600px;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentReportData = {};

$(document).ready(function() {
    // Initialize with default data
    updateReports();

    // Tab change handlers
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        const targetTab = $(e.target).attr('href').substring(1);
        if (!currentReportData[targetTab]) {
            loadTabData(targetTab);
        }
    });
});

function updateReports() {
    showLoading();

    const period = $('#reportPeriod').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();

    // Load all report data
    Promise.all([
        loadFinancialData(period, startDate, endDate),
        loadStudentData(period, startDate, endDate),
        loadCourseData(period, startDate, endDate),
        loadInstructorData(period, startDate, endDate),
        loadOperationalData(period, startDate, endDate)
    ]).then(() => {
        updateKPIs();
        hideLoading();
    }).catch(error => {
        console.error('Error loading reports:', error);
        hideLoading();
        showAlert('Error loading reports. Please try again.', 'error');
    });
}

function loadFinancialData(period, startDate, endDate) {
    return $.get('/admin/reports/api/financial/', {
        period: period,
        start_date: startDate,
        end_date: endDate
    }).done(function(response) {
        if (response.success) {
            currentReportData.financial = response.data;
            updateFinancialCharts(response.data);
        }
    });
}

function loadStudentData(period, startDate, endDate) {
    return $.get('/admin/reports/api/students/', {
        period: period,
        start_date: startDate,
        end_date: endDate
    }).done(function(response) {
        if (response.success) {
            currentReportData.students = response.data;
            updateStudentCharts(response.data);
        }
    });
}

function loadCourseData(period, startDate, endDate) {
    return $.get('/admin/reports/api/courses/', {
        period: period,
        start_date: startDate,
        end_date: endDate
    }).done(function(response) {
        if (response.success) {
            currentReportData.courses = response.data;
            updateCourseCharts(response.data);
        }
    });
}

function loadInstructorData(period, startDate, endDate) {
    return $.get('/admin/reports/api/instructors/', {
        period: period,
        start_date: startDate,
        end_date: endDate
    }).done(function(response) {
        if (response.success) {
            currentReportData.instructors = response.data;
            updateInstructorCharts(response.data);
        }
    });
}

function loadOperationalData(period, startDate, endDate) {
    return $.get('/admin/reports/api/operational/', {
        period: period,
        start_date: startDate,
        end_date: endDate
    }).done(function(response) {
        if (response.success) {
            currentReportData.operational = response.data;
            updateOperationalCharts(response.data);
        }
    });
}

function updateKPIs() {
    if (currentReportData.financial) {
        $('#totalRevenue').text('$' + numberWithCommas(currentReportData.financial.summary.total_revenue || 0));
    }

    if (currentReportData.students) {
        $('#totalStudents').text(currentReportData.students.summary.total_students || 0);
        $('#completionRate').text((currentReportData.students.summary.avg_completion_rate || 0) + '%');
    }

    if (currentReportData.instructors) {
        $('#activeInstructors').text(currentReportData.instructors.summary.total_instructors || 0);
    }
}

function updateFinancialCharts(data) {
    // Implementation for financial charts
    console.log('Updating financial charts with:', data);
}

function updateStudentCharts(data) {
    // Implementation for student charts
    console.log('Updating student charts with:', data);
}

function updateCourseCharts(data) {
    // Implementation for course charts
    console.log('Updating course charts with:', data);
}

function updateInstructorCharts(data) {
    // Implementation for instructor charts
    console.log('Updating instructor charts with:', data);
}

function updateOperationalCharts(data) {
    // Implementation for operational charts
    console.log('Updating operational charts with:', data);
}

function exportReport(reportType, format) {
    const period = $('#reportPeriod').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();

    $.post('/admin/reports/export', {
        report_type: reportType,
        format: format,
        period: period,
        start_date: startDate,
        end_date: endDate,
        _token: $('meta[name="csrf-token"]').attr('content')
    }).done(function(response) {
        if (response.success) {
            showAlert('Export initiated successfully. Download will start shortly.', 'success');
            // Auto-download the file
            window.location.href = response.download_url;
        } else {
            showAlert('Export failed. Please try again.', 'error');
        }
    }).fail(function() {
        showAlert('Export failed. Please try again.', 'error');
    });
}

function showExportModal() {
    $('#exportModal').modal('show');
}

function processExport() {
    const reportType = $('#exportReportType').val();
    const format = $('#exportFormat').val();
    const period = $('#exportPeriod').val();

    if (!reportType || !format || !period) {
        showAlert('Please fill in all required fields.', 'warning');
        return;
    }

    $('#exportModal').modal('hide');
    exportReport(reportType, format);
}

function showLoading() {
    if ($('.loading-overlay').length === 0) {
        $('body').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"></div></div>');
    }
}

function hideLoading() {
    $('.loading-overlay').remove();
}

function showAlert(message, type) {
    const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;

    $('.content-wrapper').prepend(alertHtml);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
</script>
@endsection
