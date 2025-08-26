{{-- Threat Assessment Techniques Blog Post --}}
<x-site.layout :title="'Advanced Threat Assessment Techniques for Security Officers'">
    <x-slot:head>
        <meta name="description" content="Learn professional threat assessment methodologies to identify and evaluate potential security risks effectively. Essential skills for modern security professionals.">
        <meta name="keywords" content="threat assessment, security officer, risk evaluation, security tips, florida">
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
                            <li class="breadcrumb-item active text-white" aria-current="page">Threat Assessment</li>
                        </ol>
                    </nav>

                    <div class="blog-category mb-3">
                        <span class="badge bg-success">Security Tips</span>
                    </div>

                    <h1 class="text-white mb-4">Advanced Threat Assessment Techniques for Security Officers</h1>

                    <div class="blog-meta d-flex justify-content-center align-items-center flex-wrap gap-4 text-white-50">
                        <span><i class="fas fa-calendar me-2"></i>August 10, 2025</span>
                        <span><i class="fas fa-user me-2"></i>Security Assessment Specialist</span>
                        <span><i class="fas fa-clock me-2"></i>5 min read</span>
                        <span><i class="fas fa-eye me-2"></i>675 views</span>
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
                            <img src="{{ asset('assets/img/blog/threat-assessment-hero.jpg') }}"
                                 alt="Threat Assessment Training"
                                 class="img-fluid rounded shadow-lg">
                        </div>

                        <!-- Article Content -->
                        <div class="article-content">
                            <div class="lead mb-4 text-muted">
                                Effective threat assessment is the cornerstone of professional security work. Learn advanced techniques used by security professionals to identify, evaluate, and respond to potential threats before they escalate into dangerous situations.
                            </div>

                            <h2 class="section-title">Understanding Threat Assessment Fundamentals</h2>
                            <p>
                                Threat assessment is a systematic process of evaluating potential risks and determining appropriate response measures. It combines observation skills, behavioral analysis, environmental awareness, and professional judgment to identify threats before they materialize.
                            </p>

                            <div class="definition-box">
                                <h4><i class="fas fa-info-circle text-info me-2"></i>Key Definition:</h4>
                                <p><strong>Threat Assessment:</strong> The systematic evaluation of the credibility and seriousness of a potential threat, including the ability, intent, and opportunity of a person or group to carry out harmful actions.</p>
                            </div>

                            <h2 class="section-title">The Four-Factor Threat Analysis Model</h2>
                            <p>
                                Professional threat assessment relies on evaluating four critical factors that determine the level of risk:
                            </p>

                            <div class="factors-grid">
                                <div class="factor-card">
                                    <div class="factor-icon ability">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <h3>Ability</h3>
                                    <p>Does the individual or group have the physical capability, resources, and skills to carry out the threat?</p>
                                    <ul class="factor-considerations">
                                        <li>Physical capabilities</li>
                                        <li>Technical skills</li>
                                        <li>Available resources</li>
                                        <li>Access to weapons or tools</li>
                                    </ul>
                                </div>

                                <div class="factor-card">
                                    <div class="factor-icon intent">
                                        <i class="fas fa-bullseye"></i>
                                    </div>
                                    <h3>Intent</h3>
                                    <p>Is there clear evidence of motivation and desire to cause harm or commit violence?</p>
                                    <ul class="factor-considerations">
                                        <li>Stated threats or plans</li>
                                        <li>History of violence</li>
                                        <li>Ideological motivations</li>
                                        <li>Personal grievances</li>
                                    </ul>
                                </div>

                                <div class="factor-card">
                                    <div class="factor-icon opportunity">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <h3>Opportunity</h3>
                                    <p>Does the person have access to potential targets and the ability to act without detection?</p>
                                    <ul class="factor-considerations">
                                        <li>Access to target areas</li>
                                        <li>Knowledge of security measures</li>
                                        <li>Timing and scheduling</li>
                                        <li>Environmental factors</li>
                                    </ul>
                                </div>

                                <div class="factor-card">
                                    <div class="factor-icon timeline">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <h3>Timeline</h3>
                                    <p>What is the expected timeframe for potential action, and how imminent is the threat?</p>
                                    <ul class="factor-considerations">
                                        <li>Specific dates mentioned</li>
                                        <li>Triggering events</li>
                                        <li>Escalation patterns</li>
                                        <li>External pressures</li>
                                    </ul>
                                </div>
                            </div>

                            <h2 class="section-title">Behavioral Indicators and Warning Signs</h2>
                            <p>
                                Recognizing behavioral indicators is crucial for early threat detection. These signs often appear before actual threatening behavior escalates:
                            </p>

                            <div class="indicators-section">
                                <h3>Verbal Indicators</h3>
                                <div class="indicator-category">
                                    <div class="indicator-items">
                                        <div class="indicator-item high-risk">
                                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                            <strong>Direct Threats:</strong> Explicit statements about causing harm to specific individuals or locations
                                        </div>
                                        <div class="indicator-item medium-risk">
                                            <i class="fas fa-warning text-warning me-2"></i>
                                            <strong>Implied Threats:</strong> Veiled references to violence or "getting even"
                                        </div>
                                        <div class="indicator-item low-risk">
                                            <i class="fas fa-info text-info me-2"></i>
                                            <strong>Concerning Language:</strong> Increased aggressive language, dehumanizing speech
                                        </div>
                                    </div>
                                </div>

                                <h3>Behavioral Indicators</h3>
                                <div class="indicator-category">
                                    <div class="indicator-items">
                                        <div class="indicator-item high-risk">
                                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                            <strong>Preparation Activities:</strong> Researching targets, acquiring weapons, conducting surveillance
                                        </div>
                                        <div class="indicator-item medium-risk">
                                            <i class="fas fa-warning text-warning me-2"></i>
                                            <strong>Social Withdrawal:</strong> Isolation, cutting ties with family and friends
                                        </div>
                                        <div class="indicator-item low-risk">
                                            <i class="fas fa-info text-info me-2"></i>
                                            <strong>Stress Indicators:</strong> Significant life changes, financial problems, relationship issues
                                        </div>
                                    </div>
                                </div>

                                <h3>Physical Indicators</h3>
                                <div class="indicator-category">
                                    <div class="indicator-items">
                                        <div class="indicator-item high-risk">
                                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                            <strong>Weapons Presence:</strong> Carrying weapons, displaying weapon imagery
                                        </div>
                                        <div class="indicator-item medium-risk">
                                            <i class="fas fa-warning text-warning me-2"></i>
                                            <strong>Suspicious Items:</strong> Unusual packages, inappropriate clothing for weather
                                        </div>
                                        <div class="indicator-item low-risk">
                                            <i class="fas fa-info text-info me-2"></i>
                                            <strong>Agitation Signs:</strong> Nervous behavior, excessive sweating, fidgeting
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h2 class="section-title">Environmental Threat Assessment</h2>
                            <p>
                                Assessing the environment is as important as assessing individuals. Environmental factors can increase or decrease threat levels:
                            </p>

                            <div class="environment-factors">
                                <div class="env-factor">
                                    <h4><i class="fas fa-map-marker-alt text-info me-2"></i>Location Factors</h4>
                                    <ul>
                                        <li>Proximity to high-value targets</li>
                                        <li>Crowd density and composition</li>
                                        <li>Escape routes and chokepoints</li>
                                        <li>Natural or man-made barriers</li>
                                        <li>Visibility and lighting conditions</li>
                                    </ul>
                                </div>

                                <div class="env-factor">
                                    <h4><i class="fas fa-calendar-alt text-info me-2"></i>Temporal Factors</h4>
                                    <ul>
                                        <li>Time of day and day of week</li>
                                        <li>Special events or anniversaries</li>
                                        <li>Shift changes and staffing levels</li>
                                        <li>Seasonal considerations</li>
                                        <li>Weather conditions</li>
                                    </ul>
                                </div>

                                <div class="env-factor">
                                    <h4><i class="fas fa-shield-alt text-info me-2"></i>Security Factors</h4>
                                    <ul>
                                        <li>Existing security measures</li>
                                        <li>Response time for backup</li>
                                        <li>Communication capabilities</li>
                                        <li>Surveillance coverage</li>
                                        <li>Access control effectiveness</li>
                                    </ul>
                                </div>
                            </div>

                            <h2 class="section-title">Threat Assessment Documentation</h2>
                            <p>
                                Proper documentation is essential for effective threat management and legal protection:
                            </p>

                            <div class="documentation-template">
                                <h3>Standard Assessment Report Elements:</h3>
                                <div class="report-sections">
                                    <div class="report-section">
                                        <h4>1. Incident Summary</h4>
                                        <ul>
                                            <li>Date, time, and location</li>
                                            <li>Individuals involved</li>
                                            <li>Witnesses present</li>
                                            <li>Initial threat description</li>
                                        </ul>
                                    </div>

                                    <div class="report-section">
                                        <h4>2. Threat Analysis</h4>
                                        <ul>
                                            <li>Four-factor assessment (ability, intent, opportunity, timeline)</li>
                                            <li>Risk level determination</li>
                                            <li>Supporting evidence</li>
                                            <li>Behavioral indicators observed</li>
                                        </ul>
                                    </div>

                                    <div class="report-section">
                                        <h4>3. Response Actions</h4>
                                        <ul>
                                            <li>Immediate actions taken</li>
                                            <li>Notifications made</li>
                                            <li>Resources deployed</li>
                                            <li>Follow-up requirements</li>
                                        </ul>
                                    </div>

                                    <div class="report-section">
                                        <h4>4. Recommendations</h4>
                                        <ul>
                                            <li>Security enhancements needed</li>
                                            <li>Monitoring requirements</li>
                                            <li>Training recommendations</li>
                                            <li>Policy considerations</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <h2 class="section-title">De-escalation Techniques</h2>
                            <p>
                                When threats are identified, de-escalation may be the safest approach:
                            </p>

                            <div class="deescalation-techniques">
                                <div class="technique-card">
                                    <h4>Active Listening</h4>
                                    <p>Show genuine interest in understanding the person's concerns while maintaining professional boundaries.</p>
                                    <ul>
                                        <li>Maintain eye contact</li>
                                        <li>Use reflective listening</li>
                                        <li>Avoid interrupting</li>
                                        <li>Acknowledge emotions</li>
                                    </ul>
                                </div>

                                <div class="technique-card">
                                    <h4>Verbal De-escalation</h4>
                                    <p>Use calm, non-confrontational language to reduce tension and anxiety.</p>
                                    <ul>
                                        <li>Speak slowly and clearly</li>
                                        <li>Use non-threatening language</li>
                                        <li>Avoid commands or demands</li>
                                        <li>Offer alternatives</li>
                                    </ul>
                                </div>

                                <div class="technique-card">
                                    <h4>Environmental Management</h4>
                                    <p>Control the environment to create a calming atmosphere and reduce triggers.</p>
                                    <ul>
                                        <li>Provide personal space</li>
                                        <li>Remove audiences</li>
                                        <li>Reduce distractions</li>
                                        <li>Ensure escape routes</li>
                                    </ul>
                                </div>
                            </div>

                            <h2 class="section-title">When to Escalate</h2>
                            <p>
                                Knowing when to move beyond assessment to immediate action is critical:
                            </p>

                            <div class="escalation-criteria">
                                <div class="criteria-level immediate">
                                    <h3><i class="fas fa-phone text-danger me-2"></i>Immediate Law Enforcement</h3>
                                    <ul>
                                        <li>Weapons present or threatened</li>
                                        <li>Imminent threat to life or safety</li>
                                        <li>Violent behavior occurring</li>
                                        <li>Substance abuse affecting judgment</li>
                                    </ul>
                                </div>

                                <div class="criteria-level urgent">
                                    <h3><i class="fas fa-exclamation text-warning me-2"></i>Supervisor/Management Notification</h3>
                                    <ul>
                                        <li>Credible threats made</li>
                                        <li>Concerning behavior patterns</li>
                                        <li>History of violence discovered</li>
                                        <li>Multiple risk factors present</li>
                                    </ul>
                                </div>

                                <div class="criteria-level monitoring">
                                    <h3><i class="fas fa-eye text-info me-2"></i>Enhanced Monitoring</h3>
                                    <ul>
                                        <li>Minor behavioral changes</li>
                                        <li>Stress indicators present</li>
                                        <li>Concerning statements made</li>
                                        <li>Environmental risk factors</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="best-practices-box">
                                <h3>Best Practices for Threat Assessment</h3>
                                <div class="practices-grid">
                                    <div class="practice">
                                        <i class="fas fa-users text-success me-2"></i>
                                        <strong>Team Approach:</strong> Never assess threats in isolation—involve colleagues and supervisors
                                    </div>
                                    <div class="practice">
                                        <i class="fas fa-clock text-success me-2"></i>
                                        <strong>Timely Reporting:</strong> Document and report assessments promptly while details are fresh
                                    </div>
                                    <div class="practice">
                                        <i class="fas fa-balance-scale text-success me-2"></i>
                                        <strong>Objective Analysis:</strong> Base assessments on facts and observations, not assumptions
                                    </div>
                                    <div class="practice">
                                        <i class="fas fa-sync text-success me-2"></i>
                                        <strong>Continuous Monitoring:</strong> Reassess threats regularly as situations change
                                    </div>
                                </div>
                            </div>

                            <div class="conclusion-box mt-5">
                                <h3>Mastering Threat Assessment</h3>
                                <p>
                                    Effective threat assessment is both an art and a science. It requires combining systematic methodology with intuitive judgment, continuous observation with decisive action, and individual analysis with team collaboration.
                                </p>
                                <p>
                                    <strong>Remember: The goal of threat assessment is prevention. By identifying and addressing threats early, security professionals protect lives and maintain safe environments for everyone.</strong>
                                </p>
                            </div>
                        </div>

                        <!-- Tags and Social Sharing -->
                        <div class="article-footer mt-5 pt-4 border-top">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="article-tags">
                                        <h5 class="mb-3">Tags:</h5>
                                        <span class="tag">Threat Assessment</span>
                                        <span class="tag">Security Analysis</span>
                                        <span class="tag">Risk Management</span>
                                        <span class="tag">Behavioral Indicators</span>
                                        <span class="tag">De-escalation</span>
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
                        <!-- Author Bio -->
                        <div class="sidebar-widget author-bio">
                            <div class="author-avatar">
                                <img src="{{ asset('assets/img/team/assessment-specialist.jpg') }}" alt="Author" class="rounded-circle">
                            </div>
                            <h4>Security Assessment Specialist</h4>
                            <p>Former federal agent and certified security consultant with extensive experience in threat assessment and risk analysis. Specializes in behavioral analysis and security protocol development.</p>
                            <div class="author-credentials">
                                <span class="credential">Federal Experience</span>
                                <span class="credential">Risk Analyst</span>
                                <span class="credential">Security Consultant</span>
                            </div>
                        </div>

                        <!-- Assessment Tools -->
                        <div class="sidebar-widget">
                            <h5 class="widget-title">Assessment Tools</h5>
                            <div class="tool-links">
                                <a href="#" class="tool-link">
                                    <i class="fas fa-clipboard-check text-info me-2"></i>
                                    <div>
                                        <h6>Threat Assessment Checklist</h6>
                                        <small>Comprehensive evaluation form</small>
                                    </div>
                                </a>
                                <a href="#" class="tool-link">
                                    <i class="fas fa-chart-line text-success me-2"></i>
                                    <div>
                                        <h6>Risk Matrix Calculator</h6>
                                        <small>Quantify threat levels</small>
                                    </div>
                                </a>
                                <a href="#" class="tool-link">
                                    <i class="fas fa-file-alt text-warning me-2"></i>
                                    <div>
                                        <h6>Incident Report Template</h6>
                                        <small>Standardized documentation</small>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Related Articles -->
                        <div class="sidebar-widget">
                            <h5 class="widget-title">Related Security Topics</h5>
                            <div class="related-posts">
                                <a href="{{ url('blog/essential-firearms-safety') }}" class="related-post">
                                    <img src="{{ asset('assets/img/blog/firearms-safety-thumb.jpg') }}" alt="Firearms Safety">
                                    <div class="post-info">
                                        <h6>Essential Firearms Safety Training</h6>
                                        <small>August 12, 2025</small>
                                    </div>
                                </a>
                                <a href="{{ url('blog/security-license-renewal') }}" class="related-post">
                                    <img src="{{ asset('assets/img/blog/license-renewal-thumb.jpg') }}" alt="License Renewal">
                                    <div class="post-info">
                                        <h6>Security License Renewal Guide</h6>
                                        <small>August 8, 2025</small>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Quick Reference -->
                        <div class="sidebar-widget quick-reference">
                            <h5 class="widget-title">Quick Reference</h5>
                            <div class="reference-card">
                                <h6>Emergency Contacts</h6>
                                <ul>
                                    <li><strong>911:</strong> Immediate threats</li>
                                    <li><strong>Supervisor:</strong> Risk escalation</li>
                                    <li><strong>Security Office:</strong> Internal coordination</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Threat Assessment Specific Styles */
