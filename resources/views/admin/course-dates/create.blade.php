@extends('adminlte::page')

@section('title', 'Schedule Course Date')

@section('content_header')
    <h1>Schedule New Course Date</h1>
@stop

@section('content')
    <div class="alert alert-warning">
        <i class="icon fas fa-exclamation-triangle"></i>
        <strong>Feature Under Development</strong><br>
        Course date scheduling is currently being rebuilt. Please contact a system administrator.
    </div>

    <a href="{{ route('admin.course-dates.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Course Dates
    </a>
@stop
