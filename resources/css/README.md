# FROST CSS ARCHITECTURE
## Consolidated and Organized Structure

### 📁 Root Level Structure
```
resources/css/
├── root.css                    # Main CSS variables & base styles
├── style.css                   # Central import hub (main stylesheet)
├── utilities.css               # Utility classes using Frost variables
├── components/                 # UI component styles
├── pages/                      # Page-specific styles
├── vendor/                     # Third-party overrides
└── themes/                     # Future theme variations
```

### 🎨 CSS Variables System (root.css)
- **Colors**: Frost brand colors with semantic naming
- **Spacing**: Consistent spacing scale (xs, sm, md, lg, xl, 2xl)
- **Typography**: Font families, sizes, and weights
- **Borders**: Consistent radius system
- **Shadows**: Professional shadow system
- **Transitions**: Standardized animation timings

### 🔧 Component Architecture (components/)
- `topbar.css` - Site top navigation bar
- `bottombar.css` - Site bottom navigation
- `header.css` - Main site header
- `footer.css` - Site footer
- `welcome-hero.css` - Hero section component
- `courses.css` - Course listing components
- `login-form.css` - Authentication forms
- `loader.css` - Loading states
- `scroll-up.css` - Back to top button
- `support-services.css` - Support section styling
- `top-right-nav.css` - User navigation menu

### 📄 Page Styles (pages/)
- `auth.css` - Login/register pages
- `dashboard.css` - Dashboard layouts
- `admin.css` - Admin interface
- `admin-settings.css` - Settings pages
- `site.css` - Public site pages (FAQ, Blog, Contact)

### 🔌 Vendor Overrides (vendor/)
- `bootstrap-overrides.css` - Bootstrap customizations using Frost colors
- `filepond.css` - File upload component styling

### 📈 Benefits of This Structure
1. **No Duplicates**: Single source of truth for all styles
2. **Consistent Variables**: All components use standardized Frost variables
3. **Modular**: Easy to maintain and extend individual components
4. **Performance**: Single main stylesheet with organized imports
5. **Scalable**: Clear separation of concerns and easy to add new components
6. **Theme-Ready**: Variables make it easy to create new themes

### 🚀 Usage
The main `style.css` file imports everything in the correct order:
1. Root variables and base styles
2. Layout components (header, footer, etc.)
3. UI components (hero, courses, etc.)  
4. Page-specific styles
5. Utility classes
6. Vendor overrides

### 🎯 Frost Color System
All colors follow the `--frost-` prefix convention:
- `--frost-primary-color`: #212a3e (main brand dark blue)
- `--frost-secondary-color`: #394867 (secondary blue-gray)
- `--frost-highlight-color`: #fede59 (brand yellow)
- `--frost-info-color`: #17aac9 (accent blue)
- Plus success, warning, danger, and neutral variations

This architecture ensures maintainable, consistent, and performant CSS across the entire Frost application.
