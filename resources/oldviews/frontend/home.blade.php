@extends('layouts.frontend')

@section('title', 'Home - Frost Security Training')

@section('content')
<!-- Hero Section with Course Showcase -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-8">
                <div class="hero-content mb-5">
                    <h1 class="hero-title">Welcome to The Security Training Group</h1>
                    <p class="hero-subtitle">Florida Class D Security License and Armed Statewide Firearms Class G License</p>
                </div>

                <!-- Course Cards -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="course-card">
                            <div class="course-header">
                                <div class="course-icon">
                                    <div class="monitor-display">
                                        <div class="course-badge">
                                            <span class="badge-text">Unarmed</span>
                                            <span class="badge-text">Security Officer</span>
                                            <span class="class-text">Class D</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="course-body">
                                <h3 class="course-title">Florida Class 'D' 40 Hour (5 Days)</h3>
                                <p class="course-description">Comprehensive online training program. Flexible schedule.</p>
                                <div class="course-price">$99.00</div>
                                <div class="course-actions">
                                    <button class="btn btn-outline-primary me-2">MORE DETAIL</button>
                                    <button class="btn btn-success">ENROLLED</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="course-card">
                            <div class="course-header">
                                <div class="course-icon">
                                    <div class="monitor-display">
                                        <div class="course-badge">
                                            <span class="badge-text">Armed</span>
                                            <span class="badge-text">Security Officer</span>
                                            <span class="class-text">Class G</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="course-body">
                                <h3 class="course-title">Florida Class 'G' 28 Hour</h3>
                                <p class="course-description">Combination of online learning and in-person range training.</p>
                                <div class="course-price">$100.00</div>
                                <div class="course-actions">
                                    <button class="btn btn-primary me-2">MORE DETAIL</button>
                                    <button class="btn btn-success">ENROLLED</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="user-profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <span class="avatar-initials">RC</span>
                        </div>
                        <h4 class="profile-name">Richard Clark</h4>
                        <p class="profile-email">richievc@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chat Widget -->
<div class="chat-widget">
    <div class="chat-header">
        <div class="chat-avatar">
            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23fff'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E" alt="Support">
        </div>
        <div class="chat-info">
            <span class="chat-status">Hi there, welcome to the <strong>Security Training Group</strong>, the premier security service and online training provider in Florida.</span>
        </div>
    </div>
    <div class="chat-body">
        <p>My name is Narel, I am a support chatbot.</p>
        <p>How can I help you today?</p>
        <div class="chat-options">
            <button class="chat-option-btn">I want to register for the Online Unarmed (D) License course</button>
            <button class="chat-option-btn">I want to register for the Online Armed (G) License course</button>
        </div>
    </div>
</div>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="section-title">Why Choose Frost?</h2>
                <p class="section-subtitle">Discover the features that make learning better</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4>Expert Instructors</h4>
                    <p>Learn from industry professionals with real-world experience</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4>Flexible Learning</h4>
                    <p>Study at your own pace with 24/7 access to course materials</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h4>Certifications</h4>
                    <p>Earn recognized certificates upon course completion</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section bg-primary text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2>Ready to Start Learning?</h2>
                <p class="lead">Join thousands of students already learning on Frost</p>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg">Sign Up Now</a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">Go to Dashboard</a>
                @endguest
            </div>
        </div>
    </div>
</section>
@endsection
