@extends('layouts.admin')

@section('content')
<div class="row">
     <div class="col-lg-12">
          <div id="FrostSupportCenter" ></div>
     </div>
</div>
@stop

@section('pre-scripts')
<script>
    window.APP_VER = {{ Helpers::AppVersion() }};
</script>
@endsection
