@props(['post'])

{{-- Post Header --}}
<div class="post-header mb-4">
    {{-- Featured Image --}}
    <div class="post-featured-image mb-4">
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
        
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="img-fluid rounded shadow">
        @else
            {{-- Fallback placeholder if no image available --}}
            <div class="post-image-placeholder d-flex align-items-center justify-content-center bg-light rounded shadow" style="height: 300px; background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                <div class="text-center text-muted">
                    <i class="fas fa-image fa-3x mb-3" style="opacity: 0.3;"></i>
                    <p class="mb-0">No Image Available</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Post Meta --}}
    <div class="post-meta mb-3">
        <span class="post-category badge bg-primary me-2">{{ ucfirst(str_replace('-', ' ', $post->category ?? 'General')) }}</span>
        <span class="post-date text-white-50 me-3">
            <i class="fas fa-calendar me-1"></i>{{ $post->published_at->format('F j, Y') }}
        </span>
        <span class="post-author text-white-50 me-3">
            <i class="fas fa-user me-1"></i>{{ $post->author }}
        </span>
        <span class="read-time text-white-50 me-3">
            <i class="fas fa-clock me-1"></i>{{ $post->read_time ?? 5 }} min read
        </span>
        <span class="post-views text-white-50">
            <i class="fas fa-eye me-1"></i>{{ $post->views }} views
        </span>
    </div>

    {{-- Post Title --}}
    <h1 class="post-title text-white mb-4" style="font-size: 24px">{{ $post->title }}</h1>
</div>
