@php
    $courses = App\RCache::Courses()->where('is_active', true);

    $courseD = $courses->firstWhere('id', 1);
    $courseG = $courses->firstWhere('id', 3);

    $welcomeTitle = 'Welcome to The Security Training Group';
    $welcomeSubtitle = 'Florida Security License and Armed Statewide Firearms Class G License';
    $courseDTitle = $courseD ? $courseD->full_title : 'Florida Class D Security License Course';
    $courseDInfo = 'Comprehensive online training program. Flexible schedule.';
    $courseDPrice = $courseD ? '$' . number_format($courseD->price, 2) . ' USD' : '$125.00  USD';
    $courseGTitle = $courseG ? $courseG->full_title : 'Armed Statewide Firearms Class G License Course';
    $courseGInfo = 'Combination of online learning and in-person range training.';
    $courseGPrice = $courseG ? '$' . number_format($courseG->price, 2) . ' USD' : '$250.00 USD';
    $content1 =
        'The Security Training Groups Online Security Training Program offers comprehensive courses for individuals seeking the Florida Class D Security License and the Armed Statewide Firearms Class G License. With the flexibility and convenience of online learning, students can now access high-quality training from anywhere in Florida.';
    $content2 =
        'The Florida Class D Security License course provides in-depth training on essential aspects of security operations, including legal guidelines, emergency response procedures, communication skills, and ethical conduct. Our expert instructors deliver engaging lessons that equip students with the knowledge and skills required to excel as security professionals.';
@endphp

@push('component-styles')
    @php
        $manifestPath = public_path('build/manifest.json');
        $useVite = false;
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            if (is_array($manifest) && array_key_exists('resources/css/components/welcome-hero.css', $manifest)) {
                $useVite = true;
            }
        }
    @endphp

    @if ($useVite)
        @vite(['resources/css/components/welcome-hero.css'])
    @else
        <link rel="stylesheet" href="{{ asset('css/components/welcome-hero.css') }}">
    @endif

    {{-- Custom Title Styling --}}
    <style>
        .slider-content h2 {
            color: #f1c40f !important; /* Yellowish color */
            font-weight: 700 !important; /* Bold */
            font-size: 2.5rem !important; /* Larger size */
            margin-bottom: 0.5rem !important; /* Reduced spacing below title */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Subtle shadow for readability */
            line-height: 1.2 !important; /* Better line height */
        }

        .slider-content p {
            color: #ecf0f1 !important; /* Light gray for subtitle */
            font-size: 1.1rem !important; /* Slightly larger subtitle */
            margin-bottom: 1.25rem !important; /* Reduced spacing below subtitle */
            font-weight: 400 !important; /* Normal weight */
            line-height: 1.4 !important; /* Better readability */
        }

        .slider-content {
            padding-top: 2rem; /* Add some top padding */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .slider-content h2 {
                font-size: 2rem !important;
                margin-bottom: 0.2rem !important;
            }

            .slider-content p {
                font-size: 1.2rem !important;
                margin-bottom: 4rem !important;
            }
        }

        @media (max-width: 576px) {
            .slider-content h2 {
                font-size: 1.75rem !important;
            }

            .slider-content p {
                font-size: 0.95rem !important;
            }
        }
    </style>
@endpush

<div class="slider-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="slider-content">
                    <h2>{{ $welcomeTitle }}</h2>
                    <p>{{ $welcomeSubtitle }}</p>

                    <div class="row">
                        <!-- Class D Service -->
                        <div class="col-md-6 col-lg-12 col-xl-6">
                            <div class="all-services">
                                <div class="well-services">
                                    <div class="main-wel">
                                        <div class="wel-img">
                                            <div class="big-icon">
                                                <img src="@mediaUrl('assets/icons/online-course-icon-class-d.png', 'media')"
                                                    alt="Class D Security">
                                            </div>
                                        </div>
                                        <div class="wel-content">
                                            <h4><span>{{ $courseDPrice }}</span></h4>
                                            <p>{{ $courseDInfo }}</p>
                                        </div>
                                        <div class="text-center">
                                            <a href="{{ url('/courses/detail/1') }}" class="btn btn-primary">More
                                                Detail</a>
                                            {!! App\Helpers\Helpers::EnrollButton($courseD) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Class G Service -->
                        <div class="col-md-6 col-lg-12 col-xl-6">
                            <div class="all-services">
                                <div class="well-services">
                                    <div class="main-wel">
                                        <div class="wel-img">
                                            <div class="big-icon">
                                                <img src="@mediaUrl('assets/icons/online-course-icon-class-g.png', 'media')"
                                                    alt="Class G Security">
                                            </div>
                                        </div>
                                        <div class="wel-content">
                                            <h4><span>{{ $courseGPrice }}</span></h4>
                                            <p>{{ $courseGInfo }}</p>
                                        </div>
                                        <div class="text-center">
                                            <a href="{{ url('/courses/detail/2') }}" class="btn btn-primary">More
                                                Detail</a>
                                            {!! App\Helpers\Helpers::EnrollButton($courseG) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login Panel -->
            <div class="col-lg-4">
                <div class="login-panel">
                    <div class="login-form-inner">
                        <div class="account-profile-container">
                            <div class="account-profile">
                                <div class="profile-detail text-center">
                                    <!-- Login form will be rendered here -->
                                    <x-frontend.panels.accounts.quick-profile :user="Auth::user()" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
