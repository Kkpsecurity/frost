<div class="form-group">
    @php
        // Help text for each setting
        $helpTexts = [
            'sidebar_mini' => 'Controls the sidebar mini mode size. "lg" = large screens, "md" = medium screens, "sm" = small screens.',
            'sidebar_collapse' => 'Sets the default state of the sidebar when the page loads. When enabled, sidebar starts collapsed.',
            'sidebar_collapse_auto_size' => 'Automatically collapses sidebar based on screen size.',
            'sidebar_collapse_remember' => 'Remembers the user\'s sidebar state (collapsed/expanded) across page refreshes and sessions.',
            'sidebar_collapse_remember_no_transition' => 'When restoring remembered sidebar state, skip the animation for instant loading.',
            'sidebar_scrollbar_theme' => 'Theme for the sidebar scrollbar. Options: os-theme-dark, os-theme-light.',
            'sidebar_scrollbar_auto_hide' => 'Auto-hide sidebar scrollbar. "l" = always visible, "leave" = hide when not scrolling.',
            'sidebar_nav_accordion' => 'Enable accordion behavior for sidebar navigation menus.',
            'sidebar_nav_animation_speed' => 'Speed of sidebar navigation animations in milliseconds.',
            'right_sidebar' => 'Enable or disable the right sidebar (control sidebar).',
            'right_sidebar_icon' => 'Icon to display for the right sidebar toggle button.',
            'right_sidebar_theme' => 'Color theme for the right sidebar. Options: dark, light.',
            'right_sidebar_slide' => 'Enable slide animation when opening/closing right sidebar.',
            'right_sidebar_push' => 'Push main content when right sidebar opens instead of overlaying.',
            'right_sidebar_scrollbar_theme' => 'Theme for the right sidebar scrollbar.',
            'right_sidebar_scrollbar_auto_hide' => 'Auto-hide right sidebar scrollbar behavior.',
        ];

        $helpText = $helpTexts[$key] ?? 'Configuration setting for ' . str_replace('_', ' ', $key) . '.';
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-2">
        @if($key === 'right_sidebar_scrollbar_auto_hide')
            <label for="{{ $key }}" class="mb-0">Right Sidebar Scrollbar Auto Hide</label>
        @else
            <label for="{{ $key }}" class="mb-0">{{ ucwords(str_replace(['_', '.'], ' ', $key)) }}</label>
        @endif

        <button type="button" class="btn btn-sm btn-outline-info ml-2"
                data-toggle="tooltip" data-placement="left"
                title="{{ $helpText }}"
                style="width: 20px; height: 20px; padding: 0; border-radius: 50%; font-size: 10px;">
            <i class="fas fa-question"></i>
        </button>
    </div>

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
    @elseif(in_array($key, ['right_sidebar_scrollbar_auto_hide']))
        <div class="custom-control custom-switch">
            <input type="hidden" name="{{ $key }}" value="l">
            <input type="checkbox" class="custom-control-input"
                   id="{{ $key }}" name="{{ $key }}" value="leave"
                   {{ $value !== 'l' ? 'checked' : '' }}>
            <label class="custom-control-label" for="{{ $key }}">
                <span class="text-muted">Enable auto-hide for right sidebar scrollbar</span>
            </label>
        </div>
    @elseif(in_array(strtolower($value), ['true', 'false', '1', '0']) || str_ends_with($key, '_enabled') || str_contains($key, '_active') || in_array($key, ['sidebar_collapse', 'sidebar_collapse_remember', 'sidebar_nav_accordion', 'right_sidebar']))
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
            @elseif($key === 'right_sidebar_scrollbar_auto_hide')
                @if($value !== 'l')
                    Enabled (Auto-hide on scroll)
                @else
                    Disabled (Always visible)
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
