{{-- Categories Widget --}}
<div class="sidebar-widget categories-widget mb-4">
    <div class="widget-header">
        <h5 class="widget-title">Categories</h5>
    </div>
    <div class="widget-content">
        <ul class="category-list">
            <li><a href="{{ route('blog.index', ['category' => 'training']) }}">Training & Certification <span class="count">(3)</span></a></li>
            <li><a href="{{ route('blog.index', ['category' => 'legal']) }}">Legal & Compliance <span class="count">(2)</span></a></li>
            <li><a href="{{ route('blog.index', ['category' => 'weapons']) }}">Weapons & Safety <span class="count">(4)</span></a></li>
            <li><a href="{{ route('blog.index', ['category' => 'industry']) }}">Industry News <span class="count">(1)</span></a></li>
        </ul>
    </div>
</div>
