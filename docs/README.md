# React Context-Based Architecture

This document explains the new React pattern implemented for the Instructor Portal, featuring context-based state management with cached settings.

## Architecture Overview

The application follows a layered architecture:

```
InstructorApp (Entry Point)
├── QueryClientProvider (TanStack Query)
├── SettingsProvider (Context + Cache)
├── ErrorBoundary
└── InstructorDataLayer (UI Components)
    └── Child Components (using context hooks)
```

## Key Components

### 1. Entry Point (`InstructorApp.tsx`)

The main entry point that sets up:
- React Query Client with global defaults
- Settings Context Provider with configurable cache time
- Error boundaries and loading states
- Development tools (React Query Devtools)

```tsx
<InstructorApp 
  instructorId="123"
  debug={true}
  cacheTime={15 * 60 * 1000} // 15 minutes
/>
```

### 2. Settings Context (`SettingsContext.tsx`)

Provides cached application settings with:
- **15-minute default cache** (configurable)
- **Automatic stale detection**
- **Cache management functions**
- **Error handling**
- **Loading states**

#### Available Hooks:

```tsx
// Get all settings (use sparingly)
const { config, settings, user, app, isLoading, isError } = useSettings();

// Get specific sections (recommended - better performance)
const { config } = useAppConfig();
const { settings } = useAppSettings();  
const { user } = useUser();
const { app } = useAppInfo();

// Cache management
const { lastFetched, isStale, refetch, clearCache } = useCacheStatus();
```

### 3. Data Layer (`InstructorDataLayer.tsx`)

The main UI component that:
- Uses context hooks for data
- Displays cache status indicators
- Provides refresh functionality
- Shows loading/error states

### 4. Child Components

Any component can access settings using the context hooks:

```tsx
import { useAppConfig, useUser } from '../Context/SettingsContext';

const MyComponent = () => {
  const { config } = useAppConfig();
  const { user } = useUser();
  
  return (
    <div>
      <h1>{config?.adminlte?.title}</h1>
      <p>Welcome, {user?.name}!</p>
    </div>
  );
};
```

## Cache Management

### Cache Behavior
- **Cache Duration**: 15 minutes by default (configurable)
- **Stale Detection**: Automatic based on last fetch timestamp
- **Background Refetch**: No automatic background updates
- **Manual Control**: Refresh and clear cache functions available

### Cache Status Indicators
- 🟢 **Fresh**: Data is within cache time
- 🟡 **Stale**: Data is older than cache time
- 🔄 **Loading**: Currently fetching new data

### API Configuration
The cache uses TanStack Query with these settings:
- `staleTime`: Matches cache time (15 minutes default)
- `gcTime`: Double the cache time (30 minutes default)  
- `refetchOnWindowFocus`: false
- `refetchOnMount`: false (if fresh data exists)

## Benefits

### 1. Performance
- **Reduced API Calls**: Settings cached for 15 minutes
- **Selective Re-renders**: Components only re-render when their specific data changes
- **Lazy Loading**: Components loaded on-demand

### 2. Developer Experience
- **TypeScript Support**: Full type safety
- **Debug Tools**: React Query Devtools in development
- **Error Boundaries**: Graceful error handling
- **Cache Visibility**: Clear cache status indicators

### 3. Maintainability
- **Separation of Concerns**: Clear boundaries between layers
- **Reusable Hooks**: Context hooks can be used anywhere
- **Configurable**: Cache time and debug modes configurable
- **Testable**: Each layer can be tested independently

## Usage Examples

### Basic Component
```tsx
import React from 'react';
import { useAppConfig, useUser } from '../Context/SettingsContext';

const Dashboard = () => {
  const { config, isLoading } = useAppConfig();
  const { user } = useUser();
  
  if (isLoading) return <div>Loading...</div>;
  
  return (
    <div>
      <h1>{config?.adminlte?.title}</h1>
      <p>Welcome back, {user?.name}!</p>
    </div>
  );
};
```

### Cache Management
```tsx
import React from 'react';
import { useCacheStatus } from '../Context/SettingsContext';

const CacheControls = () => {
  const { isStale, lastFetched, refetch, clearCache } = useCacheStatus();
  
  return (
    <div>
      <span className={`badge ${isStale ? 'badge-warning' : 'badge-success'}`}>
        {isStale ? 'Stale' : 'Fresh'}
      </span>
      <button onClick={refetch}>Refresh</button>
      <button onClick={clearCache}>Clear Cache</button>
      <small>Last updated: {lastFetched?.toLocaleString()}</small>
    </div>
  );
};
```

### Error Handling
```tsx
import React from 'react';
import { useSettings } from '../Context/SettingsContext';

const DataComponent = () => {
  const { config, isLoading, isError, error } = useSettings();
  
  if (isLoading) return <div>Loading...</div>;
  
  if (isError) {
    return (
      <div className="alert alert-danger">
        Error: {error?.message}
        <button onClick={() => window.location.reload()}>
          Retry
        </button>
      </div>
    );
  }
  
  return <div>{/* Your component */}</div>;
};
```

## Configuration

### Environment Variables
```typescript
// Development mode enables:
// - Console logging
// - React Query Devtools
// - Debug components
const isDev = process.env.NODE_ENV === 'development';
```

### Container Data Attributes
```html
<div 
  id="instructor-dashboard-container"
  data-instructor-id="123"
  data-debug="true"
  data-cache-time="900000"
></div>
```

## Migration Guide

### From Direct Hook Usage
```tsx
// Old pattern
const { data, isLoading, error } = useLaravelAdminHook();

// New pattern  
const { config, isLoading, isError } = useAppConfig();
```

### From Prop Drilling
```tsx
// Old pattern - passing data through props
<ParentComponent data={data}>
  <ChildComponent data={data} />
</ParentComponent>

// New pattern - using context
<ParentComponent>
  <ChildComponent /> {/* Gets data from context */}
</ParentComponent>
```

## Best Practices

1. **Use Specific Hooks**: Prefer `useAppConfig()` over `useSettings()` for better performance
2. **Handle Loading States**: Always check `isLoading` before rendering data
3. **Error Boundaries**: Wrap components in error boundaries for graceful failures
4. **Cache Awareness**: Show cache status to users when appropriate
5. **Development Tools**: Use React Query Devtools to monitor cache behavior

## File Structure

```
resources/js/React/Instructor/
├── InstructorApp.tsx           # Entry point with providers
├── Context/
│   └── SettingsContext.tsx    # Settings context and hooks
├── InstructorDataLayer.tsx     # Main UI component  
├── Components/
│   └── ExampleSettingsConsumer.tsx # Usage example
├── app.tsx                     # DOM mounting logic
└── README.md                   # This documentation
```
