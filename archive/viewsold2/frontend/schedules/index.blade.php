{{-- Course Schedules Page --}}
<x-site.layout :title="$content['title'] ?? 'Course Schedules'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'View available course schedules' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'course schedules, security training' }}">
    </x-slot:head>

    <x-site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <x-panels.schedules />
    </div>

    <x-site.partials.footer />
</x-site.layout>
