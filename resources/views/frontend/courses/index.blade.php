{{-- Courses Listing Page --}}
<x-site.site-wrapper :title="$content['title'] ?? 'Our Courses'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Professional security training courses' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security, training, certification' }}">
    </x-slot:head>

    <x-site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <x-frontend.panels.courses.schedules />
        <x-frontend.panels.courses.course-status />
    </div>

    <x-site.partials.footer />

</x-site.site-wrapper>
