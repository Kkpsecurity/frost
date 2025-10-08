{{-- Courses Listing Page --}}
<x-site.layout :title="$content['title'] ?? 'Our Courses'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Professional security training courses' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security, training, certification' }}">
    </x-slot:head>

    <x-site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <x-panels.courses />
    </div>

    <x-site.partials.footer />
</x-site.layout>
