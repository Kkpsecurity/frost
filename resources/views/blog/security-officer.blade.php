{{-- Security Officer Career Guide Blog Post --}}
<x-site.layout :title="'Security Officer Career Guide: Requirements and Opportunities in Florida'">
    <x-slot:head>
        <meta name="description" content="Complete guide to becoming a security officer in Florida, including licensing requirements, career opportunities, and professional development.">
        <meta name="keywords" content="security officer, career, florida, licensing, training, advancement, compliance">
    </x-slot:head>

    <x-site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <div class="blog-detail-page">
    <!-- Hero Section -->
    <section class="blog-hero frost-secondary-bg py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white-50">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/blog') }}" class="text-white-50">Blog</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Security Officer Career</li>
                        </ol>
                    </nav>

                    <div class="blog-category mb-3">
                        <span class="badge bg-success">Career Development</span>
                    </div>

                    <h1 class="text-white mb-4">Security Officer Career Guide: Requirements and Opportunities in Florida</h1>

                    <div class="blog-meta d-flex justify-content-center align-items-center flex-wrap gap-4 text-white-50">
                        <span><i class="fas fa-calendar me-2"></i>July 28, 2025</span>
                        <span><i class="fas fa-user me-2"></i>Career Advisor</span>
                        <span><i class="fas fa-clock me-2"></i>6 min read</span>
                        <span><i class="fas fa-eye me-2"></i>987 views</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="blog-content py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <article class="blog-article">
                        <!-- Featured Image -->
                        <div class="article-image mb-5">
                            <img src="{{ asset('assets/img/blog/security-officer-career.jpg') }}"
                                 alt="Security Officer Career"
                                 class="img-fluid rounded shadow-lg">
                        </div>

                        <!-- Article Content -->
                        <div class="article-content">
                            <div class="lead mb-4 text-muted">
                                The security industry in Florida offers excellent career opportunities for dedicated professionals. Whether you're considering a career change or just starting your professional journey, becoming a security officer provides stability, growth potential, and the satisfaction of protecting people and property.
                            </div>

                            <h2 class="section-title">Why Choose a Security Officer Career?</h2>
                            <p>
                                Security officers play a vital role in maintaining safety and security across various industries. From corporate buildings and retail establishments to healthcare facilities and educational institutions, security professionals are in high demand across Florida.
                            </p>

                            <div class="career-benefits-grid">
                                <div class="benefit-card">
                                    <i class="fas fa-dollar-sign text-success"></i>
                                    <h4>Competitive Compensation</h4>
                                    <p>Florida security officers earn competitive wages with opportunities for overtime and premium pay</p>
                                </div>
                                <div class="benefit-card">
                                    <i class="fas fa-chart-line text-success"></i>
                                    <h4>Career Growth</h4>
                                    <p>Clear advancement paths from entry-level to supervisory and management positions</p>
                                </div>
                                <div class="benefit-card">
                                    <i class="fas fa-clock text-success"></i>
                                    <h4>Flexible Scheduling</h4>
                                    <p>Various shifts available including part-time, full-time, and flexible schedule options</p>
                                </div>
                                <div class="benefit-card">
                                    <i class="fas fa-shield-alt text-success"></i>
                                    <h4>Job Security</h4>
                                    <p>High demand for security professionals ensures stable employment opportunities</p>
                                </div>
                            </div>

                            <h2 class="section-title">Florida Security License Requirements</h2>
                            <p>
                                Florida requires all security officers to obtain appropriate licensing through the Department of Agriculture and Consumer Services. Understanding these requirements is essential for career planning.
                            </p>

                            <div class="license-types">
                                <div class="license-type-card d-license">
                                    <div class="license-header">
                                        <div class="license-badge">D</div>
                                        <div class="license-info">
                                            <h3>Class D - Unarmed Security Officer</h3>
                                            <p class="license-subtitle">Entry-level security position</p>
                                        </div>
                                    </div>

                                    <div class="requirements-section">
                                        <h4>Requirements:</h4>
                                        <ul>
                                            <li>18 years of age or older</li>
                                            <li>High school diploma or equivalent</li>
                                            <li>Pass background check and fingerprinting</li>
                                            <li>Complete 40-hour training program</li>
                                            <li>Pass state examination</li>
                                            <li>Submit license application with fees</li>
                                        </ul>
                                    </div>

                                    <div class="job-duties">
                                        <h4>Typical Duties:</h4>
                                        <ul>
                                            <li>Monitor and patrol assigned areas</li>
                                            <li>Control access to facilities</li>
                                            <li>Write incident reports</li>
                                            <li>Respond to alarms and emergencies</li>
                                            <li>Provide customer service</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="license-type-card g-license">
                                    <div class="license-header">
                                        <div class="license-badge">G</div>
                                        <div class="license-info">
                                            <h3>Class G - Armed Security Officer</h3>
                                            <p class="license-subtitle">Advanced security position with firearm authority</p>
                                        </div>
                                    </div>

                                    <div class="requirements-section">
                                        <h4>Requirements:</h4>
                                        <ul>
                                            <li>21 years of age or older</li>
                                            <li>Valid Class D license</li>
                                            <li>Complete 28-hour firearms training</li>
                                            <li>Pass firearms proficiency test</li>
                                            <li>Enhanced background screening</li>
                                            <li>Mental health evaluation</li>
                                        </ul>
                                    </div>

                                    <div class="job-duties">
                                        <h4>Typical Duties:</h4>
                                        <ul>
                                            <li>All Class D responsibilities</li>
                                            <li>Armed response to threats</li>
                                            <li>High-value asset protection</li>
                                            <li>VIP and executive protection</li>
                                            <li>Cash and valuable transport</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <h2 class="section-title">Training and Education Requirements</h2>
                            <p>
                                Proper training is essential for success as a security officer. Florida requires comprehensive training programs that cover legal authority, emergency procedures, and professional conduct.
                            </p>

                            <div class="training-breakdown">
                                <div class="training-module">
                                    <h4><i class="fas fa-gavel text-info me-2"></i>Legal Authority and Responsibilities</h4>
                                    <p>Understanding the scope of authority, civil and criminal liability, and proper use of force.</p>
                                </div>
                                <div class="training-module">
                                    <h4><i class="fas fa-first-aid text-info me-2"></i>Emergency Procedures</h4>
                                    <p>Fire safety, medical emergencies, evacuation procedures, and disaster response.</p>
                                </div>
                                <div class="training-module">
                                    <h4><i class="fas fa-users text-info me-2"></i>Public Relations and Communication</h4>
                                    <p>Professional interaction with the public, de-escalation techniques, and report writing.</p>
                                </div>
                                <div class="training-module">
                                    <h4><i class="fas fa-eye text-info me-2"></i>Observation and Documentation</h4>
                                    <p>Patrol techniques, incident reporting, evidence preservation, and court testimony.</p>
                                </div>
                            </div>

                            <h2 class="section-title">Career Advancement Opportunities</h2>
                            <p>
                                The security industry offers numerous paths for career advancement. With experience and additional training, security officers can progress to leadership roles and specialized positions.
                            </p>

                            <div class="career-path">
                                <div class="path-level">
                                    <div class="level-number">1</div>
                                    <div class="level-content">
                                        <h4>Entry Level Security Officer</h4>
                                        <p>Starting position with Class D license, focusing on basic security duties and gaining experience.</p>
                                        <div class="salary-range">$28,000 - $35,000 annually</div>
                                    </div>
                                </div>

                                <div class="path-arrow">
                                    <i class="fas fa-arrow-down"></i>
                                </div>

                                <div class="path-level">
                                    <div class="level-number">2</div>
                                    <div class="level-content">
                                        <h4>Senior Security Officer / Armed Officer</h4>
                                        <p>Experienced officer with Class G license, handling complex security assignments.</p>
                                        <div class="salary-range">$35,000 - $45,000 annually</div>
                                    </div>
                                </div>

                                <div class="path-arrow">
                                    <i class="fas fa-arrow-down"></i>
                                </div>

                                <div class="path-level">
                                    <div class="level-number">3</div>
                                    <div class="level-content">
                                        <h4>Security Supervisor / Team Leader</h4>
                                        <p>Leading security teams, overseeing operations, and managing client relationships.</p>
                                        <div class="salary-range">$45,000 - $55,000 annually</div>
                                    </div>
                                </div>

                                <div class="path-arrow">
                                    <i class="fas fa-arrow-down"></i>
                                </div>

                                <div class="path-level">
                                    <div class="level-number">4</div>
                                    <div class="level-content">
                                        <h4>Security Manager / Operations Manager</h4>
                                        <p>Managing multiple sites, developing security protocols, and strategic planning.</p>
                                        <div class="salary-range">$55,000 - $70,000 annually</div>
                                    </div>
                                </div>
                            </div>

                            <h2 class="section-title">Specialized Security Fields</h2>
                            <p>
                                Security professionals can specialize in various fields, each offering unique challenges and rewards:
                            </p>

                            <div class="specialization-grid">
                                <div class="spec-card">
                                    <i class="fas fa-building text-primary"></i>
                                    <h4>Corporate Security</h4>
                                    <p>Protecting corporate facilities, executives, and sensitive information</p>
                                    <div class="spec-requirements">
                                        <strong>Requirements:</strong> Business background preferred, advanced training
                                    </div>
                                </div>

                                <div class="spec-card">
                                    <i class="fas fa-store text-primary"></i>
                                    <h4>Retail Loss Prevention</h4>
                                    <p>Preventing theft and ensuring customer and employee safety in retail environments</p>
                                    <div class="spec-requirements">
                                        <strong>Requirements:</strong> Customer service skills, attention to detail
                                    </div>
                                </div>

                                <div class="spec-card">
                                    <i class="fas fa-hospital text-primary"></i>
                                    <h4>Healthcare Security</h4>
                                    <p>Maintaining security in hospitals and healthcare facilities</p>
                                    <div class="spec-requirements">
                                        <strong>Requirements:</strong> De-escalation training, medical facility experience
                                    </div>
                                </div>

                                <div class="spec-card">
                                    <i class="fas fa-user-shield text-primary"></i>
                                    <h4>Executive Protection</h4>
                                    <p>Providing personal security for high-profile individuals</p>
                                    <div class="spec-requirements">
                                        <strong>Requirements:</strong> Class G license, advanced training, experience
                                    </div>
                                </div>

                                <div class="spec-card">
                                    <i class="fas fa-truck text-primary"></i>
                                    <h4>Transportation Security</h4>
                                    <p>Securing cargo, ports, airports, and transportation hubs</p>
                                    <div class="spec-requirements">
                                        <strong>Requirements:</strong> Background checks, federal certifications
                                    </div>
                                </div>

                                <div class="spec-card">
                                    <i class="fas fa-graduation-cap text-primary"></i>
                                    <h4>Educational Security</h4>
                                    <p>Ensuring safety in schools, colleges, and educational institutions</p>
                                    <div class="spec-requirements">
                                        <strong>Requirements:</strong> Child safety training, crisis response
                                    </div>
                                </div>
                            </div>

                            <h2 class="section-title">Getting Started: Your Next Steps</h2>
                            <p>
                                Ready to begin your security officer career? Follow these steps to get started:
                            </p>

                            <div class="getting-started-steps">
                                <div class="step-item">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4>Research and Planning</h4>
                                        <p>Determine which type of security work interests you and research local job opportunities.</p>
                                    </div>
                                </div>

                                <div class="step-item">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4>Choose Training Provider</h4>
                                        <p>Select a state-approved training school like Security Training Group for quality education.</p>
                                    </div>
                                </div>

                                <div class="step-item">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h4>Complete Training Program</h4>
                                        <p>Successfully complete the 40-hour Class D training program and pass examinations.</p>
                                    </div>
                                </div>

                                <div class="step-item">
                                    <div class="step-number">4</div>
                                    <div class="step-content">
                                        <h4>Apply for License</h4>
                                        <p>Submit application, background check, and required documentation to the state.</p>
                                    </div>
                                </div>

                                <div class="step-item">
                                    <div class="step-number">5</div>
                                    <div class="step-content">
                                        <h4>Find Employment</h4>
                                        <p>Apply for positions with security companies or directly with organizations needing security services.</p>
                                    </div>
                                </div>

                                <div class="step-item">
                                    <div class="step-number">6</div>
                                    <div class="step-content">
                                        <h4>Continue Professional Development</h4>
                                        <p>Pursue additional certifications, advanced training, and specialized skills to advance your career.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="success-tips">
                                <h3>Tips for Success as a Security Officer</h3>
                                <div class="tips-grid">
                                    <div class="tip-item">
                                        <i class="fas fa-handshake text-warning me-2"></i>
                                        <strong>Professionalism:</strong> Maintain a professional appearance and demeanor at all times
                                    </div>
                                    <div class="tip-item">
                                        <i class="fas fa-comments text-warning me-2"></i>
                                        <strong>Communication:</strong> Develop strong verbal and written communication skills
                                    </div>
                                    <div class="tip-item">
                                        <i class="fas fa-eye text-warning me-2"></i>
                                        <strong>Observation:</strong> Stay alert and develop keen observation abilities
                                    </div>
                                    <div class="tip-item">
                                        <i class="fas fa-book text-warning me-2"></i>
                                        <strong>Continuous Learning:</strong> Stay updated on security trends and best practices
                                    </div>
                                </div>
                            </div>

                            <div class="conclusion-box mt-5">
                                <h3>Start Your Security Career Today</h3>
                                <p>
                                    A career as a security officer offers stability, growth opportunities, and the satisfaction of protecting others. With proper training and dedication, you can build a rewarding career in Florida's growing security industry.
                                </p>
                                <p>
                                    <strong>Take the first step toward your security career—enroll in a quality training program and join the ranks of professional security officers making a difference every day.</strong>
                                </p>
                            </div>
                        </div>

                        <!-- Tags and Social Sharing -->
                        <div class="article-footer mt-5 pt-4 border-top">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="article-tags">
                                        <h5 class="mb-3">Tags:</h5>
                                        <span class="tag">Security Career</span>
                                        <span class="tag">Florida Jobs</span>
                                        <span class="tag">Professional Development</span>
                                        <span class="tag">Career Change</span>
                                        <span class="tag">Security Officer</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="social-sharing">
                                        <h5 class="mb-3">Share This Article:</h5>
                                        <a href="#" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                                        <a href="#" class="social-btn linkedin"><i class="fab fa-linkedin-in"></i></a>
                                        <a href="#" class="social-btn email"><i class="fas fa-envelope"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="blog-sidebar">
                        <!-- Career Resources -->
                        <div class="sidebar-widget">
                            <h5 class="widget-title">Career Resources</h5>
                            <div class="resource-list">
                                <a href="#" class="resource-item">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <div>
                                        <h6>Salary Guide 2025</h6>
                                        <small>Florida security officer compensation data</small>
                                    </div>
                                </a>
                                <a href="#" class="resource-item">
                                    <i class="fas fa-briefcase text-info me-2"></i>
                                    <div>
                                        <h6>Job Board</h6>
                                        <small>Current security officer openings</small>
                                    </div>
                                </a>
                                <a href="#" class="resource-item">
                                    <i class="fas fa-graduation-cap text-success me-2"></i>
                                    <div>
                                        <h6>Training Programs</h6>
                                        <small>Available certification courses</small>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="sidebar-widget stats-widget">
                            <h5 class="widget-title">Industry Statistics</h5>
                            <div class="stat-item">
                                <div class="stat-number">15%</div>
                                <div class="stat-label">Job Growth Rate</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">$38K</div>
                                <div class="stat-label">Average FL Salary</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">50K+</div>
                                <div class="stat-label">Security Officers in FL</div>
                            </div>
                        </div>

                        <!-- Next Steps -->
                        <div class="sidebar-widget cta-widget">
                            <h5 class="widget-title">Ready to Start?</h5>
                            <p>Begin your security officer career with professional training.</p>
                            <a href="#" class="btn btn-primary w-100 mb-2">View Training Programs</a>
                            <a href="#" class="btn btn-outline-primary w-100">Contact Admissions</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Career Guide Specific Styles */
