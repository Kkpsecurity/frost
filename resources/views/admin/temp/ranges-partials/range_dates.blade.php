<h3>Range Dates</h3>

<table border="0" cellspacing="0" cellpadding="0">
<tr class="header">
  <td align="center">ID</td>
  <td align="center">Is Active</td>
  <td align="center">#</td>
  <td align="center">Start Date</td>
  <td align="center">End Date</td>
  <td>Times</td>
  <td align="center">Price</td>
</tr>

@foreach ( $RangeDates as $RangeDate )
@php $course_auth_count = $CourseAuths->get( $RangeDate->id )->count(); @endphp
<tr>
  <td align="center">{{ $RangeDate->id }}</td>
  <td align="center">
@if ( $RangeDate->id == -1 )
    <i>cannot deactivate</i>
@else
    <form method="post" action="{{ route( 'admin.temp.ranges.rangedate.toggleactive', $RangeDate ) }}">
    @csrf
@if ( $RangeDate->is_active )
      <button type="submit" class="btn btn-sm btn-success">Active</button>
@else
      <button type="submit" class="btn btn-sm btn-warning">Inactive</button>
@endif
    </form>
@endif
  </td>
@if ( $course_auth_count )
  <td align="center" class="range_date_count_btn" data-range_date_id="{{ $RangeDate->id }}">
    <button type="button" class="btn btn-sm btn-info">
    {{ $course_auth_count ?: '-' }}
    </button>
  </td>
@else
  <td align="center">-</td>
@endif
  <td align="right">{{ $RangeDate->StartDate( 'ddd MMM DD YYYY' ) }}</td>
  <td align="right">{{ $RangeDate->EndDate(   'ddd MMM DD YYYY' ) }}</td>
  <td>{{ $RangeDate->times }}</td>
  <td align="right">{{ $RangeDate->price }}</td>
</tr>


@if ( $course_auth_count )
<tr id="range_date_students_{{ $RangeDate->id }}" style="display: none">
  <td colspan="7">
    <table border="0" cellspacing="0" cellpadding="0">
@foreach ( $CourseAuths->get( $RangeDate->id ) as $CourseAuth )
    <tr>
      <td>{{ $CourseAuth->User }}</td>
      <td><a href="mailto:{{ $CourseAuth->User->email }}">{{ $CourseAuth->User->email }}</a></td>
@endforeach
    </table>
  </td>
</tr>
@endif

@endforeach

</table>


<script>
window.addEventListener( 'load', function() {

    $( '.range_date_count_btn' ).click(function() {
        let range_date_id = $( this ).data( 'range_date_id' );
        $( '#range_date_students_' + range_date_id ).toggle();
    });

});
</script>
