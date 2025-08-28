@props([
    'posts' => collect(), 
    'categories' => collect(),
    'pageTitle' => null,
    'pageDescription' => null
])

<main class="main-page-content">
    {{-- Blog Content Area - 2 Column Layout --}}
    <div class="blog-page-area frost-white-bg area-padding-2">
        <div class="container">
            {{-- Blog Header --}}
            <x-frontend.panels.blogs.list.list-header 
                :title="$pageTitle"
                :description="$pageDescription" 
            />

            <div class="row">
                {{-- Main Blog Posts List --}}
                <x-frontend.panels.blogs.list.posts-list :posts="$posts" />
                {{-- Sidebar --}}
                <x-frontend.panels.blogs.list.list-sidebar :posts="$posts" :categories="$categories" />
            </div>
        </div>
    </div>

    {{-- Newsletter CTA Section --}}
    <x-frontend.panels.blogs.details.newsletter-cta />
</main>
