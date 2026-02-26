{{-- Newsletter/CTA Section --}}
<div class="blog-newsletter frost-primary-bg py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-7">
                <div class="newsletter-content">
                    <h3 class="text-white mb-2">{{ __('frontend.blog.newsletter_heading') }}</h3>
                    <p class="text-white-50 mb-0">{{ __('frontend.blog.newsletter_text') }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-5">
                <div class="newsletter-action text-end">
                    <a href="{{ route('pages', 'contact') }}" class="btn btn-accent btn-lg">
                        <i class="fas fa-envelope me-2"></i>{{ __('frontend.blog.contact_us') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
