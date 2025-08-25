{{-- Security Officer Career Guide Blog Post --}}
<x-site.site-wrapper :title="$content['title'] ?? 'How to Become a Security Officer: Complete Career Guide'">
    <x-slot:head>
        <meta name="description"
            content="{{ $content['description'] ?? 'Complete guide to becoming a security officer in Florida. Learn about training requirements, licensing, career opportunities, and professional development.' }}">
        <meta name="keywords"
            content="{{ $content['keywords'] ?? 'security officer, career guide, florida security, training requirements, class d license, class g license, firearms training, security career' }}">
    </x-slot:head>

    <x-site.partials.header />

    <div class="container-fluid m-0 p-0">
        <div class="blog-detail-page">
            <!-- Hero Section -->
            <x-frontend.panels.blogs.blog-hero-title />

            <!-- Content Section -->
            <x-frontend.panels.blogs.security-officer />
        </div>
    </div>

    <x-site.partials.footer />

</x-site.site-wrapper>
