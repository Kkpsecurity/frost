{{-- Course Schedules Page --}}
<x-site.layout :title="$content['title'] ?? 'Course Schedules'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'View available course schedules' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'course schedules, security training' }}">
    </x-slot:head>

    <x-site.partials.header />

    <main class="main-page-content">
        <x-panels.schedules />
        <x-panels.course-status />
    </main>

    <x-site.partials.footer />
</x-site.layout>