.definition-box {
    background: linear-gradient(135deg, var(--frost-info-color) 0%, var(--frost-secondary-color) 100%);
    color: white;
    padding: 25px;
    border-radius: 12px;
    margin: 30px 0;
}

.factors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin: 40px 0;
}

.factor-card {
    background: white;
    padding: 30px 25px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    text-align: center;
}

.factor-card:hover {
    transform: translateY(-5px);
}

.factor-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 1.8rem;
    color: white;
}

.factor-icon.ability { background: var(--frost-info-color); }
.factor-icon.intent { background: var(--frost-secondary-color); }
.factor-icon.opportunity { background: var(--frost-highlight-color); color: var(--frost-primary-color); }
.factor-icon.timeline { background: var(--frost-primary-color); }

.factor-card h3 {
    color: var(--frost-primary-color);
    margin-bottom: 15px;
}

.factor-considerations {
    list-style: none;
    padding: 0;
    text-align: left;
    margin-top: 15px;
}

.factor-considerations li {
    padding: 5px 0;
    padding-left: 20px;
    position: relative;
}

.factor-considerations li::before {
    content: "•";
    color: var(--frost-info-color);
    position: absolute;
    left: 0;
}

.indicators-section {
    margin: 40px 0;
}

.indicators-section h3 {
    color: var(--frost-primary-color);
    margin: 30px 0 20px 0;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--frost-highlight-color);
}

