@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )


  <div class="midwrapper">

    <a href="{{ route( 'admin.temp.ranges' ) }}" class="btn btn-secondary navBackBtn">Return to List</a>

    @include( 'admin.temp.ranges-partials.range_update_form' )

  </div>

  @if ( $Range->id != -1 )
  <div class="midwrapper">

    @include( 'admin.temp.ranges-partials.range_info' )

  </div>
  @endif


@include( 'admin.temp.partials.asset-loader' )
@endsection
