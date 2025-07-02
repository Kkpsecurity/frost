@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )


  <div class="midwrapper">

    <a href="{{ route( 'admin.temp.ranges' ) }}" class="btn btn-secondary navBackBtn">Return to List</a>

    <h3 class="mb-3">Create Range</h3>

    @include( 'admin.temp.ranges-partials.range_create_form' )

  </div>


@include( 'admin.temp.partials.asset-loader' )
@endsection
