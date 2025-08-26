{{-- Ensuring Compliance Blog Post --}}
<x-site.layout :title="'Ensuring Compliance in Security Operations'">
    <x-slot:head>
        <meta name="description" content="Essential guide to maintaining compliance in security operations, understanding regulations, and avoiding common violations.">
        <meta name="keywords" content="compliance, security operations, florida, licensing, violations, best practices">
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
                            <li class="breadcrumb-item active text-white" aria-current="page">Compliance</li>
                        </ol>
                    </nav>

                    <div class="blog-category mb-3">
                        <span class="badge bg-warning">Compliance & Licensing</span>
                    </div>

                    <h1 class="text-white mb-4">Ensuring Compliance in Security Operations: A Complete Guide</h1>

                    <div class="blog-meta d-flex justify-content-center align-items-center flex-wrap gap-4 text-white-50">
                        <span><i class="fas fa-calendar me-2"></i>July 25, 2025</span>
                        <span><i class="fas fa-user me-2"></i>Compliance Expert</span>
                        <span><i class="fas fa-clock me-2"></i>4 min read</span>
                        <span><i class="fas fa-eye me-2"></i>743 views</span>
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
                            <img src="{{ asset('assets/img/blog/compliance-operations.jpg') }}"
                                 alt="Security Compliance Operations"
                                 class="img-fluid rounded shadow-lg">
                        </div>

                        <!-- Article Content -->
                        <div class="article-content">
                            <div class="lead mb-4 text-muted">
                                Compliance in security operations is not just about following rules—it's about protecting your business, your clients, and your career. Understanding and maintaining compliance with Florida security regulations is essential for every security professional and company.
                            </div>

                            <h2 class="section-title">Why Compliance Matters</h2>
                            <p>
                                Security compliance protects everyone involved in the security industry. For security companies, compliance means avoiding fines, maintaining business licenses, and protecting reputation. For individual officers, compliance ensures career protection and legal safety.
                            </p>

                            <div class="compliance-importance">
                                <div class="importance-item legal">
                                    <i class="fas fa-gavel text-danger"></i>
                                    <div>
                                        <h4>Legal Protection</h4>
                                        <p>Compliance protects against criminal charges and civil liability in security incidents.</p>
                                    </div>
                                </div>
                                <div class="importance-item business">
                                    <i class="fas fa-briefcase text-warning"></i>
                                    <div>
                                        <h4>Business Continuity</h4>
                                        <p>Maintaining compliance ensures continuous operation and client trust.</p>
                                    </div>
                                </div>
                                <div class="importance-item reputation">
                                    <i class="fas fa-star text-success"></i>
                                    <div>
                                        <h4>Professional Reputation</h4>
                                        <p>Compliance violations can damage professional standing and career prospects.</p>
                                    </div>
                                </div>
                            </div>

                            <h2 class="section-title">Florida Security Licensing Compliance</h2>
                            <p>
                                The Florida Department of Agriculture and Consumer Services regulates all security activities in the state. Understanding these requirements is fundamental to compliance.
                            </p>

                            <div class="licensing-requirements">
                                <h3>Individual License Requirements</h3>
                                <div class="req-grid">
                                    <div class="req-card">
                                        <h4>Class D License (Unarmed)</h4>
                                        <ul class="req-list">
                                            <li><i class="fas fa-check text-success me-2"></i>Valid for 2 years</li>
                                            <li><i class="fas fa-check text-success me-2"></i>40 hours initial training</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Annual background check</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Continuing education (when required)</li>
                                        </ul>
                                    </div>
                                    <div class="req-card">
                                        <h4>Class G License (Armed)</h4>
                                        <ul class="req-list">
                                            <li><i class="fas fa-check text-success me-2"></i>Valid for 1 year</li>
                                            <li><i class="fas fa-check text-success me-2"></i>28 hours firearms training</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Annual firearms qualification</li>
                                            <li><i class="fas fa-check text-success me-2"></i>Enhanced background screening</li>
                                        </ul>
                                    </div>
                                </div>

                                <h3>Company License Requirements</h3>
                                <div class="company-requirements">
                                    <div class="company-req-item">
                                        <i class="fas fa-building text-info me-3"></i>
                                        <div>
                                            <h4>Class B License (Security Agency)</h4>
                                            <p>Required for companies providing security services, including proper insurance, bonding, and management qualifications.</p>
                                        </div>
                                    </div>
                                    <div class="company-req-item">
                                        <i class="fas fa-chalkboard-teacher text-info me-3"></i>
                                        <div>
                                            <h4>Class DI/K/M Instructor Licenses</h4>
                                            <p>Required for providing security training, with specific qualifications and ongoing requirements.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h2 class="section-title">Common Compliance Violations</h2>
                            <p>
                                Understanding common violations helps prevent costly mistakes. These are the most frequent compliance issues in Florida security operations:
                            </p>

                            <div class="violations-section">
                                <div class="violation-category">
                                    <h3><i class="fas fa-exclamation-triangle text-danger me-2"></i>Licensing Violations</h3>
                                    <div class="violation-list">
                                        <div class="violation-item">
                                            <strong>Working with Expired License:</strong>
                                            <p>Continuing to work after license expiration is a serious violation with immediate consequences.</p>
                                            <div class="penalty">Penalty: $1,000 fine + license suspension</div>
                                        </div>
                                        <div class="violation-item">
                                            <strong>Unlicensed Security Work:</strong>
                                            <p>Performing security duties without proper licensing or allowing unlicensed individuals to work.</p>
                                            <div class="penalty">Penalty: Criminal charges + civil penalties</div>
                                        </div>
                                        <div class="violation-item">
                                            <strong>Improper License Display:</strong>
                                            <p>Failing to wear or display license badge as required by law.</p>
                                            <div class="penalty">Penalty: $200-500 fine</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="violation-category">
                                    <h3><i class="fas fa-shield-alt text-warning me-2"></i>Operational Violations</h3>
                                    <div class="violation-list">
                                        <div class="violation-item">
                                            <strong>Exceeding Authority:</strong>
                                            <p>Performing duties beyond the scope of security officer authority, such as detaining without proper cause.</p>
                                            <div class="penalty">Penalty: License revocation + civil liability</div>
                                        </div>
                                        <div class="violation-item">
                                            <strong>Improper Use of Force:</strong>
                                            <p>Using excessive force or force in inappropriate situations.</p>
                                            <div class="penalty">Penalty: Criminal charges + license revocation</div>
                                        </div>
                                        <div class="violation-item">
                                            <strong>Inadequate Record Keeping:</strong>
                                            <p>Failing to maintain proper incident reports and documentation.</p>
                                            <div class="penalty">Penalty: $300-750 fine</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="violation-category">
                                    <h3><i class="fas fa-crosshairs text-danger me-2"></i>Firearms Violations (Class G)</h3>
                                    <div class="violation-list">
                                        <div class="violation-item">
                                            <strong>Expired Firearms Qualification:</strong>
                                            <p>Working armed with expired qualification or failing to requalify annually.</p>
                                            <div class="penalty">Penalty: Immediate suspension + criminal charges</div>
                                        </div>
                                        <div class="violation-item">
                                            <strong>Improper Firearm Storage:</strong>
                                            <p>Failing to secure firearms properly when off duty or during breaks.</p>
                                            <div class="penalty">Penalty: $500-1,500 fine + license suspension</div>
                                        </div>
                                        <div class="violation-item">
                                            <strong>Unauthorized Firearm Carry:</strong>
                                            <p>Carrying firearms in prohibited locations or when not on duty.</p>
                                            <div class="penalty">Penalty: Felony charges + permanent license revocation</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h2 class="section-title">Compliance Best Practices</h2>
                            <p>
                                Implementing systematic compliance practices protects against violations and ensures smooth operations:
                            </p>

                            <div class="best-practices">
                                <div class="practice-category">
                                    <h3>License Management</h3>
                                    <div class="practice-items">
                                        <div class="practice-item">
                                            <i class="fas fa-calendar-check text-success me-2"></i>
                                            <strong>Renewal Tracking:</strong> Set up automatic reminders 60 days before license expiration
                                        </div>
                                        <div class="practice-item">
                                            <i class="fas fa-file-alt text-success me-2"></i>
                                            <strong>Documentation:</strong> Maintain copies of all licenses, certifications, and training records
                                        </div>
                                        <div class="practice-item">
                                            <i class="fas fa-sync-alt text-success me-2"></i>
                                            <strong>Status Updates:</strong> Regularly verify license status through state systems
                                        </div>
                                    </div>
                                </div>

                                <div class="practice-category">
                                    <h3>Training and Education</h3>
                                    <div class="practice-items">
                                        <div class="practice-item">
                                            <i class="fas fa-book text-info me-2"></i>
                                            <strong>Continuing Education:</strong> Stay current with legal updates and industry changes
                                        </div>
                                        <div class="practice-item">
                                            <i class="fas fa-users text-info me-2"></i>
                                            <strong>Regular Training:</strong> Conduct refresher training on compliance topics
                                        </div>
                                        <div class="practice-item">
                                            <i class="fas fa-clipboard-list text-info me-2"></i>
                                            <strong>Policy Updates:</strong> Review and update company policies regularly
                                        </div>
                                    </div>
                                </div>

                                <div class="practice-category">
                                    <h3>Operational Compliance</h3>
                                    <div class="practice-items">
                                        <div class="practice-item">
                                            <i class="fas fa-eye text-warning me-2"></i>
                                            <strong>Regular Audits:</strong> Conduct internal compliance audits quarterly
                                        </div>
                                        <div class="practice-item">
                                            <i class="fas fa-phone text-warning me-2"></i>
                                            <strong>Incident Reporting:</strong> Establish clear procedures for reporting violations
                                        </div>
                                        <div class="practice-item">
                                            <i class="fas fa-shield-check text-warning me-2"></i>
                                            <strong>Quality Control:</strong> Implement supervisory oversight and accountability measures
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h2 class="section-title">Creating a Compliance Culture</h2>
                            <p>
                                Building a culture of compliance within your organization ensures that all employees understand the importance of following regulations:
                            </p>

                            <div class="culture-elements">
                                <div class="culture-element">
                                    <h4><i class="fas fa-bullhorn text-primary me-2"></i>Leadership Commitment</h4>
                                    <p>Management must demonstrate commitment to compliance through actions, policies, and resource allocation.</p>
                                </div>
                                <div class="culture-element">
                                    <h4><i class="fas fa-comments text-primary me-2"></i>Open Communication</h4>
                                    <p>Encourage employees to report compliance concerns without fear of retaliation.</p>
                                </div>
                                <div class="culture-element">
                                    <h4><i class="fas fa-award text-primary me-2"></i>Recognition Programs</h4>
                                    <p>Recognize and reward employees who demonstrate excellent compliance practices.</p>
                                </div>
                                <div class="culture-element">
                                    <h4><i class="fas fa-graduation-cap text-primary me-2"></i>Continuous Learning</h4>
                                    <p>Provide ongoing education and resources to keep employees informed about compliance requirements.</p>
                                </div>
                            </div>

                            <h2 class="section-title">Compliance Monitoring and Auditing</h2>
                            <p>
                                Regular monitoring and auditing help identify potential compliance issues before they become violations:
                            </p>

                            <div class="monitoring-framework">
                                <div class="monitoring-level">
                                    <h4>Daily Monitoring</h4>
                                    <ul>
                                        <li>License verification for all working officers</li>
                                        <li>Equipment and uniform compliance checks</li>
                                        <li>Incident report review</li>
                                        <li>Client feedback monitoring</li>
                                    </ul>
                                </div>
                                <div class="monitoring-level">
                                    <h4>Weekly Reviews</h4>
                                    <ul>
                                        <li>Performance and conduct evaluations</li>
                                        <li>Training compliance verification</li>
                                        <li>Policy adherence assessment</li>
                                        <li>Documentation review</li>
                                    </ul>
                                </div>
                                <div class="monitoring-level">
                                    <h4>Monthly Audits</h4>
                                    <ul>
                                        <li>Comprehensive compliance assessment</li>
                                        <li>License renewal tracking</li>
                                        <li>Violation trend analysis</li>
                                        <li>Corrective action plan review</li>
                                    </ul>
                                </div>
                            </div>

                            <h2 class="section-title">Responding to Compliance Issues</h2>
                            <p>
                                When compliance issues arise, quick and appropriate response is crucial:
                            </p>

                            <div class="response-steps">
                                <div class="response-step">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4>Immediate Assessment</h4>
                                        <p>Quickly evaluate the severity and scope of the compliance issue to determine immediate actions needed.</p>
                                    </div>
                                </div>
                                <div class="response-step">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4>Containment Actions</h4>
                                        <p>Take immediate steps to prevent further violations or minimize potential harm.</p>
                                    </div>
                                </div>
                                <div class="response-step">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h4>Investigation</h4>
                                        <p>Conduct thorough investigation to understand the root cause and full extent of the issue.</p>
                                    </div>
                                </div>
                                <div class="response-step">
                                    <div class="step-number">4</div>
                                    <div class="step-content">
                                        <h4>Corrective Action</h4>
                                        <p>Implement corrective measures to address the issue and prevent recurrence.</p>
                                    </div>
                                </div>
                                <div class="response-step">
                                    <div class="step-number">5</div>
                                    <div class="step-content">
                                        <h4>Follow-up</h4>
                                        <p>Monitor the effectiveness of corrective actions and make adjustments as needed.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="compliance-checklist">
                                <h3>Monthly Compliance Checklist</h3>
                                <div class="checklist-grid">
                                    <div class="checklist-section">
                                        <h4>License Verification</h4>
                                        <ul class="compliance-checks">
                                            <li><input type="checkbox" disabled> All officers have current licenses</li>
                                            <li><input type="checkbox" disabled> License badges properly displayed</li>
                                            <li><input type="checkbox" disabled> Renewal dates tracked</li>
                                            <li><input type="checkbox" disabled> Training certificates current</li>
                                        </ul>
                                    </div>
                                    <div class="checklist-section">
                                        <h4>Documentation Review</h4>
                                        <ul class="compliance-checks">
                                            <li><input type="checkbox" disabled> Incident reports complete</li>
                                            <li><input type="checkbox" disabled> Training records updated</li>
                                            <li><input type="checkbox" disabled> Personnel files current</li>
                                            <li><input type="checkbox" disabled> Insurance coverage verified</li>
                                        </ul>
                                    </div>
                                    <div class="checklist-section">
                                        <h4>Operational Compliance</h4>
                                        <ul class="compliance-checks">
                                            <li><input type="checkbox" disabled> Uniform standards met</li>
                                            <li><input type="checkbox" disabled> Equipment properly maintained</li>
                                            <li><input type="checkbox" disabled> Procedures being followed</li>
                                            <li><input type="checkbox" disabled> Client requirements met</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="conclusion-box mt-5">
                                <h3>Compliance is Everyone's Responsibility</h3>
                                <p>
                                    Maintaining compliance in security operations requires commitment from every level of the organization. From individual officers to company management, everyone plays a role in ensuring that operations meet regulatory requirements and professional standards.
                                </p>
                                <p>
                                    <strong>Remember: Compliance is not just about avoiding penalties—it's about maintaining the integrity and professionalism that makes the security industry trustworthy and effective.</strong>
                                </p>
                            </div>
                        </div>

                        <!-- Tags and Social Sharing -->
                        <div class="article-footer mt-5 pt-4 border-top">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="article-tags">
                                        <h5 class="mb-3">Tags:</h5>
                                        <span class="tag">Security Compliance</span>
                                        <span class="tag">Florida Regulations</span>
                                        <span class="tag">License Management</span>
                                        <span class="tag">Risk Management</span>
                                        <span class="tag">Best Practices</span>
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
                        <!-- Compliance Resources -->
                        <div class="sidebar-widget">
                            <h5 class="widget-title">Compliance Resources</h5>
                            <div class="compliance-resources">
                                <a href="#" class="resource-link">
                                    <i class="fas fa-file-contract text-warning me-2"></i>
                                    <div>
                                        <h6>Florida Security Statutes</h6>
                                        <small>Complete regulatory text</small>
                                    </div>
                                </a>
                                <a href="#" class="resource-link">
                                    <i class="fas fa-clipboard-check text-success me-2"></i>
                                    <div>
                                        <h6>Compliance Checklist</h6>
                                        <small>Monthly audit template</small>
                                    </div>
                                </a>
                                <a href="#" class="resource-link">
                                    <i class="fas fa-phone text-info me-2"></i>
                                    <div>
                                        <h6>Compliance Hotline</h6>
                                        <small>Report violations confidentially</small>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Important Dates -->
                        <div class="sidebar-widget dates-widget">
                            <h5 class="widget-title">Important Compliance Dates</h5>
                            <div class="date-item">
                                <div class="date">Oct 31</div>
                                <div class="date-info">
                                    <h6>License Renewal Deadline</h6>
                                    <p>Class D licenses expire</p>
                                </div>
                            </div>
                            <div class="date-item">
                                <div class="date">Dec 31</div>
                                <div class="date-info">
                                    <h6>G License Requalification</h6>
                                    <p>Annual firearms qualification due</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Contact -->
                        <div class="sidebar-widget contact-compliance">
                            <h5 class="widget-title">Need Compliance Help?</h5>
                            <p>Get expert assistance with compliance matters.</p>
                            <div class="contact-options">
                                <a href="tel:+1234567890" class="contact-option">
                                    <i class="fas fa-phone text-success me-2"></i>
                                    Call Compliance Team
                                </a>
                                <a href="mailto:compliance@example.com" class="contact-option">
                                    <i class="fas fa-envelope text-info me-2"></i>
                                    Email Questions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Compliance Specific Styles */
