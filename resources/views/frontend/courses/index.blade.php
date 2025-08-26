{{-- Courses Listing Page --}}
<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Our Courses'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Professional security training courses' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security, training, certification' }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <x-frontend.panels.courses.schedules />
        <x-frontend.panels.courses.course-status />
    </div>

    <x-frontend.site.partials.footer />

</x-frontend.site.site-wrapper>
