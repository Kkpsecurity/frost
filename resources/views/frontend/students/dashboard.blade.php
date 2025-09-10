<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Student Dashboard'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Student Classroom' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'classroom,student' }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    @php
        $course_auth_id = $course_auth_id ?? null;
    @endphp

    <main class="main-page-content">
        <div class="container-fluid gap-0 p-0" style="min-height: 60vh;">
            {{-- Initial Loading State --}}
            <div id="student-dashboard-loading" class="d-flex justify-content-center align-items-center" style="min-height:60vh">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading dashboard...</span>
                </div>
            </div>

            {{-- Error State (Hidden by default) --}}
            <div id="student-dashboard-error" class="alert alert-danger m-4" role="alert" style="display:none">
                An error occurred while loading the dashboard.
            </div>

            {{-- React App Mount Point --}}
            <div id="student-dashboard-container" style="min-height:60vh;display:none"></div>

            {{-- Props Data --}}
            <div
                id="props"
                data-course-auth-id="{{ $course_auth_id }}"
                data-dashboard-data="{{ json_encode([
                    'user' => Auth::user()->only(['id', 'fname', 'lname', 'email']),
                    'incompleteAuths' => $content['incompleteAuths'] ?? [],
                    'completedAuths' => $content['completedAuths'] ?? [],
                    'mergedAuths' => $content['MergedCourseAuths'] ?? [],
                    'stats' => $content['stats'] ?? [
                        'total_courses' => 0,
                        'active_courses' => 0,
                        'completed_courses' => 0,
                        'overall_progress' => 0
                    ]
                ]) }}"
                style="display:none"
            ></div>
        </div>
    </main>

    @once
        @push('scripts')
            <script>
                // Hide loading and show error message
                function showDashboardError() {
                    document.getElementById('student-dashboard-loading').style.display = 'none';
                    document.getElementById('student-dashboard-error').style.display = 'block';
                }

                // Hide loading and show dashboard
                function showDashboard() {
                    document.getElementById('student-dashboard-loading').style.display = 'none';
                    document.getElementById('student-dashboard-container').style.display = 'block';
                }

                // Handle React load error
                window.addEventListener('error', function(e) {
                    if (e.message.includes("cannot read property 'name'") || e.message.includes("can't access property 'name'")) {
                        showDashboardError();
                        console.error('Dashboard React error:', e);
                    }
                });

                // Show dashboard when React is loaded
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(showDashboard, 500); // Small delay to ensure React is mounted
                });
            </script>
        @endpush
    @endonce

    <x-frontend.site.partials.footer />

    <x-slot:scripts>
        @vite(['resources/js/student.ts'])
    </x-slot:scripts>
</x-frontend.site.site-wrapper>
