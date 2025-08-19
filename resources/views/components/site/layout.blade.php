{{-- Site Layout Component for Frontend Pages --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>

    {{-- Site Frontend Assets --}}
    @vite(['resources/css/app.css','resources/js/student.ts'])

    {{-- Page-specific head content --}}
    {{ $head ?? '' }}
</head>
<body class="antialiased">
    {{-- Header slot for navigation, etc. --}}
    {{ $header ?? '' }}

    {{-- Main content area --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Footer slot --}}
    {{ $footer ?? '' }}

    {{-- Page-specific scripts --}}
    {{ $scripts ?? '' }}
</body>
</html>
