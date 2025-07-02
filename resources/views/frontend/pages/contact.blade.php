@extends('layouts.app')

@php
    $pageTitle = $content['title'];
    $pageKeywords = $content['keywords'];
    $pageDescription = $content['description'];
    $googleMapUrl = RCache::SiteConfig('site_google_map_url');
    
    $contactInfo = [
        'address' => RCache::SiteConfig('site_company_address'),
        'phone' => RCache::SiteConfig('site_support_phone'),
        'email' => RCache::SiteConfig('site_support_email'),
        'hours' => RCache::SiteConfig('site_support_phone_hours'),
    ];
@endphp

@include('frontend.partials.meta-tags', [
    'title' => $pageTitle,
    'keywords' => $pageKeywords,
    'description' => $pageDescription
])

@section('styles')
<style>
    .contact-hero {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 80px 0 60px;
    }
    
    .contact-info-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .contact-info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .contact-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 24px;
        color: white;
    }
    
    .contact-icon.phone { background: linear-gradient(45deg, #28a745, #20c997); }
    .contact-icon.email { background: linear-gradient(45deg, #007bff, #6f42c1); }
    .contact-icon.location { background: linear-gradient(45deg, #dc3545, #fd7e14); }
    .contact-icon.hours { background: linear-gradient(45deg, #ffc107, #e83e8c); }
    
    .contact-form-section {
        background: #f8f9fa;
        padding: 80px 0;
    }
    
    .contact-form-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .form-floating .form-control {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: border-color 0.3s ease;
    }
    
    .form-floating .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .btn-contact {
        background: linear-gradient(45deg, #0d6efd, #6f42c1);
        border: none;
        border-radius: 25px;
        padding: 12px 40px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }
    
    .btn-contact:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(13, 110, 253, 0.3);
    }
    
    .map-container {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }
    
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 1rem;
    }
    
    .section-subtitle {
        font-size: 1.2rem;
        color: #6c757d;
        margin-bottom: 3rem;
    }
    
    .info-section {
        padding: 60px 0;
        background: white;
    }
    
    .faq-item {
        border: none;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
</style>
@endsection

@section('content')
    @include('frontend.partials.breadcrumbs')

    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Get In Touch</h1>
            <p class="lead mb-0">We're here to help with your security training needs. Reach out to us anytime!</p>
        </div>
    </section>

    <!-- Contact Information Cards -->
    <section class="info-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card contact-info-card h-100 text-center p-4">
                        <div class="contact-icon phone">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Call Us</h5>
                        <p class="mb-2 fs-5 fw-semibold text-primary">{{ $contactInfo['phone'] }}</p>
                        <small class="text-muted">{{ $contactInfo['hours'] ?: 'Monday - Friday, 8:00 AM - 6:00 PM EST' }}</small>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card contact-info-card h-100 text-center p-4">
                        <div class="contact-icon email">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Email Us</h5>
                        <p class="mb-2">
                            <a href="mailto:{{ $contactInfo['email'] }}" class="text-decoration-none">
                                {{ $contactInfo['email'] }}
                            </a>
                        </p>
                        <small class="text-muted">We'll respond within 24 hours</small>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card contact-info-card h-100 text-center p-4">
                        <div class="contact-icon location">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Visit Us</h5>
                        <p class="mb-2">{{ $contactInfo['address'] ?: 'Florida, United States' }}</p>
                        <small class="text-muted">Professional training center</small>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card contact-info-card h-100 text-center p-4">
                        <div class="contact-icon hours">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Support Hours</h5>
                        <p class="mb-2">Monday - Friday</p>
                        <small class="text-muted">8:00 AM - 6:00 PM EST<br>Emergency support available</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form & Map Section -->
    <section class="contact-form-section">
        <div class="container">
            <div class="row g-5 align-items-center">
                <!-- Contact Form -->
                <div class="col-lg-6">
                    <div class="card contact-form-card">
                        <div class="card-body p-5">
                            <h2 class="section-title h3 mb-4">Send us a Message</h2>
                            <p class="text-muted mb-4">Have questions about our security training programs? Need help with course enrollment? We're here to assist you.</p>
                            
                            <form id="contactForm" method="POST" action="{{ route('pages.contact.send') }}" class="needs-validation" novalidate>
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
                                            <label for="name">Full Name *</label>
                                            <div class="invalid-feedback">Please provide your name.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
                                            <label for="email">Email Address *</label>
                                            <div class="invalid-feedback">Please provide a valid email.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
                                            <label for="subject">Subject *</label>
                                            <div class="invalid-feedback">Please provide a subject.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="message" name="message" style="height: 120px" placeholder="Your Message" required></textarea>
                                            <label for="message">Message *</label>
                                            <div class="invalid-feedback">Please provide your message.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="privacy_agree" name="privacy_agree" required>
                                            <label class="form-check-label" for="privacy_agree">
                                                I agree to the <a href="{{ route('pages', 'privacy') }}" target="_blank" class="text-decoration-none">Privacy Policy</a> *
                                            </label>
                                            <div class="invalid-feedback">You must agree to the Privacy Policy.</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-contact w-100">
                                            <i class="fas fa-paper-plane me-2"></i>Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="col-lg-6">
                    <div class="map-container">
                        <iframe 
                            src="{{ $googleMapUrl }}" 
                            width="100%" 
                            height="500" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="info-section bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Frequently Asked Questions</h2>
                <p class="section-subtitle">Quick answers to common questions about our security training</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How do I enroll in a security training course?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Simply browse our course catalog, select the training program that meets your needs, and follow the enrollment process. Our team is available to help guide you through course selection if needed.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What payment methods do you accept?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We accept all major credit cards, PayPal, and can arrange payment plans for corporate training programs. All payments are processed securely.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How long does it take to complete a course?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Course duration varies depending on the specific training program. Most courses can be completed at your own pace within 30-90 days. Detailed time estimates are provided for each course.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Do you provide certification upon completion?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes! Upon successful completion of your training program, you'll receive a professional certificate that meets industry standards and regulatory requirements.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    // Bootstrap form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    // AJAX form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (this.checkValidity()) {
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            submitBtn.disabled = true;
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    this.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Thank you! Your message has been sent successfully.</div>';
                } else {
                    throw new Error('Form submission failed');
                }
            })
            .catch(error => {
                // Show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Sorry, there was an error sending your message. Please try again.';
                this.insertBefore(errorDiv, this.firstChild);
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
    });
</script>
@endsection
