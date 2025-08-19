extends('layouts.app')

@section('page-title')
    {{ $content['title'] }}
@stop

@section('page-keywords')
    {{ $content['keywords'] }}
@stop

@section('page-description')
    {{ $content['description'] }}
@stop


@section('content')
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
                                            {!! Helpers::EnrollButton( $course ) !!}
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
                                            <a href="{{ url('/courses/detail/2') }}" class="btn btn-primary">More Detail</a>
                                            {!! Helpers::EnrollButton( $course ) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    @guest
                        <x-panels.forms.frontend-login-form />
                    @endguest
                    @auth
                        <x-panels.frontend-profile />
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
@stop
