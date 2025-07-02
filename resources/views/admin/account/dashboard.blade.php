@extends('layouts.admin')

@section('page-title')                  {{$content['title']}}               @stop
@section('page-keywords')       {{$content['keywords']}}     @stop
@section('page-description')    {{$content['description']}}  @stop

@section('content')
   @include('admin.partials.titlebar')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="message-console"></div>
                @include('admin.account.partials.sidebar')
                @include('admin.account.content')
            </div>
        </div>
    </section>
@stop

@section('scripts')
    <script src="{{ asset('assets/admin/dist/js/account.js') }}"></script>
@endsection
