<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Preloader Script -->
<script>
    window.addEventListener('load', function() {
        const preloader = document.getElementById('preloaders');
        if (preloader) {
            preloader.style.display = 'none';
        }
    });
</script>

<!-- Scroll to Top Button -->
<script>
    window.addEventListener('scroll', function() {
        const scrollBtn = document.querySelector('.scrollUp');
        if (scrollBtn) {
            if (window.pageYOffset > 300) {
                scrollBtn.style.display = 'block';
            } else {
                scrollBtn.style.display = 'none';
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const scrollBtn = document.querySelector('.scrollUp');
        if (scrollBtn) {
            scrollBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    });
</script>

@stack('scripts')
