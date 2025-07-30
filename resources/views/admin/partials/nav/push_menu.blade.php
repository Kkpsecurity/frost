<ul class="navbar-nav">

    <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>

    {{-- Breadcrumb navigation --}}
    @php
        $segments = Request()->segments();
        $breadcrumbs = [];

        // Build safe breadcrumb links
        if (count($segments) > 0) {
            // First segment is always 'admin'
            if ($segments[0] === 'admin') {
                $breadcrumbs[] = [
                    'name' => 'Dashboard',
                    'url' => route('admin.dashboard')
                ];

                // Handle second segment
                if (count($segments) > 1) {
                    $secondSegment = $segments[1];

                    // Map known routes
                    $routeMap = [
                        'settings' => ['name' => 'Settings', 'route' => 'admin.settings.index'],
                        'admin-center' => ['name' => 'Admin Center', 'route' => 'admin.admin-center.admin-users.index'],
                        // Add more mappings as needed
                    ];

                    if (isset($routeMap[$secondSegment])) {
                        try {
                            $breadcrumbs[] = [
                                'name' => $routeMap[$secondSegment]['name'],
                                'url' => route($routeMap[$secondSegment]['route'])
                            ];
                        } catch (Exception $e) {
                            // Route doesn't exist, just show text
                            $breadcrumbs[] = [
                                'name' => ucwords(str_replace('-', ' ', $secondSegment)),
                                'url' => null
                            ];
                        }
                    } else {
                        // Unknown segment, just show as text
                        $breadcrumbs[] = [
                            'name' => ucwords(str_replace('-', ' ', $secondSegment)),
                            'url' => null
                        ];
                    }
                }
            }
        }
    @endphp

    @foreach ($breadcrumbs as $breadcrumb)
        <li class="nav-item">
            @if ($breadcrumb['url'])
                <a class="nav-link" href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
            @else
                <span class="nav-link text-muted">{{ $breadcrumb['name'] }}</span>
            @endif
        </li>
    @endforeach
</ul>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Function to check the sidebar state and set it accordingly
        function setSidebarStateFromLocalStorage() {
            const sidebarState = localStorage.getItem('sidebarState');
            if (sidebarState === 'closed') {
                document.body.classList.add('sidebar-collapse');
            } else if (sidebarState === 'open') {
                document.body.classList.remove('sidebar-collapse');
            }
        }

        // Initial setting of the sidebar state from localStorage
        setSidebarStateFromLocalStorage();

        // Listen to sidebar toggle button click
        const pushmenuBtn = document.querySelector('[data-widget="pushmenu"]');
        pushmenuBtn.addEventListener('click', function() {
            setTimeout(function() { // Timeout ensures we get the updated class state
                if (document.body.classList.contains('sidebar-collapse')) {
                    localStorage.setItem('sidebarState', 'closed');
                } else {
                    localStorage.setItem('sidebarState', 'open');
                }
            }, 50); // Giving a little delay to make sure the sidebar animation completes
        });

    });
</script>
