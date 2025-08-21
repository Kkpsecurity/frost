{{-- Site Home Page - Uses Site Layout Component --}}
{{-- Page data is now passed from the SitePageController --}}

<x-site.layout :title="$content['title'] ?? 'Welcome to ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Professional security training platform' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security, training, certification' }}">
    </x-slot:head>

    <x-site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <x-site.pages.render :page="$content" />
    </div>

    <x-site.partials.footer />
</x-site.layout>
