@props(['post'])

{{-- Single Blog Post Card --}}
<article class="blog-post-item mb-4">
    <div class="post-card h-100">
        <div class="row g-0">
            {{-- Post Image --}}
            <div class="col-md-5">
                <div class="post-image">
                    @php
                        $imageUrl = null;
                        $useDefaultImage = true;
                        
                        // Check if post has featured_image and if file exists
                        if ($post->featured_image) {
                            // Handle different path formats
                            $imagePath = $post->featured_image;
                            
                            // If path doesn't start with /, add it
                            if (!str_starts_with($imagePath, '/')) {
                                $imagePath = '/' . $imagePath;
                            }
                            
                            // Check if file exists in public directory
                            if (file_exists(public_path($imagePath))) {
                                $imageUrl = asset($imagePath);
                                $useDefaultImage = false;
                            }
                        }
                        
                        // Default image if none exists or file not found
                        if ($useDefaultImage) {
                            $imageUrl = asset('images/Security-Page-1.jpg');
                        }
                    @endphp
                    
                    <a href="{{ route('blog.show', $post->slug) }}">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="img-fluid">
                        @else
                            {{-- Fallback placeholder --}}
                            <div class="post-image-placeholder d-flex align-items-center justify-content-center bg-light" style="height: 200px; background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                                <div class="text-center text-muted">
                                    <i class="fas fa-image fa-2x mb-2" style="opacity: 0.3;"></i>
                                    <small>No Image</small>
                                </div>
                            </div>
                        @endif
                    </a>
                    {{-- Category Badge --}}
                    @if($post->category)
                        <div class="post-category">
                            {{ $post->category }}
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- Post Content --}}
            <div class="col-md-7">
                <div class="post-content h-100 d-flex flex-column">
                    {{-- Post Meta --}}
                    <div class="post-meta mb-3">
                        <span class="meta-item">
                            <i class="fas fa-user"></i>
                            {{ $post->author ?? 'KKP Security' }}
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $post->published_at ? $post->published_at->format('M j, Y') : $post->created_at->format('M j, Y') }}
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-clock"></i>
                            {{ $post->read_time ?? '5' }} min read
                        </span>
                    </div>

                    {{-- Post Title --}}
                    <div class="post-title mb-3">
                        <h4>
                            <a href="{{ route('blog.show', $post->slug) }}">
                                {{ $post->title }}
                            </a>
                        </h4>
                    </div>

                    {{-- Post Excerpt --}}
                    <div class="post-excerpt mb-3 flex-grow-1">
                        <p>{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 150) }}</p>
                    </div>

                    {{-- Post Footer --}}
                    <div class="post-footer mt-auto">
                        <div class="d-flex justify-content-between align-items-center">
                            {{-- Tags --}}
                            <div class="post-tags">
                                @if($post->tags && is_array($post->tags))
                                    @foreach(array_slice($post->tags, 0, 2) as $tag)
                                        <span class="tag">{{ $tag }}</span>
                                    @endforeach
                                @endif
                            </div>

                            {{-- Read More Button --}}
                            <a href="{{ route('blog.show', $post->slug) }}" class="read-more-btn">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
