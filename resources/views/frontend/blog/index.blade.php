{{-- Knowledge Library - Security Training Blog Index --}}
<x-frontend.site.site-wrapper :title="'Knowledge Library - Security Training Resources - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Stay informed with expert insights on security training, gun laws, weapons safety, and compliance requirements. Professional guidance for security officers and firearm owners.">
        <meta name="keywords" content="security training blog, gun law updates, firearm safety, weapons training, security officer resources">
        <link rel="stylesheet" href="{{ asset('css/components/blog.css') }}">
    </x-slot:head>

    <x-frontend.site.partials.header />

    {{-- Breadcrumbs Area --}}
    <x-frontend.ui.breadcrumbs />

    {{-- Blog List Component --}}
    <x-frontend.panels.blogs.list 
        :posts="$posts" 
        :categories="$categories ?? collect()"
        :page-title="$pageData['title'] ?? null"
        :page-description="$pageData['description'] ?? null" 
    />

    <x-frontend.site.partials.footer />

</x-frontend.site.site-wrapper>