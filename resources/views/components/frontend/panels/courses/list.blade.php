{{-- Courses Panel - Simple listing with payment support for enrollment --}}
@props(['courses' => collect(), 'paymentConfig' => []])

@push('component-styles')
    <link rel="stylesheet" href="{{ asset('css/components/courses.css') }}">
@endpush

<div class="frost-secondary-bg py-5">
    <div class="container courses-container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="text-white">{{ __('frontend.courses.available_courses') }}</h2>
            </div>
        </div>

        <div class="row" id="coursesContainer">
            @forelse($courses as $course)
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="course-card h-100">
                        {{-- Course Header --}}
                        <div class="course-header">
                            <div class="course-icon">
                                <i
                                    class="{{ $course->course_type === 'G' ? 'fas fa-shield-alt' : 'fas fa-user-shield' }}"></i>
                            </div>
                            <div class="course-class-badge">
                                {{ $course->course_type === 'G' ? __('frontend.courses.class_g_badge') : __('frontend.courses.class_d_badge') }}
                            </div>
                            <div class="course-type">
                                {{ $course->course_type === 'G' ? __('frontend.courses.armed_security') : __('frontend.courses.unarmed_security') }}
                            </div>
                        </div>

                        <div class="course-body">
                            <h4 class="course-title">{{ $course->title_long ?? $course->title }}</h4>

                            @if ($course->description)
                                <p class="course-description">{{ Str::limit($course->description, 100) }}</p>
                            @endif

                            {{-- Course Statistics --}}
                            <div class="course-stats mb-3">
                                <div class="row text-center">
                                    @if ($course->total_units > 0)
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <i class="fas fa-book text-info"></i>
                                                <div class="stat-number">{{ $course->total_units }}</div>
                                                <div class="stat-label">{{ __('frontend.courses.units') }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($course->total_lessons > 0)
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <i class="fas fa-play-circle text-success"></i>
                                                <div class="stat-number">{{ $course->total_lessons }}</div>
                                                <div class="stat-label">{{ __('frontend.courses.lessons') }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <i class="fas fa-clock text-warning"></i>
                                            <div class="stat-number">
                                                @if ($course->calculated_total_minutes > 0)
                                                    {{ ceil($course->calculated_total_minutes / 60) }}
                                                @elseif($course->total_minutes > 0)
                                                    {{ ceil($course->total_minutes / 60) }}
                                                @else
                                                    {{ $course->duration_days * 8 }}
                                                @endif
                                            </div>
                                            <div class="stat-label">{{ __('frontend.courses.hours') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Key Features from Lessons --}}
                            @if (!empty($course->key_features))
                                <div class="course-features">
                                    <h6 class="features-title">{{ __('frontend.courses.key_topics') }}</h6>
                                    <ul class="feature-list">
                                        @foreach (array_slice($course->key_features, 0, 4) as $feature)
                                            <li><i
                                                    class="fas fa-check text-success me-1"></i>{{ Str::limit($feature, 35) }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Course Meta --}}
                            <div class="course-meta">
                                @if ($course->duration_days > 0)
                                    <span class="duration">
                                        <i class="fas fa-calendar me-1"></i>{{ $course->duration_days }}
                                        {{ __('frontend.courses.days') }}
                                    </span>
                                @endif
                                <span class="format">
                                    <i class="fas fa-laptop me-1"></i>{{ __('frontend.courses.online_live') }}
                                </span>
                            </div>
                        </div>

                        <div class="course-footer">
                            <div class="course-price">
                                <span class="price">${{ number_format($course->price, 2) }}</span>
                                <span class="price-note">USD</span>
                            </div>
                            <div class="course-actions">
                                <a href="{{ route('courses.show', $course->id) }}"
                                    class="btn btn-outline-primary btn-sm me-2">{{ __('frontend.courses.learn_more') }}</a>
                                <a href="{{ route('courses.enroll', $course->id) }}"
                                    class="btn btn-primary btn-sm">{{ __('frontend.courses.enroll_now') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-white-50 mb-3"></i>
                        <h4 class="text-white">{{ __('frontend.courses.no_courses') }}</h4>
                        <p class="text-white-50">{{ __('frontend.courses.no_courses_hint') }}</p>
                    </div>
                </div>
            @endforelse
        </div>


    </div>
</div>
