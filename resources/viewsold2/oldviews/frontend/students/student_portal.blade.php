@extends('layouts.app')

@section('page-title') {{ $content['title'] }} @stop
@section('page-keywords') {{ $content['keywords'] }} @stop
@section('page-description') {{ $content['description'] }} @stop

@section('styles')
    <style>
        #StudentPortal {
            margin-top: 145px;
            min-height: 600px;
        }

        @media (max-width: 520px) {
            #StudentPortal {
                margin-top: 165px;
            }
        }
    </style>
@endsection

@section('content')
    <section id="StudentPortal"></section>
@stop

@section('scripts')
    <audio preload="auto" id="sound_challenge" src="{{ vasset('assets/sound/challenge.mp3') }}"></audio>
    <script>
         window.addEventListener('load', function() {
            new reloadr('/mix-manifest.json', 10);
        });
    </script>
@endsection
