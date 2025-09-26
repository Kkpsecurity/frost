@extends('layouts.app')

@section('page-title', $content['title'])
@section('page-keywords', $content['keywords'])
@section('page-description', $content['description'])

@section('content')
    @include('frontend.partials.breadcrumbs')
    <div class="course-page-area frost-secondary-bg area-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="{{ $course->image }}" class="img-fluid rounded-start" alt="D Class Security License">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <div class="card- text-dark text-dark">{!! html_entity_decode($course->description) !!}</p>
                                        <p class="card-text">
                                            <small class="text-muted">Category: {{ $course->category }} |
                                                Sub-category: {{ $course->subCategory }}
                                            </small>
                                        </p>
                                    </div>
                                    <div>
                                        <h6>Price: {{ $course->price }}</h6>
                                    </div>
                                    {!! Helpers::EnrollButton($course) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
