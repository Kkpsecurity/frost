{{-- Course Schedules Page --}}
<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Course Schedules'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'View available course schedules for armed and unarmed security training' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'course schedules, security training dates, Class D schedule, Class G schedule' }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <x-frontend.panels.courses.schedules />
        <x-frontend.panels.courses.course-status />
    </div>

    <x-frontend.site.partials.footer />

</x-frontend.site.site-wrapper>
