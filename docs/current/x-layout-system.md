# X-Layout Component System Documentation

## Overview

The FROST application uses Laravel's Blade Component system with a structured x-component architecture to create reusable, maintainable layouts and UI elements. This system provides a hierarchical organization of components that promotes consistency and reduces code duplication.

## Component Structure

The component system is organized in the following hierarchy:

```
resources/views/components/
├── admin/                    # Admin panel components
├── frontend/                 # Public-facing components
│   ├── panels/              # Content panels for different pages
│   ├── site/                # Site structure components
│   │   ├── partials/        # Reusable partial components
│   │   └── site-wrapper.blade.php  # Main layout wrapper
│   └── ui/                  # UI utility components
├── media/                   # Media-related components
└── site/                    # Legacy site components
```

## Core Layout Components

### 1. Site Wrapper (`x-frontend.site.site-wrapper`)

**Location:** `resources/views/components/frontend/site/site-wrapper.blade.php`

The main layout wrapper that provides the basic HTML structure, meta tags, and global functionality.

**Usage:**
```blade
<x-frontend.site.site-wrapper :title="'Page Title'">
    <x-slot:head>
        <!-- Additional head content -->
        <meta name="description" content="Page description">
    </x-slot:head>

    <!-- Page content here -->

</x-frontend.site.site-wrapper>
```

**Features:**
- Responsive HTML5 structure
- Meta tags and SEO optimization
- Font loading (Figtree, Font Awesome)
- Bootstrap CSS integration
- Vite asset compilation
- Professional page loader
- Smooth scroll functionality
- Mobile menu support

**Slots:**
- `$slot` - Main content area
- `$head` - Additional head content
- `$scripts` - Page-specific scripts

**Props:**
- `$title` - Page title
- `$description` - Meta description
- `$keywords` - Meta keywords
- `$bodyClass` - Additional body CSS classes

### 2. Panel Renderer (`x-frontend.site.render-panels`)

**Location:** `resources/views/components/frontend/site/render-panels.blade.php`

Dynamically renders page content panels based on configuration data.

**Usage:**
```blade
<x-frontend.site.render-panels :page="$content" />
```

**Features:**
- Dynamic component loading
- Panel-based content organization
- Flexible page structure

### 3. Page Renderer (`x-frontend.pages.render`)

**Location:** `resources/views/frontend/pages/render.blade.php`

Standard page template that combines the site wrapper with dynamic panels.

**Usage:**
```blade
{{-- Automatically used by SitePageController --}}
{{-- No direct usage needed --}}
```

## Panel Components

Panel components are content blocks that can be dynamically loaded and arranged on pages.

### Panel Categories

1. **Home Panels** (`x-frontend.panels.home.*`)
   - `welcome-hero` - Hero section with course offerings
   
2. **Blog Panels** (`x-frontend.panels.blogs.*`)
   - `list` - Blog post listing
   - `details` - Individual blog post display

3. **Course Panels** (`x-frontend.panels.courses.*`)
   - `course-curriculum` - Course content display
   - `todays-schedule` - Schedule information
   - `sidebar` - Course navigation

4. **Account Panels** (`x-frontend.panels.accounts.*`)
   - `quick-profile` - User profile widget

### Creating New Panels

To create a new panel component:

1. Create the component file:
```blade
{{-- resources/views/components/frontend/panels/category/panel-name.blade.php --}}
<div class="panel-wrapper">
    <!-- Panel content here -->
</div>
```

2. Use in page configuration:
```php
// In controller or page data
$content = [
    'panels' => [
        'category.panel-name'
    ]
];
```

## Site Structure Components

### Header (`x-frontend.site.partials.header`)
Contains the site navigation and branding elements.

### Footer (`x-frontend.site.partials.footer`)
Site footer with links and company information.

### Partials
- `topbar` - Top navigation bar
- `bottombar` - Bottom navigation
- `navbar-toggler` - Mobile menu toggle

## UI Components

### Breadcrumbs (`x-frontend.ui.breadcrumbs`)
Generates navigation breadcrumbs based on current route.

### Page Loader (`x-frontend.ui.page-loader`)
Professional loading animation for page transitions.

## Component Usage Patterns

