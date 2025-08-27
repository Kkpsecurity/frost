<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Student Dashboard'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Student Classroom' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'classroom,student' }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    @php 
        $course_auth_id = $course_auth_id ?? null;
    @endphp

    <main class="main-page-content">
        <div class="container my-4">
            <div id="student-dashboard-container" style="min-height:60vh"></div>
            <div id="props" data-course-auth-id="{{ $course_auth_id }}" style="display:none"></div>            
        </div>
    </main>

    <x-frontend.site.partials.footer />

    <x-slot:scripts>
        @vite(['resources/js/student.ts'])
    </x-slot:scripts>
</x-frontend.site.site-wrapper>