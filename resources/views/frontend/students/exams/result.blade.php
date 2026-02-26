@extends('layouts.app')

@section('page-title') {{ $content['title'] }} @stop
@section('page-keywords') {{ $content['keywords'] }} @stop
@section('page-description') {{ $content['description'] }} @stop


@section('styles')
    <style>
        /* Importing Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap');

        /* Global settings */


        /* Header Settings */
        h3 {
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #333;
        }

        /* List Group */
        .list-group-item {
            border-right: none;
            border-left: none;
            background-color: transparent;
        }

        .list-group-item span:first-child {
            font-weight: bold;
            color: #555;
        }

        /* Card styling */
        .card {
            border-radius: 10px;
            border: none;
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-body {
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        /* Other global styling */
        p,
        ul {
            color: #666;
        }
    </style>
@endsection

@section('content')

    @include('frontend.partials.breadcrumbs')
    <div class="container">
        <section class="dashboard">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <h3 class="card-title text-dark">{{ __('frontend.exam.exam_results') }}</h3>
                    <p class="alert">{{ __('frontend.exam.results_intro') }}</p>

                    @if ($ExamAuth->is_passed)
                        <p class="alert alert-success">{{ __('frontend.exam.exam_passed') }}</p>
                    @else
                        <p class="alert alert-danger">{{ __('frontend.exam.exam_failed') }}</p>
                    @endif

                    <ul class="list-group list-group-flush">

                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('frontend.exam.completed_at') }}</span>
                            <span>{{ $ExamAuth->CompletedAt('ddd MM/DD HH:mm') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('frontend.exam.score') }}</span> <span>{{ $ExamAuth->score }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('frontend.exam.is_passed') }}</span>
                            <span>{{ $ExamAuth->is_passed ? __('common.yes') : __('common.no') }}</span>
                        </li>

                        @if (!$ExamAuth->is_passed && $ExamAuth->next_attempt_at)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ __('frontend.exam.next_attempt_at') }}</span>
                                <span>{{ $ExamAuth->NextAttemptAt('ddd MM/DD HH:mm') }}</span>
                            </li>
                        @endif
                    </ul>


                    {{-- tweak this as you see fit --}}
                    {{-- 2024-08 REMOVED
                    @if ($ExamAuth->NeedsRangeSelect())
                        <p class="d-flex justify-content-center align-items-center" style="height: 120px; margin-top: -80px;">
                            <div class="text-center"><h4>In order to complete your license training requirements, <br />you must select a range and
                                date.<h4 />
                                <a class="btn btn-success btn-lg btn-rounded"
                                    href="{{ route('range_date.select', $ExamAuth->CourseAuth) }}">Select a Range Date</a>
                            </div>
                        </p>
                    @endif
                    --}}


                    @if ($Incorrect = $ExamAuth->IncorrectByLesson())
                        <!-- Title -->
                        <h5 class="mb-4">{{ __('frontend.exam.missed_questions') }}</h5>

                        <!-- List Group with Header -->
                        <ul class="list-group" style="width: 35rem">
                            <!-- Header Item -->
                            <li class="list-group-item bg-light">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ __('frontend.exam.title') }}</strong>
                                    <strong>{{ __('frontend.exam.missed') }}</strong>
                                </div>
                            </li>

                            <!-- List Items -->
                            @foreach ($Incorrect as $data)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $data->title }}</span>
                                        <span>{{ $data->missed }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                </div>
            </div>
        </section>
    </div>
@endsection
