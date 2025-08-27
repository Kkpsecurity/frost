{{-- Blog Post Card Following Template Theme --}}
@props(['post'])

<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="single-blog">
        <div class="blog-image">
            <a class="image-scale" href="{{ route('blog.show', $post->slug) }}">
                @if($post->featured_image)
                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}">
                @else
                    <img src="{{ asset('themes/frost/bultifore/img/blog/b1.jpg') }}" alt="{{ $post->title }}">
                @endif
            </a>
            <div class="blog-content">
                <div class="blog-meta">
                    <span class="admin-type">
                        <i class="fa fa-user"></i>
                        {{ $post->author }}
                    </span>
                    <span class="date-type">
                       <i class="fa fa-calendar"></i>
                        {{ $post->formatted_published_at }}
                    </span>
                    <span class="comments-type">
                        <i class="fa fa-clock-o"></i>
                        {{ $post->read_time_text }}
                    </span>
                </div>
                <a href="{{ route('blog.show', $post->slug) }}">
                    <h4>{{ $post->title }}</h4>
                </a>
                <p>{{ $post->excerpt_or_truncated_content }}</p>
                <a class="blog-btn anti-bttn" href="{{ route('blog.show', $post->slug) }}">Read more</a>
            </div>
        </div>
    </div>
</div>

<style>
.blog-post-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
}

.blog-post-card .card-title a:hover {
    color: #3498db !important;
}
</style>
