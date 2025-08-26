{{-- Site Home Page - Uses Site Layout Component --}}
{{-- Page data is now passed from the SitePageController --}}

<x-frontend.site.site-wrapper :title="$content['title'] ?? 'Welcome to ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Professional security training platform' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security, training, certification' }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <main class="main-page-content">
        <x-frontend.site.render-panels :page="$content" />
    </main>

    <x-frontend.site.partials.footer />
</x-frontend.site.site-wrapper>
