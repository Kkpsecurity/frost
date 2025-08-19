@extends('layouts.app')

@section('page-title', $content['title'])
@section('page-keywords', $content['keywords'])
@section('page-description', $content['description'])


@section('content')
    @include('frontend.partials.breadcrumbs')
    @include('frontend.panels.3column_courses_list')
@stop
