<!-- Financial Analytics Partial -->
<div class="row">

    <!-- Revenue Trends Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-2"></i>
                    Revenue Trends
                </h3>
                <div class="card-tools">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-default" onclick="toggleChartType('revenue', 'line')">Line</button>
                        <button type="button" class="btn btn-default" onclick="toggleChartType('revenue', 'bar')">Bar</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-dollar-sign mr-2"></i>
                    Financial Summary
                </h3>
            </div>
            <div class="card-body">
                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Revenue</span>
                        <strong id="financialTotalRevenue">$0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Orders</span>
                        <strong id="financialTotalOrders">0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-info" style="width: 85%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Avg Order Value</span>
                        <strong id="financialAvgOrderValue">$0</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-warning" style="width: 70%"></div>
                    </div>
                </div>

                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Refund Rate</span>
                        <strong id="financialRefundRate">0%</strong>
                    </div>
                    <div class="progress progress-sm mt-1">
                        <div class="progress-bar bg-danger" style="width: 15%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Course Sales Performance -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy mr-2"></i>
                    Top Performing Courses (Revenue)
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="courseSalesTable">
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

    <!-- Payment Methods Analysis -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-credit-card mr-2"></i>
                    Payment Methods Distribution
                </h3>
            </div>
            <div class="card-body">
                <canvas id="paymentMethodsChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">

    <!-- Monthly Revenue Comparison -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Monthly Revenue Comparison
                </h3>
            </div>
            <div class="card-body">
                <canvas id="monthlyRevenueChart" class="report-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Refund Analysis -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-undo mr-2"></i>
                    Refund Analysis
                </h3>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <canvas id="refundChart" width="200" height="200"></canvas>
                </div>
                <div class="refund-stats">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Refunds:</span>
                        <strong id="totalRefunds">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Refund Amount:</span>
                        <strong id="refundAmount">$0</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Refund Rate:</span>
                        <strong id="refundRate" class="text-danger">0%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
let revenueChart, paymentMethodsChart, monthlyRevenueChart, refundChart;

function updateFinancialCharts(data) {
    updateFinancialSummary(data.summary);
    updateRevenueChart(data.revenue);
    updateCourseSalesTable(data.course_sales);
    updatePaymentMethodsChart(data.payment_methods);
    updateRefundChart(data.refunds);
}

function updateFinancialSummary(summary) {
    $('#financialTotalRevenue').text('$' + numberWithCommas(summary.total_revenue || 0));
    $('#financialTotalOrders').text(summary.total_orders || 0);
    $('#financialAvgOrderValue').text('$' + (summary.avg_order_value || 0).toFixed(2));
    $('#financialRefundRate').text((summary.refund_rate || 0).toFixed(1) + '%');
}

function updateRevenueChart(revenueData) {
    const ctx = document.getElementById('revenueChart').getContext('2d');

    if (revenueChart) {
        revenueChart.destroy();
    }

    const labels = revenueData.daily_data?.map(item => item.date) || [];
    const revenues = revenueData.daily_data?.map(item => item.daily_revenue) || [];

    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Daily Revenue',
                data: revenues,
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
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function updateCourseSalesTable(courseSales) {
    const tbody = $('#courseSalesTable tbody');
    tbody.empty();

    if (courseSales && courseSales.length > 0) {
        courseSales.slice(0, 10).forEach(course => {
            tbody.append(`
                <tr>
                    <td class="text-truncate" style="max-width: 150px;" title="${course.course_name}">
                        ${course.course_name}
                    </td>
                    <td>${course.sales_count}</td>
                    <td>$${numberWithCommas(course.total_revenue)}</td>
                    <td>$${course.avg_price.toFixed(2)}</td>
                </tr>
            `);
        });
    } else {
        tbody.append('<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>');
    }
}

function updatePaymentMethodsChart(paymentMethods) {
    const ctx = document.getElementById('paymentMethodsChart').getContext('2d');

    if (paymentMethodsChart) {
        paymentMethodsChart.destroy();
    }

    const labels = paymentMethods?.map(method => method.payment_method) || [];
    const counts = paymentMethods?.map(method => method.transaction_count) || [];
    const colors = ['#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1'];

    paymentMethodsChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: colors.slice(0, labels.length),
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

function updateRefundChart(refundData) {
    const ctx = document.getElementById('refundChart').getContext('2d');

    if (refundChart) {
        refundChart.destroy();
    }

    const refundRate = refundData.refund_rate || 0;
    const successRate = 100 - refundRate;

    refundChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Successful Orders', 'Refunded Orders'],
            datasets: [{
                data: [successRate, refundRate],
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
                    display: false
                }
            }
        }
    });

    // Update refund stats
    $('#totalRefunds').text(refundData.refunded_orders || 0);
    $('#refundAmount').text('$' + numberWithCommas(refundData.refund_amount || 0));
    $('#refundRate').text(refundRate.toFixed(1) + '%');
}

function toggleChartType(chartName, type) {
    if (chartName === 'revenue' && revenueChart) {
        revenueChart.config.type = type;
        revenueChart.update();
    }
}
</script>
