@extends('layouts.frontend')

@section('title', 'Contact Us - Frost')

@section('content')
<!-- Page Header -->
<section class="page-header py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Contact</li>
                    </ol>
                </nav>
                <h1 class="page-title">Contact Us</h1>
                <p class="page-subtitle">Get in touch with our team</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mb-5">
                <div class="contact-form">
                    <h2>Send us a Message</h2>
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <select class="form-select" id="subject" required>
                                <option value="">Choose a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="support">Technical Support</option>
                                <option value="billing">Billing Question</option>
                                <option value="partnership">Partnership</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="contact-info">
                    <h3>Get in Touch</h3>
                    <div class="contact-item mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Address</h5>
                            <p>123 Education Street<br>Learning City, LC 12345</p>
                        </div>
                    </div>
                    <div class="contact-item mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Phone</h5>
                            <p>+1 (555) 123-4567</p>
                        </div>
                    </div>
                    <div class="contact-item mb-4">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Email</h5>
                            <p>info@frost.edu</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h5>Office Hours</h5>
                            <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container-fluid p-0">
        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.2412648750455!2d-73.98856368459394!3d40.74844097932847!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259a9b3117469%3A0xd134e199a405a163!2sEmpire%20State%20Building!5e0!3m2!1sen!2sus!4v1580902617152!5m2!1sen!2sus"
                width="100%"
                height="400"
                style="border:0;"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-preview py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2>Frequently Asked Questions</h2>
                <p>Quick answers to common questions</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="faq-item">
                    <h5>How do I enroll in a course?</h5>
                    <p>Simply create an account, browse our course catalog, and click "Enroll" on any course that interests you.</p>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="faq-item">
                    <h5>Are there any prerequisites?</h5>
                    <p>Prerequisites vary by course. Check the course description for specific requirements before enrolling.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="{{ route('faq') }}" class="btn btn-primary">View All FAQs</a>
            </div>
        </div>
    </div>
</section>
@endsection
