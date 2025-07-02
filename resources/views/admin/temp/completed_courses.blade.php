@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )


  <div class="tablewrapper">

    <h3>Missing DOL Records</h3>

    <table border="0" cellspacing="0" cellpadding="0">
    <tr class="header">
      <td></td>
      <td align="center">Completed At</td>
      <td>Student</td>
      <td>Course</td>
    </tr>
@foreach ( $DOLCourseAuths as $CourseAuth )
    <tr>
      <td>
        <a href="{{ route( 'admin.temp.completed_course_auths.course_auth', $CourseAuth ) }}" class="btn btn-sm btn-primary">View</a>
      </td>
      <td align="center">
        {{ $CourseAuth->CompletedAt() }}
      </td>
      <td>
        {{ $CourseAuth->GetUser() }}
      </td>
      <td>
        {{ $CourseAuth->GetCourse()->ShortTitle() }}
      </td>
    </tr>
@endforeach
    </table>

  </div>


@if ( $RangeDateCourseAuths->count() )

  <div class="tablewrapper mt-3">

    <h3>Missing Range Date Selection</h3>

    <table border="0" cellspacing="0" cellpadding="0">
    <tr class="header">
      <td align="center">Completed At</td>
      <td>Student</td>
      <td>Range Date</td>
      <td></td>
    </tr>
@foreach ( $RangeDateCourseAuths as $CourseAuth )
    <form method="post" action="{{ route( 'admin.temp.completed_course_auths.setrangedate', $CourseAuth ) }}" autocomplete="off">
    @csrf
    <tr>
      <td align="center">
        {{ $CourseAuth->CompletedAt() }}
      </td>
      <td>
        {{ $CourseAuth->GetUser() }}
      </td>
      <td>
        <select name="range_date_id" class="form-control form-control-sm" required >
          <option value="" selected disabled></option>
          <option value="-1">[ Set No Range Date Selected ]</option>
{!! $RangeOpts !!}
        </select>
      </td>
      <td>
        <button type="submit" class="btn btn-sm btn-success">Save</button>
      </td>
    </tr>
    </form>
@endforeach
    </table>

  </div>

@endif


@include( 'admin.temp.partials.asset-loader' )
@endsection
