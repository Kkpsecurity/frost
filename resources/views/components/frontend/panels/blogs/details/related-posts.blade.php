@props(['post'])

{{-- Related Posts Section --}}
@php
    $relatedPosts = App\Models\BlogPost::where('is_published', true)
        ->where('category', $post->category)
        ->where('id', '!=', $post->id)
        ->orderBy('published_at', 'desc')
        ->limit(3)
        ->get();
@endphp

@if ($relatedPosts && $relatedPosts->count() > 0)
    <section class="related-posts mt-5">
        <h3 class="text-white mb-4">{{ __('frontend.blog.related_articles') }}</h3>
        <div class="row">
            @foreach ($relatedPosts as $relatedPost)
                <div class="col-md-6 mb-4">
                    <div class="related-post-card bg-dark border border-secondary rounded shadow h-100">
                        @if ($relatedPost->featured_image)
                            <div class="related-post-image">
                                <img src="{{ $relatedPost->featured_image }}" alt="{{ $relatedPost->title }}"
                                    class="img-fluid rounded-top" style="height: 200px; object-fit: cover; width: 100%;">
                            </div>
                        @endif
                        <div class="p-3">
                            <span
                                class="badge bg-primary mb-2">{{ ucfirst(str_replace('-', ' ', $relatedPost->category ?? 'General')) }}</span>
                            <h5 class="mb-2">
                                <a href="{{ route('blog.show', $relatedPost->slug) }}"
                                    class="text-decoration-none text-white">{{ $relatedPost->title }}</a>
                            </h5>
                            <p class="text-white-50 small mb-2">
                                <i class="fas fa-calendar me-1"></i>{{ $relatedPost->published_at->format('M j, Y') }}
                                <i class="fas fa-clock ms-2 me-1"></i>{{ $relatedPost->read_time ?? 5 }}
                                {{ __('frontend.blog.min_label') }}
                            </p>
                            <p class="text-white-50 mb-3">
                                {{ Str::limit(strip_tags($relatedPost->excerpt ?? $relatedPost->content), 120) }}</p>
                            <a href="{{ route('blog.show', $relatedPost->slug) }}"
                                class="btn btn-outline-light btn-sm">{{ __('frontend.blog.read_more') }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
