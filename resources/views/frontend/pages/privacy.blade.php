@extends('layouts.app')

@section('styles')
    <style>
        .privacy-policy-page .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .privacy-policy-page h2,
        .privacy-policy-page h3 {
            font-size: 24px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        .privacy-policy-page h3.h4 {
            font-size: 20px;
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 15px;
            color: #ffffff;
        }

        .privacy-policy-page h5 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #e8f4fd;
        }

        .privacy-policy-page p {
            font-size: 16px;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .privacy-policy-page .lead {
            font-size: 18px;
            font-weight: 400;
            line-height: 1.7;
        }

        .privacy-policy-page .container {
            margin: 0 auto;
            padding: 30px;
        }

        .privacy-policy-page ul {
            padding-left: 20px;
            margin-bottom: 15px;
        }

        .privacy-policy-page ul li {
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .privacy-policy-page .alert-info {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .privacy-policy-page .contact-info {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .privacy-policy-page .contact-info a {
            text-decoration: none;
        }

        .privacy-policy-page .contact-info a:hover {
            text-decoration: underline;
        }

        .privacy-policy-page .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .privacy-policy-page .mb-3 {
            margin-bottom: 1rem !important;
        }

        .privacy-policy-page .mb-2 {
            margin-bottom: 0.75rem !important;
        }

        .privacy-policy-page .mt-3 {
            margin-top: 1.5rem !important;
        }
    </style>
@stop

@php
    $privacyPolicyContent = [
        [
            'heading' => 'Privacy Policy',
            'content' => 'At FloridaOnlineSecurityTraining.com, we value the privacy of our visitors and customers. This Privacy Policy explains how we collect, use, and share your personal information when you visit our website or use our services.'
        ],
        [
            'heading' => 'Information We Collect',
            'content' => 'We may collect personal information such as your name, email address, phone number, and payment information when you register for our services, purchase our products, or contact us through our website. We may also collect non-personal information such as your browser type, IP address, and device type.'
        ],
        [
            'heading' => 'How We Use Your Information',
            'content' => 'We use your personal information to provide you with our services, process your orders, respond to your inquiries, and send you promotional emails. We may also use your non-personal information to analyze how visitors use our website and improve our services.'
        ],
        [
            'heading' => 'Information Sharing',
            'content' => 'We may share your personal information with third-party service providers such as payment processors and shipping providers to fulfill your orders and process your payments. We may also share your information with marketing platforms to send you promotional emails.'
        ],
        [
            'heading' => 'Cookies',
            'content' => 'We may use cookies and similar technologies to collect non-personal information about your browsing behavior and preferences. You can choose to disable cookies through your browser settings, but some features of our website may not work properly.'
        ],
        [
            'heading' => 'Security',
            'content' => 'We take reasonable measures to protect your personal information from unauthorized access, disclosure, and misuse. However, no security system is foolproof, and we cannot guarantee the security of your information.'
        ],
        [
            'heading' => 'Your Rights',
            'content' => 'You have the right to access, modify, and delete your personal information that we hold. You can also opt-out of receiving promotional emails from us by clicking the "unsubscribe" link in the email.'
        ],
        [
            'heading' => 'Changes to This Policy',
            'content' => 'We may update this Privacy Policy from time to time, and we will post the updated policy on our website. We encourage you to review this policy periodically to stay informed about how we collect, use, and share your information.'
        ],
        [
            'heading' => 'Contact Us',
            'content' => 'If you have any questions or concerns about this Privacy Policy, please contact us at support@FloridaOnlineSecurityTraining.com.'
        ],
    ];
@endphp

@section('content')

    @include('frontend.partials.breadcrumbs')

    <div class="terms-area frost-primary-bg area-padding privacy-policy-page">
      <div class="container">

        <div class="row">
          <div class="col">
            <h2 class="text-white">
              Privacy Policy
            </h2>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col text-white">
            <strong>Effective Date:</strong> June 1, 2025<br>
            <strong>Last Updated:</strong> June 26, 2025
          </div>
        </div>
        <div class="row mb-3">
          <div class="col text-white">
            <strong>Website:</strong> <a href="https://www.floridaonlinesecuritytraining.com" class="text-white">www.floridaonlinesecuritytraining.com</a>
          </div>
        </div>

        <div class="row mb-4">
          <div class="col text-white">
            <p class="lead">
              Florida Online Security Training ("we," "our," or "us") is committed to protecting your privacy and maintaining the confidentiality of your personal information. This Privacy Policy describes how we collect, use, disclose, and protect your information when you visit our website or use our services.
            </p>
            <p>
              By using our website or services, you agree to the collection and use of information in accordance with this Privacy Policy. If you do not agree with our practices, please do not use our services.
            </p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">1. Information We Collect</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <h5 class="mb-2">Personal Information</h5>
            <p>
              We collect personal information that you voluntarily provide when you:
            </p>
            <ul class="mb-3">
              <li>Register for an account or enroll in courses</li>
              <li>Make purchases or process payments</li>
              <li>Contact customer support</li>
              <li>Subscribe to newsletters or marketing communications</li>
              <li>Participate in surveys or feedback forms</li>
            </ul>
            <p>This may include:</p>
            <ul class="mb-3">
              <li><strong>Contact Information:</strong> Name, email address, phone number, mailing address</li>
              <li><strong>Account Information:</strong> Username, password, and security questions</li>
              <li><strong>Educational Data:</strong> Course enrollment, progress, completion records, certificates, and assessment results</li>
              <li><strong>Payment Information:</strong> Credit card details, billing address (processed securely through third-party payment processors)</li>
              <li><strong>Professional Information:</strong> Job title, employer, license numbers (where applicable)</li>
            </ul>
            
            <h5 class="mb-2">Automatically Collected Information</h5>
            <p>
              When you visit our website, we automatically collect certain technical information:
            </p>
            <ul class="mb-3">
              <li><strong>Device Information:</strong> IP address, browser type and version, operating system, device type</li>
              <li><strong>Usage Data:</strong> Pages visited, time spent on pages, click-through rates, referral sources</li>
              <li><strong>Cookies and Tracking:</strong> Session data, preferences, and analytics information</li>
            </ul>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">2. How We Use Your Information</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p>
              We use the information we collect for legitimate business purposes, including:
            </p>
            <h5 class="mb-2">Service Delivery</h5>
            <ul class="mb-3">
              <li>Processing course enrollments and managing your account</li>
              <li>Delivering training content and tracking your progress</li>
              <li>Generating certificates and completion records</li>
              <li>Processing payments and maintaining transaction records</li>
            </ul>
            
            <h5 class="mb-2">Communication</h5>
            <ul class="mb-3">
              <li>Sending course-related notifications and updates</li>
              <li>Providing customer support and responding to inquiries</li>
              <li>Delivering newsletters and promotional materials (with your consent)</li>
              <li>Sending important service announcements and policy updates</li>
            </ul>
            
            <h5 class="mb-2">Business Operations</h5>
            <ul class="mb-3">
              <li>Analyzing website usage to improve user experience</li>
              <li>Conducting research and development for new services</li>
              <li>Maintaining security and preventing fraud</li>
              <li>Complying with legal and regulatory requirements</li>
            </ul>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">3. Information Sharing and Disclosure</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <div class="alert alert-info bg-primary border-0 text-white mb-3">
              <strong>We do NOT sell, rent, or trade your personal information to third parties for marketing purposes.</strong>
            </div>
            
            <p>We may share your information only in the following circumstances:</p>
            
            <h5 class="mb-2">Service Providers</h5>
            <ul class="mb-3">
              <li><strong>Payment Processors:</strong> Secure third-party services that handle payment transactions</li>
              <li><strong>Cloud Hosting:</strong> Trusted providers that host our website and store data securely</li>
              <li><strong>Email Services:</strong> Platforms that help us send course notifications and support communications</li>
              <li><strong>Analytics Tools:</strong> Services that help us understand website usage and improve functionality</li>
            </ul>
            
            <h5 class="mb-2">Legal and Regulatory Requirements</h5>
            <ul class="mb-3">
              <li><strong>Licensing Authorities:</strong> Government agencies that require certification records for security training compliance</li>
              <li><strong>Legal Process:</strong> When required by law, court order, or government investigation</li>
              <li><strong>Safety and Security:</strong> To protect our rights, property, or safety, or that of others</li>
            </ul>
            
            <h5 class="mb-2">Business Transfers</h5>
            <p>
              In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of the business transaction, with appropriate notice provided.
            </p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">4. Cookies and Tracking Technologies</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p>
              We use cookies and similar tracking technologies to enhance your browsing experience and improve our services.
            </p>
            
            <h5 class="mb-2">Types of Cookies We Use</h5>
            <ul class="mb-3">
              <li><strong>Essential Cookies:</strong> Required for basic website functionality and security</li>
              <li><strong>Performance Cookies:</strong> Help us analyze website usage and improve performance</li>
              <li><strong>Functional Cookies:</strong> Remember your preferences and settings</li>
              <li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements (with your consent)</li>
            </ul>
            
            <h5 class="mb-2">Managing Cookie Preferences</h5>
            <p>
              You can control cookies through your browser settings. However, disabling certain cookies may limit some website functionality. Most browsers allow you to:
            </p>
            <ul class="mb-3">
              <li>View and delete cookies</li>
              <li>Block cookies from specific sites</li>
              <li>Block third-party cookies</li>
              <li>Clear all cookies when you close the browser</li>
            </ul>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">5. SMS and Email Communications</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <h5 class="mb-2">SMS Communications</h5>
            <p>
              By providing your phone number, you consent to receive SMS messages from Florida Online Security Training for:
            </p>
            <ul class="mb-3">
              <li>Course enrollment confirmations and updates</li>
              <li>Important deadline reminders</li>
              <li>Customer support communications</li>
              <li>Optional promotional offers (with separate consent)</li>
            </ul>
            <p class="mb-3">
              <strong>Opt-Out:</strong> You can stop receiving SMS messages at any time by replying "STOP" to any message. Standard message and data rates may apply.
            </p>
            
            <h5 class="mb-2">Email Communications</h5>
            <p>
              We send emails for service-related purposes and marketing (with your consent). You can unsubscribe from marketing emails using the "unsubscribe" link in each email, but you will continue to receive essential service communications.
            </p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">6. Data Security</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p>
              We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction:
            </p>
            <ul class="mb-3">
              <li><strong>Encryption:</strong> All sensitive data is encrypted in transit and at rest</li>
              <li><strong>Access Controls:</strong> Limited access to personal information on a need-to-know basis</li>
              <li><strong>Regular Security Audits:</strong> Ongoing assessment of our security practices</li>
              <li><strong>Secure Payment Processing:</strong> PCI DSS compliant payment handling</li>
              <li><strong>Employee Training:</strong> Regular privacy and security training for all staff</li>
            </ul>
            <p>
              While we strive to protect your information, no method of transmission over the internet or electronic storage is 100% secure. We cannot guarantee absolute security but are committed to maintaining industry-standard protections.
            </p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">7. Your Privacy Rights</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p>
              You have the following rights regarding your personal information:
            </p>
            <ul class="mb-3">
              <li><strong>Access:</strong> Request a copy of the personal information we hold about you</li>
              <li><strong>Correction:</strong> Update or correct inaccurate personal information</li>
              <li><strong>Deletion:</strong> Request deletion of your personal information (subject to legal requirements)</li>
              <li><strong>Portability:</strong> Receive your data in a structured, commonly used format</li>
              <li><strong>Restriction:</strong> Request limitation of processing in certain circumstances</li>
              <li><strong>Objection:</strong> Object to processing based on legitimate interests</li>
              <li><strong>Withdraw Consent:</strong> Withdraw consent for marketing communications or data processing</li>
            </ul>
            <p>
              To exercise these rights, please contact us using the information provided below. We will respond to your request within 30 days.
            </p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">8. Data Retention</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p>
              We retain your personal information only as long as necessary to fulfill the purposes outlined in this Privacy Policy:
            </p>
            <ul class="mb-3">
              <li><strong>Account Information:</strong> Retained while your account is active and for 7 years after account closure</li>
              <li><strong>Course Records:</strong> Maintained for 10 years to comply with training certification requirements</li>
              <li><strong>Payment Information:</strong> Retained for 7 years for tax and accounting purposes</li>
              <li><strong>Marketing Data:</strong> Deleted within 30 days of unsubscribing</li>
              <li><strong>Support Communications:</strong> Retained for 3 years for quality assurance</li>
            </ul>
            <p>
              When information is no longer needed, we securely delete or anonymize it in accordance with our data retention schedule.
            </p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">9. Children's Privacy</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p>
              Our services are not intended for children under 18 years of age. We do not knowingly collect personal information from children under 18. If you believe we have inadvertently collected information from a child under 18, please contact us immediately, and we will take steps to delete such information.
            </p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">10. Changes to This Privacy Policy</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p>
              We may update this Privacy Policy periodically to reflect changes in our practices, services, or legal requirements. When we make changes:
            </p>
            <ul class="mb-3">
              <li>We will post the updated policy on our website</li>
              <li>We will update the "Last Updated" date at the top of this policy</li>
              <li>For material changes, we will provide additional notice via email or website notification</li>
              <li>Continued use of our services after changes constitutes acceptance of the updated policy</li>
            </ul>
            <p>
              We encourage you to review this Privacy Policy periodically to stay informed about how we protect your information.
            </p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">11. Contact Information</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p>
              If you have any questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:
            </p>
            
            <div class="contact-info bg-secondary p-3 rounded">
              <h5 class="mb-3">Florida Online Security Training</h5>
              <p class="mb-2">
                <strong>📧 Email:</strong> <a href="mailto:privacy@floridaonlinesecuritytraining.com" class="text-white">privacy@floridaonlinesecuritytraining.com</a>
              </p>
              <p class="mb-2">
                <strong>📞 Phone:</strong> <a href="tel:866-540-0817" class="text-white">866-540-0817</a>
              </p>
              <p class="mb-2">
                <strong>🕒 Business Hours:</strong> Monday - Friday, 8:00 AM - 6:00 PM EST
              </p>
              <p class="mb-0">
                <strong>📍 Address:</strong> [Company Address - To be updated with actual address]
              </p>
            </div>
            
            <p class="mt-3">
              For privacy-related requests, please include "Privacy Request" in your subject line and provide specific details about your inquiry. We will respond to all privacy requests within 30 business days.
            </p>
          </div>
        </div>

      </div>
    </div>

{{--
    <div class="terms-area frost-primary-bg area-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="section-headline text-center">
                        <h2 class="text-white">Privacy Statement</h2>
                        <p class="text-white-50">We prioritize safeguarding your privacy</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="company-terms">
                        @foreach ($privacyPolicyContent as $index => $term)
                            <div class="single-terms">
                                <h4><span class="number">{{ $index + 1 }}.</span> <span
                                        class="condition-text">{{ $term['heading'] }}</span></h4>
                                <p>{{ $term['content'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
--}}

@stop
