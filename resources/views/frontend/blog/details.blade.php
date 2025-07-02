@extends('layouts.app')

@section('page-title') {{ $content['title'] }} @stop
@section('page-keywords') {{ $content['keywords'] }} @stop
@section('page-description') {{ $content['description'] }} @stop

@section('content')
    @include('frontend.partials.breadcrumbs')
    <div class="blog-area frost-secondary-bg">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="blog-details">
                        <!-- single-blog start -->
                        <article class="blog-post-wrapper">
                            <div class="blog-banner">
                                <a href="#" class="blog-images">
                                    <img src="{{ asset('assets/img/blog/b1.jpg') }}" alt="" class="img-fluid">
                                </a>
                                <div class="blog-content">
                                    <div class="blog-meta p-2">
                                        <span class="admin-type p-1 text-dark">
                                            <i class="fa fa-user p-1"></i>
                                            STG Admin
                                        </span>
                                        <span class="date-type text-dark">
                                            <i class="fa fa-calendar"></i>
                                            {{ date('M d, Y') }}
                                        </span>
                                    </div>
                                    @include('frontend.blog.files.' . $slug)
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="right-side bg-light p-3 rounded">
                        <div>
                            <!-- Search Option Start -->
                            <form action="#">
                                <div class="d-flex">
                                    <input type="text" placeholder="Search..." class="form-control me-2">
                                    <button class="btn" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </form>
                            <!-- Search Option End -->
                        </div>
                        <div>
                            <div class="blog-category">
                                <h4>Categories</h4>
                                <ul class="list-unstyled">
                                    <li><a href="{{ url('blog/security-training') }}">Security Training</a></li>
                                    <li><a href="{{ url('blog/security-officer') }}">Security Officer</a></li>
                                    <li><a href="{{ url('blog/ensuring-compliance') }}">Security Compliance</a></li>
                                </ul>
                            </div>
                        </div>
                       
                        <div>
                            <div class="blog-tags">
                                <h4>Popular Tags</h4>
                                <ul class="list-inline">
                                    <li class="btn btn-sm btn-dark list-inline-item"><a href="#">SecurityTraining</a></li>
                                    <li class="btn btn-sm btn-dark list-inline-item"><a href="#">FloridaSecurity</a></li>
                                    <li class="btn btn-sm btn-dark list-inline-item"><a href="#">DLicense</a></li>
                                    <li class="btn btn-sm btn-dark list-inline-item"><a href="#">GLicense</a></li>
                                    <li class="btn btn-sm btn-dark list-inline-item"><a href="#">FirearmTraining</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
