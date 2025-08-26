{{-- Course Enrollment Component --}}
@props(['course' => []])

<div class="frost-secondary-bg py-5" style="min-height: calc(100vh - 200px);">
    <div class="container">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb" style="background: rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 12px 20px;">
                <li class="breadcrumb-item">
                    <a href="{{ route('courses.index') }}" class="text-info text-decoration-none">
                        <i class="fas fa-home me-1"></i>Courses
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('courses.list') }}" class="text-info text-decoration-none">Course List</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('courses.show', $course['id']) }}" class="text-info text-decoration-none">{{ $course['title'] ?? 'Course Details' }}</a>
                </li>
                <li class="breadcrumb-item active text-white" aria-current="page">Enrollment</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                {{-- Enrollment Card --}}
                <div class="card border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.95); border-radius: 15px;">
                    <div class="card-header text-center py-4" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); border-radius: 15px 15px 0 0;">
                        <h2 class="text-white mb-2">
                            <i class="{{ $course['icon'] ?? 'fas fa-shield-alt' }} me-2"></i>
                            Course Enrollment
                        </h2>
                        <p class="text-white-50 mb-0">Complete your registration for professional security training</p>
                    </div>

                    <div class="card-body p-5">
                        {{-- Course Summary --}}
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="course-summary">
                                    <div class="mb-3">
                                        <span class="badge bg-info fs-6 text-dark mb-2">{{ $course['badge'] ?? 'Security Course' }}</span>
                                    </div>
                                    <h4 class="text-dark mb-3">{{ $course['title'] ?? 'Security Training Course' }}</h4>
                                    <p class="text-muted">{{ $course['description'] ?? 'Professional security training course' }}</p>

                                    <div class="course-details mt-3">
                                        <div class="row text-sm">
                                            <div class="col-md-6">
                                                <i class="fas fa-clock text-info me-2"></i>
                                                <strong>Duration:</strong> {{ $course['duration'] ?? '3-5 Days' }}
                                            </div>
                                            <div class="col-md-6">
                                                <i class="fas fa-laptop text-info me-2"></i>
                                                <strong>Format:</strong> Hybrid (Online + Live)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="price-box p-3 rounded" style="background: rgba(52, 152, 219, 0.1); border: 2px solid #3498db;">
                                    <h5 class="text-info mb-2">Course Fee</h5>
                                    <h2 class="text-dark mb-0">${{ number_format($course['price'] ?? 0, 2) }}</h2>
                                    <small class="text-muted">USD</small>
                                </div>
                            </div>
                        </div>

                        {{-- Requirements Section --}}
                        @if(isset($course['requirements']) && count($course['requirements']) > 0)
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Prerequisites & Requirements</h6>
                                <ul class="mb-0">
                                    @foreach($course['requirements'] as $requirement)
                                        <li>{{ $requirement }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Enrollment Form --}}
                        <form method="POST" action="{{ route('courses.enroll.process', $course['id']) }}" class="mt-4">
                            @csrf

                            <div class="enrollment-confirmation p-4 rounded mb-4" style="background: rgba(40, 167, 69, 0.1); border: 1px solid #28a745;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmEnrollment" name="confirm_enrollment" required>
                                    <label class="form-check-label text-dark" for="confirmEnrollment">
                                        <strong>I confirm that I want to enroll in this course and proceed with payment</strong>
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    By checking this box, you acknowledge that you have read and agree to the course requirements and will be redirected to our secure payment processor.
                                </small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('courses.show', $course['id']) }}" class="btn btn-outline-secondary btn-lg w-100">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Course Details
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- Security Notice --}}
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="fas fa-lock me-1"></i>
                                Your payment is secured with industry-standard encryption
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Additional Information --}}
                <div class="row mt-4">
                    <div class="col-md-4 text-center mb-3">
                        <div class="info-box">
                            <i class="fas fa-certificate fa-2x text-info mb-2"></i>
                            <h6 class="text-white">State Approved</h6>
                            <small class="text-white-50">Certified training program</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="info-box">
                            <i class="fas fa-users fa-2x text-info mb-2"></i>
                            <h6 class="text-white">Expert Instructors</h6>
                            <small class="text-white-50">Industry professionals</small>
                        </div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="info-box">
                            <i class="fas fa-headset fa-2x text-info mb-2"></i>
                            <h6 class="text-white">Support Available</h6>
                            <small class="text-white-50">24/7 customer service</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