.indicator-category {
    background: var(--frost-light-color);
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.indicator-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.indicator-item {
    padding: 15px 20px;
    border-radius: 8px;
    border-left: 4px solid;
}

.indicator-item.high-risk {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
    border-color: #dc3545;
}

.indicator-item.medium-risk {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%);
    border-color: #ffc107;
}

.indicator-item.low-risk {
    background: linear-gradient(135deg, rgba(23, 170, 201, 0.1) 0%, rgba(23, 170, 201, 0.05) 100%);
    border-color: var(--frost-info-color);
}

.environment-factors {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin: 30px 0;
}

.env-factor {
    background: var(--frost-light-color);
    padding: 25px;
    border-radius: 12px;
}

.env-factor h4 {
    color: var(--frost-primary-color);
    margin-bottom: 15px;
}

.env-factor ul {
    margin: 0;
    padding-left: 20px;
}

.documentation-template {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    margin: 30px 0;
}

.report-sections {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

.report-section {
    background: var(--frost-light-color);
    padding: 20px;
    border-radius: 10px;
}

.report-section h4 {
    color: var(--frost-info-color);
    margin-bottom: 15px;
}

.deescalation-techniques {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin: 30px 0;
}

.technique-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    border-top: 4px solid var(--frost-info-color);
}

