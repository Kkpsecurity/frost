{{-- Training Ranges Page --}}
<x-frontend.site.site-wrapper :title="'Training Ranges - ' . config('app.name')">
    <x-slot:head>
        <meta name="description" content="Find authorized shooting ranges for your security training. View locations, prices, and upcoming training dates.">
        <meta name="keywords" content="shooting ranges, security training locations, firearm training ranges, Florida gun ranges">
    </x-slot:head>

    <x-frontend.site.partials.header />

    <div class="container-fluid m-0 p-0" style="min-height: 50vh;">
        <x-frontend.panels.ranges.list :ranges="$ranges" :cities="$cities" />
    </div>

    <x-frontend.site.partials.footer />

</x-frontend.site.site-wrapper>
