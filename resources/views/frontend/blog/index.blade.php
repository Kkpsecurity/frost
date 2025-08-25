{{-- Blog Index Page --}}
<x-site-wrapper :title="$content['title'] ?? 'Security Training & Gun Law Blog'">
    <x-slot:head>
        <meta name="description" content="{{ $content['description'] ?? 'Stay informed with expert insights on security training, gun laws, weapons safety, and compliance requirements. Professional guidance for security officers and firearm owners.' }}">
        <meta name="keywords" content="{{ $content['keywords'] ?? 'security training, gun laws, blog, weapons safety, compliance' }}">
    </x-slot:head>

    <x-site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <x-panels.blog />
    </div>

    <x-site.partials.footer />
</x-site-wrapper>
