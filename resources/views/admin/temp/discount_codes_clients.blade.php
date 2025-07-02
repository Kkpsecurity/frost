@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )


  <div class="tablewrapper">

    <h3>Client Discount Codes</h3>

    <table border="0" cellspacing="0" cellpadding="0" class="my-4">
@foreach ( $DiscountCodes as $DiscountCode )
    <tr class="header">
      <td>Students</td>
      <td>Client</td>
      <td>Code</td>
      <td>Course</td>
      <td align="center">Max Uses</td>
      <td align="center">Times Used</td>
    </tr>
    <tr>
      <td>
        <i class="fa fa-plus-square mr-3 show_hide_students" data-target="students_table_{{ $DiscountCode->id }}"></i>
        {{ $DiscountCode->CourseAuths->count() }}
      </td>
      <td>
        {{ $DiscountCode->client }}
      </td>
      <td>
        {{ $DiscountCode->code }}
      </td>
      <td>
        {{ $DiscountCode->GetCourse()->ShortTitle() }}
      </td>
      <td align="center">
        {{ $DiscountCode->max_count }}
      </td>
      <td align="center">
        {{ $DiscountCode->CourseAuths->count() }}
      </td>
    </tr>
    <tr id="students_table_{{ $DiscountCode->id }}" class="d-none">
      <td colspan="5">
        <table border="1" cellspacing="0" cellpadding="0" class="ml-2 mb-3">
        <tr>
          <td>Student</td>
          <td>Lessons</td>
          <td align="center">Created At</td>
          <td align="center">Completed At</td>
        </tr>
@foreach ( $DiscountCode->CourseAuths as $CourseAuth )
        <tr>
          <td>
            <a href="{{ route( 'admin.temp.course_auths.course_auth', $CourseAuth->id ) }}" target="_blank">{{ $CourseAuth->User }}</a>
          </td>
          <td align="right">
            {{ count( $CourseAuth->PCLCache() ) }}
              /
            {{ $course_lessons_counts[ $CourseAuth->course_id ] }}
          </td>
          <td align="center">
           {{ $CourseAuth->CreatedAt() }}
         </td>
          <td align="center">
            {{ $CourseAuth->CompletedAt() }}
          </td>
        </tr>
@endforeach
        </table>
      </td>
    </tr>
@endforeach
    </table>

  </div>


<script>
window.addEventListener( 'load', function() {

    $( '.show_hide_students' ).click(function() {

        if ( $( this ).hasClass( 'fa-plus-square' ) )
        {
            $( this ).removeClass( 'fa-plus-square' ).addClass( 'fa-minus-square' );
            $( '#' + $( this ).data( 'target' ) ).removeClass( 'd-none' );
        }
        else
        {
            $( this ).removeClass( 'fa-minus-square' ).addClass( 'fa-plus-square' );
            $( '#' + $( this ).data( 'target' ) ).addClass( 'd-none' );
        }

    });

});
</script>


@include( 'admin.temp.partials.asset-loader' )
@endsection
