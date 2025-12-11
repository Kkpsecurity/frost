@props(['posts'])

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
                                @php
                                    $imageUrl = null;
                                    $useDefaultImage = true;
                                    
                                    // Check if post has featured_image and if file exists
                                    if ($recentPost->featured_image) {
                                        // Handle different path formats
                                        $imagePath = $recentPost->featured_image;
                                        
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
                                
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $recentPost->title }}">
                                @else
                                    {{-- Fallback placeholder for small widget --}}
                                    <div class="recent-post-placeholder d-flex align-items-center justify-content-center bg-light" style="height: 60px; background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                                        <i class="fas fa-image text-muted" style="opacity: 0.5; font-size: 1rem;"></i>
                                    </div>
                                @endif
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
