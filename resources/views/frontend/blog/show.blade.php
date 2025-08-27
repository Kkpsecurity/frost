{{-- Blog Details Page Following Site Structure --}}
<x-frontend.site.site-wrapper :title="$post->title . ' - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="{{ $post->meta_description ?: $post->excerpt_or_truncated_content }}">
        <meta name="keywords" content="{{ $post->meta_keywords ?: implode(', ', $post->tags ?? []) }}">
        <meta name="author" content="{{ $post->author }}">

        {{-- Blog-specific CSS from your components structure --}}
        <link rel="stylesheet" href="{{ asset('css/components/blog.css') }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    {{-- Breadcrumbs Area with Better Background --}}
    <div class="page-area" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); min-height: 200px;">
        <div class="breadcumb-overlay" style="background: rgba(0,0,0,0.3);"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="breadcrumb text-center">
                        <div class="section-headline white-headline text-center">
                            <h3>Blog Details</h3>
                        </div>
                        <ul>
                            <li class="home-bread">Home</li>
                            <li><a href="{{ route('blog.index') }}">Blog</a></li>
                            <li>{{ ucfirst(str_replace('-', ' ', $post->category ?? 'General')) }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Blog Details Section - Full Width Single View --}}
    <div class="blog-section frost-secondary-bg py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    {{-- Main Content - Full Width --}}
                    <div class="col-12 mb-4">
                        <div class="post-card featured">
                            {{-- Featured Image --}}
                            @php
                                $getPostImage = function($post) {
                                    $imageMap = [
                                        'florida-gun-laws-2025' => 'florida-gun-laws.jpg',
                                        'essential-firearms-safety' => 'firearms-safety-training.jpg',
                                        'threat-assessment-techniques' => 'threat-assessment-security.jpg'
                                    ];
                                    return isset($imageMap[$post->slug])
                                        ? asset('assets/img/blog/' . $imageMap[$post->slug])
                                        : asset('assets/img/blog/b1.jpg');
                                };
                            @endphp

                            <div class="post-image">
                                <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : $getPostImage($post) }}" alt="{{ $post->title }}">
                                <div class="post-category">{{ ucfirst(str_replace('-', ' ', $post->category ?? 'General')) }}</div>
                            </div>

                            <div class="post-content">
                                {{-- Post Meta --}}
                                <div class="post-meta">
                                    <span class="post-date">
                                        <i class="fas fa-calendar me-1"></i>{{ $post->published_at->format('F j, Y') }}
                                    </span>
                                    <span class="post-author">
                                        <i class="fas fa-user me-1"></i>{{ $post->author }}
                                    </span>
                                    <span class="read-time">
                                        <i class="fas fa-clock me-1"></i>{{ $post->reading_time }} min read
                                    </span>
                                    <span class="post-views">
                                        <i class="fas fa-eye me-1"></i>{{ $post->views }} views
                                    </span>
                                </div>

                                {{-- Post Title --}}
                                <h1 class="post-title mb-4">{{ $post->title }}</h1>

                                {{-- Post Content --}}
                                <div class="post-text" style="color: #333; line-height: 1.8; font-size: 16px;">
                                        @if($post->content && strlen(strip_tags($post->content)) > 50)
                                            {!! $post->content !!}
                                        @else
                                            {{-- Basic fallback for posts without sufficient content --}}
                                            <div class="content-section">
                                                <h3>Overview</h3>
                                                <p>{{ $post->excerpt_or_truncated_content }}</p>
                                                <p class="text-muted"><em>This post is currently being updated with additional content. Please check back soon for more detailed information.</em></p>
                                            </div>
                                        @endif
                                    </div>                                    {{-- Tags and Share Section --}}
                                    <div class="post-footer mt-4">
                                        @if($post->tags && count($post->tags) > 0)
                                        <div class="post-tags">
                                            <strong>Tags:</strong>
                                            @foreach($post->tags as $tag)
                                            <span class="tag">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                        @endif

                                        <div class="post-share mt-3">
                                            <strong>Share:</strong>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-btn facebook">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" class="share-btn twitter">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-btn linkedin">
                                                <i class="fab fa-linkedin-in"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>                            {{-- Related Posts Section --}}
                            @if($relatedPosts && $relatedPosts->count() > 0)
                            <div class="related-posts mt-5">
                                <h3 class="text-white mb-4">Related Posts</h3>
                                <div class="row">
                                    @foreach($relatedPosts as $relatedPost)
                                    <div class="col-md-6 mb-4">
                                        <div class="post-card">
                                            <div class="post-image">
                                                <img src="{{ $relatedPost->featured_image ? asset('storage/' . $relatedPost->featured_image) : $getPostImage($relatedPost) }}" alt="{{ $relatedPost->title }}">
                                                <div class="post-category">{{ ucfirst(str_replace('-', ' ', $relatedPost->category ?? 'General')) }}</div>
                                            </div>
                                            <div class="post-content">
                                                <div class="post-meta">
                                                    <span class="post-date">
                                                        <i class="fas fa-calendar me-1"></i>{{ $relatedPost->published_at->format('M j, Y') }}
                                                    </span>
                                                    <span class="read-time">
                                                        <i class="fas fa-clock me-1"></i>{{ $relatedPost->reading_time }} min
                                                    </span>
                                                </div>
                                                <h5 class="post-title">
                                                    <a href="{{ route('blog.show', $relatedPost->slug) }}">{{ $relatedPost->title }}</a>
                                                </h5>
                                                <p class="post-excerpt">{{ $relatedPost->excerpt_or_truncated_content }}</p>
                                                <a href="{{ route('blog.show', $relatedPost->slug) }}" class="read-more-btn">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-frontend.site.partials.footer />

    {{-- Increment views via JavaScript --}}
    <script>
        // Increment post views
        fetch('{{ route("blog.show", $post->slug) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ increment_views: true })
        });
    </script>

</x-frontend.site.site-wrapper>
