@php

$current_date  = clone $first_date;
$current_month = 0;

@endphp
@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )

  <div class="tablewrapper pb-4">

    <div class="form-group row justify-content-center">
      <div class="col-auto">
        <select class="form-control form-control-lg doRouteSelect">
@foreach ( RCache::Courses()->where( 'is_active', 'true' ) as $_Course )
          <option value="{{ route( 'admin.temp.course_dates', $_Course ) }}" @if ( $Course->id == $_Course->id ) selected @endif>
            {{ $_Course->title_long }}
          </option>
@endforeach
        </select>
      </div>
    </div>


    <div id="course_dates_grid">
@while ( $current_date->lt( $last_date ) )

@if ( $current_month != $current_date->month )
@php $current_month = $current_date->month; @endphp
      <div class="month_row">
        {{ $current_date->isoFormat( 'YYYY MMMM' ) }}
      </div>
@endif

@foreach ( range( 0, 6 ) as $idx )
@if ( $record = $dates->get( $current_date->isoFormat( 'YYYY-MM-DD' ) ) )
      <div @class([ 'date_col', 'next_month' => ( $record->month != $current_month ) ])>
        <button type="button" data-route="{{ $record->route }}" @class([
          'date_toggle',
          'btn', 'btn-sm',
          'btn-success' => $record->is_active,
          'btn-warning' => ! $record->is_active,
        ])>{{ $current_date->isoFormat( 'ddd DD' ) }}</button>
      </div>
@else
      <div class="date_col no_course_date">
        {{ $current_date->isoFormat( 'ddd DD' ) }}
      </div>
@endif
@php $current_date->addDays( 1 ); @endphp
@endforeach

@endwhile
    </div>

  </div>


@include( 'admin.temp.partials.asset-loader' )

<style>

select { width: auto; }

#course_dates_grid
{
    display:                grid;
    grid-template-columns:  repeat( 7, 1fr );
    column-gap:             5px;
    row-gap:                3px;
}

#course_dates_grid > *
{
    white-space:        nowrap;
}

.month_row
{
    grid-column:        1 / -1;
    padding:            4px 12px;
    font-weight:        bold;
    background-color:   #bbb;
    border-radius:      3px;
}

.date_col
{
    height:             42px;
    padding:            5px;
    text-align:         center;

    /* border: 1px solid red; */
}

.no_course_date
{
    /* fix alignment */
    padding-top: 8px;
}

.date_toggle { width: 70px; }
.next_month  { background-color: #ccc; }

</style>


<script>
window.addEventListener( 'load', function() {

    $( '.date_toggle' ).click(function() {

        let button = $( this );

        $.post( button.data( 'route' ), function( res ) {

            if ( res.is_active )
                button.removeClass( 'btn-warning' ).addClass( 'btn-success' );
            else
                button.removeClass( 'btn-success' ).addClass( 'btn-warning' );

            button.blur();

        }, 'json' )
        .fail( function( res ) { alert( "Server Fail:\n" + res.responseText ); });

    });

});
</script>

@endsection
