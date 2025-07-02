@php
    // PRODUCT FEATURE
    $courses = RCache::Courses()->where('is_active', true);
    $imageUrl = url('assets/img/DArienzo-Psychology-Law-Enforcement-Badge-scaled.webp');
    $saleText = 'Sale';
    $imageText = 'DArienzo Psychology Law Enforcement Badge';
    $titleText = 'Enroll Now';
    $buttonText = 'Enroll Now';
@endphp

<section class="product-feature frost-secondary-bg">
    <div class="container-fluid">
        <div class="row justify-content-center">
            @foreach ($courses as $course)
                <div class="col-12 col-lg-4 mb-4">
                    <div class="card text-white bg-dark rounded">
                        <div class="d-flex justify-content-between p-3">
                            <p class="fw-bold text-uppercase mb-0">{{ $course->title_long }}</p>
                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center shadow-1-strong"
                                style="width: 35px; height: 35px;">
                                <p class="text-white mb-0 small">{{ $saleText }}</p>
                            </div>
                        </div>
                        <img src="{{ $imageUrl }}" class="img-fluid" alt="{{ $imageText }}" />
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <p class="small"><a href="#!" class="text-light">{{ $course->category }}</a></p>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <h5 class="mb-0" style="color: white;">{{ $course->title }}</h5>
                                <h5 class="text-dark mb-0" style="color: white; filter: brightness(50%);">
                                    ${{ number_format($course->price, 2) }}
                                </h5>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <p class=" mb-0">
                                    <a href="{{ url('courses/detail/' . $course->id) }}"
                                        class="btn btn-primary">
                                        <i class="fa fa-bars"></i> <span style="color: white;">More Info</span>
                                    </a>
                                </p>

                                {!! Helpers::EnrollButton($course) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
