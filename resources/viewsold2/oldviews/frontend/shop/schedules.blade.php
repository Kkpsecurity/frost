@extends('layouts.app')

@section('page-title', $content['title'])
@section('page-keywords', $content['keywords'])
@section('page-description', $content['description'])

@section('content')
    @include('frontend.partials.breadcrumbs')
    <div class="course-page-area frost-secondary-bg area-padding">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <h2>Course Schedules</h2>

                </div>
                <div class="col-md-8">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth'
            });

            calendar.render();

            var formattedEvents = formatEvents(@json($allEvents));
            // Load initial events for the G Course (as it's active by default)
            calendar.addEventSource(@json($allEvents));
        });


        function formatEvents(events) {
            return events.map(event => {
                event.backgroundColor = "#FF5733"; // Color for all events
                event.borderColor = "#FF5733"; // Border color for all events
                return event;
            });
        }
    </script>
@endsection
