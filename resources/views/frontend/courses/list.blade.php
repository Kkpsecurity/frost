{{-- Courses List Page --}}
<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Course List'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Browse and purchase our professional security training courses' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security courses, buy courses, training programs, certification courses' }}">
        <link rel="stylesheet" href="{{ asset('css/components/courses.css') }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <x-frontend.panels.courses.list :courses="$courses" />
    </div>

    <x-frontend.site.partials.footer />

</x-frontend.site.site-wrapper>
