<div class="form-group">
    <label for="{{ $key }}">{{ ucwords(str_replace(['_', '.'], ' ', $key)) }}</label>

    @if(in_array($key, ['layout_dark_mode', 'layout_fixed_sidebar', 'layout_fixed_navbar', 'layout_fixed_footer', 'layout_topnav', 'layout_boxed']))
        <select class="form-control" name="{{ $key }}" id="{{ $key }}">
            @if($key === 'layout_dark_mode')
                <option value="1" {{ $value == '1' ? 'selected' : '' }}>Enabled</option>
                <option value="0" {{ $value == '0' ? 'selected' : '' }}>Disabled</option>
            @elseif(in_array($key, ['layout_topnav', 'layout_boxed', 'layout_fixed_sidebar', 'layout_fixed_navbar', 'layout_fixed_footer']))
                <option value="" {{ $value === '' || is_null($value) ? 'selected' : '' }}>Default</option>
                <option value="1" {{ $value == '1' ? 'selected' : '' }}>Enabled</option>
                <option value="0" {{ $value == '0' ? 'selected' : '' }}>Disabled</option>
            @endif
        </select>
    @elseif(in_array($key, ['sidebar_mini']))
        <select class="form-control" name="{{ $key }}" id="{{ $key }}">
            <option value="lg" {{ $value === 'lg' ? 'selected' : '' }}>Large</option>
            <option value="md" {{ $value === 'md' ? 'selected' : '' }}>Medium</option>
            <option value="sm" {{ $value === 'sm' ? 'selected' : '' }}>Small</option>
            <option value="" {{ $value === '' ? 'selected' : '' }}>Disabled</option>
        </select>
    @elseif(in_array($key, ['preloader_mode']))
        <select class="form-control" name="{{ $key }}" id="{{ $key }}">
            <option value="fullscreen" {{ $value === 'fullscreen' ? 'selected' : '' }}>Fullscreen</option>
            <option value="cwrapper" {{ $value === 'cwrapper' ? 'selected' : '' }}>Content Wrapper</option>
        </select>
    @elseif(in_array(strtolower($value), ['true', 'false', '1', '0']) || str_ends_with($key, '_enabled') || str_contains($key, '_active'))
        <div class="custom-control custom-switch">
            <input type="hidden" name="{{ $key }}" value="0">
            <input type="checkbox" class="custom-control-input"
                   id="{{ $key }}" name="{{ $key }}" value="1"
                   {{ in_array(strtolower($value), ['true', '1']) ? 'checked' : '' }}>
            <label class="custom-control-label" for="{{ $key }}"></label>
        </div>
    @elseif(str_contains($key, 'img_width') || str_contains($key, 'img_height') || str_contains($key, 'animation_speed') || str_contains($key, 'loading_screen'))
        <input type="number" class="form-control"
               name="{{ $key }}" id="{{ $key }}"
               value="{{ $value }}" placeholder="Enter numeric value">
    @elseif(str_contains($key, 'url'))
        <input type="text" class="form-control"
               name="{{ $key }}" id="{{ $key }}"
               value="{{ $value }}" placeholder="Enter URL (optional)">
    @elseif(str_contains($key, 'class'))
        <input type="text" class="form-control"
               name="{{ $key }}" id="{{ $key }}"
               value="{{ $value }}" placeholder="Enter CSS classes">
    @else
        <input type="text" class="form-control"
               name="{{ $key }}" id="{{ $key }}"
               value="{{ $value }}" placeholder="Enter value">
    @endif

    <small class="form-text text-muted">
        Current: <code>
        @if(in_array($key, ['layout_dark_mode', 'layout_fixed_sidebar', 'layout_fixed_navbar', 'layout_fixed_footer', 'layout_topnav', 'layout_boxed']))
            @if($value == '1')
                Enabled
            @elseif($value == '0')
                Disabled
            @else
                Default
            @endif
        @elseif(in_array($key, ['sidebar_mini']))
            @if($value === 'lg')
                Large
            @elseif($value === 'md')
                Medium
            @elseif($value === 'sm')
                Small
            @else
                Disabled
            @endif
        @elseif(in_array($key, ['preloader_mode']))
            @if($value === 'fullscreen')
                Fullscreen
            @elseif($value === 'cwrapper')
                Content Wrapper
            @else
                {{ $value ?? 'Not Set' }}
            @endif
        @elseif(in_array(strtolower($value), ['true', 'false', '1', '0']) || str_ends_with($key, '_enabled') || str_contains($key, '_active') || str_starts_with($key, 'sidebar_'))
            @if(in_array(strtolower($value), ['true', '1']))
                Enabled
            @elseif(in_array(strtolower($value), ['false', '0']))
                Disabled
            @else
                {{ $value ?? 'Not Set' }}
            @endif
        @else
            {{ $value ?? 'Not Set' }}
        @endif
        </code>
    </small>
</div>
