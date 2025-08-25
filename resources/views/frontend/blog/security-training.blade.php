{{-- Security Training Blog Post --}}
<x-site.site-wrapper :title="$content['title'] ?? 'Comprehensive Security Training Programs in Florida'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Learn about comprehensive security training programs available in Florida, including Class D and Class G license requirements.' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security training, class d, class g, florida, online training, firearms, compliance, security officer' }}">
    </x-slot:head>

    <x-site.partials.header />
    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <div class="blog-detail-page">
            <!-- Hero Section -->
            <x-frontend.panels.blogs.blog-hero />
            <x-frontend.panels.blogs.security-training />
        </div>
    </div>

    <x-site.partials.footer />

</x-site.site-wrapper>

<style>
.blog-hero {
    background: linear-gradient(135deg, var(--frost-primary-color) 0%, var(--frost-secondary-color) 100%);
}

.breadcrumb-item a:hover {
    color: var(--frost-highlight-color) !important;
}

.feature-icon {
    font-size: 2.5rem;
}

.program-highlight {
    background: var(--frost-secondary-bg);
    padding: 2rem;
    border-radius: 12px;
    border-left: 4px solid var(--frost-primary-color);
}

.feature-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.feature-item {
    background: var(--frost-secondary-bg);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.feature-item i {
    font-size: 1.5rem;
    margin-top: 0.25rem;
}

.feature-item p {
    margin: 0;
}

.cta-section {
    background: var(--frost-secondary-bg) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.bg-dark-card {
    background-color: var(--frost-secondary-bg) !important;
}

@media (max-width: 768px) {
    .blog-meta {
        flex-direction: column;
        gap: 1rem !important;
    }

    .advantage-cards .card {
        margin-bottom: 1rem;
    }

    .feature-item {
        flex-direction: column;
        text-align: center;
    }
}
</style>
