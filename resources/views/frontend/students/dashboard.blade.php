<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Student Dashboard'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Student Classroom' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'classroom,student' }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    @php
        $course_auth_id = $course_auth_id ?? null;
    @endphp

    <main class="main-page-content frost-secondary-bg">
        <div class="container-fluid gap-0 p-0" style="min-height: 60vh; padding-top: 90px;">
            {{-- React App Mount Point --}}
            <div id="student-dashboard-container" style="min-height:60vh;"></div>

            {{-- Props Data --}}
            <script id="student-props" type="application/json">
                {!! json_encode([
                    'student' => isset($content['student']) ? $content['student'] : null,
                    'course_auths' => isset($content['course_auths']) ? $content['course_auths'] : [],
                    'course_auth_id' => $course_auth_id ?? null,
                    'lessons' => isset($content['lessons']) ? $content['lessons'] : [],
                    'has_lessons' => isset($content['has_lessons']) ? $content['has_lessons'] : false
                ]) !!}
            </script>

            {{-- Class Props (empty for student dashboard, but prevents console errors) --}}
            <div id="class-props" class-dashboard-data='{!! json_encode([
                "instructor" => null,
                "course_dates" => []
            ]) !!}'></div>

        </div>
    </main>



    <x-frontend.site.partials.footer />

    <x-slot:scripts>
        @vite(['resources/js/React/Student/app.tsx'])
    </x-slot:scripts>
</x-frontend.site.site-wrapper>
