{{-- Blog Details Page Following Site Structure --}}
<x-frontend.site.site-wrapper :title="$post->title . ' - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="{{ $post->meta_description ?: $post->excerpt_or_truncated_content }}">
        <meta name="keywords" content="{{ $post->meta_keywords ?: (is_array($post->tags) ? implode(', ', $post->tags) : '') }}">
        <meta name="author" content="{{ $post->author }}">

        {{-- Blog-specific CSS from your components structure --}}
        <link rel="stylesheet" href="{{ asset('css/components/blog.css') }}">
    </x-slot:head>

    <x-frontend.site.partials.header />
    {{-- Breadcrumbs Area --}}
    <x-frontend.ui.breadcrumbs />

    <x-frontend.panels.blogs.details.show :post="$post" />

    <x-frontend.site.partials.footer />

</x-frontend.site.site-wrapper>
