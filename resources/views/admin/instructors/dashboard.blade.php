@extends('layouts.admin')

@section('content')
<div class="row">
     <div class="col-lg-12">
          <div id="InstructorPortal" ></div>
     </div>
</div>
@stop

@section('scripts')
<script>
     window.addEventListener('load', function() {
         new reloadr('/mix-manifest.json', 10);
     });
 </script>
@endsection
