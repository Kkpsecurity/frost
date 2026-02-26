{{-- Training Certificate Search Panel --}}
<section class="cert-search-page py-5"
    style="background: linear-gradient(135deg, var(--frost-primary-color) 0%, var(--frost-secondary-color) 100%); min-height: 100vh;">
    <div class="container-fluid px-4">

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <i class="fas fa-search-plus fa-2x" style="color: var(--frost-accent-color-1);"></i>
                    <div>
                        <h2 class="text-white mb-0 fw-bold">{{ __('frontend.nav.certificate_search') }}</h2>
                        <p class="text-white-50 mb-0 small">
                            {{ __('frontend.cert_search.subtitle', ['default' => 'Florida Department of Agriculture and Consumer Services — Online Training Registry']) }}
                        </p>
                    </div>
                </div>
                <hr style="border-color: rgba(255,255,255,0.15);">
            </div>
        </div>

        {{-- Notice bar --}}
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 rounded-3"
                    style="background: rgba(0,0,0,0.35); border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(8px);">
                    <div class="d-flex align-items-center gap-2 text-white-50 small">
                        <i class="fas fa-info-circle" style="color: var(--frost-accent-color-1);"></i>
                        <span>{{ __('frontend.cert_search.notice', ['default' => 'This page loads the official FDACS certificate lookup tool. If the frame does not display, use the button to open it directly.']) }}</span>
                    </div>
                    <a href="https://licensing.fdacs.gov/OTR/" target="_blank" rel="noopener noreferrer"
                        class="btn btn-sm fw-semibold"
                        style="background: var(--frost-accent-color-1); color: #fff; border: none; white-space: nowrap;">
                        <i class="fas fa-external-link-alt me-1"></i>
                        {{ __('frontend.cert_search.open_in_tab', ['default' => 'Open in New Tab']) }}
                    </a>
                </div>
            </div>
        </div>

        {{-- iFrame container --}}
        <div class="row">
            <div class="col-12">
                <div id="cert-iframe-wrap" class="rounded-3 overflow-hidden position-relative"
                    style="border: 1px solid rgba(255,255,255,0.12); background: #fff; min-height: 700px;">

                    {{-- Blocked / loading fallback (shown while iframe loads or if blocked) --}}
                    <div id="cert-iframe-fallback"
                        class="d-flex flex-column align-items-center justify-content-center text-center p-5"
                        style="min-height: 700px; background: rgba(0,0,0,0.5); position: absolute; inset: 0; z-index: 5; display: none !important;">
                        <i class="fas fa-shield-alt fa-3x mb-3" style="color: var(--frost-accent-color-1);"></i>
                        <h5 class="text-white mb-2">
                            {{ __('frontend.cert_search.blocked_title', ['default' => 'Certificate Lookup']) }}</h5>
                        <p class="text-white-50 mb-4 small" style="max-width: 420px;">
                            {{ __('frontend.cert_search.blocked_msg', ['default' => 'The FDACS certificate search tool must be opened in a new tab. Click the button below to proceed.']) }}
                        </p>
                        <a href="https://licensing.fdacs.gov/OTR/" target="_blank" rel="noopener noreferrer"
                            class="btn fw-semibold px-4 py-2"
                            style="background: var(--frost-accent-color-1); color: #fff; border: none;">
                            <i class="fas fa-external-link-alt me-2"></i>
                            {{ __('frontend.cert_search.open_fdacs', ['default' => 'Open FDACS Certificate Search']) }}
                        </a>
                    </div>

                    {{-- The iFrame --}}
                    <iframe id="cert-iframe" src="https://licensing.fdacs.gov/OTR/"
                        title="{{ __('frontend.cert_search.iframe_title', ['default' => 'FDACS Training Certificate Search']) }}"
                        width="100%" style="min-height: 700px; height: 80vh; border: none; display: block;"
                        allowfullscreen loading="lazy"
                        sandbox="allow-forms allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox">
                    </iframe>
                </div>
            </div>
        </div>

    </div>
</section>

@push('scripts')
    <script>
        (function() {
            var iframe = document.getElementById('cert-iframe');
            var fallback = document.getElementById('cert-iframe-fallback');

            // Show fallback if iframe fails to load within 8 seconds
            var timer = setTimeout(function() {
                showFallback();
            }, 8000);

            iframe.addEventListener('load', function() {
                clearTimeout(timer);
                // Try to detect a blocked frame by checking contentDocument access
                try {
                    // If X-Frame-Options blocks, this throws or is null
                    var doc = iframe.contentDocument || iframe.contentWindow.document;
                    if (!doc || !doc.body) {
                        showFallback();
                    }
                } catch (e) {
                    showFallback();
                }
            });

            iframe.addEventListener('error', function() {
                clearTimeout(timer);
                showFallback();
            });

            function showFallback() {
                if (iframe) iframe.style.display = 'none';
                if (fallback) fallback.style.display = 'flex !important';
                // force visibility via class swap
                if (fallback) {
                    fallback.style.cssText =
                        'display: flex !important; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 3rem; min-height: 700px; background: rgba(0,0,0,0.5); position: absolute; inset: 0; z-index: 5;';
                }
            }
        })();
    </script>
@endpush
