{{-- Blog Category Page Following Template Theme --}}
<x-frontend.site.site-wrapper :title="ucfirst(str_replace('-', ' ', $category)) . ' - Security Training Blog - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Browse {{ ucfirst(str_replace('-', ' ', $category)) }} articles on security training, gun laws, weapons safety, and compliance requirements.">
        <meta name="keywords" content="security training {{ str_replace('-', ' ', $category) }}, gun law updates, firearm safety, weapons training, security officer resources">

        {{-- Blog-specific CSS from your components structure --}}
        <link rel="stylesheet" href="{{ asset('css/components/blog.css') }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    {{-- Breadcrumbs Area --}}
    <div class="page-area">
        <div class="breadcumb-overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="breadcrumb text-center">
                        <div class="section-headline white-headline text-center">
                            <h3>{{ ucfirst(str_replace('-', ' ', $category)) }}</h3>
                        </div>
                        <ul>
                            <li class="home-bread">Home</li>
                            <li><a href="{{ route('blog.index') }}">Blog</a></li>
                            <li>{{ ucfirst(str_replace('-', ' ', $category)) }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Blog Section Component --}}
    <div class="blog-section frost-secondary-bg py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="text-white mb-3">{{ ucfirst(str_replace('-', ' ', $category)) }} Articles</h2>
                    <h5 class="text-white-50">Expert insights and updates in {{ str_replace('-', ' ', $category) }}</h5>
                    <div class="title-divider mx-auto mb-4"></div>
                </div>
            </div>

            {{-- Simplified Blog Posts Layout --}}
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    @if($posts->count() > 0)
                        {{-- Image mapping helper function --}}
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

                        {{-- Featured Blog Post (First Post) --}}
                        @if($posts->count() >= 1)
                        @php $featuredPost = $posts->first(); @endphp
                        <div class="featured-post mb-5">
                            <div class="post-card featured">
                                <div class="post-image">
                                    <img src="{{ $featuredPost->featured_image ? asset('storage/' . $featuredPost->featured_image) : $getPostImage($featuredPost) }}"
                                         alt="{{ $featuredPost->title }}">
                                    <div class="post-category">{{ ucfirst(str_replace('-', ' ', $featuredPost->category ?? 'General')) }}</div>
                                </div>
                                <div class="post-content">
                                    <div class="post-meta">
                                        <span class="post-date">
                                            <i class="fas fa-calendar me-1"></i>{{ $featuredPost->published_at->format('F j, Y') }}
                                        </span>
                                        <span class="post-author">
                                            <i class="fas fa-user me-1"></i>{{ $featuredPost->author }}
                                        </span>
                                        <span class="read-time">
                                            <i class="fas fa-clock me-1"></i>{{ $featuredPost->reading_time }} min read
                                        </span>
                                    </div>
                                    <h3 class="post-title">
                                        <a href="{{ route('blog.show', $featuredPost->slug) }}">{{ $featuredPost->title }}</a>
                                    </h3>
                                    <p class="post-excerpt">{{ $featuredPost->excerpt_or_truncated_content }}</p>
                                    <div class="post-footer">
                                        <div class="post-tags">
                                            @if($featuredPost->tags)
                                                @foreach(array_slice($featuredPost->tags, 0, 3) as $tag)
                                                    <span class="tag">{{ $tag }}</span>
                                                @endforeach
                                            @endif
                                        </div>
                                        <a href="{{ route('blog.show', $featuredPost->slug) }}" class="read-more-btn">
                                            Read Full Article <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Additional Blog Posts Grid --}}
                        @if($posts->count() > 1)
                            <div class="row">
                                @foreach($posts->skip(1) as $post)
                                    <div class="col-lg-6 mb-4">
                                        <div class="post-card">
                                            <div class="post-image">
                                                <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : $getPostImage($post) }}"
                                                     alt="{{ $post->title }}">
                                                <div class="post-category">{{ ucfirst(str_replace('-', ' ', $post->category ?? 'General')) }}</div>
                                            </div>
                                            <div class="post-content">
                                                <div class="post-meta">
                                                    <span class="post-date">
                                                        <i class="fas fa-calendar me-1"></i>{{ $post->published_at->format('M j, Y') }}
                                                    </span>
                                                    <span class="read-time">
                                                        <i class="fas fa-clock me-1"></i>{{ $post->reading_time }} min
                                                    </span>
                                                </div>
                                                <h4 class="post-title">
                                                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                                </h4>
                                                <p class="post-excerpt">{{ $post->excerpt_or_truncated_content }}</p>
                                                <div class="post-footer">
                                                    <div class="post-tags">
                                                        @if($post->tags)
                                                            @foreach(array_slice($post->tags, 0, 2) as $tag)
                                                                <span class="tag">{{ $tag }}</span>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('blog.show', $post->slug) }}" class="read-more-btn">Read More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Pagination --}}
                        @if($posts->hasPages())
                            <div class="text-center mt-5">
                                {{ $posts->links() }}
                            </div>
                        @endif
                    @else
                        {{-- No Posts Available --}}
                        <div class="text-center py-5">
                            <h3 class="text-white">No posts found in "{{ ucfirst(str_replace('-', ' ', $category)) }}"</h3>
                            <p class="text-white-50">Check back soon for new articles in this category.</p>
                            <div class="mt-4">
                                <a href="{{ route('blog.index') }}" class="btn btn-accent">
                                    <i class="fas fa-arrow-left me-2"></i>View All Posts
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Category Navigation --}}
            <div class="row mt-5">
                <div class="col-12">
                    <div class="category-navigation text-center">
                        <h4 class="text-white mb-3">Browse Other Categories</h4>
                        <div class="category-links">
                            <a href="{{ route('blog.category', 'gun-laws') }}" class="btn btn-outline-light me-2 mb-2">Gun Laws</a>
                            <a href="{{ route('blog.category', 'security-tips') }}" class="btn btn-outline-light me-2 mb-2">Security Tips</a>
                            <a href="{{ route('blog.category', 'training') }}" class="btn btn-outline-light me-2 mb-2">Training</a>
                            <a href="{{ route('blog.category', 'safety') }}" class="btn btn-outline-light me-2 mb-2">Safety</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Blog Newsletter Section --}}
    <div class="blog-newsletter frost-primary-bg py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3 class="text-white mb-2">Stay Updated on {{ ucfirst(str_replace('-', ' ', $category)) }}</h3>
                    <p class="text-white-50 mb-lg-0">Get the latest insights delivered directly to your inbox</p>
                </div>
                <div class="col-lg-6">
                    <div class="newsletter-form">
                        <form class="d-flex">
                            <input type="email" class="form-control me-3" placeholder="Enter your email address" required>
                            <button type="submit" class="btn btn-accent">
                                <i class="fas fa-paper-plane me-2"></i>Subscribe
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-frontend.site.partials.footer />

    {{-- Category Page JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Newsletter subscription
            const newsletterForm = document.querySelector('.newsletter-form form');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const email = this.querySelector('input[type="email"]').value;

                    // Here you would typically send the email to your backend
                    alert('Thank you for subscribing! You\'ll receive updates on {{ str_replace("-", " ", $category) }} and other security topics.');
                    this.reset();
                });
            }

            // Smooth scroll for read more buttons
            const readMoreBtns = document.querySelectorAll('.read-more-btn');
            readMoreBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Add a subtle loading effect
                    this.style.opacity = '0.7';
                    setTimeout(() => {
                        this.style.opacity = '1';
                    }, 200);
                });
            });
        });
    </script>

</x-frontend.site.site-wrapper>
