{{-- Newsletter Widget --}}
<div class="sidebar-widget newsletter-widget mb-4">
    <div class="widget-content">
        <div class="newsletter-box">
            <div class="newsletter-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <h5>{{ __('frontend.blog.stay_updated') }}</h5>
            <p>{{ __('frontend.blog.newsletter_hint') }}</p>
            <a href="{{ route('pages', 'contact') }}"
                class="btn btn-primary btn-sm">{{ __('frontend.blog.subscribe_now') }}</a>
        </div>
    </div>
</div>
