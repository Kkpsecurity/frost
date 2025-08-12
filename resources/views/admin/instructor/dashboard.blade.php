@extends('adminlte::page')

@section('title', 'Instructor Dashboard - Frost')

@section('content_header')
    @include('admin.partials.impersonation-banner')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Instructor Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item active">Instructor Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- React Component Container -->
        <div id="instructor-dashboard-container"></div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .content-header h1 {
            color: #495057;
            font-weight: 600;
        }

        #instructor-dashboard-container {
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
                if (window.InstructorComponents && window.InstructorComponents.InstructorDashboard) {
                    // Render the Instructor Dashboard React component
                    const container = document.getElementById('instructor-dashboard-container');
                    if (container) {
                        const root = ReactDOM.createRoot(container);
                        root.render(React.createElement(window.InstructorComponents.InstructorDashboard));
                        console.log('Instructor Dashboard React component loaded!');
                    }
                } else {
                    console.error('InstructorComponents not found. Make sure admin.ts is loaded.');
                }
            }, 500);
        });
    </script>
@stop
