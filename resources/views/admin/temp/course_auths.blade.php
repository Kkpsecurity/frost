@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )


  <div class="text-center">
    <em>Instructors / Admins not shown</em>
  </div>

  <div class="tablewrapper">

    @include( 'admin.temp.course_auth-partials.active' )

  </div>

  <div class="tablewrapper">

    @include( 'admin.temp.course_auth-partials.completed' )

  </div>


<script>
window.addEventListener( 'load', function() {


    $( '#active_course_id' ).change(function() {

        let course_id = $( this ).val();

        $( '.active_course_row' ).each(function() {
            if ( course_id && course_id != $( this ).data( 'course_id' ) )
                $( this ).hide();
            else
                $( this ).show();
        });

    }).trigger( 'change' );


    $( '#inactive_course_id' ).change(function() {

        let course_id = $( this ).val();

        $( '.inactive_course_row' ).each(function() {
            if ( course_id && course_id != $( this ).data( 'course_id' ) )
                $( this ).hide();
            else
                $( this ).show();
        });

    }).trigger( 'change' );


});
</script>


@include( 'admin.temp.partials.asset-loader' )
@endsection
