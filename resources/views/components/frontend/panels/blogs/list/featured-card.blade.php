{{-- Featured Blog Post Card --}}
@props(['post'])

<div class="card border-0 shadow-lg h-100 blog-featured-card" style="background: rgba(255, 255, 255, 0.95); border-radius: 15px; transition: transform 0.3s ease;">
    @php
        $imageUrl = null;
        $hasImage = false;
        
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
                $hasImage = true;
            }
        }
        
        // Default image if none exists or file not found
        if (!$hasImage) {
            $imageUrl = asset('images/Security-Page-1.jpg');
            $hasImage = true; // We have a default image
        }
    @endphp

    <div class="card-img-top-wrapper" style="height: 200px; overflow: hidden; border-radius: 15px 15px 0 0;">
        @if($imageUrl && $hasImage)
            <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="w-100 h-100" style="object-fit: cover;">
        @else
            {{-- Fallback placeholder --}}
            <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-light" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                <div class="text-center text-muted">
                    <i class="fas fa-image fa-3x mb-2" style="opacity: 0.3;"></i>
                    <div>No Image Available</div>
                </div>
            </div>
        @endif
    </div>

    <div class="card-body p-4">
        {{-- Category Badge --}}
        <div class="mb-3">
            <span class="badge" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); font-size: 0.75rem;">
                {{ $post->category }}
            </span>
            @if($post->is_featured)
            <span class="badge bg-warning text-dark ms-2">
                <i class="fas fa-star"></i> Featured
            </span>
            @endif
        </div>

        {{-- Title --}}
        <h4 class="card-title mb-3">
            <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none">
                {{ $post->title }}
            </a>
        </h4>

        {{-- Excerpt --}}
        <p class="card-text text-muted mb-3">
            {{ $post->excerpt_or_truncated_content }}
        </p>

        {{-- Meta Info --}}
        <div class="d-flex justify-content-between align-items-center text-muted small">
            <div>
                <i class="fas fa-user me-1"></i>{{ $post->author }}
            </div>
            <div>
                <i class="fas fa-clock me-1"></i>{{ $post->read_time_text }}
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center text-muted small mt-2">
            <div>
                <i class="fas fa-calendar me-1"></i>{{ $post->formatted_published_at }}
            </div>
            <div>
                <i class="fas fa-eye me-1"></i>{{ number_format($post->views) }} views
            </div>
        </div>
    </div>

    <div class="card-footer bg-transparent border-0 p-4 pt-0">
        <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-primary w-100">
            <i class="fas fa-arrow-right me-2"></i>Read Article
        </a>
    </div>
</div>

<style>
.blog-featured-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
}
</style>
