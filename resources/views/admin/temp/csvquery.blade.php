@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )

  <div class="tablewrapper">

    <h3 class="mb-3">CSV Query</h3>

    <form method="post" action="{{ route( 'admin.temp.orders.csv.dump' ) }}">
    @csrf

      <div class="row mb-3">
        <label class="col-4 col-form-label" for="name">Start Date</label>
        <div class="col">
          <input type="text" name="start_date" id="start_date" class="form-control" placeholder="YYYY-MM-DD" />
        </div>
      </div>

      <div class="row mb-3">
        <label class="col-4 col-form-label" for="name">End Date</label>
        <div class="col">
          <input type="text" name="end_date" id="end_date" class="form-control" placeholder="YYYY-MM-DD" />
        </div>
      </div>

      <div class="row mb-3">
        <label class="col-4 col-form-label" for="course_id">Course</label>
        <div class="col">
          <select name="course_id" id="course_id" class="form-control">
            <option value=""></option>
@foreach ( RCache::Courses()->where( 'is_active', true ) as $Course )
            <option value="{{ $Course->id }}" @if ( old( 'course_id' ) == $Course->id ) selected @endif >{{ $Course }}</option>
@endforeach
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="offset-4 col">
          <input type="submit" class="form-control btn btn-primary" value="Download CSV" />
        </div>
      </div>


    </form>

  </div>


@include( 'admin.temp.partials.asset-loader' )
@endsection
