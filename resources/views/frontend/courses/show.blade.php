{{-- Course Detail Page - Universal for all course types --}}
<x-frontend.site.site-wrapper :title="($course['title'] ?? 'Course Details') . ' | KKP Security Training'">
    <x-slot:head>
        <meta name="description" content="{{ $course['description'] ?? 'Professional security training course details and enrollment information' }}">
        <meta name="keywords" content="{{ is_array($course['keywords'] ?? null) ? implode(', ', $course['keywords']) : ($course['keywords'] ?? 'security training, professional certification, course details') }}">
        <link rel="stylesheet" href="{{ asset('css/components/course-details.css') }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <x-frontend.panels.courses.show :course="$course" />
    </div>

    <x-frontend.site.partials.footer />

</x-frontend.site.site-wrapper>
