{{-- Course Card Component --}}
@props([
    'courseId' => null,
    'title' => 'Course Title',
    'description' => 'Course description',
    'icon' => 'default-icon.png',
    'detailUrl' => '#',
    'fallbackPrice' => '$199.00',
    'responsive' => 'desktop' // 'desktop' or 'mobile'
])

@php
    $course = $courseId ? App\Models\Course::find($courseId) : null;
    $cardClass = $responsive === 'mobile' ? 'scard' : 'card';
    $containerClass = $responsive === 'desktop' ? 'd-lg-block d-none' : 'd-sm-block d-lg-none';
@endphp

<div class="{{ $containerClass }} mt-3">
    <div class="{{ $cardClass }}">
        <div class="content">
            <div class="row">
                <div class="col-lg-2">
                    <div class="icon">
                        <img src="{{ asset('images/' . $icon) }}" alt="{{ $title }}">
                    </div>
                </div>
                <div class="col-lg-10">
                    <h4>{{ $title }}</h4>
                    <p>{{ $description }}</p>
                    @if($course)
                        <p>${{ number_format($course->price, 2) }} USD</p>
                        <div>
                            <a href="{{ $detailUrl }}" class="btn btn-primary">More Detail</a>
                            {{-- {!! Helpers::EnrollButton($course) !!} --}}
                            <a href="#" class="btn btn-success ms-2">Enroll Now</a>
                        </div>
                    @else
                        <p>{{ $fallbackPrice }} USD</p>
                        <div>
                            <a href="#" class="btn btn-primary">More Detail</a>
                            <a href="#" class="btn btn-success">{{ $responsive === 'mobile' ? 'Register Now' : 'Enroll Now' }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
