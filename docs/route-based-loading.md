# Route-Based Component Loading System

This system implements conditional loading of React components based on the current URL route to optimize performance by only loading components that are actually needed on each page.

## File Structure

```
resources/js/
├── app.ts              # Main entry point for student/general components
├── admin.ts            # Admin panel components
├── instructor.ts       # Instructor dashboard components  
├── support.ts          # Support panel components
├── utils/
│   └── routeUtils.ts   # Route checking utilities
└── React/
    ├── Admin/          # Admin React components
    ├── Instructor/     # Instructor React components
    ├── Student/        # Student React components
    └── Support/        # Support React components
```

## How It Works

Each entry point file (app.ts, admin.ts, etc.) checks the current URL path and only loads the React components that are needed for that specific route.

### Example Usage

```typescript
// Load components only for /classroom/portal route
if (RouteCheckers.isClassroomPortal()) {
    require("./React/Student/app");
}

// Load components only for /admin/dashboard route  
if (RouteCheckers.isAdminDashboard()) {
    require("./React/Admin/app");
}
```

## Route Checkers Available

### Admin Routes
- `isAdminDashboard()` - `/admin/dashboard`
- `isAdminMedia()` - `/admin/media`
- `isAdminSupport()` - `/admin/support`
- `isAdminInstructors()` - `/admin/instructors`
- `isAdminRoute()` - Any route starting with `/admin`

### Instructor Routes
- `isInstructorDashboard()` - `/instructor/dashboard`
- `isInstructorClassroom()` - `/instructor/classroom`
- `isInstructorStudents()` - `/instructor/students`
- `isInstructorRoute()` - Any route starting with `/instructor`
- `isLiveClass()` - Any route containing "live-class"

### Support Routes
- `isSupportDashboard()` - `/support/dashboard`
- `isSupportTickets()` - `/support/tickets`
- `isSupportStudents()` - `/support/students`
- `isSupportRoute()` - Any route starting with `/support`

### Student Routes
- `isClassroomPortal()` - `/classroom/portal`
- `isClassroomPortalZoom()` - `/classroom/portal/zoom`
- `isAccountProfile()` - `/account/profile`
- `isStudentOffline()` - `/student/offline`
- `isLessonViewer()` - Any route containing "lesson"

## Adding New Routes

1. Add the route checker to `routeUtils.ts`:
```typescript
export const RouteCheckers = {
    // Add your new checker
    isNewRoute: () => isRoute(["your", "route", "segments"]),
    // ... existing checkers
};
```

2. Add the conditional loading to the appropriate entry file:
```typescript
if (RouteCheckers.isNewRoute()) {
    require("./React/YourComponent");
}
```

3. Update the Vite config if you need a new entry point:
```javascript
input: [
    "resources/js/your-new-entry.ts",
    // ... existing entries
]
```

## Benefits

- **Performance**: Only loads JavaScript for components actually used on the current page
- **Maintainable**: Clear separation of concerns by route
- **Scalable**: Easy to add new routes and components
- **Debuggable**: Console logging shows which routes are loaded

## Blade Template Integration

In your Blade templates, include the appropriate entry point:

```blade
{{-- For admin pages --}}
@vite(['resources/js/admin.ts'])

{{-- For instructor pages --}}
@vite(['resources/js/instructor.ts'])

{{-- For student pages --}}
@vite(['resources/js/app.ts'])

{{-- For support pages --}}
@vite(['resources/js/support.ts'])
```

## Development

Run the development server:
```bash
npm run dev
```

Build for production:
```bash
npm run build
```

The system will automatically log route information to the console for debugging purposes.
