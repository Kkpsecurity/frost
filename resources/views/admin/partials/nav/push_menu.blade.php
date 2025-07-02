<ul class="navbar-nav">

    <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>

    <?php $segments = []; ?>
    @foreach (Request()->segments() as $key => $segment)
        @if (!is_numeric($segment))
            <li class="nav-item"><a class="nav-link"
                    href="{{ route(Request()->segment(1), array_push($segments, $segment)) }}">{{ ucwords($segment) }}</a>
            </li>
        @endif
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
