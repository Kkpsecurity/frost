<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta charset="utf-8" />

<meta name="apple-mobile-web-app-capable" content="yes" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />

<meta name="application-name" content="{{ env('app_name', 'Welcome: ') }}" />
<meta name="referrer" content="unsafe-url" />

<meta name="csrf-token" content="{{ csrf_token() }}" />

<meta name="keywords" content="@yield('page-keywords')" />
<meta name="description" content="@yield('page-description')" />
<meta name="referrer" content="strict-origin-when-cross-origin">

<link rel="preconnect" href="//fonts.googleapis.com" />

<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

<link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/jqvmap/jqvmap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css?v=3.2.0') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/summernote/summernote-bs4.min.css') }}">
@yield('styles')

<title> {{ setting('company_name', 'Welcome: ') }} | @yield('page-title') </title>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
    <script type="text/javascript" src="{{ asset('assets/vendor/utils/html5shiv.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/utils/respond.min.js') }}"></script>
<![endif]-->
