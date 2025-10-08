    @php
        $courses = App\RCache::Courses()->where('is_active', true);

        $welcomeTitle = 'Welcome to The Security Training Group';
        $welcomeSubtitle = 'Florida Class D Security License and Armed Statewide Firearms Class G License';
        $classDIcon = asset('assets/img/icon/online-course-icon-class-d.png');
        $classGIcon = asset('assets/img/icon/online-course-icon-class-g.png');
        $courseDTitle = 'Florida Class D Security License Course';
        $courseDInfo = 'Comprehensive online training program. Flexible schedule.';
        $courseDPrice = '$125.00 USD';
        $courseGTitle = 'Armed Statewide Firearms Class G License Course';
        $courseGInfo = 'Combination of online learning and in-person range training.';
        $courseGPrice = '$250.00 USD';
        $content1 =
            'The Security Training Groups Online Security Training Program offers comprehensive courses for individuals seeking the Florida Class D Security License and the Armed Statewide Firearms Class G License. With the flexibility and convenience of online learning, students can now access high-quality training from anywhere in Florida.';
        $content2 =
            'The Florida Class D Security License course provides in-depth training on essential aspects of security operations, including legal guidelines, emergency response procedures, communication skills, and ethical conduct. Our expert instructors deliver engaging lessons that equip students with the knowledge and skills required to excel as security professionals.';
    @endphp


    <div class="slider-area">
        <div class="container">
            <div class="row">
                <div class="col-md-7 col-sm-12 col-xs-12">
                    <div class="slider-content d-flex flex-column justify-content-center align-items-center">
                        <h2 class="title2 mb-3">{{ $welcomeTitle }}</h2>
                        <p>{{ $welcomeSubtitle }}</p>
                    </div>

                    <x-panels.class_display_block :courses="$courses" :classDIcon="$classDIcon" :classGIcon="$classGIcon"
                        :courseDTitle="$courseDTitle" :courseDInfo="$courseDInfo" :courseDPrice="$courseDPrice" :courseGTitle="$courseGTitle" :courseGInfo="$courseGInfo"
                        :courseGPrice="$courseGPrice" />
                </div>
                <div class="col-md-5 col-sm-12 col-xs-12">
                    <div class="login-panel">
                        <div class="login-form-inner">
                            @guest()
                                <div class="single-login-form">
                                    <div class="inner-form">
                                        <x-frontend.forms.login-form />
                                    </div>
                                </div>
                            @endguest

                            @auth
                                <div class="account-profile-container shadow-lg mt-3 mb-2">
                                    <div class="account-profile">
                                        <div class="avatar">
                                            <img src="{{ Auth()->user()->getAvatar('thumb') }}" alt="Avatar">
                                        </div>
                                        <div class="profile-details">
                                            <h4 class="text-white">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</h4>
                                            <p class="text-white-50">{{ Auth::user()->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
