{{-- About Us Content Section --}}
<section class="about-content py-5" style="background: linear-gradient(135deg, var(--frost-primary-color) 0%, var(--frost-secondary-color) 100%); min-height: 100vh;">
    <div class="container">
        {{-- Hero Section --}}
        <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <div class="about-hero-content p-4 rounded-3" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                    <h1 class="display-4 mb-4" style="color: var(--frost-primary-color); font-weight: 700;">
                        About {{ setting('company_name', config('app.name')) }}
                    </h1>
                    <p class="lead mb-4" style="color: var(--frost-base-color);">
                        Florida's premier security training provider, dedicated to professional excellence and safety education since our founding.
                    </p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="stat-item p-3 rounded mb-3" style="background: var(--frost-accent-color-1); color: white;">
                                <h3 class="mb-1">10,000+</h3>
                                <p class="mb-0">Students Trained</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="stat-item p-3 rounded mb-3" style="background: var(--frost-accent-color-2); color: white;">
                                <h3 class="mb-1">15+</h3>
                                <p class="mb-0">Years Experience</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-image-container p-4">
                    <img src="{{ asset('images/Security-Page-1.jpg') }}" alt="Security Training"
                         class="img-fluid rounded-3 shadow-lg"
                         style="width: 100%; height: 400px; object-fit: cover;">
                </div>
            </div>
        </div>

        {{-- Mission & Vision Section --}}
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="mission-card h-100 p-4 rounded-3" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                    <div class="card-icon mb-3 text-center">
                        <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                             style="width: 80px; height: 80px; background: var(--frost-accent-color-1);">
                            <i class="fas fa-bullseye fa-2x text-white"></i>
                        </div>
                    </div>
                    <h3 class="text-center mb-4" style="color: var(--frost-primary-color);">Our Mission</h3>
                    <p style="color: var(--frost-base-color); line-height: 1.8;">
                        To provide comprehensive, professional security training that empowers individuals with the knowledge,
                        skills, and certification needed to excel in the security industry while maintaining the highest
                        standards of safety and professionalism.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="vision-card h-100 p-4 rounded-3" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                    <div class="card-icon mb-3 text-center">
                        <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                             style="width: 80px; height: 80px; background: var(--frost-accent-color-2);">
                            <i class="fas fa-eye fa-2x text-white"></i>
                        </div>
                    </div>
                    <h3 class="text-center mb-4" style="color: var(--frost-primary-color);">Our Vision</h3>
                    <p style="color: var(--frost-base-color); line-height: 1.8;">
                        To be Florida's leading security training institution, recognized for excellence in education,
                        innovative training methods, and our commitment to developing security professionals who protect
                        and serve their communities with integrity.
                    </p>
                </div>
            </div>
        </div>

        {{-- Why Choose Us Section --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="why-choose-us p-5 rounded-3" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                    <h2 class="text-center mb-5" style="color: var(--frost-primary-color);">Why Choose {{ setting('company_name') }}?</h2>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="feature-item text-center p-3">
                                <div class="feature-icon mb-3">
                                    <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                                         style="width: 70px; height: 70px; background: var(--frost-accent-color-1);">
                                        <i class="fas fa-certificate fa-2x text-white"></i>
                                    </div>
                                </div>
                                <h4 style="color: var(--frost-primary-color);">State Certified</h4>
                                <p style="color: var(--frost-base-color);">
                                    Fully licensed and approved by Florida state agencies for security training and certification programs.
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
                                <h4 style="color: var(--frost-primary-color);">Expert Instructors</h4>
                                <p style="color: var(--frost-base-color);">
                                    Learn from experienced security professionals with real-world expertise in law enforcement and private security.
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
                                <h4 style="color: var(--frost-primary-color);">Flexible Scheduling</h4>
                                <p style="color: var(--frost-base-color);">
                                    Multiple class times and online options to fit your busy schedule and learning preferences.
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
                <div class="programs-overview p-4 rounded-3" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                    <h2 class="mb-4" style="color: var(--frost-primary-color);">Our Training Programs</h2>
                    <div class="programs-list">
                        <div class="program-item d-flex mb-3 p-3 rounded" style="background: var(--frost-light-color);">
                            <div class="program-icon me-3">
                                <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                                     style="width: 50px; height: 50px; background: var(--frost-accent-color-1);">
                                    <i class="fas fa-shield-alt text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h5 style="color: var(--frost-primary-color);">Security Officer Training</h5>
                                <p class="mb-0" style="color: var(--frost-base-color);">
                                    Comprehensive 40-hour program covering all aspects of security operations and legal requirements.
                                </p>
                            </div>
                        </div>

                        <div class="program-item d-flex mb-3 p-3 rounded" style="background: var(--frost-light-color);">
                            <div class="program-icon me-3">
                                <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                                     style="width: 50px; height: 50px; background: var(--frost-accent-color-2);">
                                    <i class="fas fa-crosshairs text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h5 style="color: var(--frost-primary-color);">Firearms Training</h5>
                                <p class="mb-0" style="color: var(--frost-base-color);">
                                    Professional firearms safety, handling, and marksmanship training for security personnel.
                                </p>
                            </div>
                        </div>

                        <div class="program-item d-flex mb-3 p-3 rounded" style="background: var(--frost-light-color);">
                            <div class="program-icon me-3">
                                <div class="icon-circle d-inline-flex align-items-center justify-content-center rounded-circle"
                                     style="width: 50px; height: 50px; background: var(--frost-accent-color-3);">
                                    <i class="fas fa-graduation-cap text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h5 style="color: var(--frost-primary-color);">Continuing Education</h5>
                                <p class="mb-0" style="color: var(--frost-base-color);">
                                    Ongoing education programs to maintain certifications and stay current with industry standards.
                                </p>
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
                    <div class="facility-info p-3 rounded-3" style="background: var(--frost-highlight-color);">
                        <h5 style="color: var(--frost-primary-color);">Modern Facilities</h5>
                        <p class="mb-0" style="color: var(--frost-primary-color); font-size: 0.9rem;">
                            State-of-the-art classrooms and training facilities located in Boynton Beach, Florida.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact CTA Section --}}
        <div class="row">
            <div class="col-12">
                <div class="contact-cta text-center p-5 rounded-3" style="background: var(--frost-accent-color-4); color: white;">
                    <h2 class="mb-3">Ready to Start Your Security Career?</h2>
                    <p class="lead mb-4">
                        Join thousands of successful graduates who have launched their careers with {{ setting('company_name') }}
                    </p>
                    <div class="cta-buttons">
                        <a href="{{ route('pages', 'contact') }}" class="btn btn-lg me-3"
                           style="background: white; color: var(--frost-primary-color); border: none; transition: all 0.3s ease;"
                           onmouseover="this.style.transform='translateY(-2px)'"
                           onmouseout="this.style.transform='translateY(0)'">
                            <i class="fas fa-envelope me-2"></i>Contact Us Today
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
