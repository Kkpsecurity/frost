@props([
    'title' => null,
    'description' => null
])

@php
    // Dynamic title and description based on context
    $pageTitle = $title ?? 'Security Training & Legal Insights';
    $pageDescription = $description ?? 'Expert guidance on security training, firearms regulations, and professional development for security professionals';
    
    // Check for site configuration override
    if (function_exists('App\RCache::SiteConfig')) {
        $pageTitle = App\RCache::SiteConfig('blog_page_title', $pageTitle);
        $pageDescription = App\RCache::SiteConfig('blog_page_description', $pageDescription);
    }
@endphp

{{-- Blog Page Header Section --}}
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="section-headline text-center mb-5">
            <h2 class="text-white">{{ $pageTitle }}</h2>
            <p class="text-white-50">{{ $pageDescription }}</p>
            <div class="title-divider mx-auto"></div>
        </div>
    </div>
</div>