.career-benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin: 40px 0;
}

.benefit-card {
    background: white;
    padding: 30px 25px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.benefit-card:hover {
    transform: translateY(-5px);
}

.benefit-card i {
    font-size: 2.5rem;
    margin-bottom: 20px;
}

.benefit-card h4 {
    color: var(--frost-primary-color);
    margin-bottom: 15px;
}

.license-types {
    margin: 40px 0;
}

.license-type-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    overflow: hidden;
}

.d-license .license-header {
    background: linear-gradient(135deg, var(--frost-info-color) 0%, var(--frost-secondary-color) 100%);
    color: white;
    padding: 25px;
}

.g-license .license-header {
    background: linear-gradient(135deg, var(--frost-secondary-color) 0%, var(--frost-primary-color) 100%);
    color: white;
    padding: 25px;
}

.license-header {
    display: flex;
    align-items: center;
}

.license-badge {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    font-weight: bold;
    margin-right: 20px;
}

.license-info h3 {
    margin-bottom: 5px;
}

.license-subtitle {
    opacity: 0.8;
    margin: 0;
}

.requirements-section,
.job-duties {
    padding: 25px;
}

.requirements-section h4,
.job-duties h4 {
    color: var(--frost-primary-color);
    margin-bottom: 15px;
}

.requirements-section ul,
.job-duties ul {
    margin: 0;
    padding-left: 20px;
}

