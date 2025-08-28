{{-- Blog Sidebar --}}
<div class="col-lg-4 col-md-5 col-sm-12">
    <div class="blog-sidebar">
        {{-- Search Widget --}}
        @include('frontend.blog.partials.widgets.search-widget')

        {{-- Categories Widget --}}
        @include('frontend.blog.partials.widgets.categories-widget')

        {{-- Recent Posts Widget --}}
        @include('frontend.blog.partials.widgets.recent-posts-widget')

        {{-- Newsletter Widget --}}
        @include('frontend.blog.partials.widgets.newsletter-widget')

        {{-- Popular Tags Widget --}}
        @include('frontend.blog.partials.widgets.tags-widget')
    </div>
</div>
