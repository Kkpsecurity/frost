# React Panel & Widget Architecture

This document explains the new Panel and Widget architecture for building modular, reusable React components.

## Architecture Hierarchy

```
Pages (InstructorDataLayer.tsx)
├── Panels (Large sections/blocks)
│   ├── header.panel.tsx
│   ├── management.panel.tsx
│   ├── loading.panel.tsx
│   ├── error.panel.tsx
│   └── debug.panel.tsx
└── Widgets (Small reusable components)
    ├── statBox.widget.tsx
    ├── actionButton.widget.tsx
    ├── cacheStatus.widget.tsx
    ├── instructorStats.widget.tsx
    ├── instructorManagement.widget.tsx
    ├── classManagement.widget.tsx
    └── studentManagement.widget.tsx
```

## Naming Conventions

### Panels
- **File naming**: `name.panel.tsx`
- **Component naming**: `NamePanel`
- **Purpose**: Large sections or blocks that contain multiple widgets
- **Examples**: `header.panel.tsx`, `management.panel.tsx`

### Widgets
- **File naming**: `name.widget.tsx`
- **Component naming**: `NameWidget`
- **Purpose**: Small, reusable components (buttons, stats, charts, etc.)
- **Examples**: `statBox.widget.tsx`, `actionButton.widget.tsx`

## Component Types

### 1. Panels
Large section components that organize the page layout:

#### LoadingPanel (`loading.panel.tsx`)
```tsx
<LoadingPanel 
  message="Loading data..."
  minHeight="400px"
  showSpinner={true}
/>
```

#### ErrorPanel (`error.panel.tsx`)
```tsx
<ErrorPanel
  title="Error Loading Data"
  message="Something went wrong"
  onRetry={() => refetch()}
  retryText="Try Again"
/>
```

#### HeaderPanel (`header.panel.tsx`)
```tsx
<HeaderPanel 
  title="Dashboard Title"
  subtitle="Dashboard description"
  showStats={true}
/>
```

#### ManagementPanel (`management.panel.tsx`)
```tsx
<ManagementPanel 
  showInstructors={true}
  showClasses={true}
  showStudents={true}
/>
```

### 2. Widgets
Small, focused components for specific functionality:

#### StatBoxWidget (`statBox.widget.tsx`)
```tsx
<StatBoxWidget
  icon="fas fa-users"
  iconColor="bg-info"
  label="Users"
  value="150"
  description="Total Active Users"
  onClick={() => console.log('Clicked')}
/>
```

#### ActionButtonWidget (`actionButton.widget.tsx`)
```tsx
<ActionButtonWidget
  icon="fas fa-plus"
  text="Add New"
  variant="outline-primary"
  size="sm"
  onClick={() => console.log('Add clicked')}
/>
```

#### CacheStatusWidget (`cacheStatus.widget.tsx`)
```tsx
<CacheStatusWidget className="ml-2" />
```

## Benefits

### 1. Modularity
- **Reusable Components**: Widgets can be used across multiple panels
- **Consistent Design**: Standardized components ensure UI consistency
- **Easy Maintenance**: Changes to a widget affect all instances

### 2. Separation of Concerns
- **Panels**: Handle layout and organization
- **Widgets**: Handle specific functionality
- **Pages**: Compose panels into complete interfaces

### 3. Development Efficiency
- **Faster Development**: Pre-built widgets speed up new feature development
- **Consistent Patterns**: Developers know where to find and how to structure components
- **Easy Testing**: Small, focused components are easier to test

## Usage Examples

### Creating a New Panel
```tsx
// newFeature.panel.tsx
import React from 'react';
import { StatBoxWidget, ActionButtonWidget } from '../Widgets';

interface NewFeaturePanelProps {
  showStats?: boolean;
  className?: string;
}

const NewFeaturePanel: React.FC<NewFeaturePanelProps> = ({
  showStats = true,
  className = "",
}) => {
  return (
    <div className={`row ${className}`}>
      <div className="col-12">
        <div className="card">
          <div className="card-header">
            <h3 className="card-title">New Feature</h3>
          </div>
          <div className="card-body">
            {showStats && (
              <div className="row">
                <div className="col-md-6">
                  <StatBoxWidget
                    icon="fas fa-star"
                    label="New Items"
                    value="42"
                    iconColor="bg-success"
                  />
                </div>
                <div className="col-md-6">
                  <ActionButtonWidget
                    icon="fas fa-plus"
                    text="Add New Item"
                    variant="primary"
                    onClick={() => console.log('Add new item')}
                  />
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default NewFeaturePanel;
```

