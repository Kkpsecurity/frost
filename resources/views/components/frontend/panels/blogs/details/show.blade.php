@props(['post'])

{{-- Blog Post Detail Layout --}}
<main class="main-page-content frost-secondary-bg">
    <div class="container py-5">
        <div class="row">
            {{-- Main Content --}}
            <div class="col-lg-8">
                <article class="blog-post-detail">
                    <x-frontend.panels.blogs.details.post-header :post="$post" />
                    <x-frontend.panels.blogs.details.post-content :post="$post" />
                    <x-frontend.panels.blogs.details.post-footer :post="$post" />
                    <x-frontend.panels.blogs.details.related-posts :post="$post" />
                </article>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                @php
                    $sidebarCategories = App\Models\BlogPost::published()
                        ->pluck('category')
                        ->filter()
                        ->unique()
                        ->values();
                @endphp
                <x-frontend.panels.blogs.sidebar :categories="$sidebarCategories" :current-post="$post" />
            </div>
        </div>
    </div>

    {{-- Newsletter CTA --}}
    <x-frontend.panels.blogs.details.newsletter-cta />
</main>
