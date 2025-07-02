<?php
$isofmt = 'ddd MM/DD HH:mm';
?>
@extends('layouts.admin')


@section('content')
@include( 'admin.temp.partials.messages' )


  <div class="tablewrapper mb-2">

    <a href="{{ route( 'admin.temp.course_auths' ) }}" class="btn btn-secondary navBackBtn">Return to List</a>

    @include( 'admin.temp.course_auth-partials.info' )

  </div>

  <div class="midwrapper mb-2" style="max-width: 440px;">
@if ( ! is_null( $CourseAuth->GetUser()->student_info ) )

    @include( 'admin.temp.course_auth-partials.student_info' )

@else

    <div class="text-center">
      Student has not yet entered their DOL information.
    </div>

@endif
  </div>


  <div class="tablewrapper mb-2">

    @include( 'admin.temp.course_auth-partials.lessons' )

  </div>

  <div class="tablewrapper pb-4">

    @include( 'admin.temp.course_auth-partials.exams' )

  </div>


@include( 'admin.temp.partials.asset-loader' )
@endsection
