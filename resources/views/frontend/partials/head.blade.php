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

<link rel="preconnect" href="https://cdn.jsdelivr.net" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

<link rel="stylesheet" id="theme-link" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cookieconsent@3.1.1/build/cookieconsent.min.css" />

<!-- Icon Font Stylesheet -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Theme Styles -->
<link href="{{ vasset('assets/css/vendor.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ vasset('assets/css/theme.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ vasset('assets/css/site.css') }}" rel="stylesheet" type="text/css" />

<!-- Favicon Icon -->
<link href="{{ asset('assets/img/favicon.png') }}" sizes="128x128" rel="shortcut icon" type="image/x-icon" />
<link href="{{ asset('assets/img/favicon.png') }}" sizes="128x128" rel="shortcut icon" />

@yield('styles')

<title>{{ setting('company_name', 'Welcome: ') }} | @yield('page-title') </title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
    <script type="text/javascript" src="{{ asset('assets/vendor/utils/modernizr-2.8.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/utils/html5shiv.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/utils/respond.min.js') }}"></script>
<![endif]-->

@if ( App::environment() == 'production' && request()->segment(1) != 'classroom' )
<!-- Google tag (gtag.js) --> <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17215206357"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'AW-17215206357'); </script>
@endif
