@extends('adminlte::page')

@section('title', 'Admin Dashboard - Frost')

@section('content_header')
    @include('admin.partials.impersonation-banner')
    <x-admin.dashboard.header />
@stop

@section('content')
    <x-admin.dashboard.user-stats />


@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .content-header h1 {
            color: #495057;
            font-weight: 600;
        }

        .small-box .icon {
            top: 10px;
            right: 10px;
        }

        .card-title {
            font-weight: 600;
        }

        .badge {
            font-size: 0.75rem;
        }

        .btn-block {
            width: 100%;
        }
    </style>
@stop

@section('js')
    <script> console.log('Frost Admin Dashboard loaded!'); </script>
@stop
