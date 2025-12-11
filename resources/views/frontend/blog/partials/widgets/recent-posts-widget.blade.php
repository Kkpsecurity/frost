{{-- Recent Posts Widget --}}
<div class="sidebar-widget recent-posts-widget mb-4">
    <div class="widget-header">
        <h5 class="widget-title">Recent Articles</h5>
    </div>
    <div class="widget-content">
        <div class="recent-posts">
            @if($posts->count() > 0)
                @foreach($posts->take(3) as $recentPost)
                    <div class="recent-post-item">
                        <div class="recent-post-image">
                            <a href="{{ route('blog.show', $recentPost->slug) }}">
                                <img src="{{ $recentPost->featured_image ? asset($recentPost->featured_image) : asset('images/Security-Page-1.jpg') }}" alt="{{ $recentPost->title }}">
                            </a>
                        </div>
                        <div class="recent-post-content">
                            <h6><a href="{{ route('blog.show', $recentPost->slug) }}">{{ Str::limit($recentPost->title, 50) }}</a></h6>
                            <span class="post-date">{{ $recentPost->published_at ? $recentPost->published_at->format('M j, Y') : $recentPost->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
