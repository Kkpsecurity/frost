<x-site.site-wrapper :title="$content['title'] ?? 'Student Dashboard'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Student Classroom' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'classroom,student' }}">
    </x-slot:head>

    <x-site.partials.header />

    <main class="main-page-content">
        <div class="container my-4">
            <div id="student-dashboard-container" style="min-height:60vh"></div>
            @if(isset($course_auth_id))
                <div id="props" data-course-auth-id="{{ $course_auth_id }}" style="display:none"></div>
            @endif
        </div>
    </main>

    <x-site.partials.footer />

    @push('scripts')
        @vite(['resources/js/student.ts'])
    @endpush
</x-site.site-wrapper>
