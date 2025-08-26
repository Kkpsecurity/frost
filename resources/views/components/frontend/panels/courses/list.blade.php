{{-- Courses Panel - Product-style listing for security training courses --}}
@props(['courses' => collect()])

<div class="frost-secondary-bg py-5">
    <div class="container courses-container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="text-white">Available Security Courses</h2>
                <h5 class="text-white-50">Professional security training courses designed to help you succeed in the security industry</h5>
            </div>
        </div>

        <div class="row" id="coursesContainer">
            @forelse($courses as $course)
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="course-card h-100">
                        <div class="course-header">
                            <div class="course-icon">
                                <i class="{{ strpos($course->title, 'G28') !== false || strpos($course->title, "Class 'G'") !== false ? 'fas fa-shield-alt' : 'fas fa-user-shield' }}"></i>
                            </div>
                            <div class="course-class-badge">{{ strpos($course->title, 'G28') !== false || strpos($course->title, "Class 'G'") !== false ? 'CLASS G' : 'CLASS D' }}</div>
                            <div class="course-type">{{ strpos($course->title, 'G28') !== false || strpos($course->title, "Class 'G'") !== false ? 'Armed Security' : 'Unarmed Security' }}</div>
                        </div>
                        <div class="course-body">
                            <h4 class="course-title">{{ $course->title_long ?? $course->title }}</h4>
                            <p class="course-description">
                                @if(strpos($course->title, 'G28') !== false || strpos($course->title, "Class 'G'") !== false)
                                    Essential training for armed security professionals with firearms certification. This course covers firearms safety, legal requirements, use of force protocols, and professional responsibilities for armed security officers.
                                @else
                                    Comprehensive training for unarmed security professionals and private investigators. Learn surveillance techniques, report writing, legal boundaries, and professional conduct standards.
                                @endif
                            </p>

                            <div class="course-features">
                                <h6>What You'll Learn:</h6>
                                <ul class="feature-list">
                                    @if(strpos($course->title, 'G28') !== false || strpos($course->title, "Class 'G'") !== false)
                                        <li><i class="fas fa-check text-success me-2"></i>Firearms Training & Certification</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Legal Requirements & Regulations</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Use of Force Protocols</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Professional Responsibilities</li>
                                        <li><i class="fas fa-check text-success me-2"></i>State Exam Preparation</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Certificate Upon Completion</li>
                                    @else
                                        <li><i class="fas fa-check text-success me-2"></i>Surveillance Techniques</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Professional Report Writing</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Legal Boundaries & Ethics</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Communication Skills</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Emergency Procedures</li>
                                        <li><i class="fas fa-check text-success me-2"></i>State Certification</li>
                                    @endif
                                </ul>
                            </div>

                            <div class="course-meta">
                                <span class="duration">
                                    <i class="fas fa-clock me-2"></i>
                                    @if($course->total_minutes)
                                        {{ ceil($course->total_minutes / 60 / 8) }} Days
                                    @elseif(strpos($course->title, 'G28') !== false || strpos($course->title, "Class 'G'") !== false)
                                        3 Days
                                    @elseif(strpos($course->title, '10 Nights') !== false || strpos($course->title, 'Nt') !== false)
                                        10 Nights
                                    @else
                                        5 Days
                                    @endif
                                </span>
                                <span class="course-format"><i class="fas fa-laptop me-2"></i>Online + Live</span>
                            </div>
                        </div>
                        <div class="course-footer">
                            <div class="course-price">
                                <span class="price">${{ number_format($course->price, 2) }}</span>
                                <span class="price-note">USD</span>
                            </div>
                            <div class="course-actions">
                                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-primary btn-sm me-2">Learn More</a>
                                <a href="{{ route('courses.enroll', $course->id) }}" class="btn btn-primary btn-sm">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-white-50 mb-3"></i>
                        <h4 class="text-white">No Courses Available</h4>
                        <p class="text-white-50">Please check back later for available courses.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Call to Action Section --}}
        <div class="row mt-5">
            <div class="col-12 text-center">
                <p class="text-white-50 mb-4">Need help choosing the right course? Our training specialists are here to help.</p>
                <a href="#" class="btn btn-primary btn-lg me-3">Contact Us</a>
                <a href="#" class="btn btn-outline-light btn-lg">View All Courses</a>
            </div>
        </div>
    </div>
</div>

