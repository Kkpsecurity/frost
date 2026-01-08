@extends('adminlte::page')

@section('title', 'Support Dashboard')

@section('content_header')
    <h1>Support Dashboard</h1>
@stop

@section('content')
    <div id="support-dashboard-container"></div>
    <script id="support-props" type="application/json">
        {!! json_encode([
            'isAdmin' => $isAdmin ?? false,
            'isSysAdmin' => $isSysAdmin ?? false
        ]) !!}
    </script>
@stop

@section('js')
    @vite('resources/js/support.ts')
@stop
