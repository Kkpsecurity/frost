/**
 * Environment-aware component loading
 * Provides different loading strategies based on environment
 */

import { RouteCheckers } from './routeUtils';

// Environment detection
const isDevelopment = process.env.NODE_ENV === 'development';
const isProduction = process.env.NODE_ENV === 'production';

// Component loading configuration
interface ComponentConfig {
    path: string;
    preload?: boolean;  // Whether to preload this component
    critical?: boolean; // Whether this is a critical component
    dependencies?: string[]; // Other components this depends on
}

// Component registry
const componentRegistry: Record<string, ComponentConfig> = {
    // Student Components
    'student-portal': {
        path: './React/Student/app',
        critical: true,
        preload: true
    },
    'video-player': {
        path: './React/Student/Components/VideoPlayer',
        dependencies: ['student-portal']
    },
    'lesson-viewer': {
        path: './React/Student/Components/LessonViewer',
        preload: true
    },
    'assignment-submission': {
        path: './React/Student/Offline/AssignmentSubmission'
    },
    'student-dashboard': {
        path: './React/Student/StudentDashboard',
        critical: true
    },

    // Admin Components
    'admin-dashboard': {
        path: './React/Admin/app',
        critical: true,
        preload: true
    },
    'media-manager': {
        path: './React/Admin/MediaManager/AdvancedUploadModal'
    },

    // Instructor Components
    'instructor-dashboard': {
        path: './React/Instructor/app',
        critical: true
    },
    'classroom-manager': {
        path: './React/Instructor/Classroom/ClassroomManager'
    },
    'student-management': {
        path: './React/Instructor/Classroom/StudentManagement'
    },
    'live-class-controls': {
        path: './React/Instructor/Classroom/LiveClassControls'
    },

    // Support Components
    'support-dashboard': {
        path: './React/Support/app',
        critical: true
    },
    'ticket-manager': {
        path: './React/Support/Components/TicketManager'
    },
    'student-search': {
        path: './React/Support/Components/StudentSearch'
    }
};

// Enhanced component loader with error handling and performance tracking
export class ComponentLoader {
    private loadedComponents = new Set<string>();
    private loadingPromises = new Map<string, Promise<void>>();

    async loadComponent(componentKey: string): Promise<boolean> {
        if (this.loadedComponents.has(componentKey)) {
            console.log(`📦 Component already loaded: ${componentKey}`);
            return true;
        }

        if (this.loadingPromises.has(componentKey)) {
            console.log(`⏳ Component loading in progress: ${componentKey}`);
            await this.loadingPromises.get(componentKey);
            return this.loadedComponents.has(componentKey);
        }

        const config = componentRegistry[componentKey];
        if (!config) {
            console.error(`❌ Component not found in registry: ${componentKey}`);
            return false;
        }

        const loadPromise = this.performLoad(componentKey, config);
        this.loadingPromises.set(componentKey, loadPromise);

        try {
            await loadPromise;
            this.loadedComponents.add(componentKey);
            console.log(`✅ Component loaded successfully: ${componentKey}`);
            return true;
        } catch (error) {
            console.error(`❌ Failed to load component: ${componentKey}`, error);
            return false;
        } finally {
            this.loadingPromises.delete(componentKey);
        }
    }

    private async performLoad(componentKey: string, config: ComponentConfig): Promise<void> {
        const startTime = performance.now();

        // Load dependencies first
        if (config.dependencies) {
            await Promise.all(
                config.dependencies.map(dep => this.loadComponent(dep))
            );
        }

        // Load the component
        return new Promise((resolve, reject) => {
            try {
                require(config.path);
                const loadTime = performance.now() - startTime;

                if (isDevelopment) {
                    console.log(`📊 ${componentKey} loaded in ${loadTime.toFixed(2)}ms`);
                }

                resolve();
            } catch (error) {
                reject(error);
            }
        });
    }

    // Preload critical components
    async preloadCriticalComponents(): Promise<void> {
        const criticalComponents = Object.entries(componentRegistry)
            .filter(([_, config]) => config.critical)
            .map(([key, _]) => key);

        if (criticalComponents.length > 0) {
            console.log('🚀 Preloading critical components:', criticalComponents);
            await Promise.all(
                criticalComponents.map(key => this.loadComponent(key))
            );
        }
    }

    // Get loading statistics
    getStats() {
        return {
            loaded: Array.from(this.loadedComponents),
            loading: Array.from(this.loadingPromises.keys()),
            total: Object.keys(componentRegistry).length
        };
    }
}

// Global component loader instance
export const componentLoader = new ComponentLoader();

// Route-based loading functions
export const routeBasedLoader = {
    // Student routes
    loadStudentComponents: async () => {
        if (RouteCheckers.isClassroomPortal()) {
            await componentLoader.loadComponent('student-portal');
        }
        if (RouteCheckers.isClassroomPortalZoom()) {
            await componentLoader.loadComponent('video-player');
        }
        if (RouteCheckers.isAccountProfile()) {
            await componentLoader.loadComponent('student-dashboard');
        }
        if (RouteCheckers.isStudentOffline()) {
            await componentLoader.loadComponent('assignment-submission');
        }
        if (RouteCheckers.isLessonViewer()) {
            await componentLoader.loadComponent('lesson-viewer');
        }
    },

    // Admin routes
    loadAdminComponents: async () => {
        if (RouteCheckers.isAdminDashboard()) {
            await componentLoader.loadComponent('admin-dashboard');
        }
        if (RouteCheckers.isAdminMedia()) {
            await componentLoader.loadComponent('media-manager');
        }
    },

    // Instructor routes
    loadInstructorComponents: async () => {
        if (RouteCheckers.isInstructorDashboard()) {
            await componentLoader.loadComponent('instructor-dashboard');
        }
        if (RouteCheckers.isInstructorClassroom()) {
            await componentLoader.loadComponent('classroom-manager');
        }
        if (RouteCheckers.isInstructorStudents()) {
            await componentLoader.loadComponent('student-management');
        }
        if (RouteCheckers.isLiveClass()) {
            await componentLoader.loadComponent('live-class-controls');
        }
    },

    // Support routes
    loadSupportComponents: async () => {
        if (RouteCheckers.isSupportDashboard()) {
            await componentLoader.loadComponent('support-dashboard');
        }
        if (RouteCheckers.isSupportTickets()) {
            await componentLoader.loadComponent('ticket-manager');
        }
        if (RouteCheckers.isSupportStudents()) {
            await componentLoader.loadComponent('student-search');
        }
    }
};

// Initialize preloading for production
if (isProduction) {
    // Preload critical components in production
    componentLoader.preloadCriticalComponents();
}
