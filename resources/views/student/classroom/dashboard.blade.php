@extends('layouts.frontend')

@section('title', 'My Classroom - Frost')

@section('content')
    <div class="main-content">
        <!-- React Component Container -->
        <div id="student-dashboard-container"></div>
    </div>
@stop

@section('css')
    <style>
        .main-content {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        #student-dashboard-container {
            min-height: 500px;
        }
    </style>
@stop

@section('js')
    @vite(['resources/js/app.ts'])
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for React components to load
            setTimeout(() => {
                if (window.StudentComponents && window.StudentComponents.StudentDashboard) {
                    // Render the Student Dashboard React component
                    const container = document.getElementById('student-dashboard-container');
                    if (container) {
                        const root = ReactDOM.createRoot(container);
                        root.render(React.createElement(window.StudentComponents.StudentDashboard));
                        console.log('Student Dashboard React component loaded!');
                    }
                } else {
                    console.error('StudentComponents not found. Make sure app.ts is loaded.');
                }
            }, 500);
        });
    </script>
@stop
