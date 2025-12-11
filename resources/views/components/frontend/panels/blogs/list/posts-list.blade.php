@props(['posts'])

{{-- Blog Posts List Section --}}
<div class="col-lg-8 col-md-7 col-sm-12">
    <div class="blog-posts-wrapper">
        @if($posts->count() > 0)
            @foreach($posts as $post)
                <x-frontend.panels.blogs.list.post-card :post="$post" />
            @endforeach

            {{-- Pagination --}}
            @if($posts->hasPages())
                <div class="blog-pagination mt-5">
                    {{ $posts->links('pagination::bootstrap-4') }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-book fa-3x text-white-50 mb-3"></i>
                <h3 class="text-white">No Articles Available</h3>
                <p class="text-white-50">Check back soon for new security training insights and updates.</p>
            </div>
        @endif
    </div>
</div>
