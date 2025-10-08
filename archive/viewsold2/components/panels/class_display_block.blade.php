<div class="row all-services">
    @foreach ($courses as $course)
        <div class="col-md-6 col-xs-12 relative">
            <div class="well-services p-3">
                <div class="well-img p-3">
                    <a class="big-icon" href="{{ url('/') }}">
                        <img src="{{ $course->id == 1 ? $classDIcon : $classGIcon }}" alt="{{ $course->title_long }}" />
                    </a>
                </div>
                
                <div class="main-wel">
                    <div class="wel-content">
                        <h4>{{ $course->title_long }}</h4>
                        <p>{{ nl2br($course->id == 1 ? $courseDInfo : $courseGInfo) }}</p>
                        <p class="price">${{ number_format($course->price, 2) }}</p>
                    </div>

                    <div class="d-flex justify-content-center">
                        <a href="{{ url('courses/detail/' . $course->id) }}" class="btn btn-primary">More Detail</a>                       
                        {!! App\Helpers\Helpers::EnrollButton( $course ) !!}
                    </div>
                </div>
            </div>
        </div>
    @endforeach  
</div>
