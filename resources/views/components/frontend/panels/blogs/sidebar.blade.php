{{-- Blog Sidebar --}}
@props(['categories', 'currentPost' => null])

<div class="blog-sidebar">
    {{-- Search Widget --}}
    <div class="card border-secondary shadow mb-4 bg-dark">
        <div class="card-header text-center py-3"
            style="background: linear-gradient(135deg, #1a3a4a 0%, #1a5276 100%); border-radius: 10px 10px 0 0;">
            <h5 class="text-white mb-0">
                <i class="fas fa-search me-2"></i>{{ __('frontend.blog.search_articles') }}
            </h5>
        </div>
        <div class="card-body p-3 bg-dark">
            <form action="{{ route('blog.search') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="q" class="form-control bg-dark text-white border-secondary"
                        placeholder="{{ __('frontend.blog.search_posts_placeholder') }}" value="{{ request('q') }}"
                        required>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Categories Widget --}}
    @if ($categories->count() > 0)
        <div class="card border-secondary shadow mb-4 bg-dark">
            <div class="card-header text-center py-3"
                style="background: linear-gradient(135deg, #1a3a4a 0%, #1a5276 100%); border-radius: 10px 10px 0 0;">
                <h5 class="text-white mb-0">
                    <i class="fas fa-folder me-2"></i>{{ __('frontend.blog.categories') }}
                </h5>
            </div>
            <div class="card-body p-0 bg-dark">
                <div class="list-group list-group-flush">
                    @foreach ($categories as $category)
                        <a href="{{ route('blog.category', Str::slug($category)) }}"
                            class="list-group-item list-group-item-action border-0 py-3 px-4 bg-dark text-white"
                            style="border-bottom: 1px solid rgba(255,255,255,0.1) !important;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-tag me-2 text-info"></i>
                                    {{ $category }}
                                </div>
                                <span class="badge bg-info">
                                    {{ App\Models\BlogPost::published()->byCategory($category)->count() }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Newsletter Signup --}}
    <div class="card border-secondary shadow mb-4 bg-dark">
        <div class="card-header text-center py-3"
            style="background: linear-gradient(135deg, #1a3a4a 0%, #1a5276 100%); border-radius: 10px 10px 0 0;">
            <h5 class="text-white mb-0">
                <i class="fas fa-envelope me-2"></i>{{ __('frontend.blog.newsletter') }}
            </h5>
        </div>
        <div class="card-body p-4 bg-dark">
            <p class="text-white-50 mb-3" style="font-size: 0.9rem;">
                {{ __('frontend.blog.newsletter_sidebar_hint') }}
            </p>
            <form id="newsletter-form">
                <div class="mb-3">
                    <input type="email" class="form-control bg-dark text-white border-secondary"
                        placeholder="{{ __('frontend.blog.email_placeholder') }}" required>
                </div>
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-paper-plane me-2"></i>{{ __('frontend.blog.subscribe') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Popular Tags --}}
    <div class="card border-secondary shadow mb-4 bg-dark">
        <div class="card-header text-center py-3"
            style="background: linear-gradient(135deg, #1a3a4a 0%, #1a5276 100%); border-radius: 10px 10px 0 0;">
            <h5 class="text-white mb-0">
                <i class="fas fa-tags me-2"></i>{{ __('frontend.blog.popular_topics') }}
            </h5>
        </div>
        <div class="card-body p-3 bg-dark">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('blog.tag', 'gun-laws') }}"
                    class="badge bg-secondary text-decoration-none px-2 py-1">Gun Laws</a>
                <a href="{{ route('blog.tag', 'security') }}"
                    class="badge bg-secondary text-decoration-none px-2 py-1">Security</a>
                <a href="{{ route('blog.tag', 'training') }}"
                    class="badge bg-secondary text-decoration-none px-2 py-1">Training</a>
                <a href="{{ route('blog.tag', 'florida') }}"
                    class="badge bg-secondary text-decoration-none px-2 py-1">Florida</a>
                <a href="{{ route('blog.tag', 'compliance') }}"
                    class="badge bg-secondary text-decoration-none px-2 py-1">Compliance</a>
                <a href="{{ route('blog.tag', 'firearms-safety') }}"
                    class="badge bg-secondary text-decoration-none px-2 py-1">Firearms Safety</a>
            </div>
        </div>
    </div>

    {{-- Contact Info --}}
    <div class="card border-secondary shadow bg-dark">
        <div class="card-header text-center py-3"
            style="background: linear-gradient(135deg, #1a3a4a 0%, #1a5276 100%); border-radius: 10px 10px 0 0;">
            <h5 class="text-white mb-0">
                <i class="fas fa-question-circle me-2"></i>{{ __('frontend.blog.need_help') }}
            </h5>
        </div>
        <div class="card-body p-4 text-center bg-dark">
            <p class="text-white-50 mb-3" style="font-size: 0.9rem;">
                {{ __('frontend.blog.need_help_hint') }}
            </p>
            <a href="{{ route('pages', 'contact') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-phone me-2"></i>{{ __('frontend.nav.contact_us') }}
            </a>
        </div>
    </div>
</div>

<script>
    // Newsletter subscription
    document.getElementById('newsletter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = this.querySelector('input[type="email"]').value;

        fetch('{{ route('blog.subscribe') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('{{ __('frontend.blog.thank_you_subscribe') }}');
                    this.reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __('frontend.blog.subscribe_error') }}');
            });
    });
</script>
