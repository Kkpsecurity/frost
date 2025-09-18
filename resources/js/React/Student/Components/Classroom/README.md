# Modern Classroom UI Components

This directory contains the modernized classroom interface components designed to replace the existing student classroom UI while maintaining the familiar layout and color scheme.

## Components Overview

### Core Components

#### `StudentClassroomShell.tsx`
Main layout wrapper that provides the responsive shell for the entire classroom interface.
- **Features**: Responsive sidebar toggle, full-height layout, mobile-responsive
- **Props**: `studentName`, `courseTitle`

#### `StudentClassroomSidebar.tsx` 
Lesson sidebar with modernized green cards matching the current design.
- **Features**: Collapsible sidebar (300px → 60px), lesson progress tracking, hover effects
- **States**: Expanded, collapsed, with tooltips in collapsed mode
- **Color Scheme**: Green cards for completed, yellow for in-progress, gray for not started

#### `ClassroomContent.tsx`
Main content area with title bar and tab navigation.
- **Features**: Course title display, "Take Exam" button, three-tab navigation
- **Tabs**: Home | Videos | Documents

### Tab Components

#### `DashboardTab.tsx` (Home Tab)
Course overview with cards matching the current design.
- **Left Card**: Course Details (purchase date, start date, expiry, completion)
- **Right Card**: Student Information (name, email, DOB, phone)
- **Bottom Card**: Lesson Progress with completion tracking

#### `VideoRoomTab.tsx`
Video library interface with progress tracking.
- **Features**: Video grid, progress indicators, search functionality
- **Interactive**: Play buttons, completion status, hover effects

#### `DocumentsTab.tsx`
Document management with categories and search.
- **Features**: Category filtering, search, file type icons, download/view actions
- **Categories**: Course Materials, Assignments, Reference, Certificates

## Design Features

### Color Scheme (Matching Current Design)
- **Background**: Dark navy (`#1a1f36`)
- **Cards**: Dark secondary (`#2c3448`) 
- **Lesson Cards**: Green (`#28a745`) for completed, Yellow (`#ffc107`) for progress
- **Headers**: Orange (`#f39c12`) for section headers
- **Text**: White with proper contrast ratios

### Responsive Behavior
- **Desktop (≥992px)**: Full sidebar (300px), all features visible
- **Collapsed**: Sidebar (60px) with lesson initials and tooltips
- **Mobile (<992px)**: Offcanvas sidebar with hamburger toggle

### Interactive Elements
- **Hover Effects**: Transform and shadow animations
- **Progress Tracking**: Visual progress bars and completion indicators
- **Search Functionality**: Real-time filtering in Videos and Documents tabs
- **Accessibility**: ARIA attributes, keyboard navigation, focus management

## Usage Example

```tsx
import { StudentClassroomShell } from './Classroom';

const MyClassroom = () => {
    return (
        <StudentClassroomShell 
            studentName="John Doe"
            courseTitle="FLORIDA CLASS 'G' 28 HOUR"
        />
    );
};
```

## Integration Notes

- All components use **Bootstrap 5** and **React-Bootstrap** exclusively
- **No custom CSS** - uses utility classes and inline styles
- **Data Props**: Currently uses mock data, ready for real data integration
- **State Management**: Local React state only, no external dependencies
- **Build**: Compiles successfully with existing Vite configuration

## Future Enhancements

- [ ] Mobile offcanvas sidebar implementation
- [ ] Real data integration with existing backend
- [ ] Video player integration
- [ ] Document viewer integration
- [ ] Progress persistence
- [ ] Keyboard shortcuts