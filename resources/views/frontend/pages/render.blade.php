{{-- Site Home Page - Uses Site Layout Component --}}
{{-- Page data is now passed from the SitePageController --}}

<x-site.site-wrapper :title="$content['title'] ?? 'Welcome to ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Professional security training platform' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security, training, certification' }}">
    </x-slot:head>

    <x-site.partials.header />

    <main class="main-page-content">
        <x-site.render-panels :page="$content" />
    </main>

    <x-site.partials.footer />
</x-site.site-wrapper>
