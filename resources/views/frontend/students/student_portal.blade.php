@extends('layouts.app')

@section('content')
    @php
        // Controller supplies $content and optionally $course_auth_id
        $pageTitle = $content['pageTitle'] ?? ($content['meta_title'] ?? 'Student Classroom');
    @endphp

    @section('page-title')
        {{ $pageTitle }}
    @endsection

    <div class="container my-4">
        <div id="student-dashboard-container" style="min-height:60vh"></div>
        @if(isset($course_auth_id))
            <div id="props" data-course-auth-id="{{ $course_auth_id }}" style="display:none"></div>
        @endif
    </div>
@endsection

@section('scripts')
    {{-- Load the student Vite entry which dynamically loads the React student app --}}
    @vite(['resources/js/student.ts'])
@endsection
