@php
    $cards = [
        [
            'icon' => 'bi bi-person-plus-fill',
            'title' => 'Enroll in a Course',
            'text' => 'Visit our website and navigate to the registration page to enroll in a course that suits your needs.',
        ],
        [
            'icon' => 'bi bi-book-fill',
            'title' => 'Take the Course',
            'text' => 'Complete the course at your own pace, with a combination of live sessions and self-paced modules.',
        ],
        [
            'icon' => 'bi bi-file-earmark-check-fill',
            'title' => 'Achieve Certification',
            'text' => 'Upon course completion and passing the required exam, download your certificate from your student profile.',
        ]
    ];
@endphp

<div class="panel-3-card-container">
    <div class="container bg-color-secondary panel-card-container">
        <div class="row">
            @foreach ($cards as $card)
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="card present-card bg-color-secondary border-0 p-3">
                        <div class="card-body text-center">
                            <div class="big-icon"><i class="{{ $card['icon'] }}"></i></div>
                            <h4 class="card-title">{{ $card['title'] }}</h4>
                            <p class="card-text">{{ $card['text'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
