@extends('layouts.admin')

@section('page-title') {{ $content['title'] }} @stop
@section('page-keywords') {{ $content['keywords'] }} @stop
@section('page-description') {{ $content['description'] }} @stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @include('admin.partials.titlebar')
                    <section class="content">
                        @include('admin.partials.admin-messages')
                        <div class="container-fluid">
                            @include('admin.frost.support.dashboard.toprow')
                        </div>
                        <div class="container mt-5" style="border-radius: 25px">
                            <h3>Search for a Student</h3>
                            @include('admin.frost.support.dashboard.search')
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
@endsection
