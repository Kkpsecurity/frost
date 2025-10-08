@extends('layouts.fe_zoom')



@section('content')
    <div id="StudentZoomPlayer" 
        data-course_date_id="{{ $content['course_date_id'] }}"
        data-course_auth_id="{{ $content['course_auth_id'] }}"
    ></div>
@stop

