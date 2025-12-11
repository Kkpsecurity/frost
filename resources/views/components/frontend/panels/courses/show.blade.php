@props(['course' => []])

@push('component-styles')
    <link rel="stylesheet" href="{{ asset('css/components/course-details.css') }}">
@endpu                                <a href="{{ route('payments.course', $course['id']) }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-graduation-cap me-2"></i>Enroll Now
                                </a><div class="frost-secondary-bg py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                {{-- Course Details Content --}}
                <div class="course-details-wrapper">
                    {{-- Course Header --}}
                    <div class="course-details-header mb-4">
                        <div class="d-flex align-items-start">
                            <div class="course-icon-large me-4 text-primary">
                                <i class="{{ $course['icon'] ?? 'fas fa-graduation-cap' }} text-primary fa-4x"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h1 class="course-title-large text-white">{{ $course['title'] ?? 'Course Details' }}</h1>
                                @if(isset($course['type']))
                                    <p class="course-type-large text-white-50 lead mb-2">{{ $course['type'] }}</p>
                                @endif
                                @if(isset($course['badge']))
                                    <div class="course-badge-large mb-3">
                                        <span class="badge bg-primary">{{ $course['badge'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if(isset($course['description']))
                            <div class="course-description-full mt-4">
                                <p class="lead text-white-50">{{ $course['description'] }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Course Overview --}}
                    @if(isset($course['fullDescription']))
                        <div class="mb-5">
                            <h3 class="section-title text-white">Course Overview</h3>
                            <div class="course-description-full text-white-50">
                                {!! $course['fullDescription'] !!}
                            </div>
                        </div>
                    @endif

                    {{-- What You'll Learn --}}
                    @if(isset($course['features']) && is_array($course['features']))
                        <div class="mb-5">
                            <h3 class="section-title text-white">What You'll Learn</h3>
                            <ul class="feature-list-detailed">
                                @foreach($course['features'] as $feature)
                                    <li><i class="fas fa-check text-success"></i><span class="text-white-50">{{ $feature }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Requirements --}}
                    @if(isset($course['requirements']) && is_array($course['requirements']))
                        <div class="mb-5">
                            <h3 class="section-title text-white">Requirements</h3>
                            <ul class="requirements-list">
                                @foreach($course['requirements'] as $requirement)
                                    <li><i class="fas fa-info-circle text-info"></i><span class="text-white-50">{{ $requirement }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Course Sidebar --}}
                <div class="course-sidebar">
                    <div class="card sticky-top">
                        <div class="card-body">
                            {{-- Price Display --}}
                            <div class="price-display text-center mb-4">
                                <div class="price-large">${{ number_format($course['price'] ?? 0, 2) }}</div>
                            </div>

                            {{-- Course Meta Information --}}
                            <div class="course-meta-info mb-4">
                                @if(isset($course['duration']))
                                    <div class="info-item d-flex justify-content-between">
                                        <span class="info-label"><i class="fas fa-clock me-1"></i>Duration:</span>
                                        <span class="info-value">{{ $course['duration'] }}</span>
                                    </div>
                                @endif
                                @if(isset($course['format']))
                                    <div class="info-item d-flex justify-content-between">
                                        <span class="info-label"><i class="fas fa-laptop me-1"></i>Format:</span>
                                        <span class="info-value">{{ $course['format'] }}</span>
                                    </div>
                                @endif
                                @if(isset($course['level']))
                                    <div class="info-item d-flex justify-content-between">
                                        <span class="info-label"><i class="fas fa-signal me-1"></i>Level:</span>
                                        <span class="info-value">{{ $course['level'] }}</span>
                                    </div>
                                @endif
                                @if(isset($course['language']))
                                    <div class="info-item d-flex justify-content-between">
                                        <span class="info-label"><i class="fas fa-language me-1"></i>Language:</span>
                                        <span class="info-value">{{ $course['language'] }}</span>
                                    </div>
                                @endif
                                @if(isset($course['certification']))
                                    <div class="info-item d-flex justify-content-between">
                                        <span class="info-label"><i class="fas fa-certificate me-1"></i>Certification:</span>
                                        <span class="info-value">{{ $course['certification'] }}</span>
                                    </div>
                                @endif
                                @if(isset($course['classSize']))
                                    <div class="info-item d-flex justify-content-between">
                                        <span class="info-label"><i class="fas fa-users me-1"></i>Class Size:</span>
                                        <span class="info-value">{{ $course['classSize'] }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Course Benefits --}}
                            @if(isset($course['certification']) || isset($course['features']))
                                <div class="enrollment-features mb-4">
                                    @if(isset($course['certification']))
                                        <div class="feature-item mb-2">
                                            <i class="fas fa-certificate text-success me-2"></i>{{ $course['certification'] }}
                                        </div>
                                    @endif
                                    <div class="feature-item mb-2">
                                        <i class="fas fa-clock text-success me-2"></i>Flexible scheduling available
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-support text-success me-2"></i>Instructor support included
                                    </div>
                                </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="course-actions d-grid gap-2">
                                <a href="{{ route('payments.course', $course['id']) }}" class="btn btn-primary btn-lg"
                                   onclick="console.log('Enrolling in course {{ $course['id'] }}', '{{ route('payments.course', $course['id']) }}'); return true;">
                                    <i class="fas fa-graduation-cap me-2"></i>Enroll Now
                                </a>
                                <a href="{{ route('courses.list') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Courses
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
