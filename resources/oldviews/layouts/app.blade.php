<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>@include('frontend.partials.head')</head>

<body class="font-sans antialiased">

    <!--[if lt IE 8]>
        <div class="alert alert-danger browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</div>
    <![endif]-->

    <div id="preloaders"></div>

    @include('frontend.partials.header')
    @yield('content')
    @include('frontend.partials.footer')

    <button class="scrollUp btn btn-primary rounded-circle">
        <i class="fa fa-arrow-up"></i>
    </button>

    @if (Request()->segment(1) == 'classroom')
        @php
            $course_auth_id = request()->segment(4);
        @endphp

        <div id="props" data-course-auth-id="{{ $course_auth_id }}"></div>
    @endif

    @yield('modals')
    @yield('pre-scripts')

    @production
        <script>
            (function(d, w, c) {
                w.ChatraID = 'vwXc7koSXftnpPvwG';
                var s = d.createElement('script');
                w[c] = w[c] || function() {
                    (w[c].q = w[c].q || []).push(arguments);
                };
                s.async = true;
                s.src = 'https://call.chatra.io/chatra.js';
                if (d.head) d.head.appendChild(s);
            })
            (document, window, 'Chatra');
        </script>
    @endproduction

    <!-- ### S C R I P T S ############################# -->
    <script src="{{ vasset('assets/js/vendor.js') }}"></script>
    <script src="{{ vasset('assets/js/theme.js') }}"></script>
    <script src="{{ vasset('assets/js/site.js') }}"></script>
    <script src="{{ vasset('js/app.js') }}"></script>

    @yield('scripts')


</body></html>
