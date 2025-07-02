@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )

  <div class="midwrapper">

    <a href="{{ route( 'admin.temp.ranges.show', $Range ) }}" class="btn btn-secondary navBackBtn">Return to {{ $Range }}</a>

  </div>


@if ( ! $Range->appt_only )
  @include( 'admin.temp.ranges-partials.range_dates_form' )
@endif

  <div class="tablewrapper">

    @include( 'admin.temp.ranges-partials.range_dates' )

  </div>


@include( 'admin.temp.partials.asset-loader' )
@endsection
