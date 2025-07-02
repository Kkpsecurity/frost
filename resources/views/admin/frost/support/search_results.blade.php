@extends('layouts.admin')

@section('page-title') {{ $content['title'] }} @stop
@section('page-keywords') {{ $content['keywords'] }} @stop
@section('page-description') {{ $content['description'] }} @stop

<?php $searchResults = $content['students'] ?? []; ?>

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @include('admin.partials.titlebar')

                    <section class="content">
                        <div class="container-fluid">
                            @include('admin.frost.support.dashboard.toprow')
                        </div>
                        <div class="container mt-5" style="border-radius: 25px">
                            <h3>Search results</h3>
                            <ul class="list-group">
                                @foreach ($searchResults as $result)
                                    <?php $result->avatar = $result->getAvatar('thumb'); ?>
                                    <li class="list-group-item">
                                        <span>
                                            <img src="{{ $result->avatar }}" style="width: 30px; height: 30px;"
                                                class="mr-2" />
                                            {{ $result->fname }} {{ $result->lname }} - {{ $result->email }}
                                        </span>

                                        <span class="float-right">
                                            <a href="{{ route('admin.frost-support.dashboard.get-student-data', [$result->id]) }}"
                                                class="btn btn-primary btn-sm">View Student</a>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
@endsection