.technique-card h4 {
    color: var(--frost-primary-color);
    margin-bottom: 15px;
}

.escalation-criteria {
    margin: 30px 0;
}

.criteria-level {
    margin-bottom: 25px;
    padding: 25px;
    border-radius: 12px;
    border-left: 5px solid;
}

.criteria-level.immediate {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
    border-color: #dc3545;
}

.criteria-level.urgent {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%);
    border-color: #ffc107;
}

.criteria-level.monitoring {
    background: linear-gradient(135deg, rgba(23, 170, 201, 0.1) 0%, rgba(23, 170, 201, 0.05) 100%);
    border-color: var(--frost-info-color);
}

.criteria-level h3 {
    margin-bottom: 15px;
}

.best-practices-box {
    background: linear-gradient(135deg, var(--frost-secondary-color) 0%, var(--frost-primary-color) 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin: 40px 0;
}

.best-practices-box h3 {
    color: var(--frost-highlight-color);
    margin-bottom: 25px;
}

.practices-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.practice {
    display: flex;
    align-items: flex-start;
    background: rgba(255, 255, 255, 0.1);
    padding: 20px;
    border-radius: 10px;
}

.tool-links {
    background: white;
    border-radius: 12px;
    overflow: hidden;
}

.tool-link {
    display: flex;
    align-items: center;
    padding: 20px;
    text-decoration: none;
    color: var(--frost-dark-color);
    border-bottom: 1px solid var(--frost-light-color);
    transition: all 0.3s ease;
}

.tool-link:hover {
    background: var(--frost-light-color);
    text-decoration: none;
    color: var(--frost-info-color);
}

.tool-link:last-child {
    border-bottom: none;
}

.tool-link i {
    margin-right: 15px;
    font-size: 1.5rem;
}

.tool-link h6 {
    margin-bottom: 2px;
}

.quick-reference {
    background: var(--frost-primary-color);
    color: white;
    padding: 25px;
    border-radius: 12px;
}

.quick-reference .widget-title {
    color: var(--frost-highlight-color);
    border-color: var(--frost-highlight-color);
}

.reference-card {
    background: rgba(255, 255, 255, 0.1);
    padding: 20px;
    border-radius: 8px;
}

.reference-card h6 {
    color: var(--frost-highlight-color);
    margin-bottom: 15px;
}

.reference-card ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.reference-card ul li {
    padding: 8px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.reference-card ul li:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .factors-grid {
        grid-template-columns: 1fr;
    }

    .indicator-items {
        gap: 10px;
    }

    .environment-factors {
        grid-template-columns: 1fr;
    }

    .report-sections {
        grid-template-columns: 1fr;
    }

    .deescalation-techniques {
        grid-template-columns: 1fr;
    }

    .practices-grid {
        grid-template-columns: 1fr;
    }
}
</style>
        </div>
    </div>

    <x-site.partials.footer />
</x-site.layout>