### Creating a New Widget
```tsx
// chartDisplay.widget.tsx
import React from 'react';

interface ChartDisplayWidgetProps {
  title: string;
  data: any[];
  chartType?: 'bar' | 'line' | 'pie';
  className?: string;
}

const ChartDisplayWidget: React.FC<ChartDisplayWidgetProps> = ({
  title,
  data,
  chartType = 'bar',
  className = "",
}) => {
  return (
    <div className={`chart-widget ${className}`}>
      <h5>{title}</h5>
      <div className="chart-container">
        {/* Chart implementation */}
        <p>Chart will be rendered here</p>
      </div>
    </div>
  );
};

export default ChartDisplayWidget;
```

### Using Panels in Pages
```tsx
// page.tsx
import React from 'react';
import { HeaderPanel, ManagementPanel, LoadingPanel } from './Panels';

const MyPage: React.FC = () => {
  const { isLoading } = useSettings();

  if (isLoading) {
    return <LoadingPanel message="Loading page..." />;
  }

  return (
    <div className="page-container">
      <HeaderPanel title="My Page" />
      <ManagementPanel />
      {/* Add more panels as needed */}
    </div>
  );
};
```

## File Structure

```
resources/js/React/Instructor/
├── Panels/
│   ├── index.ts                    # Export all panels
│   ├── loading.panel.tsx
│   ├── error.panel.tsx
│   ├── header.panel.tsx
│   ├── management.panel.tsx
│   ├── debug.panel.tsx
│   └── [newFeature].panel.tsx
├── Widgets/
│   ├── index.ts                    # Export all widgets
│   ├── statBox.widget.tsx
│   ├── actionButton.widget.tsx
│   ├── cacheStatus.widget.tsx
│   ├── instructorStats.widget.tsx
│   ├── instructorManagement.widget.tsx
│   ├── classManagement.widget.tsx
│   ├── studentManagement.widget.tsx
│   └── [newWidget].widget.tsx
├── InstructorDataLayer.tsx         # Main page component
└── PANELS_WIDGETS.md              # This documentation
```

## Import Patterns

### Individual Imports
```tsx
import LoadingPanel from './Panels/loading.panel';
import StatBoxWidget from './Widgets/statBox.widget';
```

### Bulk Imports (using index files)
```tsx
import { LoadingPanel, HeaderPanel, ManagementPanel } from './Panels';
import { StatBoxWidget, ActionButtonWidget } from './Widgets';
```

## Best Practices

### 1. Panel Guidelines
- **Single Responsibility**: Each panel should handle one major section
- **Configurable**: Use props to control what widgets are shown
- **Responsive**: Design for mobile and desktop
- **Accessible**: Include proper ARIA labels and keyboard navigation

### 2. Widget Guidelines
- **Focused Functionality**: Each widget should do one thing well
- **Reusable**: Design for use across multiple panels
- **Consistent API**: Similar widgets should have similar prop structures
- **Self-Contained**: Widgets should not depend on external layout

### 3. Composition Guidelines
- **Logical Hierarchy**: Pages → Panels → Widgets
- **Prop Drilling**: Minimize prop drilling by using context when appropriate
- **Performance**: Use React.memo for widgets that don't change often
- **Testing**: Test widgets and panels independently

## Migration Guide

### From Old Structure
```tsx
// Old way - everything in one component
const OldComponent = () => (
  <div>
    <div className="header-section">
      {/* Inline header code */}
    </div>
    <div className="stats-section">
      {/* Inline stats code */}
    </div>
    <div className="management-section">
      {/* Inline management code */}
    </div>
  </div>
);

// New way - using panels and widgets
const NewComponent = () => (
  <div>
    <HeaderPanel showStats={true} />
    <ManagementPanel />
  </div>
);
```

### Benefits of Migration
- **Reduced Code Duplication**: Reusable widgets
- **Better Testing**: Individual component testing
- **Easier Maintenance**: Changes in one place
- **Improved Readability**: Clear component hierarchy
- **Faster Development**: Pre-built components

This architecture provides a scalable foundation for building complex React applications with consistent, maintainable components.
