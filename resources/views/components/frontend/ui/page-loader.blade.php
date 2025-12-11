{{-- Professional Page Loader --}}
<div id="page-loader" class="page-loader">
    <div class="loader-container">
        <div class="loader-spinner">
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
        </div>
        <div class="loader-text">
            <h3>Loading...</h3>
            <p>Please wait while we prepare your content</p>
        </div>
    </div>
</div>

<style>
/* Page Loader Styles */
.page-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease-out;
}

.page-loader.fade-out {
    opacity: 0;
    pointer-events: none;
}

.loader-container {
    text-align: center;
    color: white;
}

.loader-spinner {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto 30px;
}

.spinner-ring {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 4px solid transparent;
    border-top: 4px solid rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    animation: spin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
}

.spinner-ring:nth-child(1) { animation-delay: -0.45s; }
.spinner-ring:nth-child(2) { animation-delay: -0.3s; }
.spinner-ring:nth-child(3) { animation-delay: -0.15s; }

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loader-text h3 {
    font-size: 24px;
    font-weight: 600;
    margin: 0 0 10px;
    opacity: 0.9;
}

.loader-text p {
    font-size: 14px;
    margin: 0;
    opacity: 0.7;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hide loader after page load
    window.addEventListener('load', function() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            setTimeout(() => {
                loader.classList.add('fade-out');
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            }, 300); // Small delay for better UX
        }
    });

    // Fallback to hide loader if page takes too long
    setTimeout(() => {
        const loader = document.getElementById('page-loader');
        if (loader && !loader.classList.contains('fade-out')) {
            loader.classList.add('fade-out');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }
    }, 5000); // Hide after 5 seconds maximum
});
</script>
