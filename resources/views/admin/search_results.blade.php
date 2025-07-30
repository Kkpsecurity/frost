@extends('adminlte::page')

@section('page-title') {{$content['title']}}  @stop
@section('page-keywords') {{$content['keywords']}}  @stop
@section('page-description') {{$content['description']}}  @stop


@section('content')
    <!-- Contact Start -->
        <style>
            @import "http://fonts.googleapis.com/css?family=Roboto:300,400,500,700";

            .container { margin-top: 20px; }
            .mb20 { margin-bottom: 20px; }

            hgroup { padding-left: 15px; border-bottom: 1px solid #ccc; }
            hgroup h1 { font: 500 normal 1.625em "Roboto",Arial,Verdana,sans-serif; color: #2a3644; margin-top: 0; line-height: 1.15; }
            hgroup h2.lead { font: normal normal 1.125em "Roboto",Arial,Verdana,sans-serif; color: #2a3644; margin: 0; padding-bottom: 10px; }

            .search-result .thumbnail { border-radius: 0 !important; }
            .search-result:first-child { margin-top: 0 !important; }
            .search-result { margin-top: 20px; }
            .search-result .col-md-2 { border-right: 1px dotted #ccc; min-height: 140px; }
            .search-result ul { padding-left: 0 !important; list-style: none;  }
            .search-result ul li { font: 400 normal .85em "Roboto",Arial,Verdana,sans-serif;  line-height: 30px; }
            .search-result ul li i { padding-right: 5px; }
            .search-result .col-md-7 { position: relative; }
            .search-result h3 { font: 500 normal 1.375em "Roboto",Arial,Verdana,sans-serif; margin-top: 0 !important; margin-bottom: 10px !important; }
            .search-result h3 > a, .search-result i { color: #248dc1 !important; }
            .search-result p { font: normal normal 1.125em "Roboto",Arial,Verdana,sans-serif; }
            .search-result span.plus { position: absolute; right: 0; top: 126px; }
            .search-result span.plus a { background-color: #248dc1; padding: 5px 5px 3px 5px; }
            .search-result span.plus a:hover { background-color: #414141; }
            .search-result span.plus a i { color: #fff !important; }
            .search-result span.border { display: block; width: 97%; margin: 0 15px; border-bottom: 1px dotted #ccc; }
        </style>
        <div class="container-fluid p-5">
            <div class="row g-5">
                <div class="col-lg-12">

                    <hgroup class="mb20">
                        <h1>{{ __('Search Results') }}</h1>
                        <h2 class="lead"><strong class="text-danger">{{ $result ->count()  }}</strong>
                            {{ __('results were found for the search for') }} <strong class="text-danger">{{request()->get('search')}}</strong></h2>
                    </hgroup>

                    @if($result ->count() <= 0)
                        <div class="alert alert-danger">{{ __('No Search result found for') }}: <strong>{{request()->get('search')}}</strong></div>
                    @else
                        <section class="col-xs-12 col-sm-6 col-md-12">
                            @foreach($result as $item)
                                <article class="search-result row">
                                    <div class="col-xs-12 col-sm-12 col-md-3">
                                        @if($item->model == 'User')
                                            <a href="{{ $item->view_link }}" title="{{ $item->model}}" class="thumbnail">
                                                <img src="{{ $item->getAvatar($item) }}" alt="{{ $item->fullname() }}" />
                                            </a>
                                        @endif
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-2">
                                        <ul class="meta-search">
                                            @if($item->model == 'User')
                                                <li><i class="fa fa-calendar"></i> <span>{{ $item->created_at }}</span></li>
                                                <li><i class="fa fa-tags"></i> <span>{{ $item->model }}</span></li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-7 excerpet">
                                        <h3><a href="{{ $item->view_link }}" title="{{ $item->fullname() }}">{{ $item->fullname() }}</a></h3>
                                        <p>{{ $item->email }}</p>
                                    </div>
                                    <span class="clearfix borda"></span>
                                </article>
                            @endforeach
                        </section>
                    @endif
                </div>
            </div>
        </div>
@stop



