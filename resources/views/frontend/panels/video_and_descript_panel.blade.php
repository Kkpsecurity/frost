@php
    $images = [
        'first_image' => asset('assets/img/about/overview.jpg'),
        'second_image' => asset('assets/img/about/overview.jpg'),
    ];

    $video_link = 'https://www.youtube.com/watch?v=O33uuBh6nXA';
    $heading = [
        'part1' => 'STG Online Firearm Training',
        'part2' => 'Licensed Online Weapons Training for D and G Licenses in Florida.',
    ];

    $description = 'STG provides state-accredited online firearm training for D and G licenses in Florida. Our program is cost-effective, secure, and user-friendly, with live customer support to guide you through the process. Get your firearm license conveniently online with STG.';
    $features = [
        [
            'icon' => 'flaticon-079-graphic',
            'title' => 'Cost-Effective',
            'description' => 'STG online training for D and G licenses is priced affordably, making it a cost-effective choice for firearm enthusiasts and professionals.',
        ],
        [
            'icon' => 'flaticon-007-document-2',
            'title' => 'Secure & Reliable',
            'description' => 'We prioritize your safety. Our online platform is secure, and your personal and payment information is well-protected.',
        ],
        [
            'icon' => 'flaticon-107-speech-bubbles',
            'title' => 'Live Support',
            'description' => 'Our dedicated team is always available to provide live support and assistance throughout your online training journey.',
        ],
    ];
    
@endphp

<div class="about-area p-5">
    <div class="container">
        <div class="row">
            <!-- Start column-->
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="about-image">
                    <img src="{{ $images['first_image'] }}" alt="" class="ab-first-img">
                    <img src="{{ $images['second_image'] }}" alt="" class="ab-second-img">
                    <a href="{{ $video_link }}" class="video-play vid-zone">
                        <i class="fa fa-play"></i>
                    </a>
                </div>
            </div>
            <!-- End column-->
            <!-- Start column-->
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="about-content">
                    <h3>
                        <span class="h3-title">{{ $heading['part1'] }}</span>
                        <br>
                        <span class="h3-title1">{{ $heading['part2'] }}</span>
                    </h3>
                    <hr class="hr-style" />
                    <p class="lead text-white-50 hidden-sm"> {{ $description }} </p>
                    <div class="about-details">
                        @foreach ($features as $feature)
                            <div class="single-about">
                                <a href="#"><i class="{{ $feature['icon'] }}"></i></a>
                                <div class="icon-text">
                                    <h5>{{ $feature['title'] }}</h5>
                                    <p class="lead text-white-50">{{ $feature['description'] }} </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- End column-->
        </div>
    </div>
</div>
