<!doctype html><html lang="{{ App::getLocale() }}">
<head>@include('admin.partials.head')</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    @include('admin.partials.navbar')
    @include('admin.partials.sidebar')

    <div class="content-wrapper">
        @yield('content')
    </div>

    <footer class="main-footer">
        <strong>{{ __('Copyright') }} &copy; {{ now()->addYears(1)->format('Y') }} {{ config('app.name') }}.</strong>
        {{ __('All rights reserved') }}.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version:</b> {{ config('define.version', '1.0.0') }}
        </div>
    </footer>

    <aside class="control-sidebar control-sidebar-dark">
        {{-- Chat apps--}}
    </aside>
</div>

@yield('modals')
@yield('pre-scripts')

<script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/sparklines/sparkline.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/summernote/summernote-bs4.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('assets/admin/dist/js/adminlte.js?v=3.2.0') }}"></script>
{!! JSData::toHTML() !!}
<script src="{{ vasset('assets/js/site.js') }}"></script>
<script src="{{ vasset('js/admin.js') }}"></script>
@yield('scripts')
<script>
    $(document).ready(function() {
        // Close all parent menu items initially
        $(".nav-item.menu-open").removeClass("menu-open");

        // Handle menu item clicks
        $(".nav-item").on("click", function() {
            if (!$(this).hasClass("menu-open") && !$(this).find(".menu-open").length) {
                // Close all open parent menu items
                $(".nav-item.menu-open").removeClass("menu-open");
            }
        });
    });
</script>
</body></html>
