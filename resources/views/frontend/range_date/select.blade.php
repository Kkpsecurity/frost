@extends('layouts.app')

@section('title', $content['title'])
@section('page-keywords', $content['keywords'])
@section('page-description', $content['description'])

@section('styles')
    <style>
        .list-group-item:hover {
            background-color: #f5f5f5;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid bg-light py-5" style="margin-top: 120px;">
        <div class="container bg-light py-4">

            <div class="alert alert-info text-white ">
                <h4 class="text-white">
                    Congratulations on completing the online portion of your training.
                </h4>
                <p>
                    When selecting your range date, you must coordinate with the K Instructor
                    at the location you have chosen, their contact information will be provided
                    to you. If you have any questions about the price of the training, firearm
                    rental, ammunition purchases, directions, parking, proper range attire, you
                    must contact the K Instructor that will be providing you with the range
                    training. Do not forget that you are required to print a copy of your
                    certificate to be given to your range training instructor upon arrival at
                    your selected range.
                </p>
            </div>

            {{-- Student selects No Range Date --}}
            <div class="alert" role="alert">
                <h4 class="alert-heading text-dark">
                    Have Your Own Training Range
                </h4>
                <p class="mb-3" style="font-size: 18px">
                    If you have your have a preferred range for completing your training, you can set your own range by
                    clicking the button below.
                </p>
                <form method="post" action="{{ route('range_date.update', [$CourseAuth, -1]) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary float-center">Have My Own Range</button>
                </form>
            </div>


            @foreach ($UpcomingRangeDates as $Record)
                <h3 class="mt-4">{{ $Record->get('Range')->city }}</h3>

                <ul class="list-group mt-2">
                    @foreach ($Record->get('RangeDates') as $RangeDate)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $RangeDate->DateStr() }}</span>
                            <form method="post" action="{{ route('range_date.update', [$CourseAuth, $RangeDate]) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Select</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endforeach

        </div>
    </div>
@endsection
