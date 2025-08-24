@extends('layouts.frontend')

@section('title', 'Security Training & Gun Law Blog - Expert Insights')
@section('meta_description', 'Stay informed with expert insights on security training, gun laws, weapons safety, and compliance requirements. Professional guidance for security officers and firearm owners.')

@section('content')
<!-- Include the blog component -->
@include('components.panels.blog')

@endsection

@push('scripts')
<script>
// Additional blog functionality can go here
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced search functionality
    const searchInput = document.querySelector('.blog-search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    window.location.href = `/blog/search?q=${encodeURIComponent(query)}`;
                }
            }
        });
    }

    // Category filtering with URL updates
    const categoryItems = document.querySelectorAll('.category-item');
    categoryItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;

            if (category === 'all') {
                window.history.pushState({}, '', '/blog');
            } else {
                window.history.pushState({}, '', `/blog/category/${category}`);
            }
        });
    });

    // Reading progress indicator
    const article = document.querySelector('.blog-article');
    if (article) {
        const progressBar = document.createElement('div');
        progressBar.className = 'reading-progress';
        progressBar.innerHTML = '<div class="progress-fill"></div>';
        document.body.appendChild(progressBar);

        window.addEventListener('scroll', function() {
            const articleHeight = article.offsetHeight;
            const articleTop = article.offsetTop;
            const scrolled = window.pageYOffset;
            const progress = Math.min(100, Math.max(0, ((scrolled - articleTop) / articleHeight) * 100));

            document.querySelector('.progress-fill').style.width = progress + '%';
        });
    }
});
</script>

<style>
.reading-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: rgba(0, 0, 0, 0.1);
    z-index: 9999;
}

.progress-fill {
    height: 100%;
    background: var(--frost-highlight-color);
    transition: width 0.1s ease;
}
</style>
@endpush
