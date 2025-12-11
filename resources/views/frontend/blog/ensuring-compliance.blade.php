{{-- Ensuring Compliance Blog Post (modern theme wrapper) --}}
<x-site.site-wrapper :title="$content['title'] ?? 'Ensuring Compliance with Online Security Training'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Essential guide to maintaining compliance in security operations, understanding regulations, and avoiding common violations.' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security compliance, firearms training, online training, recordkeeping' }}">
    </x-slot:head>

    <x-site.partials.header />


    <div class="container-fluid m-0 p-0">
        <div class="blog-detail-page">
            <!-- Hero Section -->
            <x-frontend.panels.blogs.blog-hero-compliance />

            <!-- Content Section -->
            <x-frontend.panels.blogs.ensuring-compliance />

        </div>
    </div>



    <x-site.partials.footer />

</x-site.site-wrapper>
