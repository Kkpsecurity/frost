@extends('adminlte::page')

@section('title', 'Payment Methods')

@section('content_header')
    <h1>Payment Methods</h1>
@stop

@section('content')
    <!-- Stats Overview -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_methods'] }}</h3>
                    <p>Active Methods</p>
                </div>
                <div class="icon">
                    <i class="fas fa-credit-card"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($stats['total_orders']) }}</h3>
                    <p>Total Transactions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>${{ number_format($stats['total_revenue'], 2) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Cards -->
    <div class="row">
        @foreach($paymentMethods as $method)
        <div class="col-lg-3 col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-{{ $method->id == 1 ? 'money-bill-wave' : ($method->id == 2 ? 'credit-card' : ($method->id == 3 ? 'university' : 'wallet')) }}"></i>
                        {{ $method->name }}
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-7">Total Transactions:</dt>
                        <dd class="col-sm-5">
                            <span class="badge badge-info">{{ number_format($method->count) }}</span>
                        </dd>

                        <dt class="col-sm-7">Total Revenue:</dt>
                        <dd class="col-sm-5">
                            <strong>${{ number_format($method->total, 2) }}</strong>
                        </dd>

                        <dt class="col-sm-7">Average Amount:</dt>
                        <dd class="col-sm-5">
                            ${{ number_format($method->average, 2) }}
                        </dd>

                        <dt class="col-sm-7">Revenue Share:</dt>
                        <dd class="col-sm-5">
                            @php
                                $percentage = $stats['total_revenue'] > 0 ? ($method->total / $stats['total_revenue']) * 100 : 0;
                            @endphp
                            <span class="badge badge-success">{{ number_format($percentage, 1) }}%</span>
                        </dd>
                    </dl>

                    <div class="progress mt-3" style="height: 20px;">
                        <div class="progress-bar bg-{{ $method->id == 1 ? 'warning' : ($method->id == 2 ? 'info' : ($method->id == 3 ? 'success' : 'primary')) }}"
                             role="progressbar"
                             style="width: {{ number_format($percentage, 1) }}%"
                             aria-valuenow="{{ $percentage }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ number_format($percentage, 1) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Payment Methods Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table"></i> Payment Methods Overview
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-success" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Payment Method</th>
                            <th>Total Transactions</th>
                            <th>Total Revenue</th>
                            <th>Average Transaction</th>
                            <th>Revenue Share</th>
                            <th>Chart</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentMethods as $method)
                        @php
                            $percentage = $stats['total_revenue'] > 0 ? ($method->total / $stats['total_revenue']) * 100 : 0;
                        @endphp
                        <tr>
                            <td>
                                <i class="fas fa-{{ $method->id == 1 ? 'money-bill-wave' : ($method->id == 2 ? 'credit-card' : ($method->id == 3 ? 'university' : 'wallet')) }}"></i>
                                <strong>{{ $method->name }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ number_format($method->count) }}</span>
                            </td>
                            <td>
                                <strong>${{ number_format($method->total, 2) }}</strong>
                            </td>
                            <td>
                                ${{ number_format($method->average, 2) }}
                            </td>
                            <td>
                                <span class="badge badge-success">{{ number_format($percentage, 1) }}%</span>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $method->id == 1 ? 'warning' : ($method->id == 2 ? 'info' : ($method->id == 3 ? 'success' : 'primary')) }}"
                                         role="progressbar"
                                         style="width: {{ number_format($percentage, 1) }}%">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No payment methods found</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td>TOTALS</td>
                            <td>
                                <span class="badge badge-primary">{{ number_format($paymentMethods->sum('count')) }}</span>
                            </td>
                            <td>
                                <strong>${{ number_format($paymentMethods->sum('total'), 2) }}</strong>
                            </td>
                            <td>
                                ${{ number_format($paymentMethods->avg('average'), 2) }}
                            </td>
                            <td>
                                <span class="badge badge-success">100%</span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Methods Chart -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Revenue by Payment Method
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Transactions by Payment Method
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="transactionsChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- All Payment Types (Active & Inactive) -->
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> All Payment Types (System Configuration)
            </h3>
        </div>
        <div class="card-body">
            @php
                $allPaymentTypes = \App\Models\PaymentType::all();
            @endphp
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Model Class</th>
                            <th>Controller Class</th>
                            <th>Usage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allPaymentTypes as $type)
                        <tr>
                            <td>{{ $type->id }}</td>
                            <td>{{ $type->name }}</td>
                            <td>
                                @if($type->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <code>{{ $type->model_class ?: 'N/A' }}</code>
                            </td>
                            <td>
                                <code>{{ $type->controller_class ?: 'N/A' }}</code>
                            </td>
                            <td>
                                @php
                                    $usage = \App\Models\Order::where('payment_type_id', $type->id)->count();
                                @endphp
                                <span class="badge badge-info">{{ number_format($usage) }} orders</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'pie',
        data: {
            labels: [
                @foreach($paymentMethods as $method)
                    '{{ $method->name }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($paymentMethods as $method)
                        {{ $method->total }},
                    @endforeach
                ],
                backgroundColor: [
                    '#ffc107',
                    '#17a2b8',
                    '#28a745',
                    '#007bff',
                    '#6c757d',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += '$' + context.parsed.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Transactions Chart
    const transactionsCtx = document.getElementById('transactionsChart').getContext('2d');
    const transactionsChart = new Chart(transactionsCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($paymentMethods as $method)
                    '{{ $method->name }}',
                @endforeach
            ],
            datasets: [{
                label: 'Transactions',
                data: [
                    @foreach($paymentMethods as $method)
                        {{ $method->count }},
                    @endforeach
                ],
                backgroundColor: '#17a2b8'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@stop

@section('css')
<style>
    @media print {
        .card-header .card-tools,
        .btn-group,
        .small-box,
        canvas {
            display: none !important;
        }
    }
</style>
@stop
