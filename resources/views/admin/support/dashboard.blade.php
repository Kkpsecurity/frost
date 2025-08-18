@extends('adminlte::page')

@section('title', 'Support Dashboard - Frost')

@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book"></i> Documentation Center
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> New Article
                        </button>
                        <button type="button" class="btn btn-info btn-sm ml-1">
                            <i class="fas fa-cogs"></i> Manage
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- React Component Container -->
                    <div id="support-dashboard-container">
                        <div class="text-center p-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2">Loading Documentation Center...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    @vite('resources/css/admin.css')
    <style>
        .support-dashboard {
            background: transparent !important;
            min-height: 400px;
        }

        .support-dashboard .bg-gray-50 {
            background: transparent !important;
        }

        .support-dashboard .text-gray-800 {
            color: #495057 !important;
        }

        #support-dashboard-container {
            min-height: 500px;
        }
    </style>
@stop

@section('js')
    @vite(['resources/js/admin.ts'])
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Support Dashboard: DOM loaded, initializing...');
            
            // Wait for React components to load
            const checkAndRender = () => {
                if (window.renderSupportComponent) {
                    console.log('✅ Support: renderSupportComponent found, rendering...');
                    window.renderSupportComponent('SupportDashboard', 'support-dashboard-container');
                } else {
                    console.log('⏳ Support: Waiting for renderSupportComponent...');
                    setTimeout(checkAndRender, 100);
                }
            };

            // Start checking after a brief delay
            setTimeout(checkAndRender, 500);
        });
    </script>
@stop
