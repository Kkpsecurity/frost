@php
    // Get popular tags from published posts
    $popularTags = App\Models\BlogPost::published()
        ->whereNotNull('tags')
        ->get()
        ->pluck('tags')
        ->flatten()
        ->countBy()
        ->sortDesc()
        ->take(8)
        ->keys();
@endphp

{{-- Popular Tags Widget --}}
<div class="sidebar-widget tags-widget mb-4">
    <div class="widget-header">
        <h5 class="widget-title">Popular Tags</h5>
    </div>
    <div class="widget-content">
        <div class="tag-cloud">
            @if($popularTags->count() > 0)
                @foreach($popularTags as $tag)
                    <a href="{{ route('blog.index', ['tag' => Str::slug($tag)]) }}" class="tag-item">{{ $tag }}</a>
                @endforeach
            @else
                {{-- Fallback static tags --}}
                <a href="#" class="tag-item">Security Training</a>
                <a href="#" class="tag-item">Gun Laws</a>
                <a href="#" class="tag-item">Firearms</a>
                <a href="#" class="tag-item">Compliance</a>
                <a href="#" class="tag-item">Safety</a>
                <a href="#" class="tag-item">Certification</a>
                <a href="#" class="tag-item">Legal Updates</a>
                <a href="#" class="tag-item">Training</a>
            @endif
        </div>
    </div>
</div>
