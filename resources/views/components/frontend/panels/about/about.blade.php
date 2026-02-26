{{-- About Us Content Section --}}
<section class="about-content py-5"
    style="background: linear-gradient(135deg, var(--frost-primary-color) 0%, var(--frost-secondary-color) 100%); min-height: 100vh;">
    <div class="container">
        {{-- Hero Section --}}
        <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <div class="about-hero-content p-4 rounded-3"
                    style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
                    <h1 class="display-4 mb-4 text-white" style="font-weight: 700;">
                        {{ __('frontend.about.title') }} {{ setting('company_name', config('app.name')) }}
                    </h1>
                    <p class="lead mb-4 text-white-50">
                        {{ __('frontend.about.tagline') }}
                    </p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="stat-item p-3 rounded mb-3"
                                style="background: var(--frost-accent-color-1); color: white;">
                                <h3 class="mb-1">10,000+</h3>
                                <p class="mb-0">{{ __('frontend.about.students_trained') }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="stat-item p-3 rounded mb-3"
                                style="background: var(--frost-accent-color-2); color: white;">
                                <h3 class="mb-1">15+</h3>
                                <p class="mb-0">{{ __('frontend.about.years_experience') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-image-container p-4">
                    <img src="{{ asset('images/Security-Page-1.jpg') }}" alt="Security Training"
                        class="img-fluid rounded-3 shadow-lg" style="width: 100%; height: 400px; object-fit: cover;">
                </div>
            </div>
        </div>

        {{-- Mission & Vision Section --}}
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="mission-card h-100 p-4 rounded-3"
                    style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
                    <div class="card-icon mb-3 text-center">
                        <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                            style="width: 80px; height: 80px; background: var(--frost-accent-color-1);">
                            <i class="fas fa-bullseye fa-2x text-white"></i>
                        </div>
                    </div>
                    <h3 class="text-center mb-4 text-white">{{ __('frontend.about.our_mission') }}</h3>
                    <p class="text-white-50" style="line-height: 1.8;">
                        {{ __('frontend.about.mission_text') }}
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="vision-card h-100 p-4 rounded-3"
                    style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
                    <div class="card-icon mb-3 text-center">
                        <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                            style="width: 80px; height: 80px; background: var(--frost-accent-color-2);">
                            <i class="fas fa-eye fa-2x text-white"></i>
                        </div>
                    </div>
                    <h3 class="text-center mb-4 text-white">{{ __('frontend.about.our_vision') }}</h3>
                    <p class="text-white-50" style="line-height: 1.8;">
                        {{ __('frontend.about.vision_text') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Why Choose Us Section --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="why-choose-us p-5 rounded-3"
                    style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
                    <h2 class="text-center mb-5 text-white">
                        {{ __('frontend.about.why_choose', ['company' => setting('company_name')]) }}</h2>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="feature-item text-center p-3">
                                <div class="feature-icon mb-3">
                                    <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                                        style="width: 70px; height: 70px; background: var(--frost-accent-color-1);">
                                        <i class="fas fa-certificate fa-2x text-white"></i>
                                    </div>
                                </div>
                                <h4 class="text-white">{{ __('frontend.about.state_certified') }}</h4>
                                <p class="text-white-50">
                                    {{ __('frontend.about.state_certified_text') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-item text-center p-3">
                                <div class="feature-icon mb-3">
                                    <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                                        style="width: 70px; height: 70px; background: var(--frost-accent-color-2);">
                                        <i class="fas fa-users fa-2x text-white"></i>
                                    </div>
                                </div>
                                <h4 class="text-white">{{ __('frontend.about.expert_instructors') }}</h4>
                                <p class="text-white-50">
                                    {{ __('frontend.about.expert_instructors_text') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-item text-center p-3">
                                <div class="feature-icon mb-3">
                                    <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                                        style="width: 70px; height: 70px; background: var(--frost-accent-color-3);">
                                        <i class="fas fa-clock fa-2x text-white"></i>
                                    </div>
                                </div>
                                <h4 class="text-white">{{ __('frontend.about.flexible_scheduling') }}</h4>
                                <p class="text-white-50">
                                    {{ __('frontend.about.flexible_text') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Training Programs Overview --}}
        <div class="row mb-5">
            <div class="col-lg-8">
                <div class="programs-overview p-4 rounded-3"
                    style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 8px 32px rgba(0,0,0,0.3);">
                    <h2 class="mb-4 text-white">{{ __('frontend.about.our_programs') }}</h2>
                    <div class="programs-list">
                        <div class="program-item d-flex mb-3 p-3 rounded" style="background: rgba(0,0,0,0.2);">
                            <div class="program-icon me-3">
                                <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 50px; height: 50px; background: var(--frost-accent-color-1);">
                                    <i class="fas fa-shield-alt text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-white">{{ __('frontend.about.prog_officer') }}</h5>
                                <p class="mb-0 text-white-50">{{ __('frontend.about.prog_officer_text') }}</p>
                            </div>
                        </div>

                        <div class="program-item d-flex mb-3 p-3 rounded" style="background: rgba(0,0,0,0.2);">
                            <div class="program-icon me-3">
                                <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 50px; height: 50px; background: var(--frost-accent-color-2);">
                                    <i class="fas fa-crosshairs text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-white">{{ __('frontend.about.prog_firearms') }}</h5>
                                <p class="mb-0 text-white-50">{{ __('frontend.about.prog_firearms_text') }}</p>
                            </div>
                        </div>

                        <div class="program-item d-flex mb-3 p-3 rounded" style="background: rgba(0,0,0,0.2);">
                            <div class="program-icon me-3">
                                <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                                    style="width: 50px; height: 50px; background: var(--frost-accent-color-3);">
                                    <i class="fas fa-graduation-cap text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-white">{{ __('frontend.about.prog_continuing') }}</h5>
                                <p class="mb-0 text-white-50">{{ __('frontend.about.prog_continuing_text') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="training-image-container">
                    <img src="{{ asset('images/200851589.jpg') }}" alt="Training Facility"
                        class="img-fluid rounded-3 shadow-lg mb-3"
                        style="width: 100%; height: 250px; object-fit: cover;">
                    <div class="facility-info p-3 rounded-3"
                        style="background: rgba(0,0,0,0.35); border: 1px solid rgba(255,255,255,0.1);">
                        <h5 class="text-white">{{ __('frontend.about.modern_facilities') }}</h5>
                        <p class="mb-0 text-white-50" style="font-size: 0.9rem;">
                            {{ __('frontend.about.facilities_text') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact CTA Section --}}
        <div class="row">
            <div class="col-12">
                <div class="contact-cta text-center p-5 rounded-3"
                    style="background: var(--frost-accent-color-4); color: white;">
                    <h2 class="mb-3">{{ __('frontend.about.cta_heading') }}</h2>
                    <p class="lead mb-4">
                        {{ __('frontend.about.cta_text', ['company' => setting('company_name')]) }}
                    </p>
                    <div class="cta-buttons">
                        <a href="{{ route('pages', 'contact') }}" class="btn btn-lg me-3"
                            style="background: white; color: var(--frost-primary-color); border: none; transition: all 0.3s ease;"
                            onmouseover="this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.transform='translateY(0)'">
                            <i class="fas fa-envelope me-2"></i>{{ __('frontend.about.cta_contact') }}
                        </a>
                        <a href="tel:{{ setting('support_phone') }}" class="btn btn-outline-light btn-lg"
                            style="border: 2px solid white; transition: all 0.3s ease;"
                            onmouseover="this.style.background='white'; this.style.color='var(--frost-accent-color-4)'"
                            onmouseout="this.style.background='transparent'; this.style.color='white'">
                            <i class="fas fa-phone me-2"></i>{{ setting('support_phone') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
