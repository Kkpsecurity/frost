/**
 * Enhanced route-based lazy loading with React.lazy()
 * For better code splitting and performance
 */

import React, { Suspense } from 'react';

// Lazy load components based on routes
export const LazyComponents = {
    // Admin Components
    AdminDashboard: React.lazy(() => import('../React/Admin/AdminDashboard')),
    MediaManager: React.lazy(() => import('../React/Admin/MediaManager/AdvancedUploadModal')),

    // Student Components
    StudentDashboard: React.lazy(() => import('../React/Student/StudentDashboard')),
    VideoPlayer: React.lazy(() => import('../React/Student/Components/VideoPlayer')),
    LessonViewer: React.lazy(() => import('../React/Student/Components/LessonViewer')),
    AssignmentSubmission: React.lazy(() => import('../React/Student/Offline/AssignmentSubmission')),

    // Instructor Components
    InstructorDashboard: React.lazy(() => import('../React/Instructor/Classroom/InstructorDashboard')),
    ClassroomManager: React.lazy(() => import('../React/Instructor/Classroom/ClassroomManager')),
    StudentManagement: React.lazy(() => import('../React/Instructor/Classroom/StudentManagement')),
    LiveClassControls: React.lazy(() => import('../React/Instructor/Classroom/LiveClassControls')),

    // Support Components
    SupportDashboard: React.lazy(() => import('../React/Support/Components/SupportDashboard')),
    TicketManager: React.lazy(() => import('../React/Support/Components/TicketManager')),
    StudentSearch: React.lazy(() => import('../React/Support/Components/StudentSearch')),
};

// Loading fallback component
const ComponentLoader = ({ componentName }: { componentName: string }) => (
    <div className="d-flex justify-content-center align-items-center p-4">
        <div className="spinner-border text-primary" role="status">
            <span className="visually-hidden">Loading {componentName}...</span>
        </div>
        <span className="ms-2">Loading {componentName}...</span>
    </div>
);

// Wrapper component for lazy loading with suspense
export const LazyComponentWrapper: React.FC<{
    component: keyof typeof LazyComponents;
    fallbackName?: string;
    props?: any;
}> = ({ component, fallbackName, props = {} }) => {
    const Component = LazyComponents[component];

    return (
        <Suspense fallback={<ComponentLoader componentName={fallbackName || component} />}>
            <Component {...props} />
        </Suspense>
    );
};

// Route-based component renderer
export function renderLazyComponent(
    componentName: keyof typeof LazyComponents,
    containerId: string,
    props: any = {}
) {
    const container = document.getElementById(containerId);
    if (container && LazyComponents[componentName]) {
        const { createRoot } = require('react-dom/client');
        const root = createRoot(container);

        root.render(
            <LazyComponentWrapper
                component={componentName}
                fallbackName={componentName}
                props={props}
            />
        );

        console.log(`🚀 Lazy loaded: ${componentName}`);
    } else {
        console.error(`❌ Container '${containerId}' or component '${componentName}' not found`);
    }
}
