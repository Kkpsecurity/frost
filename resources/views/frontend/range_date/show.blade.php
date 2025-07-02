@extends('layouts.app')

@section('title', $content['title'])
@section('page-keywords', $content['keywords'])
@section('page-description', $content['description'])

@section('content')
    <div class="container-fluid bg-light py-5" style="margin-top: 140px">
        <div class="container py-4">

            <h2 class="mb-4">Range Details</h2>

            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item">Dates: {{ $RangeDate->DateStr() }}</li>
                <li class="list-group-item">Range: {{ $Range->name }}</li>
                <li class="list-group-item">Address:<br />@nl2br( $Range->address )</li>
                <li class="list-group-item">Instructor: {{ $Range->inst_name }}</li>

                @if ( $Range->inst_email )
                    <li class="list-group-item">
                        Email: <a href="mailto:{{ $Range->inst_email }}">{{ $Range->inst_email }}</a>
                    </li>
                @endif

                @if ( $Range->inst_phone )
                    <li class="list-group-item">Phone: {{ $Range->inst_phone }}</li>
                @endif

                <li class="list-group-item">Price: ${{ $Range->price }}</li>
            </ul>

            <div class="bg-white p-3 rounded">
              <h4>Info:</h4>
              {!! nl2br($Range->range_html) !!}
          </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* To make email links have default link colors */
        a[href^="mailto:"] {
            color: inherit;
            text-decoration: underline;
        }
    </style>
@endsection
