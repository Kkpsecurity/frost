@extends('layouts.app')

@section('title', $content['title'])
@section('page-keywords', $content['keywords'])
@section('page-description', $content['description'])

@section('content')
    @include('frontend/partials/breadcrumbs')
    <div class="row settings">
        <div class="col-lg-12">
            <h1>Settings</h1>
        </div>
    </div>

@endsection
