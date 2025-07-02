<?php
    $features = [
        [
            'image' => asset('assets/img/feature/f1.jpg'),
            'title' => 'Flexible Training',
            'description' => 'Enroll in both online virtual classes and in-classroom courses. Customize your training experience according to your schedule and learning style.',
        ],
        [
            'image' => asset('assets/img/feature/f2.jpg'),
            'title' => 'Live Instruction',
            'description' => 'Our online virtual classes are live sessions led by experienced instructors. Participate in real-time discussions, ask questions, and receive immediate feedback.',
        ],
        [
            'image' => asset('assets/img/feature/f3.jpg'),
            'title' => '75/25% Rule Compliance',
            'description' => 'Complete 25% of the course at your own pace. Our platform offers pre-recorded sessions that count towards the training hours, providing flexibility for students who may have missed a module session.',
        ],
        [
            'image' => asset('assets/img/feature/f4.jpg'),
            'title' => 'Financing Available',
            'description' => 'We understand that financing your training can be a concern. We offer a convenient "Buy now, pay later" solution from trusted 3rd party providers.',
        ],
    ];
?>

<div class="feature-area p-3">
    <div class="container">
        <div class="row">
            <!-- Start single column-->
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="feature-image">
                    <div class="rotate-image rotatemed">
                        <img src="{{ asset('assets/img/about/target.png') }}" width="500" alt="">
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="row">
                    @foreach ($features as $feature)
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="feature-text wow fadeInUp" data-wow-delay="0.2s">
                                <img src="{{ asset($feature['image']) }}" alt="{{ $feature['title'] }}"
                                    class="feature-img">
                                <div class="featture-content">
                                    <h4>{{ $feature['title'] }}</h4>
                                    <p>{{ $feature['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
