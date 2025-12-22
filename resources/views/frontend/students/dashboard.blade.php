<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Student Dashboard'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Student Classroom' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'classroom,student' }}">
        <link rel="stylesheet" href="{{ asset('themes/frost/bultifore/css/account-dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('themes/frost/bultifore/css/table.css') }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    @php
        $course_auth_id = $course_auth_id ?? null;
    @endphp

    <main class="main-page-content frost-secondary-bg">
        <div class="container-fluid gap-0 p-0" style="min-height: 60vh;">
            {{-- DEBUG: Check if Lesson 1 data exists and its status --}}
            @php
                $lesson1Status = 'NOT_FOUND';
                if (isset($content['lessons'][2]['lessons'])) {
                    foreach ($content['lessons'][2]['lessons'] as $lesson) {
                        if ($lesson['id'] == 1) {
                            $lesson1Status = $lesson['status'] ?? 'NO_STATUS';
                            break;
                        }
                    }
                }
            @endphp
            <div style="position: fixed; top: 0; left: 0; background: black; color: lime; padding: 5px; z-index: 99999; font-size: 11px;">
                🐛 DEBUG: Page generated {{ now()->format('H:i:s') }} | Lesson 1 status: <strong>{{ $lesson1Status }}</strong>
            </div>

            {{-- React App Mount Point --}}
            <div id="student-dashboard-container" data-content="{{ json_encode($content) }}"
                data-course-auth-id="{{ $course_auth_id }}" class="w-100">
                {{-- React Component will be mounted here --}}
            </div>

            {{-- Props Data --}}
            <script id="student-props" type="application/json">
                {!! json_encode([
                    'student' => isset($content['student']) ? $content['student'] : null,
                    'course_auths' => isset($content['course_auths']) ? $content['course_auths'] : [],
                    'course_auth_id' => $course_auth_id ?? null,
                    'selected_course_auth_id' => $content['selected_course_auth_id'] ?? $course_auth_id ?? null,
                    'lessons' => isset($content['lessons']) ? $content['lessons'] : [],
                    'has_lessons' => isset($content['has_lessons']) ? $content['has_lessons'] : false,
                    'validations' => isset($content['validations']) ? $content['validations'] : null,
                    'student_attendance' => isset($content['student_units'][0]) ? $content['student_units'][0] : null,
                    'student_units' => isset($content['student_units']) ? $content['student_units'] : []
                ]) !!}
            </script>

            {{-- Class Props - Include real classroom data if available --}}
            @php
            // DEBUG: Log what we're outputting to the DOM
            if (isset($content['classroom']['instructor'])) {
                \Log::info('Blade template: Outputting instructor to DOM', [
                    'has_instructor' => true,
                    'instructor_id' => $content['classroom']['instructor']['id'] ?? null,
                    'instructor_email' => $content['classroom']['instructor']['email'] ?? null,
                ]);
            } else {
                \Log::warning('Blade template: NO instructor data to output');
            }
            @endphp
            <div id="class-props" class-dashboard-data='{!! json_encode([
                'instructor' => isset($content['classroom']['instructor']) ? $content['classroom']['instructor'] : null,
                'course_dates' => isset($content['classroom']['course_dates']) ? $content['classroom']['course_dates'] : [],
                'inst_unit' => isset($content['classroom']['inst_unit']) ? $content['classroom']['inst_unit'] : null,
            ]) !!}'></div>

        </div>
    </main>



    <x-frontend.site.partials.footer />

    <x-slot:scripts>
        {{-- Zoom Meeting SDK --}}
        <link type="text/css" rel="stylesheet" href="https://source.zoom.us/3.9.0/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="https://source.zoom.us/3.9.0/css/react-select.css" />
        <script src="https://source.zoom.us/3.9.0/lib/vendor/react.min.js"></script>
        <script src="https://source.zoom.us/3.9.0/lib/vendor/react-dom.min.js"></script>
        <script src="https://source.zoom.us/3.9.0/lib/vendor/redux.min.js"></script>
        <script src="https://source.zoom.us/3.9.0/lib/vendor/redux-thunk.min.js"></script>
        <script src="https://source.zoom.us/3.9.0/lib/vendor/lodash.min.js"></script>
        <script src="https://source.zoom.us/zoom-meeting-3.9.0.min.js"></script>

        @vite(['resources/js/React/Student/app.tsx'])
    </x-slot:scripts>
</x-frontend.site.site-wrapper>
