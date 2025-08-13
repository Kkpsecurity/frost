@extends('adminlte::page')

@section('title', 'Instructor Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- React Component Container -->
        <div id="instructor-dashboard-container"></div>
    </div>
@stop

@section('js')
    @vite(['resources/js/instructor.ts'])
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add a delay to ensure Vite loads the modules
            setTimeout(function() {
                // The instructor.ts file handles component loading automatically based on route
                // No need to manually call renderInstructorComponent
            }, 500);
        });
    </script>
@stop
