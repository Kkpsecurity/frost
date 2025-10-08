@extends('layouts.frontend')

@section('title', 'Reviews - Frost')

@section('content')
<!-- Page Header -->
<section class="page-header py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Reviews</li>
                    </ol>
                </nav>
                <h1 class="page-title">Student Reviews</h1>
                <p class="page-subtitle">What our students say about Frost</p>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="reviews-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="review-card">
                    <div class="review-rating">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <div class="review-content">
                        <p>"Frost has completely transformed my learning experience. The courses are well-structured and the instructors are incredibly knowledgeable."</p>
                    </div>
                    <div class="review-author">
                        <img src="/images/reviews/student-1.jpg" alt="Student" class="review-avatar">
                        <div class="author-info">
                            <h5>Sarah Martinez</h5>
                            <span class="course">Web Development Course</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="review-card">
                    <div class="review-rating">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <div class="review-content">
                        <p>"The flexibility to learn at my own pace while working full-time has been amazing. Highly recommend Frost to anyone looking to advance their career."</p>
                    </div>
                    <div class="review-author">
                        <img src="/images/reviews/student-2.jpg" alt="Student" class="review-avatar">
                        <div class="author-info">
                            <h5>Michael Chen</h5>
                            <span class="course">Data Science Bootcamp</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="review-card">
                    <div class="review-rating">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="far fa-star text-muted"></i>
                    </div>
                    <div class="review-content">
                        <p>"Great platform with excellent content. The only improvement I'd suggest is more interactive elements in some courses."</p>
                    </div>
                    <div class="review-author">
                        <img src="/images/reviews/student-3.jpg" alt="Student" class="review-avatar">
                        <div class="author-info">
                            <h5>Emily Johnson</h5>
                            <span class="course">Digital Marketing</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- More Reviews -->
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="review-card">
                    <div class="review-rating">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <div class="review-content">
                        <p>"The instructor support is outstanding. They're always available to answer questions and provide detailed feedback on assignments."</p>
                    </div>
                    <div class="review-author">
                        <img src="/images/reviews/student-4.jpg" alt="Student" class="review-avatar">
                        <div class="author-info">
                            <h5>David Wilson</h5>
                            <span class="course">Python Programming</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="review-card">
                    <div class="review-rating">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <div class="review-content">
                        <p>"I've completed three courses so far and each one has exceeded my expectations. The certificates are recognized by employers too!"</p>
                    </div>
                    <div class="review-author">
                        <img src="/images/reviews/student-5.jpg" alt="Student" class="review-avatar">
                        <div class="author-info">
                            <h5>Lisa Thompson</h5>
                            <span class="course">UI/UX Design</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="review-card">
                    <div class="review-rating">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="far fa-star text-muted"></i>
                    </div>
                    <div class="review-content">
                        <p>"Solid platform with good course material. Would love to see more advanced courses in cybersecurity."</p>
                    </div>
                    <div class="review-author">
                        <img src="/images/reviews/student-6.jpg" alt="Student" class="review-avatar">
                        <div class="author-info">
                            <h5>James Rodriguez</h5>
                            <span class="course">Network Security</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="review-stats py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">4.8</div>
                    <div class="stat-label">Average Rating</div>
                    <div class="rating-stars">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">2,847</div>
                    <div class="stat-label">Total Reviews</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">96%</div>
                    <div class="stat-label">Would Recommend</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">89%</div>
                    <div class="stat-label">Completion Rate</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
