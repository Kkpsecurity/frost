@props(['post'])


{{-- Post Content --}}
<div class="post-content-block py-4">
    @if ($post->content && strlen(strip_tags($post->content)) > 50)
        <div class="content">
            {!! $post->content !!}
        </div>
    @else
        {{-- Basic fallback for posts without sufficient content --}}
        <div class="content-section">
            <h3>{{ __('frontend.blog.overview') }}</h3>
            <p>{{ $post->excerpt_or_truncated_content }}</p>
            <p class="text-white-50"><em>{{ __('frontend.blog.content_updating') }}</em></p>
        </div>
    @endif

</div>