.compliance-importance {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin: 40px 0;
}

.importance-item {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: flex-start;
}

.importance-item i {
    font-size: 2rem;
    margin-right: 20px;
    flex-shrink: 0;
}

.importance-item h4 {
    color: var(--frost-primary-color);
    margin-bottom: 10px;
}

.licensing-requirements {
    margin: 40px 0;
}

.req-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin: 25px 0;
}

.req-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    border-top: 4px solid var(--frost-info-color);
}

.req-card h4 {
    color: var(--frost-primary-color);
    margin-bottom: 20px;
}

.req-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.req-list li {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
}

.company-requirements {
    margin: 30px 0;
}

.company-req-item {
    background: var(--frost-light-color);
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
}

.company-req-item h4 {
    color: var(--frost-primary-color);
    margin-bottom: 10px;
}

.violations-section {
    margin: 40px 0;
}

.violation-category {
    background: white;
    border-radius: 12px;
    margin-bottom: 30px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.violation-category h3 {
    background: var(--frost-light-color);
    padding: 20px 25px;
    margin-bottom: 0;
    border-left: 5px solid var(--frost-info-color);
}

.violation-list {
    padding: 0 25px 25px 25px;
}

.violation-item {
    padding: 20px 0;
    border-bottom: 1px solid var(--frost-light-color);
}

.violation-item:last-child {
    border-bottom: none;
}

.violation-item strong {
    color: var(--frost-primary-color);
    display: block;
    margin-bottom: 8px;
}

.penalty {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
    color: #dc3545;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-top: 10px;
    display: inline-block;
}

.best-practices {
    margin: 40px 0;
}

.practice-category {
    background: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.practice-category h3 {
    color: var(--frost-primary-color);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--frost-highlight-color);
}

.practice-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.practice-item {
    display: flex;
    align-items: flex-start;
    padding: 15px;
    background: var(--frost-light-color);
    border-radius: 8px;
}

.culture-elements {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin: 30px 0;
}

.culture-element {
    background: var(--frost-light-color);
    padding: 25px;
    border-radius: 12px;
    border-left: 4px solid var(--frost-info-color);
}

.culture-element h4 {
    color: var(--frost-primary-color);
    margin-bottom: 15px;
}

.monitoring-framework {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin: 30px 0;
}

.monitoring-level {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.monitoring-level h4 {
    color: var(--frost-primary-color);
    margin-bottom: 15px;
    text-align: center;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--frost-highlight-color);
}

.monitoring-level ul {
    margin: 0;
    padding-left: 20px;
}

.monitoring-level ul li {
    margin-bottom: 8px;
}

.response-steps {
    margin: 40px 0;
}

.response-step {
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

.compliance-checklist {
    background: var(--frost-light-color);
    padding: 30px;
    border-radius: 15px;
    margin: 40px 0;
}

.compliance-checklist h3 {
    color: var(--frost-primary-color);
    margin-bottom: 25px;
    text-align: center;
}

.checklist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.checklist-section {
    background: white;
    padding: 20px;
    border-radius: 10px;
}

.checklist-section h4 {
    color: var(--frost-info-color);
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.compliance-checks {
    list-style: none;
    padding: 0;
    margin: 0;
}

.compliance-checks li {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
}

.compliance-checks input[type="checkbox"] {
    margin-right: 8px;
}

.compliance-resources {
    background: white;
    border-radius: 12px;
    overflow: hidden;
}

.resource-link {
    display: flex;
    align-items: center;
    padding: 20px;
    text-decoration: none;
    color: var(--frost-dark-color);
    border-bottom: 1px solid var(--frost-light-color);
    transition: all 0.3s ease;
}

.resource-link:hover {
    background: var(--frost-light-color);
    text-decoration: none;
    color: var(--frost-info-color);
}

.resource-link:last-child {
    border-bottom: none;
}

.resource-link h6 {
    margin-bottom: 2px;
}

.dates-widget {
    background: var(--frost-primary-color);
    color: white;
    padding: 25px;
    border-radius: 12px;
}

.dates-widget .widget-title {
    color: var(--frost-highlight-color);
    border-color: var(--frost-highlight-color);
}

.date-item {
    display: flex;
    align-items: center;
    margin: 20px 0;
    padding: 15px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
}

.date {
    background: var(--frost-highlight-color);
    color: var(--frost-primary-color);
    padding: 10px 15px;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
    margin-right: 15px;
    min-width: 60px;
}

.date-info h6 {
    color: var(--frost-highlight-color);
    margin-bottom: 5px;
}

.date-info p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
}

.contact-compliance {
    background: var(--frost-light-color);
    padding: 25px;
    border-radius: 12px;
}

.contact-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.contact-option {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    background: white;
    border-radius: 8px;
    text-decoration: none;
    color: var(--frost-dark-color);
    transition: all 0.3s ease;
}

.contact-option:hover {
    background: var(--frost-info-color);
    color: white;
    text-decoration: none;
    transform: translateX(5px);
}

@media (max-width: 768px) {
    .compliance-importance {
        grid-template-columns: 1fr;
    }

    .req-grid {
        grid-template-columns: 1fr;
    }

    .culture-elements {
        grid-template-columns: 1fr;
    }

    .monitoring-framework {
        grid-template-columns: 1fr;
    }

    .checklist-grid {
        grid-template-columns: 1fr;
    }
}
</style>
        </div>
    </div>

    <x-site.partials.footer />
</x-site.layout>
