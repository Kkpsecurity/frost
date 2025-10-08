{{--
    Site Pages Render Component
    Renders panels based on the page array
    $page variable contains array of panel names to render
--}}

@if(isset($page['panels']) && is_array($page['panels']))
    {{-- Loop through page panels array and render each panel --}}
    @foreach($page['panels'] as $panel)
        <x-dynamic-component :component="'panels.' . $panel" />
    @endforeach
@elseif(is_array($page))
    {{-- If $page is directly an array of panel names --}}
    @foreach($page as $panel)
        <x-dynamic-component :component="'panels.' . $panel" />
    @endforeach
@else
    <div class="alert alert-info">
        <p>No panels configured for this page.</p>
    </div>
@endif
