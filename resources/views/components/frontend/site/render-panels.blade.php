{{--
 * Render the panels for the given page content.
 * each panel should be wrapped in a default panel wrapper
 * $page should contain the necessary data for each panel
 * $page->panels should be an array of panel data
 * $pageName should be the current page name for folder structure
 *
 * @param array $page The page content data.
 * @param string $pageName The current page name (optional, defaults to 'home')
 * @return \Illuminate\View\View
--}}

@php
    // Determine the page name for folder structure
    $pageName = $pageName ?? 'home';

    // If we can extract the page from global variables, use that
    if (isset($currentPage)) {
        $pageName = $currentPage;
    }
@endphp

@if(isset($page['panels']) && is_array($page['panels']))
    {{-- Loop through page panels array and render each panel --}}
    @foreach($page['panels'] as $panel)
        <x-dynamic-component :component="'frontend.panels.' . $panel" />
    @endforeach
@elseif(is_array($page))
    {{-- If $page is directly an array of panel names --}}
    @foreach($page as $panel)
        <x-dynamic-component :component="'frontend.panels.' . $panel" />
    @endforeach
@else
    <div class="alert alert-info">
        <p>No panels configured for this page.</p>
    </div>
@endif
