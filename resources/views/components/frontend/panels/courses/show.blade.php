@props(['course' => []])

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
    <div class="frost-secondary-bg py-5">
        <div class="container">
            <div class="row">
                {{-- Main Content --}}
                <div class="col-lg-8 col-md-12">
                    <article class="course-details-wrapper">
                        {{-- Course Header --}}
                        <div class="course-details-header mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="course-icon-large me-3">
                                    <i class="{{ $course['icon'] ?? 'fas fa-shield-alt' }} fa-3x text-info"></i>
                                </div>
                                <div>
                                    <div class="course-badge-large mb-2">
                                        <span class="badge bg-info fs-6 text-dark">{{ $course['badge'] ?? 'Security Course' }}</span>
                                        @if ($course['popular'] ?? false)
                                            <span class="badge bg-warning text-dark ms-2">Most Popular</span>
                                        @endif
                                    </div>
                                    <h1 class="course-title-large mb-2 text-white">{{ $course['title'] ?? 'Security Training Course' }}</h1>
                                    <p class="course-type-large text-white-50">
                                        {{ $course['type'] ?? 'Professional Security Training' }}</p>
                                </div>
                            </div>

                            {{-- Course Meta Information --}}
                            <div class="course-meta-details row g-3 p-3 rounded" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
                                <div class="col-md-3 col-sm-6">
                                    <div class="meta-item text-center">
                                        <i class="fas fa-clock text-info mb-2"></i>
                                        <div class="meta-label text-white-50">Duration</div>
                                        <div class="meta-value text-white">{{ $course['duration'] ?? '3-5 Days' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="meta-item text-center">
                                        <i class="fas fa-laptop text-info mb-2"></i>
                                        <div class="meta-label text-white-50">Format</div>
                                        <div class="meta-value text-white">{{ $course['format'] ?? 'Online + Live' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="meta-item text-center">
                                        <i class="fas fa-certificate text-info mb-2"></i>
                                        <div class="meta-label text-white-50">Certification</div>
                                        <div class="meta-value text-white">{{ $course['certification'] ?? 'State Approved' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="meta-item text-center">
                                        <i class="fas fa-users text-info mb-2"></i>
                                        <div class="meta-label text-white-50">Class Size</div>
                                        <div class="meta-value text-white">{{ $course['classSize'] ?? '12 Students Max' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Course Description --}}
                        <div class="course-content-section mb-5">
                            <h3 class="section-title text-white">Course Overview</h3>
                            <div class="course-description-full">
                                <p class="lead text-white">
                                    {{ $course['description'] ?? 'Professional security training course designed to prepare you for success in the security industry.' }}
                                </p>

                                @if (isset($course['fullDescription']))
                                    <div class="text-white-50">{!! $course['fullDescription'] !!}</div>
                                @else
                                    @if (str_contains(strtolower($course['badge'] ?? ''), 'd') || str_contains(strtolower($course['type'] ?? ''), 'armed'))
                                        <p class="text-white-50">This comprehensive Class D training program prepares you for a successful career as an armed security professional. You'll master firearms safety, legal protocols, crisis management, and professional responsibilities required for armed security work.</p>
                                        <p class="text-white-50">Our expert instructors combine decades of law enforcement and private security experience with hands-on training scenarios. You'll learn critical decision-making skills, proper use of force protocols, and emergency response procedures.</p>
                                        <p class="text-white-50">Upon successful completion, you'll receive state-approved certification qualifying you to work as an armed security officer in Florida and reciprocal states. Small class sizes ensure personalized instruction and maximum skill development.</p>
                                    @else
                                        <p class="text-white-50">This comprehensive Class G training program provides essential skills for unarmed security professionals and private investigators. You'll master surveillance techniques, professional communication, legal boundaries, and ethical conduct standards.</p>
                                        <p class="text-white-50">Our experienced instructors emphasize practical application through real-world scenarios and case studies. You'll develop professional report writing skills, learn de-escalation techniques, and understand your legal authority and limitations.</p>
                                        <p class="text-white-50">Upon completion, you'll receive state certification opening doors to careers in corporate security, retail loss prevention, private investigation, and facility protection. Our graduates are highly sought after by employers throughout Florida.</p>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- What You'll Learn Section --}}
                        <div class="course-curriculum-section mb-5">
                            <h3 class="section-title text-white">What You'll Learn</h3>
                            <div class="row">
                                <div class="col-lg-6">
                                    <ul class="feature-list-detailed">
                                        @if (isset($course['features']) && is_array($course['features']))
                                            @foreach (array_slice($course['features'], 0, ceil(count($course['features']) / 2)) as $feature)
                                                <li class="text-white-50">
                                                    <i class="fas fa-check-circle text-success me-3"></i>
                                                    {{ $feature }}
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                                <div class="col-lg-6">
                                    <ul class="feature-list-detailed">
                                        @if (isset($course['features']) && is_array($course['features']))
                                            @foreach (array_slice($course['features'], ceil(count($course['features']) / 2)) as $feature)
                                                <li class="text-white-50">
                                                    <i class="fas fa-check-circle text-success me-3"></i>
                                                    {{ $feature }}
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Course Curriculum Section (using real course units) --}}
                        @if (isset($course['courseUnits']) && $course['courseUnits']->count() > 0)
                            <div class="course-curriculum-detailed mb-5">
                                <h3 class="section-title text-white">Course Curriculum</h3>
                                <div class="curriculum-content">
                                    @foreach ($course['courseUnits'] as $index => $unit)
                                        <div class="curriculum-unit mb-3 p-3 rounded" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                                            <h5 class="unit-title text-info mb-2">
                                                <span class="unit-number badge bg-info text-dark me-2">{{ $index + 1 }}</span>
                                                {{ $unit->title }}
                                            </h5>
                                            @if ($unit->admin_title && $unit->admin_title !== $unit->title)
                                                <p class="unit-subtitle text-white-50 small mb-2">{{ $unit->admin_title }}</p>
                                            @endif
                                            @if (isset($unit->curriculum_content))
                                                <p class="unit-description text-white-50 mb-0">{{ $unit->curriculum_content }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Course Requirements Section --}}
                        <div class="course-requirements-section mb-5">
                            <h3 class="section-title text-white">Requirements</h3>
                            <div class="requirements-content">
                                <ul class="requirements-list">
                                    @if (isset($course['requirements']))
                                        @foreach ($course['requirements'] as $requirement)
                                            <li class="text-white-50"><i
                                                    class="fas fa-exclamation-circle text-warning me-2"></i>{{ $requirement }}
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="text-white-50"><i class="fas fa-exclamation-circle text-warning me-2"></i>Must be 18 years
                                            or older</li>
                                        <li class="text-white-50"><i class="fas fa-exclamation-circle text-warning me-2"></i>Valid
                                            government-issued photo ID required</li>
                                        <li class="text-white-50"><i class="fas fa-exclamation-circle text-warning me-2"></i>Background check
                                            may be required</li>
                                        <li class="text-white-50"><i class="fas fa-exclamation-circle text-warning me-2"></i>High school
                                            diploma or equivalent preferred</li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        {{-- Course Schedule Section --}}
                        @if (isset($course['schedule']))
                            <div class="course-schedule-section mb-5">
                                <h3 class="section-title text-white">Upcoming Sessions</h3>
                                <div class="schedule-content">
                                    @foreach ($course['schedule'] as $session)
                                        <div class="schedule-item p-3 rounded mb-3" style="border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.05);">
                                            <div class="row align-items-center">
                                                <div class="col-md-3">
                                                    <div class="schedule-date text-white-50">
                                                        <i class="fas fa-calendar text-info me-2"></i>
                                                        {{ $session['date'] ?? 'TBD' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="schedule-time text-white-50">
                                                        <i class="fas fa-clock text-info me-2"></i>
                                                        {{ $session['time'] ?? '9:00 AM - 5:00 PM' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="schedule-location text-white-50">
                                                        <i class="fas fa-map-marker-alt text-info me-2"></i>
                                                        {{ $session['location'] ?? 'Online/Hybrid' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="schedule-availability text-end">
                                                        <span
                                                            class="badge {{ $session['available'] ?? true ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $session['available'] ?? true ? 'Available' : 'Full' }}
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
                        <div class="card mb-4 shadow-sm" style="background: linear-gradient(145deg, rgba(37, 99, 235, 0.15) 0%, rgba(124, 58, 237, 0.15) 100%); border: 1px solid rgba(255, 255, 255, 0.1);">
                            <div class="card-body text-center">
                                <div class="price-display mb-3">
                                    <span class="price-large text-white" style="font-size: 2rem; font-weight: bold;">${{ number_format($course['price'] ?? 299, 2) }}</span>
                                    <span class="price-currency text-white-50">USD</span>
                                </div>
                                <p class="text-white-50 mb-4">Complete training package with certification</p>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('courses.enroll', $course['id'] ?? 1) }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-credit-card me-2"></i>Enroll Now
                                    </a>
                                    <a href="#" class="btn btn-outline-light" data-bs-toggle="modal"
                                        data-bs-target="#contactModal">
                                        <i class="fas fa-phone me-2"></i>Contact Us
                                    </a>
                                </div>

                                <div class="enrollment-features mt-4">
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-shield-check text-success me-2"></i>
                                        <small class="text-white-50">Money-back guarantee</small>
                                    </div>
                                    <div class="feature-item d-flex align-items-center mb-2">
                                        <i class="fas fa-certificate text-success me-2"></i>
                                        <small class="text-white-50">State-approved certification</small>
                                    </div>
                                    <div class="feature-item d-flex align-items-center">
                                        <i class="fas fa-headset text-success me-2"></i>
                                        <small class="text-white-50">24/7 student support</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Info Card --}}
                        <div class="card mb-4" style="background: linear-gradient(145deg, rgba(37, 99, 235, 0.15) 0%, rgba(124, 58, 237, 0.15) 100%); border: 1px solid rgba(255, 255, 255, 0.1);">
                            <div class="card-header" style="background: rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                                <h5 class="mb-0 text-white">Course Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="info-item d-flex justify-content-between mb-2">
                                    <span class="info-label text-white-50">Duration:</span>
                                    <span class="info-value text-white">{{ $course['duration'] ?? '3-5 Days' }}</span>
                                </div>
                                <div class="info-item d-flex justify-content-between mb-2">
                                    <span class="info-label text-white-50">Format:</span>
                                    <span class="info-value text-white">{{ $course['format'] ?? 'Hybrid' }}</span>
                                </div>
                                <div class="info-item d-flex justify-content-between mb-2">
                                    <span class="info-label text-white-50">Level:</span>
                                    <span class="info-value text-white">{{ $course['level'] ?? 'Beginner' }}</span>
                                </div>
                                <div class="info-item d-flex justify-content-between mb-2">
                                    <span class="info-label text-white-50">Language:</span>
                                    <span class="info-value text-white">{{ $course['language'] ?? 'English' }}</span>
                                </div>
                                <div class="info-item d-flex justify-content-between">
                                    <span class="info-label text-white-50">Students:</span>
                                    <span class="info-value text-white">{{ $course['studentsEnrolled'] ?? '200+' }}
                                        enrolled</span>
                                </div>
                            </div>
                        </div>

                        {{-- Related Courses --}}
                        <div class="card" style="background: linear-gradient(145deg, rgba(37, 99, 235, 0.15) 0%, rgba(124, 58, 237, 0.15) 100%); border: 1px solid rgba(255, 255, 255, 0.1);">
                            <div class="card-header" style="background: rgba(255, 255, 255, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                                <h5 class="mb-0 text-white">Related Training</h5>
                            </div>
                            <div class="card-body">
                                @if (str_contains(strtolower($course['badge'] ?? ''), 'd') || str_contains(strtolower($course['type'] ?? ''), 'armed'))
                                    {{-- Related courses for Class D (Armed Security) --}}
                                    <div class="related-course-item mb-3">
                                        <h6 class="mb-1">
                                            <a href="{{ route('courses.list') }}" class="text-decoration-none text-info">Class G - Unarmed Security</a>
                                        </h6>
                                        <p class="small text-white-50 mb-1">Essential unarmed security training</p>
                                        <span class="badge bg-info text-dark">Starting at $199</span>
                                    </div>
                                    <div class="related-course-item mb-3">
                                        <h6 class="mb-1">
                                            <a href="{{ route('courses.schedules') }}" class="text-decoration-none text-info">Firearms Recertification</a>
                                        </h6>
                                        <p class="small text-white-50 mb-1">Annual firearms qualification renewal</p>
                                        <span class="badge bg-info text-dark">Contact Us</span>
                                    </div>
                                    <div class="related-course-item">
                                        <h6 class="mb-1">
                                            <a href="{{ route('courses.schedules') }}" class="text-decoration-none text-info">Advanced Tactical Training</a>
                                        </h6>
                                        <p class="small text-white-50 mb-1">Enhanced skills for experienced officers</p>
                                        <span class="badge bg-info text-dark">Contact Us</span>
                                    </div>
                                @else
                                    {{-- Related courses for Class G (Unarmed Security) --}}
                                    <div class="related-course-item mb-3">
                                        <h6 class="mb-1">
                                            <a href="{{ route('courses.list') }}" class="text-decoration-none text-info">Class D - Armed Security</a>
                                        </h6>
                                        <p class="small text-white-50 mb-1">Advanced armed security certification</p>
                                        <span class="badge bg-info text-dark">Starting at $299</span>
                                    </div>
                                    <div class="related-course-item mb-3">
                                        <h6 class="mb-1">
                                            <a href="{{ route('courses.schedules') }}" class="text-decoration-none text-info">Private Investigator License</a>
                                        </h6>
                                        <p class="small text-white-50 mb-1">PI license and surveillance training</p>
                                        <span class="badge bg-info text-dark">Contact Us</span>
                                    </div>
                                    <div class="related-course-item">
                                        <h6 class="mb-1">
                                            <a href="{{ route('courses.schedules') }}" class="text-decoration-none text-info">Loss Prevention Specialist</a>
                                        </h6>
                                        <p class="small text-white-50 mb-1">Retail security and investigation</p>
                                        <span class="badge bg-info text-dark">Contact Us</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
