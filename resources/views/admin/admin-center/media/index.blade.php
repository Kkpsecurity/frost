@extends('adminlte::page')

@section('title', 'Media Manager')

@section('css')
    @vite('resources/css/admin.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Media Manager Styles Component -->
    <x-admin.media-manager.styles />
@stop


@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <!-- Media Manager Layout Component -->
    <x-admin.media-manager.layout />
@stop

@section('js')
    <!-- Media Manager Scripts Component -->
    <x-admin.media-manager.scripts />

    <!-- React Upload Modal -->
    @vite('resources/js/upload-modal-manager.tsx')
@stop
