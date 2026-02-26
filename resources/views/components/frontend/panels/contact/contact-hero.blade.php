{{-- Contact Hero Section --}}
<section class="contact-hero py-3 frost-secondary-bg mb-0 pb-0">
    <div class="hero-background">
        <div class="container text-center">
            <div class="hero-badge mb-3">
                <span class="badge px-3 py-2">
                    <i class="fas fa-phone me-2"></i>{{ __('frontend.contact.badge') }}
                </span>
            </div>
            <h1 class="text-white mb-3">{{ __('frontend.contact.heading') }} <span
                    class="text-highlight">{{ setting('company_name', 'Our Expert Team') }}</span></h1>
            <p class="lead text-white-75 mb-4">{{ __('frontend.contact.subtext') }}</p>

            <div class="hero-stats row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">{{ setting('support_phone_hours', '24/7') }}</div>
                        <div class="stat-label">{{ __('frontend.contact.stat_1_label') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">{{ __('frontend.contact.stat_2_number') }}</div>
                        <div class="stat-label">{{ __('frontend.contact.stat_2_label') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">{{ __('frontend.contact.stat_3_number') }}</div>
                        <div class="stat-label">{{ __('frontend.contact.stat_3_label') }}</div>
                    </div>
                </div>
            </div>

            <a href="#contact-form" class="btn btn-outline-light btn-lg">
                <i class="fas fa-envelope me-2"></i>{{ __('frontend.contact.send_message') }}
            </a>
        </div>
    </div>
</section>
