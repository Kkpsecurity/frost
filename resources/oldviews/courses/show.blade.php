{{-- Course Details Page - Individual course information --}}

<x-site.layout :title="$content['title'] ?? 'Course Details - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Professional security training course details' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security, training, certification, course details' }}">
    </x-slot:head>

    <x-site.partials.header />

    <main class="main-page-content">
        {{-- Breadcrumb Section --}}
        <div class="frost-secondary-bg py-4">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 frost-breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('pages', ['page' => '']) }}" class="text-white-50">
                                        <i class="fas fa-home me-1"></i>Home
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('courses.index') }}" class="text-white-50">Courses</a>
                                </li>
                                <li class="breadcrumb-item active text-white" aria-current="page">
                                    {{ $course['title'] ?? 'Course Details' }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        {{-- Course Details Section --}}
        <div class="bg-white py-5">
            <div class="container">
                <div class="row">
                    {{-- Main Content --}}
                    <div class="col-lg-8 col-md-12">
                        <article class="course-details-wrapper">
                            {{-- Course Header --}}
                            <div class="course-details-header mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="course-icon-large me-3">
                                        <i class="{{ $course['icon'] ?? 'fas fa-shield-alt' }} fa-3x text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="course-badge-large mb-2">
                                            <span class="badge bg-primary fs-6">{{ $course['badge'] ?? 'Security Course' }}</span>
                                            @if($course['popular'] ?? false)
                                                <span class="badge bg-warning text-dark ms-2">Most Popular</span>
                                            @endif
                                        </div>
                                        <h1 class="course-title-large mb-2">{{ $course['title'] ?? 'Course Title' }}</h1>
                                        <p class="course-type-large text-muted">{{ $course['type'] ?? 'Professional Training' }}</p>
                                    </div>
                                </div>

                                {{-- Course Meta Information --}}
                                <div class="course-meta-details row g-3 p-3 bg-light rounded">
                                    <div class="col-md-3 col-sm-6">
                                        <div class="meta-item text-center">
                                            <i class="fas fa-clock text-primary mb-2"></i>
                                            <div class="meta-label">Duration</div>
                                            <div class="meta-value">{{ $course['duration'] ?? '3-5 Days' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="meta-item text-center">
                                            <i class="fas fa-laptop text-primary mb-2"></i>
                                            <div class="meta-label">Format</div>
                                            <div class="meta-value">{{ $course['format'] ?? 'Online + Live' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="meta-item text-center">
                                            <i class="fas fa-certificate text-primary mb-2"></i>
                                            <div class="meta-label">Certification</div>
                                            <div class="meta-value">{{ $course['certification'] ?? 'State Approved' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="meta-item text-center">
                                            <i class="fas fa-users text-primary mb-2"></i>
                                            <div class="meta-label">Class Size</div>
                                            <div class="meta-value">{{ $course['classSize'] ?? '12 Students Max' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Course Description --}}
                            <div class="course-content-section mb-5">
                                <h3 class="section-title">Course Overview</h3>
                                <div class="course-description-full">
                                    <p class="lead">{{ $course['description'] ?? 'Professional security training course designed to prepare you for success in the security industry.' }}</p>

                                    @if(isset($course['fullDescription']))
                                        {!! $course['fullDescription'] !!}
                                    @else
                                        <p>This comprehensive training program is designed to provide you with the knowledge, skills, and certification needed to excel in the security industry. Our expert instructors bring real-world experience and ensure you receive practical, applicable training.</p>

                                        <p>Upon successful completion of this course, you will receive a state-approved certificate that qualifies you to work in professional security roles. We maintain small class sizes to ensure personalized attention and hands-on learning opportunities.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- What You'll Learn Section --}}
                            <div class="course-curriculum-section mb-5">
                                <h3 class="section-title">What You'll Learn</h3>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <ul class="feature-list-detailed">
                                            @if(isset($course['features']) && is_array($course['features']))
                                                @foreach(array_slice($course['features'], 0, ceil(count($course['features'])/2)) as $feature)
                                                    <li>
                                                        <i class="fas fa-check-circle text-success me-3"></i>
                                                        {{ $feature }}
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="col-lg-6">
                                        <ul class="feature-list-detailed">
                                            @if(isset($course['features']) && is_array($course['features']))
                                                @foreach(array_slice($course['features'], ceil(count($course['features'])/2)) as $feature)
                                                    <li>
                                                        <i class="fas fa-check-circle text-success me-3"></i>
                                                        {{ $feature }}
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Course Requirements Section --}}
                            <div class="course-requirements-section mb-5">
                                <h3 class="section-title">Requirements</h3>
                                <div class="requirements-content">
                                    <ul class="requirements-list">
                                        @if(isset($course['requirements']))
                                            @foreach($course['requirements'] as $requirement)
                                                <li><i class="fas fa-exclamation-circle text-warning me-2"></i>{{ $requirement }}</li>
                                            @endforeach
                                        @else
                                            <li><i class="fas fa-exclamation-circle text-warning me-2"></i>Must be 18 years or older</li>
                                            <li><i class="fas fa-exclamation-circle text-warning me-2"></i>Valid government-issued photo ID required</li>
                                            <li><i class="fas fa-exclamation-circle text-warning me-2"></i>Background check may be required</li>
                                            <li><i class="fas fa-exclamation-circle text-warning me-2"></i>High school diploma or equivalent preferred</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>

                            {{-- Course Schedule Section --}}
                            @if(isset($course['schedule']))
                            <div class="course-schedule-section mb-5">
                                <h3 class="section-title">Upcoming Sessions</h3>
                                <div class="schedule-content">
                                    @foreach($course['schedule'] as $session)
                                        <div class="schedule-item p-3 border rounded mb-3">
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <div class="schedule-date">
                                                        <i class="fas fa-calendar text-primary me-2"></i>
                                                        {{ $session['date'] ?? 'TBD' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="schedule-time">
                                                        <i class="fas fa-clock text-primary me-2"></i>
                                                        {{ $session['time'] ?? '9:00 AM - 5:00 PM' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="schedule-location">
                                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                        {{ $session['location'] ?? 'Online/Hybrid' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="schedule-availability text-end">
                                                        <span class="badge {{ ($session['available'] ?? true) ? 'bg-success' : 'bg-danger' }}">
                                                            {{ ($session['available'] ?? true) ? 'Available' : 'Full' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </article>
                    </div>

                    {{-- Sidebar --}}
                    <div class="col-lg-4 col-md-12">
                        <div class="course-sidebar">
                            {{-- Pricing Card --}}
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="price-display mb-3">
                                        <span class="price-large">${{ number_format($course['price'] ?? 299, 2) }}</span>
                                        <span class="price-currency">USD</span>
                                    </div>
                                    <p class="text-muted mb-4">Complete training package with certification</p>

                                    <div class="d-grid gap-2">
                                        <a href="{{ $course['enrollUrl'] ?? '/enroll' }}" class="btn btn-primary btn-lg">
                                            <i class="fas fa-credit-card me-2"></i>Enroll Now
                                        </a>
                                        <a href="#" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#contactModal">
                                            <i class="fas fa-phone me-2"></i>Contact Us
                                        </a>
                                    </div>

                                    <div class="enrollment-features mt-4">
                                        <div class="feature-item d-flex align-items-center mb-2">
                                            <i class="fas fa-shield-check text-success me-2"></i>
                                            <small>Money-back guarantee</small>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-2">
                                            <i class="fas fa-certificate text-success me-2"></i>
                                            <small>State-approved certification</small>
                                        </div>
                                        <div class="feature-item d-flex align-items-center">
                                            <i class="fas fa-headset text-success me-2"></i>
                                            <small>24/7 student support</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Quick Info Card --}}
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Course Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-item d-flex justify-content-between mb-2">
                                        <span class="info-label">Duration:</span>
                                        <span class="info-value">{{ $course['duration'] ?? '3-5 Days' }}</span>
                                    </div>
                                    <div class="info-item d-flex justify-content-between mb-2">
                                        <span class="info-label">Format:</span>
                                        <span class="info-value">{{ $course['format'] ?? 'Hybrid' }}</span>
                                    </div>
                                    <div class="info-item d-flex justify-content-between mb-2">
                                        <span class="info-label">Level:</span>
                                        <span class="info-value">{{ $course['level'] ?? 'Beginner' }}</span>
                                    </div>
                                    <div class="info-item d-flex justify-content-between mb-2">
                                        <span class="info-label">Language:</span>
                                        <span class="info-value">{{ $course['language'] ?? 'English' }}</span>
                                    </div>
                                    <div class="info-item d-flex justify-content-between">
                                        <span class="info-label">Students:</span>
                                        <span class="info-value">{{ $course['studentsEnrolled'] ?? '150+' }} enrolled</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Related Courses --}}
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Related Courses</h5>
                                </div>
                                <div class="card-body">
                                    <div class="related-course-item mb-3">
                                        <h6 class="mb-1">
                                            <a href="#" class="text-decoration-none">Advanced Security Protocols</a>
                                        </h6>
                                        <p class="small text-muted mb-1">Professional development course</p>
                                        <span class="badge bg-primary">$399</span>
                                    </div>
                                    <div class="related-course-item mb-3">
                                        <h6 class="mb-1">
                                            <a href="#" class="text-decoration-none">Firearms Safety & Training</a>
                                        </h6>
                                        <p class="small text-muted mb-1">Specialized certification</p>
                                        <span class="badge bg-primary">$249</span>
                                    </div>
                                    <div class="related-course-item">
                                        <h6 class="mb-1">
                                            <a href="#" class="text-decoration-none">Emergency Response Training</a>
                                        </h6>
                                        <p class="small text-muted mb-1">Crisis management skills</p>
                                        <span class="badge bg-primary">$199</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-site.partials.footer />
</x-site.layout>
