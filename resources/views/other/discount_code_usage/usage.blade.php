@extends( 'other.discount_code_usage.layout' )

@section( 'content' )

  <div class="d-table mx-auto my-3">
    <div class="grid_table" id="discount_code_info">

      <div>
        Course
      </div>
      <div class="grid-span-2 fw-bold">
        {{ $DiscountCode->GetCourse()->LongTitle() }}
      </div>

      <div>
        Max Usage
      </div>
      <div class="grid-span-2 fw-bold">
        {{ $DiscountCode->max_count }}
      </div>

      <div>
        Codes Used
      </div>
      <div class="fw-bold">
        {{ $Orders->count() }}
      </div>
      <div class="text-end ps-2">
        <a href="{{ route( 'discount_codes.usage.csv', $DiscountCode ) }}">Download CSV</a>
      </div>

    </div>
  </div>


  <div class="d-table mx-auto my-3">
    <div class="grid_table" id="orders_table">

      <div class="fw-bold">Student</div>
      <div class="fw-bold">Email</div>
      <div class="fw-bold">Order Completed</div>
      <div class="fw-bold">Course Started</div>
      <div class="fw-bold">Course Completed</div>
      <div class="fw-bold">Status</div>

@foreach ( $Orders as $Order )
      <div>
        {{ $Order->User }}
      </div>
      <div>
        {{ $Order->User->email }}
      </div>
      <div>
        {{ $Order->CompletedAt() }}
      </div>
      <div>
        {{ $Order->CourseAuth->StartedAt() }}
      </div>
      <div>
        {{ $Order->CourseAuth->CompletedAt() }}
      </div>
      <div class="text-wrap">
        {!! $Order->CourseAuth->FinalStatus( true ) !!}
      </div>
@endforeach

    </div>
  </div>

@local
  <!-- devel helper -->
  <div class="position-absolute top-0 end-0 me-2">
    <a href="{{ route( 'discount_codes.usage.forget', $DiscountCode ) }}">Forget</a>
  </div>
@endlocal

@endsection
