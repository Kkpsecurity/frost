@extends('layouts.frontend')

@section('title', 'Our Team - Frost')

@section('content')
<!-- Page Header -->
<section class="page-header py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Team</li>
                    </ol>
                </nav>
                <h1 class="page-title">Our Team</h1>
                <p class="page-subtitle">Meet the experts behind Frost</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-card">
                    <div class="team-image">
                        <img src="/images/team/ceo.jpg" alt="CEO" class="img-fluid">
                    </div>
                    <div class="team-info">
                        <h4>Jane Doe</h4>
                        <p class="position">Chief Executive Officer</p>
                        <p class="bio">Jane has over 15 years of experience in educational technology and leads our vision for accessible learning.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-card">
                    <div class="team-image">
                        <img src="/images/team/cto.jpg" alt="CTO" class="img-fluid">
                    </div>
                    <div class="team-info">
                        <h4>John Smith</h4>
                        <p class="position">Chief Technology Officer</p>
                        <p class="bio">John oversees our technical infrastructure and ensures our platform runs smoothly for all users.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-card">
                    <div class="team-image">
                        <img src="/images/team/education-director.jpg" alt="Education Director" class="img-fluid">
                    </div>
                    <div class="team-info">
                        <h4>Sarah Johnson</h4>
                        <p class="position">Director of Education</p>
                        <p class="bio">Sarah designs our curriculum and works with instructors to deliver high-quality educational content.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
