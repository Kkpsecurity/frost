{{-- Contact Hero Section --}}
<section class="contact-hero py-3">
    <div class="hero-background">
        <div class="container text-center">
            <div class="hero-badge mb-3">
                <span class="badge px-3 py-2">
                    <i class="fas fa-phone me-2"></i>Contact Us
                </span>
            </div>
            <h1 class="text-white mb-3">Get in Touch with <span class="text-highlight">{{ setting('company_name', 'Our Expert Team') }}</span></h1>
            <p class="lead text-white-75 mb-4">Have questions about our security training programs? We're here to help you find the right path for your career goals.</p>

            <div class="hero-stats row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">{{ setting('support_phone_hours', '24/7') }}</div>
                        <div class="stat-label">Support Available</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">&lt;30min</div>
                        <div class="stat-label">Response Time</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">Expert</div>
                        <div class="stat-label">Guidance</div>
                    </div>
                </div>
            </div>

            <a href="#contact-form" class="btn btn-outline-light btn-lg">
                <i class="fas fa-envelope me-2"></i>Send Message
            </a>
        </div>
    </div>
</section>
