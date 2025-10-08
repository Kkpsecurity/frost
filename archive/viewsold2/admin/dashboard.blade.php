{{-- Admin Instructor Dashboard - Uses AdminLTE Package Layout --}}
@extends('adminlte::page')

@section('title', 'Instructor Dashboard')

@section('content_header')
    <x-admin.partials.titlebar
        title="Welcome Back!"
        :breadcrumbs="[
            ['title' => 'Admin', 'url' => url('admin')],
            ['title' => 'Dashboard']
        ]"
    />
@endsection

@section('content')
    {{-- Quick Stats Cards --}}

@endsection

@section('css')
    {{-- Custom timeline styles --}}

@endsection

@section('js')


@endsection
