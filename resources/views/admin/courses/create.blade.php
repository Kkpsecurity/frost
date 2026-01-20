@extends('adminlte::page')

@section('title', 'Create Course')

@section('content_header')
    <h1>Create New Course</h1>
@stop

@section('content')
    <div class="alert alert-warning">
        <i class="icon fas fa-exclamation-triangle"></i>
        <strong>Feature Under Development</strong><br>
        Course creation is currently being rebuilt. Please contact a system administrator to create new courses.
    </div>

    <a href="{{ route('admin.courses.management.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Courses
    </a>
@stop
