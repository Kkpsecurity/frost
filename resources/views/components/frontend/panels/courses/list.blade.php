{{-- Courses Panel - Simple listing with payment support for enrollment --}}
@props(['courses' => collect(), 'paymentConfig' => []])

@push('component-styles')
    <link rel="stylesheet" href="{{ asset('css/components/courses.css') }}">
@endpush

<div class="frost-secondary-bg py-5">
    <div class="container courses-container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="text-white">Available Courses</h2>
            </div>
        </div>

        <div class="row" id="coursesContainer">
            @forelse($courses as $course)
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="course-card h-100">
                        <div class="course-body">
                            <h4 class="course-title">{{ $course->title_long ?? $course->title }}</h4>
                            @if($course->description)
                                <p class="course-description">{{ Str::limit($course->description, 120) }}</p>
                            @endif

                            <div class="course-meta">
                                @if($course->total_minutes)
                                    <span class="duration">
                                        <i class="fas fa-clock me-2"></i>{{ ceil($course->total_minutes / 60) }} Hours
                                    </span>
                                @endif
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


    </div>
</div>



