@extends('adminlte::page')

@section('title', 'Test Instructor Dashboard')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Test Instructor Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        <li class="breadcrumb-item active">Test Instructor Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- React Component Container -->
                <div id="instructor-dashboard-container">
                    <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .instructor-dashboard {
            background: transparent;
        }

        .instructor-dashboard .card {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            margin-bottom: 1rem;
        }

        .instructor-dashboard .btn {
            border-radius: 0.25rem;
        }

        .instructor-dashboard .badge {
            font-size: 0.75em;
        }

        /* Custom styles for our components */
        .classroom-interface {
            min-height: 400px;
        }

        .bulletin-board .card,
        .class-dashboard .card {
            border: 1px solid #dee2e6;
        }

        .course-card {
            transition: transform 0.2s ease-in-out;
        }

        .course-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,.1);
        }

        .announcement-item:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
        }

        .resource-item,
        .course-item {
            transition: background-color 0.2s ease;
        }

        .resource-item:hover,
        .course-item:hover {
            background-color: #f8f9fa;
        }
    </style>
@stop

@section('js')
    @vite(['resources/js/React/Instructor/app.tsx'])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let attempts = 0;
            const maxAttempts = 50; // 5 seconds max wait

            // Wait for React components to be available
            const waitForReact = () => {
                attempts++;

                if (window.renderInstructorComponent && window.InstructorComponents) {
                    console.log('✅ React components available:', Object.keys(window.InstructorComponents || {}));

                    // Check if ClassroomInterface is available
                    if (window.InstructorComponents.ClassroomInterface) {
                        console.log('✅ ClassroomInterface found, rendering...');

                        // Render the ClassroomInterface component which will handle the routing logic
                        window.renderInstructorComponent(
                            'ClassroomInterface',
                            'instructor-dashboard-container',
                            {
                                instructorId: null, // Use authenticated user
                                className: 'instructor-dashboard'
                            }
                        );
                    } else {
                        console.error('❌ ClassroomInterface not found in InstructorComponents');
                        console.log('Available components:', Object.keys(window.InstructorComponents));
                    }
                } else {
                    if (attempts >= maxAttempts) {
                        console.error('❌ Timeout waiting for React components');
                        document.getElementById('instructor-dashboard-container').innerHTML = `
                            <div class="alert alert-danger">
                                <h5>Failed to Load Components</h5>
                                <p>The instructor dashboard components failed to load. Please check the console for errors.</p>
                                <p><strong>Debug Info:</strong></p>
                                <ul>
                                    <li>renderInstructorComponent: ${typeof window.renderInstructorComponent}</li>
                                    <li>InstructorComponents: ${typeof window.InstructorComponents}</li>
                                    <li>Attempts: ${attempts}/${maxAttempts}</li>
                                </ul>
                            </div>
                        `;
                        return;
                    }

                    console.log(`⏳ Waiting for React components... (${attempts}/${maxAttempts})`);
                    // Wait a bit longer if React components aren't ready
                    setTimeout(waitForReact, 100);
                }
            };

            waitForReact();
        });
    </script>
@stop
