{{-- FAQs Hero Section --}}
<section class="faqs-hero py-3 frost-secondary-bg mb-0 pb-0">
    <div class="hero-background">
        <div class="container text-center">
            <div class="hero-badge mb-3">
                <span class="badge px-3 py-2">
                    <i class="fas fa-question-circle me-2"></i>{{ __('frontend.faqs.badge') }}
                </span>
            </div>
            <h1 class="text-white mb-3">{{ __('frontend.faqs.heading') }} <span
                    class="text-highlight">{{ __('frontend.faqs.heading_accent') }}</span></h1>
            <p class="lead text-white-75 mb-4">{{ __('frontend.faqs.subtext') }}</p>

            <div class="hero-stats row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">{{ __('frontend.faqs.stat_1_number') }}</div>
                        <div class="stat-label">{{ __('frontend.faqs.stat_1_label') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">{{ __('frontend.faqs.stat_2_number') }}</div>
                        <div class="stat-label">{{ __('frontend.faqs.stat_2_label') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">{{ __('frontend.faqs.stat_3_number') }}</div>
                        <div class="stat-label">{{ __('frontend.faqs.stat_3_label') }}</div>
                    </div>
                </div>
            </div>

            <a href="#contact" class="btn btn-outline-light btn-lg">
                <i class="fas fa-phone me-2"></i>{{ __('frontend.faqs.contact_support') }}
            </a>
        </div>
    </div>
</section>