.requirements-section ul li,
.job-duties ul li {
    margin-bottom: 8px;
}

.training-breakdown {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin: 40px 0;
}

.training-module {
    background: var(--frost-light-color);
    padding: 25px;
    border-radius: 12px;
    border-left: 4px solid var(--frost-info-color);
}

.training-module h4 {
    color: var(--frost-primary-color);
    margin-bottom: 15px;
}

.career-path {
    margin: 40px 0;
    background: var(--frost-light-color);
    padding: 40px;
    border-radius: 15px;
}

.path-level {
    display: flex;
    align-items: center;
    background: white;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.level-number {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--frost-info-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin-right: 20px;
    flex-shrink: 0;
}

.level-content h4 {
    color: var(--frost-primary-color);
    margin-bottom: 10px;
}

.salary-range {
    background: var(--frost-highlight-color);
    color: var(--frost-primary-color);
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
    margin-top: 10px;
}

.path-arrow {
    text-align: center;
    margin: 10px 0;
}

.path-arrow i {
    font-size: 1.5rem;
    color: var(--frost-info-color);
}

.specialization-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin: 40px 0;
}

.spec-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease;
}

.spec-card:hover {
    transform: translateY(-3px);
}

.spec-card i {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.spec-card h4 {
    color: var(--frost-primary-color);
    margin-bottom: 15px;
}

.spec-requirements {
    background: var(--frost-light-color);
    padding: 10px 15px;
    border-radius: 8px;
    margin-top: 15px;
    font-size: 0.9rem;
}

.getting-started-steps {
    margin: 40px 0;
}

.step-item {
    display: flex;
    align-items: flex-start;
    background: white;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--frost-info-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 20px;
    flex-shrink: 0;
}

.step-content h4 {
    color: var(--frost-primary-color);
    margin-bottom: 10px;
}

.success-tips {
    background: linear-gradient(135deg, var(--frost-highlight-color) 0%, rgba(254, 222, 89, 0.8) 100%);
    padding: 30px;
    border-radius: 15px;
    margin: 40px 0;
}

.success-tips h3 {
    color: var(--frost-primary-color);
    margin-bottom: 25px;
    text-align: center;
}

.tips-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.tip-item {
    background: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 10px;
    display: flex;
    align-items: flex-start;
}

.resource-list {
    background: white;
    border-radius: 12px;
    overflow: hidden;
}

.resource-item {
    display: flex;
    align-items: center;
    padding: 20px;
    text-decoration: none;
    color: var(--frost-dark-color);
    border-bottom: 1px solid var(--frost-light-color);
    transition: all 0.3s ease;
}

.resource-item:hover {
    background: var(--frost-light-color);
    text-decoration: none;
    color: var(--frost-info-color);
}

.resource-item:last-child {
    border-bottom: none;
}

.resource-item h6 {
    margin-bottom: 2px;
}

.stats-widget {
    background: var(--frost-primary-color);
    color: white;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
}

.stats-widget .widget-title {
    color: var(--frost-highlight-color);
    border-color: var(--frost-highlight-color);
}

.stat-item {
    margin: 20px 0;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--frost-highlight-color);
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.cta-widget {
    background: var(--frost-light-color);
    padding: 25px;
    border-radius: 12px;
    text-align: center;
}

@media (max-width: 768px) {
    .career-benefits-grid {
        grid-template-columns: 1fr;
    }

    .license-header {
        flex-direction: column;
        text-align: center;
    }

    .license-badge {
        margin-right: 0;
        margin-bottom: 15px;
    }

    .path-level {
        flex-direction: column;
        text-align: center;
    }

    .level-number {
        margin-right: 0;
        margin-bottom: 15px;
    }

    .specialization-grid {
        grid-template-columns: 1fr;
    }

    .tips-grid {
        grid-template-columns: 1fr;
    }
}
</style>
        </div>
    </div>

    <x-site.partials.footer />
</x-site.layout>
