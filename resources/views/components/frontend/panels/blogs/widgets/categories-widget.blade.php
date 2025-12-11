@props(['categories' => collect()])

{{-- Categories Widget --}}
<div class="sidebar-widget categories-widget mb-4">
    <div class="widget-header">
        <h5 class="widget-title">Categories</h5>
    </div>
    <div class="widget-content">
        <ul class="category-list">
            @if($categories->count() > 0)
                @foreach($categories as $category)
                    @php
                        $categorySlug = Str::slug(strtolower($category));
                        $postCount = App\Models\BlogPost::published()->where('category', $category)->count();
                    @endphp
                    <li>
                        <a href="{{ route('blog.index', ['category' => $categorySlug]) }}">
                            {{ $category }} 
                            <span class="count">({{ $postCount }})</span>
                        </a>
                    </li>
                @endforeach
            @else
              <div class="alert alert-info text-white">No categories available.</div>
            @endif
        </ul>
    </div>
</div>
