@extends('adminlte::page')

@section('title', 'Page Title')

@section('css')
    @vite('resources/css/admin.css')   
@stop


@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
      <div class="container-fluid"></div>
@stop

@section('js')
    <script>
   
    </script>
@stop
