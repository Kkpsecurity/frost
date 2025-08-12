@extends('adminlte::page')

@section('title', 'Support Dashboard - Frost')

@section('content_header')
    @include('admin.partials.impersonation-banner')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Support Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item active">Support Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- React Component Container -->
        <div id="support-dashboard-container"></div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .content-header h1 {
            color: #495057;
            font-weight: 600;
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
            // Wait for React components to load
            setTimeout(() => {
                if (window.renderSupportComponent) {
                    // Render the Support Dashboard React component
                    window.renderSupportComponent('SupportDashboard', 'support-dashboard-container');
                    console.log('Support Dashboard React component loaded!');
                } else {
                    console.error('renderSupportComponent not found. Make sure admin.ts is loaded.');
                }
            }, 500);
        });
    </script>
@stop
