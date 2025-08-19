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
    // Privacy policy content is now directly in the HTML below
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
            <strong>Website:</strong> <a href="https://www.floridaonlinesecuritytraining.com" class="text-white">www.floridaonlinesecuritytraining.com</a>
          </div>
        </div>

        <div class="row mb-4">
          <div class="col text-white">
            <p class="lead">
              Florida Online Security Training ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains what personal information we collect, how we use it, and your choices regarding that information.
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
            <p>
              We may collect the following types of personal information when you interact with our website or services:
            </p>
            <ul class="mb-3">
              <li>Name</li>
              <li>Email address</li>
              <li>Phone number</li>
              <li>Mailing address</li>
              <li>Course enrollment and completion data</li>
              <li>Payment details (processed securely by third-party processors)</li>
            </ul>
            <p>
              We also collect usage data via cookies and analytics tools to improve site functionality and user experience.
            </p>
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
              We use your personal information to:
            </p>
            <ul class="mb-3">
              <li>Process course enrollments and payments</li>
              <li>Communicate with you regarding your training, support, and promotions</li>
              <li>Improve our website and services</li>
              <li>Comply with legal obligations</li>
            </ul>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">3. Who We Share Your Information With</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p><strong>We do not sell or rent your personal information to third parties.</strong></p>
            <p>We may share your information with:</p>
            <ul class="mb-3">
              <li>Secure third-party service providers who help us operate our business (e.g., payment processors, email service platforms)</li>
              <li>Government licensing bodies, if required for certification</li>
              <li>Legal authorities when required by law</li>
            </ul>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">4. SMS Communications</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p>
              By providing your phone number, you agree to receive SMS messages from Florida Online Security Training for course updates, support, or promotional offers.
            </p>
            <p>
              SMS consent is not shared with third parties or affiliates.
            </p>
            <p>
              You can opt out of SMS messages at any time by replying "STOP."
            </p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">5. Your Privacy Choices</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p>
              You may opt out of marketing communications at any time. You can also request access to or the deletion of your personal information by contacting us.
            </p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col text-white">
            <h3 class="h4 mb-3">6. Contact Us</h3>
          </div>
        </div>
        <div class="row mb-4">
          <div class="col text-white">
            <p>
              If you have questions about this Privacy Policy, please contact us at:
            </p>
            <p>
              <strong>📧 Email:</strong> <a href="mailto:support@floridaonlinesecuritytraining.com" class="text-white">support@floridaonlinesecuritytraining.com</a><br>
              <strong>📞 Phone:</strong> <a href="tel:866-540-0817" class="text-white">866-540-0817</a>
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
