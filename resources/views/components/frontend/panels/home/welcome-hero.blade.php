@php
    $courses = App\Models\Course::where('is_active', true)->get();

    $courseD = $courses->firstWhere('id', 1);
    $courseG = $courses->firstWhere('id', 3);

    // Section: Welcome Titles & Subtitles
    $welcomeTitle = 'Welcome to The Security Training Group';
    $welcomeSubtitle = 'Florida Security License and Armed Statewide Firearms Class G License';

    // Section: Class D Course Details
    $classDIcon = asset('assets/img/icon/online-course-icon-class-d.png');
    $courseDTitle = $courseD ? $courseD->full_title : 'Florida Class D Security License Course';
    $courseDInfo = 'Comprehensive online training program. Flexible schedule.';
    $courseDPrice = $courseD ? '$' . number_format($courseD->price, 2) . ' USD' : '';

    // Section: Class G Course Details
    $classGIcon = asset('assets/img/icon/online-course-icon-class-g.png');
    $courseGTitle = $courseG ? $courseG->full_title : 'Armed Statewide Firearms Class G License Course';
    $courseGInfo = 'Combination of online learning and in-person range training.';
    $courseGPrice = $courseG ? '$' . number_format($courseG->price, 2) . ' USD' : '';

    // Section: Content
    $content1 =
        'The Security Training Groups Online Security Training Program offers comprehensive courses for individuals seeking the Florida Class D Security License and the Armed Statewide Firearms Class G License. With the flexibility and convenience of online learning, students can now access high-quality training from anywhere in Florida.';
    $content2 =
        'The Florida Class D Security License course provides in-depth training on essential aspects of security operations, including legal guidelines, emergency response procedures, communication skills, and ethical conduct. Our expert instructors deliver engaging lessons that equip students with the knowledge and skills required to excel as security professionals.';

@endphp

<!-- Hero Section with Course Showcase -->
<div class="welcome-login-area frost-secondary-bg sarea-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="section-headline p-3">
                            <h3>{{ $welcomeTitle }}</h3>
                            <p>{{ $welcomeSubtitle }}</p>
                        </div>
                    </div>
                    <div class="card-body content">
                        <p>{{ $content1 }}</p>
                        <p>{{ $content2 }}</p>
                    </div>
                </div>

                <div class="d-lg-block d-none mt-3">
                    <div class="card">
                        <div class="content">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="icon">
                                        <img src="{{ $classDIcon }}" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <h4>{{ $courseDTitle }}</h4>
                                    <p>{{ $courseDInfo }}</p>
                                    <p>${{ number_format(App\Models\Course::find(1)->price, 2) }} USD</p>
                                    <div>
                                        <a href="{{ url('/courses/detail/1') }}" class="btn btn-primary">More Detail</a>
                                        @if($courseD)
                                            {!! App\Helpers\Helpers::EnrollButton($courseD) !!}
                                        @else
                                            <a href="{{ url('/courses/enroll/1') }}" class="btn btn-success">Enroll Now</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="content">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="icon">
                                        <img src="{{ $classGIcon }}" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <h4>{{ $courseGTitle }}</h4>
                                    <p>{{ $courseGInfo }}</p>
                                    <p>${{ number_format(App\Models\Course::find(3)->price, 2) }} USD</p>
                                    <div>
                                        <a href="{{ url('/courses/detail/2') }}" class="btn btn-primary">More
                                            Detail</a>
                                        @if($courseG)
                                            {!! App\Helpers\Helpers::EnrollButton($courseG) !!}
                                        @else
                                            <a href="{{ url('/courses/enroll/3') }}" class="btn btn-success">Enroll Now</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                @guest
                    <div class="border-0 shadow-lg bg-default" style="z-index: 1000">
                        <x-frontend.forms.quick-login-form />
                    </div>
                @endguest

                @auth
                    <div class="border-0 shadow-lg bg-default">
                        <div class="card-body login-form">
                            <div class="account-profile d-flex flex-column align-items-center mt-2">
                                <div class="avatar">
                                    <img src="{{ Auth()->user()->getAvatar('thumb') }}" alt="Avatar">
                                </div>
                                <div class="profile-details text-white">
                                    <h4 class="text-white">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</h4>
                                    <p class="text-white-50">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth

                <div class="d-sm-block d-lg-none mt-3">
                    <div class="scard">
                        <div class="content">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="icon">
                                        <img src="{{ $classDIcon }}" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <h4>{{ $courseDTitle }}</h4>
                                    <p>{{ $courseDInfo }}</p>
                                    <div>
                                        <a href="#" class="btn btn-primary">More Detail</a>
                                        <a href="#" class="btn btn-primary">Register Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="scard">
                        <div class="content">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="icon">
                                        <img src="{{ $classGIcon }}" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-10">
                                    <h4>{{ $courseGTitle }}</h4>
                                    <p>{{ $courseGInfo }}</p>
                                    <div>
                                        <a href="#" class="btn btn-primary">More Detail</a>
                                        <a href="#" class="btn btn-primary">Register Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
