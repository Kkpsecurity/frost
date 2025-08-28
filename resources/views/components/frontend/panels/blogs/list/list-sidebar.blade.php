@props(['posts', 'categories'])

{{-- Blog Sidebar --}}
<div class="col-lg-4 col-md-5 col-sm-12">
    <div class="blog-sidebar">
        {{-- Search Widget --}}
        <x-frontend.panels.blogs.widgets.search-widget />

        {{-- Categories Widget --}}
        <x-frontend.panels.blogs.widgets.categories-widget :categories="$categories" />

        {{-- Recent Posts Widget --}}
        <x-frontend.panels.blogs.widgets.recent-posts-widget :posts="$posts" />

        {{-- Newsletter Widget --}}
        <x-frontend.panels.blogs.widgets.newsletter-widget />

        {{-- Popular Tags Widget --}}
        <x-frontend.panels.blogs.widgets.tags-widget />
    </div>
</div>
