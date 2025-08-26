{{-- Course Enrollment Page --}}
<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Course Enrollment'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Complete your enrollment for professional security training courses' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'course enrollment, security training registration, professional development' }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <x-frontend.panels.courses.enroll :course="$course" />
    </div>

    <x-frontend.site.partials.footer />

</x-frontend.site.site-wrapper>
