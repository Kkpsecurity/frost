# React Component Structure Documentation

## Overview
This document outlines the React component architecture for the Frost application. The structure is organized into four main sections, each with its own entry point and component library.

## Directory Structure

```
resources/js/React/
├── Admin/
│   ├── app.tsx                 # Entry point for Admin components
│   └── Components/             # Admin-specific components
│       ├── AdminDashboard.tsx
│       ├── UserManagement.tsx
│       ├── SystemSettings.tsx
│       ├── ReportsManager.tsx
│       └── RoleManager.tsx
├── Support/
│   ├── app.tsx                 # Entry point for Support components
│   └── Components/             # Support-specific components
│       ├── SupportDashboard.tsx
│       ├── StudentSearch.tsx
│       └── TicketManager.tsx
├── Instructor/
│   ├── app.tsx                 # Entry point for Instructor components
│   └── Components/             # Instructor-specific components
│       ├── InstructorDashboard.tsx
│       ├── LiveClassControls.tsx
│       ├── ClassroomManager.tsx
│       └── StudentManagement.tsx
└── Student/
    ├── app.tsx                 # Entry point for Student components
    └── Components/             # Student-specific components
        ├── StudentDashboard.tsx
        ├── LessonViewer.tsx
        ├── VideoPlayer.tsx
        └── AssignmentSubmission.tsx
```

## Component Architecture

### Entry Points
Each section has its own `app.tsx` file that serves as the entry point. These files:
- Import all components for that section
- Expose a global render function for Laravel Blade integration
- Handle component mounting to DOM elements

### Blade Components Structure
Laravel Blade components are organized alongside React components for full modularity:

```
resources/views/components/admin/dashboard/
├── header.blade.php              # Dashboard header with title and date
├── stats-card.blade.php          # Reusable stats card component
├── mini-stats-card.blade.php     # Smaller stats card for support section
├── section-header.blade.php      # Section headers with icons
├── user-stats.blade.php          # User statistics section
└── support-stats.blade.php       # Support system statistics section
```

### Blade Component Usage
```blade
<!-- Dashboard Header -->
<x-admin.dashboard.header title="Custom Title" :showDate="true" />

<!-- Stats Card -->
<x-admin.dashboard.stats-card 
    title="Total Users"
    :count="150"
    icon="fas fa-users"
    color="info"
    link="/admin/users"
    linkText="Manage Users"
/>

<!-- Section Header -->
<x-admin.dashboard.section-header 
    title="Support Overview"
    icon="fas fa-life-ring"
    subtitle="(Live Data)"
    iconColor="primary"
/>
```

### Global Render Functions
- `window.renderAdminComponent(componentName, containerId, props)`
- `window.renderSupportComponent(componentName, containerId, props)`
- `window.renderInstructorComponent(componentName, containerId, props)`
- `window.renderStudentComponent(componentName, containerId, props)`

## Section Responsibilities

### Admin Section
**Purpose**: System administration and management
**Components**:
- `AdminDashboard`: System overview and statistics
- `UserManagement`: User CRUD operations and management
- `SystemSettings`: Configuration and settings management
- `ReportsManager`: Generate various system reports
- `RoleManager`: Role and permission management

**Planned Components**:
- `MediaManager`: File and media management system

### Support Section
**Purpose**: Customer support and help desk functionality
**Components**:
- `SupportDashboard`: Support team overview
- `StudentSearch`: Search and find student information
- `TicketManager`: Support ticket management

### Instructor Section
**Purpose**: Teaching tools and classroom management
**Components**:
- `InstructorDashboard`: Instructor overview and quick actions
- `LiveClassControls`: Real-time class management tools
- `ClassroomManager`: Virtual classroom setup and management
- `StudentManagement`: Student roster and management

### Student Section
**Purpose**: Learning interface and student tools
**Components**:
- `StudentDashboard`: Student portal and overview
- `LessonViewer`: Lesson content display
- `VideoPlayer`: Video playback with controls
- `AssignmentSubmission`: Assignment upload and submission

## Integration with Laravel

### Blade Template Usage
Components are integrated into Laravel Blade templates using the global render functions:

```html
<!-- Admin MediaManager Example -->
<div id="media-manager-container"></div>
<script>
    window.renderAdminComponent('MediaManager', 'media-manager-container', {
        disk: 'public',
        folder: 'images'
    });
</script>

<!-- Student Dashboard Example -->
<div id="student-dashboard-container"></div>
<script>
    window.renderStudentComponent('StudentDashboard', 'student-dashboard-container', {
        studentId: {{ $student->id }}
    });
</script>
```

### Build Configuration
Components are compiled through Vite and included in the appropriate pages based on user roles and sections.

## Migration Plan

### Phase 1: Structure Setup ✅
- [x] Create directory structure
- [x] Set up entry points
- [x] Create example components

### Phase 2: Component Migration (In Progress)
- [ ] Move existing components to appropriate sections
- [ ] Update imports and dependencies
- [ ] Test component functionality
- [ ] Update Blade templates

### Phase 3: Integration Testing
- [ ] Test all components in their new locations
- [ ] Verify global render functions work correctly
- [ ] Update build process if needed

### Phase 4: Documentation and Cleanup
- [ ] Complete component documentation
- [ ] Remove old component files
- [ ] Update team documentation

## Component Migration Tracking

### Completed Migrations
- **Settings Structure Migration** - Updated from dot notation (`chat.username`) to group field structure (`group: 'chat', key: 'username'`)
  - Updated SettingsController to handle both old and new formats
  - Updated edit.blade.php to show group information
  - Created migration command: `php artisan settings:migrate-to-groups`

### In Progress
*Will track components as they are moved*

### Pending Migration
*Will identify components that need to be moved*

## Development Guidelines

### Creating New Components
1. Determine which section the component belongs to
2. Create the component in the appropriate `/Components/` folder
3. Add the component to the section's `app.tsx` file
4. Update this documentation
5. Test integration with Blade templates

### Naming Conventions
- Component files: PascalCase (e.g., `MediaManager.tsx`)
- Component names: Match the file name
- Props interfaces: `{ComponentName}Props`

### TypeScript Requirements
- All components must be TypeScript
- Props must be typed with interfaces
- Use React.FC type for functional components

---

*Last Updated: August 10, 2025*
*Version: 1.0*
