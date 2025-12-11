{{-- Search Widget --}}
<div class="sidebar-widget search-widget mb-4">
    <div class="widget-content">
        <form action="{{ route('blog.index') }}" method="GET" class="search-form">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search articles..." value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>
