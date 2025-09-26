<div class="slider-area">
    <div class="container">
        <div class="row">
            <div class="col-md-7 col-sm-12 col-xs-12">
                <div class="slider-content d-flex flex-column justify-content-center align-items-center">
                    <h2 class="title2 mb-3">{{ $welcomeTitle }}</h2>
                    <p>{{ $welcomeSubtitle }}</p>
                </div>

                @include('frontend/partials/class_display_block')
            </div>
            <div class="col-md-5 col-sm-12 col-xs-12">
                <div class="login-panel">
                    <div class="login-form-inner">
                        @guest()
                            <div class="single-login-form">
                                <div class="inner-form">
                                    @include('frontend.forms.login-form')
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
