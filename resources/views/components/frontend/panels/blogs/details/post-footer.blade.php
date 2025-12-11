@props(['post'])

{{-- Tags and Share Section --}}
<div class="post-footer mt-4 bg-light rounded p-3">
    @if($post->tags && is_array($post->tags) && count($post->tags) > 0)
    <div class="post-tags mb-3">
        <strong class="me-2">Tags:</strong>
        @foreach($post->tags as $tag)
        <span class="badge bg-secondary me-1">{{ $tag }}</span>
        @endforeach
    </div>
    @endif

    <div class="post-share">
        <strong class="me-2">Share:</strong>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
            <i class="fab fa-facebook-f"></i> Facebook
        </a>
        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" class="btn btn-sm btn-outline-info me-2">
            <i class="fab fa-twitter"></i> Twitter
        </a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-sm btn-outline-primary">
            <i class="fab fa-linkedin-in"></i> LinkedIn
        </a>
    </div>
</div>
