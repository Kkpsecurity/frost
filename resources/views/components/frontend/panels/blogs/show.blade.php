@props(['post' => []])

<main class="main-page-content">
    {{-- Blog Details Section --}}
    <div class="blog-section frost-secondary-bg py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    {{-- Main Blog Post Content --}}
                    <x-frontend.panels.blogs.details.post-header :post="$post" />
                    <article class="blog-post-detail">
                        {{-- Post Header --}}                      
                        <x-frontend.panels.blogs.details.post-content :post="$post" />
                    </article>

                    {{-- Related Posts Section --}}
                    <x-frontend.panels.blogs.details.related-posts :post="$post" />
                </div>
            </div>
        </div>
    </div>
</main>
