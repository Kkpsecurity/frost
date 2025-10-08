@extends('layouts.frontend')

@section('title', 'About Us - Frost')

@section('content')
<!-- Page Header -->
<section class="page-header py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">About</li>
                    </ol>
                </nav>
                <h1 class="page-title">About Frost</h1>
                <p class="page-subtitle">Learn more about our mission and vision</p>
            </div>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="about-content py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <h2>Our Mission</h2>
                <p>At Frost, we believe that education should be accessible, engaging, and effective. Our mission is to provide a comprehensive learning management system that empowers educators and students to achieve their full potential.</p>

                <h3>What We Offer</h3>
                <ul class="feature-list">
                    <li><i class="fas fa-check text-primary"></i> Interactive course content</li>
                    <li><i class="fas fa-check text-primary"></i> Real-time progress tracking</li>
                    <li><i class="fas fa-check text-primary"></i> Collaborative learning tools</li>
                    <li><i class="fas fa-check text-primary"></i> Mobile-friendly platform</li>
                    <li><i class="fas fa-check text-primary"></i> Expert instructor support</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="about-image">
                    <img src="/images/about-us.jpg" alt="About Frost" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section bg-light py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">10,000+</div>
                    <div class="stat-label">Students</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Courses</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Instructors</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Success Rate</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-preview py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2>Meet Our Team</h2>
                <p>Dedicated professionals committed to your success</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-card text-center">
                    <img src="/images/team/member-1.jpg" alt="Team Member" class="team-image mb-3">
                    <h4>Jane Doe</h4>
                    <p class="text-muted">Chief Technology Officer</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-card text-center">
                    <img src="/images/team/member-2.jpg" alt="Team Member" class="team-image mb-3">
                    <h4>John Smith</h4>
                    <p class="text-muted">Head of Education</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-card text-center">
                    <img src="/images/team/member-3.jpg" alt="Team Member" class="team-image mb-3">
                    <h4>Sarah Johnson</h4>
                    <p class="text-muted">Lead Designer</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <a href="{{ route('team') }}" class="btn btn-primary">View Full Team</a>
            </div>
        </div>
    </div>
</section>
@endsection
