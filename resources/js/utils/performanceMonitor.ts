/**
 * Performance monitoring for route-based component loading
 */

interface LoadMetrics {
    route: string;
    componentsLoaded: string[];
    loadTime: number;
    timestamp: number;
}

class ComponentLoadMonitor {
    private loadStartTime: number;
    private loadedComponents: string[] = [];

    constructor() {
        this.loadStartTime = performance.now();
    }

    trackComponentLoad(componentName: string) {
        this.loadedComponents.push(componentName);
        console.log(`📦 Component loaded: ${componentName}`);
    }

    finishLoading() {
        const loadTime = performance.now() - this.loadStartTime;
        const metrics: LoadMetrics = {
            route: window.location.pathname,
            componentsLoaded: this.loadedComponents,
            loadTime,
            timestamp: Date.now()
        };

        console.log('📊 Loading Performance:', {
            route: metrics.route,
            components: metrics.componentsLoaded.length,
            loadTime: `${loadTime.toFixed(2)}ms`
        });

        // Store metrics for analytics
        this.storeMetrics(metrics);

        return metrics;
    }

    private storeMetrics(metrics: LoadMetrics) {
        // Store in localStorage for development debugging
        if (typeof window !== 'undefined') {
            const existing = JSON.parse(localStorage.getItem('componentLoadMetrics') || '[]');
            existing.push(metrics);

            // Keep only last 50 entries
            if (existing.length > 50) {
                existing.splice(0, existing.length - 50);
            }

            localStorage.setItem('componentLoadMetrics', JSON.stringify(existing));
        }
    }

    static getStoredMetrics(): LoadMetrics[] {
        if (typeof window !== 'undefined') {
            return JSON.parse(localStorage.getItem('componentLoadMetrics') || '[]');
        }
        return [];
    }
}

export const loadMonitor = new ComponentLoadMonitor();

// Enhanced safe require with monitoring
export function monitoredRequire(componentPath: string, componentName: string = componentPath) {
    try {
        const startTime = performance.now();
        require(componentPath);
        const endTime = performance.now();

        loadMonitor.trackComponentLoad(componentName);
        console.log(`✅ ${componentName} loaded in ${(endTime - startTime).toFixed(2)}ms`);
    } catch (error) {
        console.error(`❌ Failed to load: ${componentName}`, error);
    }
}