### Basic Page Layout
```blade
<x-frontend.site.site-wrapper :title="'Page Title'">
    <x-frontend.site.partials.header />
    
    <main class="main-page-content">
        <!-- Page content -->
    </main>
    
    <x-frontend.site.partials.footer />
</x-frontend.site.site-wrapper>
```

### Dynamic Panel Page
```blade
<x-frontend.site.site-wrapper :title="$pageTitle">
    <x-frontend.site.partials.header />
    
    <main class="main-page-content">
        <x-frontend.site.render-panels :page="$pageContent" />
    </main>
    
    <x-frontend.site.partials.footer />
</x-frontend.site.site-wrapper>
```

### Blog Page Example
```blade
<x-frontend.site.site-wrapper :title="'Knowledge Library'">
    <x-slot:head>
        <meta name="description" content="Security training resources">
        <link rel="stylesheet" href="{{ asset('css/components/blog.css') }}">
    </x-slot:head>

    <x-frontend.site.partials.header />
    <x-frontend.ui.breadcrumbs />
    
    <x-frontend.panels.blogs.list 
        :posts="$posts" 
        :categories="$categories"
    />
    
    <x-frontend.site.partials.footer />
</x-frontend.site.site-wrapper>
```

## Styling System

### CSS Organization
Components can include their own styling using the `@push('component-styles')` directive:

```blade
@push('component-styles')
    @php
        $manifestPath = public_path('build/manifest.json');
        $useVite = false;
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            if (is_array($manifest) && array_key_exists('resources/css/components/component-name.css', $manifest)) {
                $useVite = true;
            }
        }
    @endphp

    @if ($useVite)
        @vite(['resources/css/components/component-name.css'])
    @else
        <link rel="stylesheet" href="{{ asset('css/components/component-name.css') }}">
    @endif
@endpush
```

### CSS File Organization
```
resources/css/
├── style.css                 # Main stylesheet with imports
├── components/               # Component-specific styles
│   ├── blog.css
│   ├── welcome-hero.css
│   ├── courses.css
│   └── ...
└── app.css                   # Application styles
```

## Best Practices

### 1. Component Naming
- Use descriptive, hierarchical names
- Follow Laravel's dot notation for nested components
- Example: `x-frontend.panels.courses.curriculum`

### 2. Component Isolation
- Each component should be self-contained
- Include necessary styles within the component
- Use `@push` and `@stack` for CSS and JavaScript

### 3. Props and Slots
- Use typed props with default values
- Provide meaningful slot names
- Document expected props and slots

### 4. Responsive Design
- Components should be mobile-first
- Use Bootstrap classes for consistency
- Test on various screen sizes

### 5. Performance
- Use Vite for asset compilation
- Optimize images and media
- Minimize CSS and JavaScript

## Advanced Features

### Dynamic Component Loading
```blade
<x-dynamic-component :component="'frontend.panels.' . $panelName" />
```

### Component Composition
```blade
{{-- Parent component --}}
<div class="parent-wrapper">
    <x-frontend.ui.breadcrumbs />
    {{ $slot }}
    <x-frontend.ui.pagination :items="$items" />
</div>
```

### Data Binding
```blade
<x-frontend.panels.courses.list 
    :courses="$courses"
    :filters="$filters"
    :user="Auth::user()"
/>
```

## Troubleshooting

### Common Issues

1. **Component Not Found**
   - Check file path matches component name
   - Verify namespace structure
   - Clear view cache: `php artisan view:clear`

2. **Styles Not Loading**
   - Check Vite manifest file
   - Verify CSS file exists
   - Run `npm run build` to compile assets

3. **Props Not Passing**
   - Use `:prop` syntax for PHP variables
   - Check for typos in prop names
   - Verify component accepts the prop

### Debugging Tips
- Use `@dump($variableName)` to inspect data
- Check Laravel logs for component errors
- Use browser developer tools for CSS issues

## Migration Guide

### From Legacy Views
1. Wrap content in `x-frontend.site.site-wrapper`
2. Convert sections to panel components
3. Move styles to component-specific CSS files
4. Update controllers to use new data structure

### Component Updates
- Always test components in isolation
- Update documentation when changing interfaces
- Consider backward compatibility

This component system provides a robust foundation for building maintainable, scalable Laravel applications with consistent UI patterns and professional styling.
