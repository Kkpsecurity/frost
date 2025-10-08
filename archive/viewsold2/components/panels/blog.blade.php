{{-- Blog Section Component --}}
<div class="blog-section frost-secondary-bg py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="text-white mb-3">Security Training & Gun Law Insights</h2>
                <h5 class="text-white-50">Stay informed with the latest in security training, gun laws, and weapons
                    safety</h5>
                <div class="title-divider mx-auto mb-4"></div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-lg-3 col-md-4">
                <div class="blog-sidebar">
                    <div class="sidebar-widget mb-4">
                        <h5 class="widget-title text-white mb-3">Categories</h5>
                        <div class="category-list">
                            <a href="#" class="category-item active" data-category="all">
                                <i class="fas fa-list-alt me-2"></i>All Topics
                                <span class="post-count">12</span>
                            </a>
                            <a href="#" class="category-item" data-category="gun-laws">
                                <i class="fas fa-gavel me-2"></i>Gun Laws & Regulations
                                <span class="post-count">4</span>
                            </a>
                            <a href="#" class="category-item" data-category="weapons-training">
                                <i class="fas fa-crosshairs me-2"></i>Weapons Training
                                <span class="post-count">3</span>
                            </a>
                            <a href="#" class="category-item" data-category="security-tips">
                                <i class="fas fa-shield-alt me-2"></i>Security Tips
                                <span class="post-count">3</span>
                            </a>
                            <a href="#" class="category-item" data-category="compliance">
                                <i class="fas fa-certificate me-2"></i>Compliance & Licensing
                                <span class="post-count">2</span>
                            </a>
                        </div>
                    </div>

                    <div class="sidebar-widget mb-4">
                        <h5 class="widget-title text-white mb-3">Featured Topics</h5>
                        <div class="featured-tags">
                            <span class="blog-tag">Florida Gun Laws</span>
                            <span class="blog-tag">Concealed Carry</span>
                            <span class="blog-tag">Firearm Safety</span>
                            <span class="blog-tag">Security Training</span>
                            <span class="blog-tag">D License</span>
                            <span class="blog-tag">G License</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9 col-md-8">
                <div class="blog-posts-grid">
                    <!-- Featured Blog Post -->
                    <div class="featured-post mb-5" data-category="gun-laws">
                        <div class="post-card featured">
                            <div class="post-image">
                                <img src="{{ asset('assets/img/blog/featured-gun-laws.jpg') }}"
                                    alt="Florida Gun Laws 2025">
                                <div class="post-category">Gun Laws</div>
                            </div>
                            <div class="post-content">
                                <div class="post-meta">
                                    <span class="post-date">
                                        <i class="fas fa-calendar me-1"></i>August 15, 2025
                                    </span>
                                    <span class="post-author">
                                        <i class="fas fa-user me-1"></i>Security Expert
                                    </span>
                                    <span class="read-time">
                                        <i class="fas fa-clock me-1"></i>8 min read
                                    </span>
                                </div>
                                <h3 class="post-title">
                                    <a href="{{ url('blog/florida-gun-laws-2025') }}">Complete Guide to Florida Gun Laws
                                        2025: What Every Gun Owner Should Know</a>
                                </h3>
                                <p class="post-excerpt">
                                    Navigate Florida's complex gun laws with confidence. This comprehensive guide covers
                                    everything from purchasing requirements to concealed carry permits, recent
                                    legislative changes, and compliance requirements for 2025.
                                </p>
                                <div class="post-footer">
                                    <div class="post-tags">
                                        <span class="tag">Florida Laws</span>
                                        <span class="tag">Gun Rights</span>
                                        <span class="tag">Concealed Carry</span>
                                    </div>
                                    <a href="{{ url('blog/florida-gun-laws-2025') }}" class="read-more-btn">
                                        Read Full Article <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Regular Blog Posts Grid -->
                    <div class="row" id="blogPostsContainer">
                        <!-- Weapons Training Post -->
                        <div class="col-lg-6 mb-4" data-category="weapons-training">
                            <div class="post-card">
                                <div class="post-image">
                                    <img src="{{ asset('assets/img/blog/firearms-safety.jpg') }}"
                                        alt="Firearms Safety Training">
                                    <div class="post-category">Training</div>
                                </div>
                                <div class="post-content">
                                    <div class="post-meta">
                                        <span class="post-date">
                                            <i class="fas fa-calendar me-1"></i>August 12, 2025
                                        </span>
                                        <span class="read-time">
                                            <i class="fas fa-clock me-1"></i>6 min
                                        </span>
                                    </div>
                                    <h4 class="post-title">
                                        <a href="{{ url('blog/essential-firearms-safety') }}">Essential Firearms Safety:
                                            Building Fundamental Skills</a>
                                    </h4>
                                    <p class="post-excerpt">
                                        Master the four fundamental rules of firearm safety and advanced handling
                                        techniques essential for security professionals.
                                    </p>
                                    <div class="post-footer">
                                        <div class="post-tags">
                                            <span class="tag">Safety</span>
                                            <span class="tag">Training</span>
                                        </div>
                                        <a href="{{ url('blog/essential-firearms-safety') }}" class="read-more-btn">Read
                                            More</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Tips Post -->
                        <div class="col-lg-6 mb-4" data-category="security-tips">
                            <div class="post-card">
                                <div class="post-image">
                                    <img src="{{ asset('assets/img/blog/threat-assessment.jpg') }}"
                                        alt="Threat Assessment">
                                    <div class="post-category">Security</div>
                                </div>
                                <div class="post-content">
                                    <div class="post-meta">
                                        <span class="post-date">
                                            <i class="fas fa-calendar me-1"></i>August 10, 2025
                                        </span>
                                        <span class="read-time">
                                            <i class="fas fa-clock me-1"></i>5 min
                                        </span>
                                    </div>
                                    <h4 class="post-title">
                                        <a href="{{ url('blog/threat-assessment-techniques') }}">Advanced Threat
                                            Assessment Techniques for Security Officers</a>
                                    </h4>
                                    <p class="post-excerpt">
                                        Learn professional threat assessment methodologies to identify and evaluate
                                        potential security risks effectively.
                                    </p>
                                    <div class="post-footer">
                                        <div class="post-tags">
                                            <span class="tag">Assessment</span>
                                            <span class="tag">Security</span>
                                        </div>
                                        <a href="{{ url('blog/threat-assessment-techniques') }}"
                                            class="read-more-btn">Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compliance Post -->
                        <div class="col-lg-6 mb-4" data-category="compliance">
                            <div class="post-card">
                                <div class="post-image">
                                    <img src="{{ asset('assets/img/blog/license-renewal.jpg') }}"
                                        alt="License Renewal">
                                    <div class="post-category">Compliance</div>
                                </div>
                                <div class="post-content">
                                    <div class="post-meta">
                                        <span class="post-date">
                                            <i class="fas fa-calendar me-1"></i>August 8, 2025
                                        </span>
                                        <span class="read-time">
                                            <i class="fas fa-clock me-1"></i>4 min
                                        </span>
                                    </div>
                                    <h4 class="post-title">
                                        <a href="{{ url('blog/security-license-renewal') }}">Security License Renewal:
                                            Complete Checklist for 2025</a>
                                    </h4>
                                    <p class="post-excerpt">
                                        Stay compliant with updated renewal requirements for Class D and Class G
                                        security licenses in Florida.
                                    </p>
                                    <div class="post-footer">
                                        <div class="post-tags">
                                            <span class="tag">Licensing</span>
                                            <span class="tag">D License</span>
                                        </div>
                                        <a href="{{ url('blog/security-license-renewal') }}"
                                            class="read-more-btn">Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gun Laws Post -->
                        <div class="col-lg-6 mb-4" data-category="gun-laws">
                            <div class="post-card">
                                <div class="post-image">
                                    <img src="{{ asset('assets/img/blog/concealed-carry.jpg') }}"
                                        alt="Concealed Carry Laws">
                                    <div class="post-category">Gun Laws</div>
                                </div>
                                <div class="post-content">
                                    <div class="post-meta">
                                        <span class="post-date">
                                            <i class="fas fa-calendar me-1"></i>August 5, 2025
                                        </span>
                                        <span class="read-time">
                                            <i class="fas fa-clock me-1"></i>7 min
                                        </span>
                                    </div>
                                    <h4 class="post-title">
                                        <a href="{{ url('blog/concealed-carry-florida') }}">Concealed Carry in
                                            Florida: Rights, Restrictions, and Responsibilities</a>
                                    </h4>
                                    <p class="post-excerpt">
                                        Understand your rights and responsibilities as a concealed carry permit holder
                                        in Florida, including recent constitutional carry changes.
                                    </p>
                                    <div class="post-footer">
                                        <div class="post-tags">
                                            <span class="tag">Concealed Carry</span>
                                            <span class="tag">Constitutional Carry</span>
                                        </div>
                                        <a href="{{ url('blog/concealed-carry-florida') }}"
                                            class="read-more-btn">Read More</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional posts would continue here... -->
                    </div>

                    <!-- Load More Button -->
                    <div class="text-center mt-5">
                        <button class="btn btn-outline-light btn-lg load-more-btn">
                            <i class="fas fa-plus me-2"></i>Load More Articles
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Blog Newsletter Section -->
<div class="blog-newsletter frost-primary-bg py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h3 class="text-white mb-2">Stay Updated on Security & Gun Law Changes</h3>
                <p class="text-white-50 mb-lg-0">Get the latest insights delivered directly to your inbox</p>
            </div>
            <div class="col-lg-6">
                <div class="newsletter-form">
                    <form class="d-flex">
                        <input type="email" class="form-control me-3" placeholder="Enter your email address"
                            required>
                        <button type="submit" class="btn btn-accent">
                            <i class="fas fa-paper-plane me-2"></i>Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Blog Section Styles */
    .blog-section {
        min-height: 80vh;
    }

    .title-divider {
        width: 80px;
        height: 3px;
        background: var(--frost-highlight-color);
    }

    /* Sidebar Styles */
    .blog-sidebar {
        background: var(--frost-primary-color);
        padding: 30px 25px;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .widget-title {
        font-weight: 700;
        font-size: 1.2rem;
        border-bottom: 2px solid var(--frost-highlight-color);
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .category-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: var(--frost-light-color);
        text-decoration: none;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .category-item:hover,
    .category-item.active {
        background: var(--frost-secondary-color);
        color: var(--frost-white-color);
        border-color: var(--frost-highlight-color);
        transform: translateX(5px);
    }

    .post-count {
        background: var(--frost-highlight-color);
        color: var(--frost-primary-color);
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .featured-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .blog-tag {
        background: var(--frost-info-color);
        color: var(--frost-white-color);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .blog-tag:hover {
        background: var(--frost-highlight-color);
        color: var(--frost-primary-color);
        cursor: pointer;
    }

    /* Post Card Styles */
    .post-card {
        background: var(--frost-white-color);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.4s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .post-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .post-card.featured {
        background: linear-gradient(135deg, var(--frost-white-color) 0%, var(--frost-light-color) 100%);
    }

    .post-image {
        position: relative;
        height: 220px;
        overflow: hidden;
    }

    .post-card.featured .post-image {
        height: 300px;
    }

    .post-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .post-card:hover .post-image img {
        transform: scale(1.05);
    }

    .post-category {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--frost-info-color);
        color: var(--frost-white-color);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .post-content {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .post-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 0.85rem;
        color: var(--frost-gray-color);
    }

    .post-meta span {
        display: flex;
        align-items: center;
    }

    .post-title {
        margin-bottom: 15px;
        flex-grow: 1;
    }

    .post-card.featured .post-title {
        font-size: 1.8rem;
        font-weight: 700;
    }

    .post-title a {
        color: var(--frost-dark-color);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .post-title a:hover {
        color: var(--frost-info-color);
    }

    .post-excerpt {
        color: var(--frost-gray-color);
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .post-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .post-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .tag {
        background: var(--frost-light-primary-color);
        color: var(--frost-dark-color);
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .read-more-btn {
        color: var(--frost-info-color);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .read-more-btn:hover {
        color: var(--frost-secondary-color);
        text-decoration: none;
    }

    .load-more-btn {
        border: 2px solid var(--frost-light-color);
        color: var(--frost-light-color);
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .load-more-btn:hover {
        background: var(--frost-highlight-color);
        color: var(--frost-primary-color);
        border-color: var(--frost-highlight-color);
    }

    /* Newsletter Section */
    .blog-newsletter {
        background: linear-gradient(135deg, var(--frost-primary-color) 0%, var(--frost-secondary-color) 100%);
    }

    .newsletter-form .form-control {
        border: none;
        padding: 12px 20px;
        border-radius: 25px;
        background: rgba(255, 255, 255, 0.9);
    }

    .newsletter-form .form-control:focus {
        box-shadow: 0 0 0 3px rgba(254, 222, 89, 0.3);
        background: var(--frost-white-color);
    }

    .btn-accent {
        background: var(--frost-highlight-color);
        color: var(--frost-primary-color);
        border: none;
        padding: 12px 25px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-accent:hover {
        background: var(--frost-white-color);
        color: var(--frost-primary-color);
        transform: translateY(-2px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .blog-sidebar {
            margin-bottom: 30px;
        }

        .post-meta {
            flex-direction: column;
            gap: 8px;
        }

        .post-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .newsletter-form .d-flex {
            flex-direction: column;
            gap: 15px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Category filtering
        const categoryItems = document.querySelectorAll('.category-item');
        const blogPosts = document.querySelectorAll('[data-category]');

        categoryItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active class from all items
                categoryItems.forEach(cat => cat.classList.remove('active'));
                // Add active class to clicked item
                this.classList.add('active');

                const selectedCategory = this.dataset.category;

                // Filter posts
                blogPosts.forEach(post => {
                    if (selectedCategory === 'all' || post.dataset.category ===
                        selectedCategory) {
                        post.style.display = 'block';
                    } else {
                        post.style.display = 'none';
                    }
                });
            });
        });

        // Newsletter subscription
        const newsletterForm = document.querySelector('.newsletter-form form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = this.querySelector('input[type="email"]').value;

                // Here you would typically send the email to your backend
                alert(
                    'Thank you for subscribing! You\'ll receive updates on security training and gun law changes.');
                this.reset();
            });
        }

        // Load more functionality
        const loadMoreBtn = document.querySelector('.load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function() {
                // Here you would load more posts via AJAX
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';

                // Simulate loading delay
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-plus me-2"></i>Load More Articles';
                    alert('More articles would be loaded here via AJAX');
                }, 1500);
            });
        }
    });
</script>
