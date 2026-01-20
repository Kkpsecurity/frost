@extends('adminlte::page')

@section('title', 'Create Lesson')

@section('content_header')
    <h1>Create New Lesson</h1>
@stop

@section('content')
    <div class="alert alert-warning">
        <i class="icon fas fa-exclamation-triangle"></i>
        <strong>Feature Under Development</strong><br>
        Lesson creation is currently being rebuilt. Please contact a system administrator.
    </div>

    <a href="{{ route('admin.lessons.management.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Lessons
    </a>
@stop
