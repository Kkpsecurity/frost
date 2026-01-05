
@extends('adminlte::page')

@section('title', $content['page_title'] ?? 'Admin Dashboard')

@section('content_header')
    @if(isset($content['breadcrumbs']))
        <x-admin.partials.titlebar :title="$content['page_title'] ?? 'Admin'" :breadcrumbs="$content['breadcrumbs']" />
    @else
        <x-admin.partials.titlebar title="Admin" :breadcrumbs="[['title' => 'Admin', 'url' => url('admin')], ['title' => 'Dashboard']]" />
    @endif
@endsection

@section('content')
    {{-- Load dashboard widgets/components. These components live under resources/views/components/admin --}}

    @if(isset($content['widgets']))
        {{-- Enhanced Dashboard with Charts and Metrics --}}
        @if(view()->exists('components.admin.dashboard.enhanced-stats'))
            <x-admin.dashboard.enhanced-stats :widgets="$content['widgets']" />
        @endif

        {{-- Legacy User Stats Component (fallback) --}}
        @if(!view()->exists('components.admin.dashboard.enhanced-stats') && view()->exists('components.admin.dashboard.user-stats'))
            <x-admin.dashboard.user-stats :widgets="$content['widgets']" />
        @endif
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Welcome to the Admin Dashboard</h4>
                        <p>No widgets configured. Please check the dashboard service configuration.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('css')
    {{-- Chart.js --}}
    <style>
        .small-box .icon {
            font-size: 70px;
        }
        .info-box-icon {
            font-size: 3rem;
        }
        .progress-group {
            padding: 10px 0;
        }
        .progress-text {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .progress-number {
            font-size: 1.25rem;
        }
    </style>
@endsection

@section('js')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    {{-- Custom admin js --}}
    <script>
        $(document).ready(function() {
            console.log('🏠 ADMIN DASHBOARD LOADED');
            console.log('📊 Charts initialized');

            // Refresh data every 5 minutes
            setInterval(function() {
                console.log('🔄 Refreshing dashboard data...');
                location.reload();
            }, 300000); // 5 minutes
        });
    </script>
@endsection
