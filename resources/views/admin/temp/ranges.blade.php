@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )


  <div class="tablewrapper">

    <h3>Ranges
@if ( Auth::user()->IsSysAdmin() )
        <a href="{{ route( 'admin.temp.ranges.create' ) }}" class="btn btn-outline-danger float-right">Create Range</a>
@endif
    </h3>

    <table border="0" cellspacing="0" cellpadding="0">
    <tr class="header">
      <td></td>
      <td>City</td>
      <td>Name</td>
      <td>Price</td>
      <td></td>
    </tr>

@foreach ( $Ranges as $Range )
    <tr @if ( ! $Range->is_active ) style="background-color: #ddd;" @endif>
      <td>
        <a href="{{ route( 'admin.temp.ranges.show', $Range ) }}" class="btn btn-sm btn-primary">View</a>
      </td>
      <td @if ( ! $Range->is_active ) style="text-decoration: line-through" @endif>{{ $Range->city }}</td>
      <td @if ( ! $Range->is_active ) style="text-decoration: line-through" @endif>{{ $Range->name }}</td>
      <td @if ( ! $Range->is_active ) style="text-decoration: line-through" @endif align="right">{{ $Range->price }}</td>
      <td>
@if ( $Range->is_active )
        <a href="{{ route( 'admin.temp.ranges.showdates', $Range ) }}" class="btn btn-sm btn-secondary">Dates</a>
@endif
      </td>
    </tr>
@endforeach

    </table>

  </div>


@include( 'admin.temp.partials.asset-loader' )
@endsection
